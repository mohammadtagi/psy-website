<?php

namespace App\Http\Requests\Auth;

use App\Rules\ValidIranMobile;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', new ValidIranMobile],
            'code'   => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'code.required'   => 'کد تایید الزامی است.',
            'code.digits'     => 'کد تایید باید ۶ رقم باشد.',
        ];
    }
}
