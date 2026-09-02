<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class OtpService
{
    public function __construct(
        protected MobileNormalizationService $normalizer,
        protected SmsGatewayInterface $sms,
    ) {
    }

    /**
     * تولید و ارسال کد. کدهای فعال قبلی برای همان شماره/هدف لغو می‌شوند.
     */
    public function issue(string $mobileInput, string $purpose = 'login'): OtpCode
    {
        $mobile = $this->normalizer->normalize($mobileInput);
        $length = max(4, (int) config('sms.otp.length', 6));
        $code   = str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);

        return DB::transaction(function () use ($mobile, $purpose, $code) {
            OtpCode::where('mobile', $mobile)
                ->where('purpose', $purpose)
                ->active()
                ->update(['revoked_at' => now()]);

            $otp = OtpCode::create([
                'mobile'     => $mobile,
                'code_hash'  => Hash::make($code),
                'purpose'    => $purpose,
                'attempts'   => 0,
                'expires_at' => now()->addMinutes((int) config('sms.otp.ttl_minutes', 5)),
            ]);

            $this->sms->send($mobile, "کد ورود شما: {$code}");

            return $otp;
        });
    }

    /**
     * بررسی کد. در صورت موفقیت، OTP مصرف (تک‌مصرف) می‌شود.
     */
    public function verify(string $mobileInput, string $code, string $purpose = 'login'): OtpCode
    {
        $mobile = $this->normalizer->normalize($mobileInput);

        $otp = OtpCode::where('mobile', $mobile)
            ->where('purpose', $purpose)
            ->active()
            ->latest('id')
            ->first();

        if (! $otp) {
            throw new RuntimeException('کد فعالی یافت نشد؛ دوباره درخواست کنید.');
        }

        if ($otp->attempts >= (int) config('sms.otp.max_attempts', 5)) {
            $otp->update(['revoked_at' => now()]);
            throw new RuntimeException('تعداد تلاش‌های ناموفق بیش از حد مجاز است.');
        }

        if (! $otp->matches($code)) {
            $otp->increment('attempts');
            throw new RuntimeException('کد وارد شده صحیح نیست.');
        }

        $otp->update(['consumed_at' => now()]);

        return $otp;
    }
}
