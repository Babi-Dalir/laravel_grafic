<?php

namespace App\Http\Requests;

use App\Enums\DiscountCampaignType;
use Illuminate\Foundation\Http\FormRequest;

class DiscountCampaignRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],

            'type' => [
                'required',
                'string',
                'in:' . implode(',', [
                    DiscountCampaignType::Product->value,
                    DiscountCampaignType::Category->value,
                    DiscountCampaignType::Global->value,
                ]),
            ],

            'percent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'starts_at' => [
                'nullable',
                'string',
            ],

            'expires_at' => [
                'nullable',
                'string',
                'after_or_equal:starts_at',
            ],

            'target_ids' => [
                'nullable',
                'array',
            ],

            'target_ids.*' => [
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام کمپین الزامی است',

            'type.required' => 'نوع کمپین الزامی است',
            'type.in' => 'نوع کمپین معتبر نیست',

            'percent.required' => 'درصد تخفیف الزامی است',
            'percent.numeric' => 'درصد باید عدد باشد',
            'percent.min' => 'حداقل درصد 0 است',
            'percent.max' => 'حداکثر درصد 100 است',

            'expires_at.after_or_equal' => 'تاریخ پایان باید بعد از تاریخ شروع باشد',

            'target_ids.array' => 'شناسه‌های هدف باید به صورت لیست باشند',
        ];
    }
}
