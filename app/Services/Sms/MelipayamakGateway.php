<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * پیاده‌سازی درگاه ملی‌پیامک (Melipayamak REST API).
 */
class MelipayamakGateway implements SmsGatewayInterface
{
    public function __construct(protected array $config)
    {
    }

    public function send(string $to, string $message): bool
    {
        try {
            $response = Http::asJson()->post(rtrim($this->config['base_uri'], '/'), [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'to'       => $to,
                'from'     => $this->config['origin'] ?: config('sms.from'),
                'text'     => $message,
                'isflash'  => false,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['Value']) && (string) $body['Value'] !== '') {
                return true;
            }

            Log::warning('[Melipayamak] ارسال ناموفق', ['response' => $body]);

            return false;
        } catch (Throwable $e) {
            Log::error('[Melipayamak] خطای اتصال: '.$e->getMessage());

            return false;
        }
    }
}
