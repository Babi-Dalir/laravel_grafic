<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerProductRequest extends FormRequest
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
        // گرفتن id محصول (برای update)
        $productId = $this->route('product')?->id
            ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],

            'e_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'e_name')->ignore($productId),
            ],

            'main_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'spacial_start' => [
                'nullable',
                'string',
            ],

            'spacial_expiration' => [
                'nullable',
                'string',
            ],

            'tags' => [
                'nullable',
                'array',
            ],

            'tags.*' => [
                'integer',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام محصول الزامی است',

            'e_name.required' => 'نام انگلیسی الزامی است',
            'e_name.unique' => 'این نام انگلیسی قبلاً استفاده شده',

            'main_price.required' => 'قیمت محصول الزامی است',
            'main_price.numeric' => 'قیمت باید عدد باشد',
            'main_price.min' => 'قیمت نمی‌تواند منفی باشد',

            'category_id.required' => 'دسته‌بندی الزامی است',
            'category_id.exists' => 'دسته‌بندی معتبر نیست',

            'image.image' => 'فایل باید تصویر باشد',
            'image.mimes' => 'فرمت تصویر معتبر نیست',
            'image.max' => 'حجم تصویر نباید بیشتر از 2MB باشد',

            'discount.numeric' => 'تخفیف باید عدد باشد',
            'discount.max' => 'حداکثر تخفیف 100٪ است',
        ];
    }
}
