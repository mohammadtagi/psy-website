<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\MelipayamakService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class OtpLoginController extends Controller
{
    private const SESSION_MOBILE_KEY = 'auth.otp.mobile';

    public function __construct(
        private readonly MelipayamakService $sms,
    ) {
    }

    public function showMobileForm(): View
    {
        return view('auth', [
            'step' => 'mobile',
        ]);
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'max:30'],
        ]);

        $mobile = $this->normalizeMobile(
            (string) $request->input('mobile')
        );

        if ($mobile === null) {
            throw ValidationException::withMessages([
                'mobile' => 'شماره موبایل معتبر نیست.',
            ]);
        }

        $this->sendCode($mobile);

        $request->session()->put(
            self::SESSION_MOBILE_KEY,
            $mobile
        );

        return redirect()
            ->route('auth.otp.form')
            ->with('status', 'کد تأیید برای شما ارسال شد.');

    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE_KEY);

        if (! is_string($mobile) || $mobile === '') {
            return redirect()->route('auth.login');
        }

        return view('auth', [
            'step' => 'otp',
            'mobile' => $mobile,
        ]);
    }


    public function verifyOtp(Request $request): RedirectResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE_KEY);

        if (! is_string($mobile) || $mobile === '') {
            return redirect()
                ->route('auth.login')
                ->withErrors([
                    'mobile' => 'فرآیند ورود منقضی شده است.',
                ]);
        }

        $length = (int) config('services.otp.length', 6);

        $code = $this->normalizeDigits(
            (string) $request->input('code')
        );

        if (! preg_match('/^\d{' . $length . '}$/', $code)) {
            throw ValidationException::withMessages([
                'code' => 'کد تأیید باید ' . $length . ' رقم باشد.',
            ]);
        }

        $result = DB::transaction(function () use ($mobile, $code): array {
            $otp = OtpCode::query()
                ->where('mobile', $mobile)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($otp === null) {
                return [
                    'error' => 'کد تأیید پیدا نشد. دوباره درخواست کد کنید.',
                ];
            }

            if ($otp->isConsumed()) {
                return [
                    'error' => 'این کد قبلاً استفاده شده است.',
                ];
            }

            if ($otp->isExpired()) {
                return [
                    'error' => 'کد تأیید منقضی شده است.',
                ];
            }

            if ($otp->hasReachedMaximumAttempts()) {
                return [
                    'error' => 'تعداد تلاش‌های مجاز به پایان رسیده است.',
                ];
            }

            if (! Hash::check($code, $otp->code_hash)) {
                $otp->increment('attempts');

                $message = $otp->attempts >= (int) config(
                    'services.otp.max_attempts',
                    5
                )
                    ? 'تعداد تلاش‌های مجاز به پایان رسیده است.'
                    : 'کد تأیید نادرست است.';

                return [
                    'error' => $message,
                ];
            }

            $otp->forceFill([
                'consumed_at' => now(),
            ])->save();

            $user = User::query()->firstOrCreate(
                [
                    'mobile' => $mobile,
                ],
                [
                    'name' => null,
                    'email' => null,
                    'password' => null,
                    'role' => User::ROLE_CLIENT,
                ]
            );


            return [
                'user' => $user,
            ];
        });

        if (isset($result['error'])) {
            throw ValidationException::withMessages([
                'code' => $result['error'],
            ]);
        }

        /** @var User $user */
        $user = $result['user'];

        Auth::login($user);

        $request->session()->forget(self::SESSION_MOBILE_KEY);
        $request->session()->regenerate();

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_PSYCHOLOGIST => redirect()->route('psychologist.dashboard'),
                        default => redirect()->route('client.dashboard'),
        };
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE_KEY);

        if (! is_string($mobile) || $mobile === '') {
            return redirect()->route('auth.login');
        }

        $this->sendCode($mobile);

        return redirect()
            ->route('auth.otp.form')
            ->with('status', 'کد تأیید جدید ارسال شد.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    private function sendCode(string $mobile): void
    {
        $lock = Cache::lock('otp:send:' . $mobile, 10);

        if (! $lock->get()) {
            throw ValidationException::withMessages([
                'mobile' => 'لطفاً کمی بعد دوباره تلاش کنید.',
            ]);
        }

        try {
            $latestOtp = OtpCode::query()
                ->where('mobile', $mobile)
                ->latest('id')
                ->first();

            $resendSeconds = (int) config(
                'services.otp.resend_seconds',
                60
            );

            if (
                $latestOtp !== null
                && $latestOtp->created_at !== null
                && $latestOtp->created_at->gt(
                    now()->subSeconds($resendSeconds)
                )
            ) {
                $nextAvailableAt = $latestOtp->created_at
                    ->copy()
                    ->addSeconds($resendSeconds);

                $remainingSeconds = max(
                    1,
                    (int) now()->diffInSeconds($nextAvailableAt)
                );

                throw ValidationException::withMessages([
                    'mobile' => sprintf(
                        'لطفاً %d ثانیه قبل از ارسال مجدد صبر کنید.',
                        $remainingSeconds
                    ),
                ]);
            }

            $length = (int) config('services.otp.length', 6);
            $code = str_pad(
                (string) random_int(0, (10 ** $length) - 1),
                $length,
                '0',
                STR_PAD_LEFT
            );

            $otp = OtpCode::query()->create([
                'mobile' => $mobile,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(
                    (int) config('services.otp.expires_in', 2)
                ),
                'attempts' => 0,
            ]);

            try {
                $this->sms->sendOtp($mobile, $code);
            } catch (Throwable $exception) {
                $otp->delete();

                report($exception);

                throw ValidationException::withMessages([
                    'mobile' => 'ارسال پیامک انجام نشد. لطفاً دوباره تلاش کنید.',
                ]);
            }

            OtpCode::query()
                ->where('mobile', $mobile)
                ->whereNull('consumed_at')
                ->where('id', '!=', $otp->id)
                ->update([
                    'consumed_at' => now(),
                ]);
        } finally {
            $lock->release();
        }
    }

    private function normalizeMobile(string $value): ?string
    {
        $value = $this->normalizeDigits($value);

        $value = preg_replace('/[^\d+]/u', '', $value) ?? '';

        if (str_starts_with($value, '+98')) {
            $value = '0' . substr($value, 3);
        } elseif (str_starts_with($value, '0098')) {
            $value = '0' . substr($value, 4);
        } elseif (str_starts_with($value, '98')) {
            $value = '0' . substr($value, 2);
        }

        return preg_match('/^09\d{9}$/', $value) === 1
            ? $value
            : null;
    }

    private function normalizeDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',
        ]);
    }
}
