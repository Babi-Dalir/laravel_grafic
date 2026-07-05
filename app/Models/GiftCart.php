<?php

namespace App\Models;

use App\Enums\GiftCartStatus;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * 🟢 شنود تغییرات مدل برای غیرفعال‌سازی خودکار در صورت اتمام موجودی (امنیتی آنلاین)
     */
    protected static function boot()
    {
        parent::boot();

        // به محض اینکه فیلد balance در هر کجای پروژه آپدیت شد، این بخش اجرا می‌شود
        self::updating(function ($giftCart) {
            if ($giftCart->isDirty('balance') && $giftCart->balance <= 0) {
                $giftCart->status = GiftCartStatus::InActive->value;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function calculateGiftCart($shop_data, $total_price, $gif_cart_code_price)
    {
        $gift_cart = self::query()
            ->where('code', $shop_data['gift_cart_code'])
            ->where('user_id', auth()->id())
            ->where('balance', '>', 0)
            ->where('status', GiftCartStatus::Active->value)
            ->where('expiration_date', '>=', now())
            ->first();

        if ($gift_cart) {
            if ($gift_cart->balance >= $total_price) {
                $gif_cart_code_price = $total_price;
                $total_price = 0;
            } else {
                $gif_cart_code_price = $gift_cart->balance;
                $total_price -= $gift_cart->balance;
            }
        }

        return [
            'total_price' => $total_price,
            'gif_cart_code_price' => $gif_cart_code_price,
            'gift_cart_id' => $gift_cart ? $gift_cart->id : null
        ];
    }
}
