<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * درایور فیک برای توسعه/تست — پیام‌ها در storage/logs/laravel.log ثبت می‌شوند.
 */
class FakeSmsGateway implements SmsGatewayInterface
{
    public function __construct(protected array $config = [])
    {
    }

    public function send(string $to, string $message): bool
    {
        Log::info('[FakeSms] پیامک ارسال شد', [
            'to'      => $to,
            'message' => $message,
            'from'    => config('sms.from'),
        ]);

        return true;
    }
}
