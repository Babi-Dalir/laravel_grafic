<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100'],

            'last_name' => ['required', 'string', 'min:2', 'max:100'],

            'brand_name' => ['nullable', 'string', 'max:255'],

            'national_code' => [
                'required',
                'digits:10',
            ],

            'card_number' => [
                'required',
                'digits:16',
            ],

            'account_number' => [
                'required',
                'string',
                'min:6',
                'max:30',
            ],

            'iban' => [
                'required',
                'string',
                'regex:/^IR[0-9]{24}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'نام الزامی است',
            'last_name.required' => 'نام خانوادگی الزامی است',

            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',

            'card_number.required' => 'شماره کارت الزامی است',
            'card_number.digits' => 'شماره کارت باید 16 رقم باشد',

            'account_number.required' => 'شماره حساب الزامی است',

            'iban.required' => 'شماره شبا الزامی است',
            'iban.regex' => 'فرمت شبا صحیح نیست (مثال: IRxxxxxxxxxxxxxxxxxxxxxx)',
        ];
    }
}
