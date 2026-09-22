<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ZarinpalPaymentService
{
    public function request(Payment $payment): string
    {
        $merchantId = (string) config('services.zarinpal.merchant_id');

        if ($merchantId === '') {
            throw new RuntimeException(
                'Zarinpal merchant ID is not configured.'
            );
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(15)
            ->post(config('services.zarinpal.request_url'), [
                'merchant_id' => $merchantId,
                'amount' => $payment->amount * 10,
                'callback_url' => config('services.zarinpal.callback_url'),
                'description' => sprintf(
                    'پرداخت نوبت شماره %d',
                    $payment->appointment_id,
                ),
                'metadata' => [
                    'mobile' => $payment->client?->mobile,
                    'email' => $payment->client?->email,
                ],
            ]);

        $data = $this->decode($response);

        $code = (int) data_get($data, 'data.code', 0);
        $authority = data_get($data, 'data.authority');

        if ($code !== 100 || ! is_string($authority) || $authority === '') {
            $message = data_get(
                $data,
                'errors.message',
                'دریافت کد پرداخت از زرین‌پال ناموفق بود.'
            );

            throw new RuntimeException((string) $message);
        }

        return $authority;
    }

    /**
     * @return array{success: bool, ref_id: string|null, message: string|null}
     */
    public function verify(Payment $payment, string $authority): array
    {
        $merchantId = (string) config('services.zarinpal.merchant_id');

        if ($merchantId === '') {
            throw new RuntimeException(
                'Zarinpal merchant ID is not configured.'
            );
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(15)
            ->post(config('services.zarinpal.verify_url'), [
                'merchant_id' => $merchantId,
                'amount' => $payment->amount * 10,
                'authority' => $authority,
            ]);

        $data = $this->decode($response);

        $code = (int) data_get($data, 'data.code', 0);
        $refId = data_get($data, 'data.ref_id');

        if (in_array($code, [100, 101], true)) {
            return [
                'success' => true,
                'ref_id' => is_scalar($refId)
                    ? (string) $refId
                    : null,
                'message' => null,
            ];
        }

        $message = data_get(
            $data,
            'errors.message',
            'تأیید پرداخت توسط زرین‌پال ناموفق بود.'
        );

        return [
            'success' => false,
            'ref_id' => null,
            'message' => (string) $message,
        ];
    }

    public function startUrl(string $authority): string
    {
        return rtrim(
                (string) config('services.zarinpal.start_url'),
                '/',
            ).'/'.urlencode($authority);
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(Response $response): array
    {
        if (! $response->successful()) {
            throw new RuntimeException(
                'ارتباط با زرین‌پال برقرار نشد.'
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException(
                'پاسخ زرین‌پال معتبر نیست.'
            );
        }

        return $data;
    }
}
