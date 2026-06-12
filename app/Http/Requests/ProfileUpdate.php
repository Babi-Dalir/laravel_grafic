<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // user table
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'user_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'user_name')->ignore(auth()->id()),
            ],

            // profile table
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^09[0-9]{9}$/',
            ],

            'telegram' => [
                'nullable',
                'string',
                'max:100',
            ],

            'eta' => [
                'nullable',
                'string',
                'max:100',
            ],

            'instagram' => [
                'nullable',
                'string',
                'max:100',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // image
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // name
            'name.required' => 'نام الزامی است',
            'name.string' => 'نام باید متن باشد',
            'name.max' => 'نام نباید بیشتر از 255 کاراکتر باشد',

            // username
            'user_name.required' => 'نام کاربری الزامی است',
            'user_name.unique' => 'این نام کاربری قبلاً استفاده شده است',

            // phone
            'phone.regex' => 'شماره موبایل معتبر نیست',

            // website
            'website.url' => 'آدرس وب‌سایت معتبر نیست',

            // image
            'image.image' => 'فایل باید تصویر باشد',
            'image.mimes' => 'فرمت تصویر معتبر نیست',
            'image.max' => 'حجم تصویر نباید بیشتر از 2MB باشد',
        ];
    }
}
