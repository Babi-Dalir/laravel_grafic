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
        'address_id',
        'order_code',
        'status',
        'transaction_id',
        'total_price',
        'receive_day',
        'receive_time',
        'send_type',
        'discount_price',
        'discount_code',
        'gift_cart_price',
        'gift_cart_code',
        'post_number',
        'payment_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
    public function userTransactions()
    {
        return $this->hasMany(UserTransaction::class);
    }

    public static function createOrder($user, $address, $total_price, $shop_data, $discount_code_price, $gif_cart_code_price)
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_code' => rand(11111, 99999),
            'status' => OrderStatus::WaitPayment->value,
            'total_price' => $total_price,
            'receive_day' => $shop_data['receive_day'],
            'receive_time' => $shop_data['receive_time'],
            'send_type' => $shop_data['send_type'],
            'discount_price' => $discount_code_price,
            'discount_code' => $shop_data['discount_code'],
            'gift_cart_price' => $gif_cart_code_price,
            'gift_cart_code' => $shop_data['gift_cart_code'],
            'payment_type' => $shop_data['payment_type'],
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
                ->where('color_id', $order_detail->color_id)
                ->where('guaranty_id', $order_detail->guaranty_id)
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
