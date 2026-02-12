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
        'color_id',
        'guaranty_id',
        'main_price',
        'price',
        'discount',
        'count',
        'description',
        'status',

    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    public function guaranty()
    {
        return $this->belongsTo(Guaranty::class);
    }

    public static function createOrderDetail($order,$cart,$product_price)
    {
        return OrderDetail::query()->create([
            'seller_id'=>$product_price->user_id,
            'order_id'=>$order->id,
            'product_id'=>$cart->product_id,
            'color_id'=>$cart->color_id,
            'guaranty_id'=>$cart->guaranty_id,
            'main_price'=>$product_price->main_price,
            'price'=>$product_price->price,
            'discount'=>$product_price->discount,
            'count'=>$cart->count,
            'status'=>OrderDetailStatus::Waiting->value,
        ]);
    }

    public static function calculateMoneyForCommission($order_detail)
    {
        if ($order_detail->product->category->commission){
            return $order_detail->price - ((($order_detail->product->category->commission->commission_percent) * $order_detail->price) / 100);
        }else{
            return $order_detail->price;
        }
    }
}
