<?php

namespace App\Models;

use App\Enums\DiscountStatus;
use App\Helpers\CreateUniqueCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'discount',
        'status',
        'expiration_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => DiscountStatus::class,
            'expiration_date' => 'datetime',
        ];
    }

    /**
     * 🟢 اصلاح ساختاری: انتقال خودکار زمان انقضا به آخرین ثانیه روز جهت پایداری سیستم
     */
    protected function expirationDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                return Carbon::parse($value)->endOfDay();
            }
        );
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
        $discount = self::query()
            ->where('code', $shop_data['discount_code'])
            ->where('status', DiscountStatus::Active->value)
            ->where('discount', '>', 0)
            // 🟢 اصلاح طلایی: بررسی اینکه تاریخ انقضا بزرگتر یا مساوی امروز باشد (بدون در نظر گرفتن ساعت)
            ->whereDate('expiration_date', '>=', today())
            ->first();

        if ($discount) {
            $discount_code_price = min($discount->discount, $total_price);
            $total_price -= $discount_code_price;
        }

        return [
            'total_price' => $total_price,
            'discount_code_price' => $discount_code_price,
        ];
    }
}
