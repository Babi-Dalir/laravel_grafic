<?php

namespace App\Models;

use App\Enums\OrderDetailStatus;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'product_id',
        'main_price',
        'price',
        'discount',
        'description',
        'status',
        'seller_share',
        'site_share'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function createOrderDetail($order, $cart, $product)
    {
        $commission_percent = 20;

        $price = $product->final_price;
        $discount = $product->main_price - $product->final_price;

        $siteShare = ($price * $commission_percent) / 100;
        $sellerShare = $price - $siteShare;

        return OrderDetail::query()->create([
            'seller_id' => $product->user_id,
            'order_id' => $order->id,
            'product_id' => $cart->product_id,

            'main_price' => $product->main_price,
            'price' => $price,
            'discount' => $discount,

            'status' => OrderDetailStatus::Waiting->value,

            'seller_share' => $sellerShare,
            'site_share' => $siteShare,
        ]);
    }

    public static function calculateMoneyForCommission($order_detail)
    {
        if ($order_detail->product->category->commission) {
            return $order_detail->price - ((($order_detail->product->category->commission->commission_percent) * $order_detail->price) / 100);
        } else {
            return $order_detail->price;
        }
    }
}
