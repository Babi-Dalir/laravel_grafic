<?php

namespace App\Models;

use App\Enums\DiscountStatus;
use App\Helpers\CreateUniqueCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'discount',
        'remaining_count',
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
            'remaining_count' => $request->input('remaining_count', 100),
            'expiration_date' => $request->input('expiration_date')
        ]);
    }

    /**
     * ارزیابی تخفیف و ایجاد رکورد رزرو در سبد خرید (بدون decrement زودهنگام)
     */
    public static function calculateDiscount($shop_data, $total_price, $discount_code_price, $orderId, $userId)
    {
        $discount = self::query()
            ->lockForUpdate()
            ->where('code', $shop_data['discount_code'])
            ->where('status', DiscountStatus::Active->value)
            ->where('remaining_count', '>', 0)
            ->where('discount', '>', 0)
            ->whereDate('expiration_date', '>=', today())
            ->first();

        if ($discount) {
            $discount_code_price = min($discount->discount, $total_price);
            $total_price -= $discount_code_price;


            DB::table('discount_usages')->updateOrInsert(
                [
                    'discount_id' => $discount->id,
                    'order_id' => $orderId
                ],
                [
                    'user_id' => $userId,
                    'status' => 'reserved',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        return [
            'total_price' => $total_price,
            'discount_code_price' => $discount_code_price,
        ];
    }
}
