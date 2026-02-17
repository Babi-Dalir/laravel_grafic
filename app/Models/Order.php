<?php

namespace App\Models;

use App\Enums\CartType;
use App\Enums\DiscountStatus;
use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_code',
        'transaction_id',
        'total_price',
        'discount_price',
        'discount_code',
        'gift_cart_price',
        'gift_cart_code',
        'status',
        'paid_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function userTransactions()
    {
        return $this->hasMany(UserTransaction::class);
    }
    private static function generateOrderCode()
    {
        do {
            $code = mt_rand(10000000, 99999999);
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }

    public static function createOrder($user, $total_price, $shop_data, $discount_code_price, $gif_cart_code_price)
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'order_code' => self::generateOrderCode(),
            'status' => OrderStatus::WaitPayment->value,
            'total_price' => $total_price,
            'discount_price' => $discount_code_price,
            'discount_code' => $shop_data['discount_code'],
            'gift_cart_price' => $gif_cart_code_price,
            'gift_cart_code' => $shop_data['gift_cart_code'],
        ]);
    }

    public static function successPayment($order, $order_details,$discount_code,$gift_cart_code)
    {
        $order->update([
            'status' => OrderStatus::Payed->value
        ]);
        foreach ($order_details as $order_detail) {
            $order_detail->update([
                'status' => OrderDetailStatus::Processing->value
            ]);

            $product_price = ProductPrice::query()
                ->where('product_id', $order_detail->product_id)
                ->first();
            $product_price->decrement('count', $order_detail->count);

            $product = Product::query()->find($order_detail->product_id);
            $product->increment('sold', $order_detail->count);
        }
        $carts = UserCart::query()
            ->where('user_id', $order->user_id)
            ->where('type', CartType::Main->value)->get();
        foreach ($carts as $cart) {
            $cart->delete();
        }
        if ($discount_code){
            $discount = Discount::query()
                ->where('code', $discount_code)
                ->where('discount', '>', 0)
                ->where('expiration_date', '>=',Carbon::now()->toDateTimeString())
                ->first();
            if ($discount) {
                $discount->update([
                    'discount'=>0,
                    'status'=>DiscountStatus::InActive->value
                ]);
            }
        }
        if ($gift_cart_code){
            $gift_cart = GiftCart::query()
                ->where('code', $gift_cart_code)
                ->where('user_id', auth()->user()->id)
                ->where('gift_price', '>', 0)
                ->where('expiration_date', '>=',Carbon::now()->toDateTimeString())
                ->first();
            if ($gift_cart) {
                $gift_cart->update([
                    'gift_price'=>0
                ]);
            }
        }
    }

    public static function isBuyer($product_id,$user_id)
    {
        return Order::query()->whereHas('orderDetails',function ($q) use ($product_id){
            $q->where('product_id',$product_id);
        })
            ->where('user_id',$user_id)
            ->where('status',OrderStatus::Payed->value)
            ->exists();
    }
}
