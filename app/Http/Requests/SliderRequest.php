<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
            'link' => [
                'nullable',
                'url',
                'max:500',
            ],

            'image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'link.url' => 'لینک وارد شده معتبر نیست',
            'link.max' => 'طول لینک نباید بیشتر از 500 کاراکتر باشد',

            'image.required' => 'تصویر اسلایدر الزامی است',
            'image.image' => 'فایل باید تصویر باشد',
            'image.mimes' => 'فرمت تصویر باید jpg, jpeg, png یا webp باشد',
            'image.max' => 'حجم تصویر نباید بیشتر از 2 مگابایت باشد',
        ];
    }
}
