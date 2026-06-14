<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
                'max:255'
            ],

            'mobile' => [
                'required',
                'regex:/^09[0-9]{9}$/',
                'unique:users,mobile'
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults()
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' =>
                'نام الزامی است.',

            'mobile.required' =>
                'شماره موبایل الزامی است.',

            'mobile.regex' =>
                'شماره موبایل معتبر نیست.',

            'mobile.unique' =>
                'این شماره موبایل قبلاً ثبت شده است.',

            'password.required' =>
                'رمز عبور الزامی است.',

            'password.confirmed' =>
                'تکرار رمز عبور صحیح نیست.',
        ];
    }
}
