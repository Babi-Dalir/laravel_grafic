<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        // گرفتن آیدی کاربر از روی روت (با هر دو نام احتمالی id یا user)
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],

            // استفاده از Rule برای نادیده گرفتن کاربر فعلی هنگام ویرایش
            'email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('users', 'email')->ignore($userId)
            ],

            'mobile' => [
                'required',
                'regex:/^09[0-9]{9}$/',
                Rule::unique('users', 'mobile')->ignore($userId)
            ],

            // پسورد در ویرایش اختیاری است، اما اگر پر شود باید حداقل ۸ کاراکتر باشد
            'password' => ['nullable', 'string', 'min:8'],

            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * سفارشی‌سازی نام فیلدها برای پیام‌های خطا
     */
    public function attributes(): array
    {
        return [
            'name' => 'نام و نام خانوادگی',
            'email' => 'ایمیل',
            'mobile' => 'شماره موبایل',
            'password' => 'کلمه عبور',
            'image' => 'تصویر پروفایل',
        ];
    }
}
