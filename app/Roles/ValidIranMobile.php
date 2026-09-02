<?php

namespace App\Rules;

use App\Services\MobileNormalizationService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidIranMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(MobileNormalizationService::class)->isValid((string) $value)) {
            $fail('شماره موبایل معتبر نیست.');
        }
    }
}
