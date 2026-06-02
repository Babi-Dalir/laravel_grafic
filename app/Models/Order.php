<?php

namespace App\Models;

use App\Enums\CartType;
use App\Enums\DiscountStatus;
use App\Enums\GiftCartStatus;
use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

    public static function successPayment($order, $order_details, $discount_code, $gift_cart_code)
    {
        // جلوگیری از اجرای مجدد
        if ($order->status == OrderStatus::Payed->value) {
            return;
        }

        $order->update([
            'status' => OrderStatus::Payed->value,
            'paid_at' => now(),
        ]);

        foreach ($order_details as $order_detail) {

            $order_detail->update([
                'status' => OrderDetailStatus::Processing->value
            ]);

            $product = Product::find($order_detail->product_id);

            if ($product) {
                $product->increment('sold');
            }

            $downloadExists = Downloads::query()
                ->where('order_detail_id', $order_detail->id)
                ->exists();

            if (!$downloadExists) {

                Downloads::createDownload($order_detail);
            }
        }

        UserCart::query()
            ->where('user_id', $order->user_id)
            ->where('type', CartType::Main->value)
            ->delete();

        self::handleDiscount($discount_code);

        self::handleGiftCart(
            $gift_cart_code,
            $order
        );
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

    private static function handleDiscount($discount_code)
    {
        if (!$discount_code) {
            return;
        }

        $discount = Discount::query()
            ->where('code', $discount_code)
            ->where('status', DiscountStatus::Active->value)
            ->first();

        if (!$discount) {
            return;
        }

        $discount->update([
            'discount' => 0,
            'status' => DiscountStatus::InActive->value
        ]);
    }
    private static function handleGiftCart($gift_cart_code, $order)
    {
        if (!$gift_cart_code) {
            return;
        }

        $gift_cart = GiftCart::query()
            ->where('code', $gift_cart_code)
            ->where('user_id', $order->user_id)
            ->first();

        if (!$gift_cart) {
            return;
        }

        $amountToDeduct = $order->gift_cart_price;

        if ($amountToDeduct > 0) {

            if ($gift_cart->balance >= $amountToDeduct) {

                $gift_cart->decrement(
                    'balance',
                    $amountToDeduct
                );

            } else {

                $gift_cart->update([
                    'balance' => 0
                ]);
            }
        }

        $gift_cart = $gift_cart->fresh();

        if ($gift_cart->balance <= 0) {

            $gift_cart->update([
                'status' => GiftCartStatus::InActive->value
            ]);
        }
    }
}
