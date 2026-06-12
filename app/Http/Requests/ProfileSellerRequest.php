<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileSellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'brand_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'portfolio' => [
                'required',
                'url',
                'max:500',
            ],

            'reason' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],

            'resume' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'brand_name.required' => 'نام برند الزامی است',
            'brand_name.min' => 'نام برند باید حداقل 3 کاراکتر باشد',

            'portfolio.required' => 'لینک نمونه کار الزامی است',
            'portfolio.url' => 'لینک نمونه کار معتبر نیست',

            'reason.required' => 'توضیحات الزامی است',
            'reason.min' => 'توضیحات باید حداقل 20 کاراکتر باشد',

            'resume.mimes' => 'فرمت رزومه باید pdf یا doc یا docx باشد',
            'resume.max' => 'حجم رزومه نباید بیشتر از 5 مگابایت باشد',
        ];
    }
}
