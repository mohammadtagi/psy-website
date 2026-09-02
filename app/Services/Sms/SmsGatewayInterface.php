<?php

namespace App\Services\Sms;

interface SmsGatewayInterface
{
    /**
     * ارسال پیامک عمومی و قابل تعویض.
     *
     * @param  string  $to      شماره گیرنده در فرم E.164 (+98912...)
     * @param  string  $message متن پیام
     * @return bool
     */
    public function send(string $to, string $message): bool;
}
