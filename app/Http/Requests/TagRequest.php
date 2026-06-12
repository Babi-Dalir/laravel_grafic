<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'name')->ignore($this->tag),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام تگ الزامی است',
            'name.string' => 'نام تگ باید متن باشد',
            'name.max' => 'نام تگ نباید بیشتر از 255 کاراکتر باشد',
            'name.unique' => 'این تگ قبلاً ثبت شده است',
        ];
    }
}
