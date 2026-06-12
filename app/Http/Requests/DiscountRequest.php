<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount' => [
                'required',
                'numeric',
                'min:1',
                'max:1000000', // یا هر سقفی که می‌خوای
            ],

            'expiration_date' => [
                'required',
                'date',
                'after:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'discount.required' => 'مقدار تخفیف الزامی است',
            'discount.numeric' => 'تخفیف باید عدد باشد',
            'discount.min' => 'حداقل مقدار تخفیف 1 است',

            'expiration_date.required' => 'تاریخ انقضا الزامی است',
            'expiration_date.date' => 'فرمت تاریخ معتبر نیست',
            'expiration_date.after' => 'تاریخ انقضا باید بعد از امروز باشد',
        ];
    }
}
