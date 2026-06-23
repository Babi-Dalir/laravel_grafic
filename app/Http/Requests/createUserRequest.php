<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createUserRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            // ولیدیشن برای شماره موبایل‌های استاندارد ایران
            'mobile' => ['required', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:8'],
            // اضافه شدن ولیدیشن تصویر برای امنیت پروژه گرافیکی شما
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
