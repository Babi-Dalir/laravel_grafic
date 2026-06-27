<?php

namespace App\Models;

use App\Enums\DiscountStatus;
use App\Helpers\CreateUniqueCode;
use App\Helpers\DateManager;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'discount',
        'status',
        'expiration_date',
    ];

    // فعال کردن کستینگ انوم برای همخوانی ۱۰۰ درصدی لایه دیتابیس با انوم پروژه
    protected function casts(): array
    {
        return [
            'status' => DiscountStatus::class,
            'expiration_date' => 'datetime',
        ];
    }

    public static function createDiscount($request)
    {
        self::query()->create([
            'code' => CreateUniqueCode::generateRandomString(6, Discount::class),
            'discount' => $request->input('discount'),
            'expiration_date' => $request->input('expiration_date')
        ]);
    }

    public static function calculateDiscount($shop_data, $total_price, $discount_code_price)
    {
        // 🟢 بهینه‌سازی سرور: بررسی وضعیت فعال بودن و هماهنگ‌سازی تایم‌زون سرور با تابع bany now() لاراول
        $discount = self::query()
            ->where('code', $shop_data['discount_code'])
            ->where('status', DiscountStatus::Active->value) // اضافه شدن بررسی فیلد وضعیت ادمین
            ->where('discount', '>', 0)
            ->where('expiration_date', '>=', now()) // استفاده از now() بومی لاراول برای حل باگ اختلاف ساعت سرور
            ->first();

        if ($discount) {
            $discount_code_price = min(
                $discount->discount,
                $total_price
            );

            $total_price -= $discount_code_price;
        }

        return [
            'total_price' => $total_price,
            'discount_code_price' => $discount_code_price,
        ];
    }
}
