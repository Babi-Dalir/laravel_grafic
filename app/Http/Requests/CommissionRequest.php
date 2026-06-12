<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionRequest extends FormRequest
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
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
                'unique:commissions,category_id,' . $this->route('commission'),
            ],

            'commission_percent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'دسته‌بندی الزامی است',
            'category_id.exists' => 'دسته‌بندی معتبر نیست',
            'category_id.unique' => 'برای این دسته قبلاً کمیسیون ثبت شده',

            'commission_percent.required' => 'درصد کمیسیون الزامی است',
            'commission_percent.numeric' => 'درصد باید عدد باشد',
            'commission_percent.min' => 'حداقل درصد 0 است',
            'commission_percent.max' => 'حداکثر درصد 100 است',
        ];
    }
}
