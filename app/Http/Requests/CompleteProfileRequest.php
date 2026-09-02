<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:120'],
            'gender'     => ['required', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'نام الزامی است.',
            'gender.required'   => 'جنسیت الزامی است.',
            'birth_date.before' => 'تاریخ تولد باید در گذشته باشد.',
        ];
    }
}
