<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MelipayamakService
{
    public function sendOtp(string $mobile, string $code): void
    {
        $username = config('services.melipayamak.username');
        $password = config('services.melipayamak.password');
        $patternId = config('services.melipayamak.pattern_id');

        if (! $username || ! $password || ! $patternId) {
            throw new RuntimeException(
                'تنظیمات سرویس ملی پیامک کامل نشده است.'
            );
        }

        /*
         * در پترن 388165 باید ترتیب متغیرها مطابق متن ثبت‌شده
         * در پنل ملی پیامک باشد.
         *
         * اگر پترن فقط یک متغیر، یعنی کد OTP، داشته باشد:
         */
        $text = $code;

        $response = Http::asForm()
            ->timeout(15)
            ->post(
                'https://rest.payamak-panel.com/api/SendSMS/SendByBaseNumber',
                [
                    'username' => $username,
                    'password' => $password,
                    'text' => $text,
                    'to' => $mobile,
                    'bodyId' => $patternId,
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'ارتباط با سرویس پیامک برقرار نشد.'
            );
        }

        $result = trim($response->body());

        /*
         * طبق مستندات، شناسه ارسال معمولاً عددی بزرگ‌تر از ۱۵ رقم است.
         * مقادیر منفی یا صفر نشان‌دهنده خطا هستند.
         */
        if (! is_numeric($result) || (int) $result <= 0) {
            throw new RuntimeException(
                'ارسال پیامک ناموفق بود. کد سرویس: ' . $result
            );
        }
    }
}
