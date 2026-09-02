<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * نرمال‌سازی شماره موبایل ایران به فرم استاندارد +989XXXXXXXXX
 */
class MobileNormalizationService
{
    public function normalize(?string $input): string
    {
        $digits = $this->toEnglishDigits(trim((string) $input));
        $digits = preg_replace('/\D+/', '', $digits);

        if (str_starts_with($digits, '00989')) {
            $digits = substr($digits, 4);
        } elseif (str_starts_with($digits, '989')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0'.$digits;
        }

        if (strlen($digits) !== 11 || ! preg_match('/^09\d{9}$/', $digits)) {
            throw new InvalidArgumentException('شماره موبایل معتبر نیست.');
        }

        return '+98'.substr($digits, 1);
    }

    public function isValid(?string $input): bool
    {
        try {
            $this->normalize($input);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /** تبدیل ارقام فارسی/عربی به انگلیسی */
    public function toEnglishDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }
}
