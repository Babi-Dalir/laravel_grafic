<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyGroupRequest extends FormRequest
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
            'name'=>'required',
            'category_id'=>'required',
            Rule::in(array_keys(Category::getLayer2Categories()))
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'لطفاً عنوان گروه ویژگی را وارد کنید.',
            'name.string'   => 'عنوان گروه ویژگی باید به صورت متن باشد.',
            'name.max'      => 'عنوان گروه ویژگی نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'category_id.required' => 'انتخاب دسته‌بندی برای گروه ویژگی الزامی است.',
            'category_id.in'       => 'دسته‌بندی انتخاب شده مجاز نیست! شما فقط می‌توانید برای دسته‌های لایه دوم (زیرمجموعه‌های میانی) ویژگی تعریف کنید.',
        ];
    }
}
