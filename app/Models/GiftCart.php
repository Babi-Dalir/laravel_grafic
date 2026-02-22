<?php

namespace App\Models;

use App\Enums\GiftCartStatus;
use Carbon\Carbon;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function calculateGiftCart($shop_data, $total_price, $gif_cart_code_price)
    {
        $gift_cart = GiftCart::query()
            ->where('code', $shop_data['gift_cart_code'])
            ->where('user_id', auth()->user()->id)
            ->where('balance', '>', 0) // استفاده از مانده اعتبار جدید
            ->where('status', GiftCartStatus::Active->value)
            ->where('expiration_date', '>=', Carbon::now())
            ->first();

        if ($gift_cart) {
            if ($gift_cart->balance >= $total_price) {
                // اعتبار کارت بیشتر از خرید است
                $gif_cart_code_price = $total_price; // مقدار کسر شده
                $total_price = 0;
            } else {
                // اعتبار کارت بخشی از مبلغ را پوشش می‌دهد
                $gif_cart_code_price = $gift_cart->balance; // مقدار کسر شده
                $total_price -= $gift_cart->balance;
            }
        }

        return [
            'total_price' => $total_price,
            'gif_cart_code_price' => $gif_cart_code_price, // برگرداندن همان متغیر
            'gift_cart_id' => $gift_cart ? $gift_cart->id : null
        ];
    }
}
