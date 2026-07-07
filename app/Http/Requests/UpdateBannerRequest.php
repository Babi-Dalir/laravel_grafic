<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'link' => ['nullable', 'url', 'max:255'], // اضافه شدن ولیدیشن لینک به صورت اختیاری و با فرمت معتبر URL
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3048'], // تبدیل الزامی به nullable (اختیاری)
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع بنر الزامی است',
            'link.url' => 'فرمت آدرس وارد شده برای لینک معتبر نیست (باید با http یا https شروع شود)',
            'image.image' => 'فایل باید تصویر باشد',
            'image.mimes' => 'فرمت تصویر باید jpg, jpeg, png یا webp باشد',
            'image.max' => 'حجم تصویر نباید بیشتر از 3 مگابایت باشد',
        ];
    }
}
