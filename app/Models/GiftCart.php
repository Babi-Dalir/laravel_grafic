<?php

namespace App\Models;

use App\Enums\GiftCartStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class GiftCart extends Model
{
    protected $fillable = [
        'gift_title',
        'code',
        'gift_price',
        'balance',
        'status',
        'user_id',
        'expiration_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => GiftCartStatus::class,
            'expiration_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    /**
     * محاسبه و اعمال گارد اتمیک سخت‌گیرانه برای کنترل موجودی کارت هدیه در گام نهایی سبد خرید
     */
    public static function calculateGiftCart($shop_data, $total_price, &$gif_cart_code_price, $userId)
    {
        $gift_cart = self::query()
            ->lockForUpdate()
            ->where('code', $shop_data['gift_cart_code'])
            ->where('user_id', $userId)
            ->where('balance', '>', 0)
            ->where('status', GiftCartStatus::Active->value)
            ->whereDate('expiration_date', '>=', today())
            ->first();

        if ($gift_cart) {
            if ($gift_cart->balance >= $total_price) {
                $gif_cart_code_price = $total_price;
                $total_price = 0;
            } else {
                $gif_cart_code_price = $gift_cart->balance;
                $total_price -= $gift_cart->balance;
            }
        } else {
            $gif_cart_code_price = 0;
        }

        return [
            'total_price' => $total_price,
            'gift_cart_code_price' => $gif_cart_code_price,
            'gift_cart_id' => $gift_cart ? $gift_cart->id : null
        ];
    }
}
