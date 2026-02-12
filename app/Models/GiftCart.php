<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GiftCart extends Model
{
    protected $fillable = [
        'gift_title',
        'code',
        'gift_price',
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
            ->where('gift_price', '>', 0)
            ->where('expiration_date', '>=',Carbon::now()->toDateTimeString())
            ->first();
        if ($gift_cart) {
            $total_price -= $gift_cart->gift_price;
            $gif_cart_code_price = $gift_cart->gift_price;
        }
        return [
            'total_price' => $total_price,
            'gif_cart_code_price' => $gif_cart_code_price,
        ];
    }
}
