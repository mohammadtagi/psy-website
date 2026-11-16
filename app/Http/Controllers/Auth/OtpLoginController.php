<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\MelipayamakService;
use Illuminate\Contracts\View\View;
use App\Support\BookingAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
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
    private const SESSION_REMEMBER_KEY = 'auth.otp.remember';

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

    public function sendOtp(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'max:30'],
            'remember' => ['sometimes', 'boolean'],
        ]);


        $mobile = $this->normalizeMobile(
            (string) $request->input('mobile')
        );

        if ($mobile === null) {
            throw ValidationException::withMessages([
                'mobile' => 'شماره موبایل معتبر نیست.',
            ]);
        }

        $otp = $this->sendCode($mobile, $request);
        $request->session()->put([
            self::SESSION_MOBILE_KEY => $mobile,
            self::SESSION_REMEMBER_KEY => $request->boolean('remember'),
        ]);


        if ($request->wantsJson()) {
            return $this->sentResponse($mobile, $otp);
        }

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


    public function verifyOtp(Request $request): RedirectResponse|JsonResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE_KEY);

        if (! is_string($mobile) || $mobile === '') {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages(['code' => 'فرآیند ورود منقضی شده است.']);
            }
            return redirect()
                ->route('auth.login')
                ->withErrors([
                    'mobile' => 'فرآیند ورود منقضی شده است.',
                ]);
        }

        if ($request->wantsJson()) {
            $request->validate(['code' => ['required', 'string', 'max:32']]);
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

        $remember = (bool) $request->session()->get(
                self::SESSION_REMEMBER_KEY,
                false
            ) && in_array($user->role, [

                User::ROLE_CLIENT,
                User::ROLE_PSYCHOLOGIST,
            ], true);

        /** @var \Illuminate\Auth\SessionGuard $guard */
        $guard = Auth::guard('web');

// مدت کوکی ورود پایدار، بر حسب دقیقه: ۳۰ روز
        $guard->setRememberDuration(60 * 24 * 30);

        $guard->login($user, $remember);

        $request->session()->forget([
            self::SESSION_MOBILE_KEY,
            self::SESSION_REMEMBER_KEY,
        ]);

        $request->session()->regenerate();

        if ($request->wantsJson()) {
            $dashboard = match ($user->role) {
                User::ROLE_ADMIN => 'admin.dashboard',
                User::ROLE_PSYCHOLOGIST => 'psychologist.dashboard',
                default => 'client.dashboard',
            };

            return response()->json(BookingAccount::payload($user) + [
                    'dashboard_url' => route($dashboard),
                    'csrf_token' => $request->session()->token(),
                ])->header('Cache-Control', 'no-store');
        }

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_PSYCHOLOGIST => redirect()->route('psychologist.dashboard'),
            default => redirect()->route('client.dashboard'),
        };
    }

    public function resendOtp(Request $request): RedirectResponse|JsonResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE_KEY);

        if (! is_string($mobile) || $mobile === '') {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages(['mobile' => 'فرآیند ورود منقضی شده است.']);
            }
            return redirect()->route('auth.login');
        }

        $otp = $this->sendCode($mobile, $request);

        if ($request->wantsJson()) {
            return $this->sentResponse($mobile, $otp);
        }

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

    private function sentResponse(string $mobile, OtpCode $otp): JsonResponse
    {
        return response()->json([
            'mobile_masked' => substr($mobile, 0, 4).'***'.substr($mobile, -4),
            'otp_length' => (int) config('services.otp.length', 6),
            'resend_after' => max(0, (int) ceil(now()->diffInSeconds(
                $otp->created_at->copy()->addSeconds((int) config('services.otp.resend_seconds', 60)),
                false,
            ))),
            'expires_in' => max(0, (int) ceil(now()->diffInSeconds($otp->expires_at, false))),
        ])->header('Cache-Control', 'no-store');
    }

    private function rateLimited(Request $request, string $message, int $seconds): never
    {
        if ($request->wantsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $message,
                'errors' => ['mobile' => [$message]],
            ], 429)->header('Retry-After', (string) max(1, $seconds))
                ->header('Cache-Control', 'no-store'));
        }

        throw ValidationException::withMessages(['mobile' => $message]);
    }

    private function sendCode(string $mobile, Request $request): OtpCode
    {
        $lock = Cache::lock('otp:send:' . $mobile, 10);

        if (! $lock->get()) {
            $this->rateLimited($request, 'لطفاً کمی بعد دوباره تلاش کنید.', 10);
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
                    (int) ceil(now()->diffInSeconds($nextAvailableAt, false))
                );

                $this->rateLimited(
                    $request,
                    sprintf('لطفاً %d ثانیه قبل از ارسال مجدد صبر کنید.', $remainingSeconds),
                    $remainingSeconds,
                );
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

            return $otp;
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
