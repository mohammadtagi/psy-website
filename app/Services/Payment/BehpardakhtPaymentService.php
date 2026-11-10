<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;

class BehpardakhtPaymentService
{
    private string $wsdl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
    private string $startPayUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';

    private string $terminalId;
    private string $userName;
    private string $userPassword;

    public function __construct()
    {
        $this->terminalId = (string) config('payment.drivers.behpardakht.terminalId');
        $this->userName = (string) config('payment.drivers.behpardakht.username');
        $this->userPassword = (string) config('payment.drivers.behpardakht.password');
    }

    public function getStartPayUrl(): string
    {
        return $this->startPayUrl;
    }

    private function getSoapClient(): SoapClient
    {
        return new SoapClient($this->wsdl, [
            'encoding' => 'UTF-8',
            'trace' => true,
            'exceptions' => true,
            'connection_timeout' => 15,
        ]);
    }

    /**
     * ارسال درخواست پرداخت به درگاه (bpPayRequest)
     * مبلغ به تومان دریافت و به ریال تبدیل می‌شود
     */
    public function request(Payment $payment, string $callbackUrl): string
    {
        $client = $this->getSoapClient();
        $now = CarbonImmutable::now('Asia/Tehran');

        // تبدیل تومان به ریال
        $amountInRials = $payment->amount * 10;

        $params = [
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => (int) $payment->sale_order_id,
            'amount' => $amountInRials,
            'localDate' => $now->format('Ymd'),
            'localTime' => $now->format('His'),
            'additionalData' => '',
            'callBackUrl' => $callbackUrl,
            'payerId' => 0,
        ];

        try {
            $response = $client->bpPayRequest($params);
            $result = explode(',', (string) $response->return);

            if ($result[0] === '0') {
                return $result[1]; // RefId درگاه
            }

            Log::error('Behpardakht bpPayRequest failed', [
                'res_code' => $result[0],
                'order_id' => $payment->sale_order_id,
            ]);

            throw new Exception("خطای درگاه ملت: کد پاسخ {$result[0]}");
        } catch (SoapFault $e) {
            Log::error('Behpardakht SoapFault in bpPayRequest', ['error' => $e->getMessage()]);
            throw new Exception('خطا در برقراری ارتباط با درگاه به‌پرداخت ملت.');
        }
    }

    /**
     * تأیید تراکنش (bpVerifyRequest)
     */
    public function verify(int $orderId, int $saleOrderId, string $saleReferenceId): bool
    {
        $client = $this->getSoapClient();

        $params = [
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => $orderId,
            'saleOrderId' => $saleOrderId,
            'saleReferenceId' => (int) $saleReferenceId,
        ];

        try {
            $response = $client->bpVerifyRequest($params);
            $resCode = trim((string) $response->return);

            // کد 0 به معنای موفقیت است. در مستندات کد 43 نیز یعنی قبلاً verify شده است
            return $resCode === '0' || $resCode === '43';
        } catch (Exception $e) {
            Log::error('Behpardakht bpVerifyRequest exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * واریز قطعی وجه (bpSettleRequest)
     */
    public function settle(int $orderId, int $saleOrderId, string $saleReferenceId): bool
    {
        $client = $this->getSoapClient();

        $params = [
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => $orderId,
            'saleOrderId' => $saleOrderId,
            'saleReferenceId' => (int) $saleReferenceId,
        ];

        try {
            $response = $client->bpSettleRequest($params);
            $resCode = trim((string) $response->return);

            // کد 0 موفقیت است. کد 45 یعنی قبلاً settle شده است
            return $resCode === '0' || $resCode === '45';
        } catch (Exception $e) {
            Log::error('Behpardakht bpSettleRequest exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * بازگشت وجه به حساب مشتری (bpReversalRequest)
     */
    public function reverse(int $orderId, int $saleOrderId, string $saleReferenceId): bool
    {
        $client = $this->getSoapClient();

        $params = [
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => $orderId,
            'saleOrderId' => $saleOrderId,
            'saleReferenceId' => (int) $saleReferenceId,
        ];

        try {
            $response = $client->bpReversalRequest($params);
            $resCode = trim((string) $response->return);

            // کد 0 موفقیت در ریورسال است. کد 48 یعنی قبلاً ریورس شده است
            return $resCode === '0' || $resCode === '48';
        } catch (Exception $e) {
            Log::error('Behpardakht bpReversalRequest exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
