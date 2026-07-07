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

    // 🟢 تبدیل خودکار اعداد فارسی/عربی به انگلیسی پیش از بررسی ولیدیشن
    protected function prepareForValidation()
    {
        $fields = ['national_code', 'card_number', 'account_number', 'iban'];
        $replacements = [
            '۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4', '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9',
            '٠'=>'0', '١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9'
        ];

        $input = $this->all();

        foreach ($fields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                // تبدیل اعداد
                $cleanValue = strtr($this->input($field), $replacements);
                // حذف فاصله‌های خالی احتمالی که کاربر وارد کرده
                $cleanValue = str_replace(' ', '', $cleanValue);

                $input[$field] = $cleanValue;
            }
        }

        $this->merge($input);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100'],
            'last_name' => ['required', 'string', 'min:2', 'max:100'],
            'brand_name' => ['nullable', 'string', 'max:255'],

            // کد ملی
            'national_code' => ['required', 'digits:10'],

            // شماره کارت (۱۶ رقم عددی)
            'card_number' => ['required', 'digits:16'],

            // شماره حساب
            'account_number' => ['required', 'string', 'min:6', 'max:30'],

            // 🟢 اصلاح ریجکس شبا برای پذیرش حروف کوچک و بزرگ (Case-Insensitive)
            'iban' => [
                'required',
                'string',
                'regex:/^(IR|ir)[0-9]{24}$/',
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
