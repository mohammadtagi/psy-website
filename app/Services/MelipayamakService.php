<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MelipayamakService
{
    public function sendOtp(string $mobile, string $code): void
    {
        $username = (string) config('services.melipayamak.username');
        $password = (string) config('services.melipayamak.password');
        $patternId = (int) config('services.melipayamak.pattern_id');
        $endpoint = (string) config('services.melipayamak.endpoint');

        if ($username === '' || $password === '' || $patternId <= 0) {
            throw new RuntimeException(
                'تنظیمات سرویس ملی پیامک کامل نشده است.'
            );
        }
        $response = Http::asJson()
            ->timeout(15)
            ->post($endpoint, [
/*                'username' => $username,
                'password' => $password,*/
                'args' => ["$code"],
                'to' => "$mobile",
                'bodyId' => $patternId,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'ارتباط با سرویس ملی پیامک برقرار نشد.'
            );
        }

        $result = $this->extractResult($response);

        /*
         * طبق مستندات، ارسال موفق یک شناسه عددی بزرگ برمی‌گرداند.
         * کدهای کوچک یا منفی، کد خطای سرویس هستند.
         */

        if (! preg_match('/^\d{16,}$/', $result)) {
            throw new RuntimeException(
                'ارسال پیامک ناموفق بود. کد سرویس: ' . $result
            );
        }
    }

    private function extractResult(Response $response): string
    {
        $body = trim($response->body());

        // اگر پاسخ یک عدد خالص (مثبت یا منفی) بود
        if (preg_match('/^-?\d+$/', $body)) {
            return $body;
        }

        $json = $response->json();

        if (is_numeric($json)) {
            return (string) $json;
        }

        if (is_array($json)) {
            // اضافه شدن ReturnValue و returnValue
            $candidateKeys = [
                'ReturnValue',
                'returnValue',
                'Value',
                'value',
                'Result',
                'result',
                'SendSmsResult',
                'sendSmsResult',
                'recId',
            ];

            foreach ($candidateKeys as $key) {
                if (array_key_exists($key, $json) && is_numeric($json[$key])) {
                    return (string) $json[$key];
                }
            }
        }

        // اگر هیچ‌کدام نبود، پاسخ خام را در متن ارور قرار می‌دهیم تا دیباگ ساده باشد
        throw new RuntimeException(
            'پاسخ سرویس ملی پیامک قابل شناسایی نیست. پاسخ دریافتی: ' . $body
        );
    }

}
