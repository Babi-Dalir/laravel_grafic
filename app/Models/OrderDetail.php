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
        'coupon_discount',
        'description',
        'status',
        'seller_share',
        'site_share',
        'platform_subsidy'
    ];

    protected $dispatchesEvents = [
        'created' => \App\Events\OrderDetailSaved::class,
        'updated' => \App\Events\OrderDetailSaved::class,
        'deleted' => \App\Events\OrderDetailSaved::class,
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderDetailStatus::class,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function download()
    {
        return $this->hasOne(Downloads::class, 'order_detail_id');
    }

    /**
     * ساخت اتمیک جزئیات سفارش با معماری استاندارد سوبسید (Subsidy)
     */
    public static function createOrderDetail($order, $cart, Product $product)
    {
        $seller = $product->user;
        $commissionPercent = $product->category?->commission?->commission_percent ?? 20;

        $mainPrice = $product->main_price;
        $productBasePrice = $product->final_price;
        $productInternalDiscount = $mainPrice - $productBasePrice;

        // ۱. سناریوی محصولی که از ابتدا توسط فروشنده رایگان منتشر شده است
        if ($productBasePrice <= 0) {
            return self::query()->create([
                'seller_id'        => $product->user_id,
                'order_id'         => $order->id,
                'product_id'       => $cart->product_id,
                'main_price'       => $mainPrice,
                'price'            => 0,
                'discount'         => $productInternalDiscount,
                'coupon_discount'  => 0,
                'status'           => OrderDetailStatus::Waiting,
                'seller_share'     => 0,
                'site_share'       => 0,
                'platform_subsidy' => 0,
            ]);
        }

        // ۲. محاسبه سهم‌های استاندارد بر اساس قیمت پولی محصول در سایت
        if ($seller && $seller->hasRole('مدیر')) {
            $siteShare = $productBasePrice;
            $sellerShare = 0;
        } else {
            $siteShare = ($productBasePrice * $commissionPercent) / 100;
            $sellerShare = $productBasePrice - $siteShare;
        }

        // --------------------------------------------------------------------------
        // 🧠 بررسی وضعیت کوپن بر اساس منطق دقیق و اتمیک جدید شما
        // --------------------------------------------------------------------------
        $couponDiscountForThisItem = 0;

        // اگر کل فاکتور با کوپن ۱۰۰٪ رایگان شده باشد
        if ($order->discount_price > 0 && $order->total_price <= 0) {
            $couponDiscountForThisItem = $productBasePrice;
        }

        // آیا این آیتم خاص به واسطه کوپن برای مشتری رایگان شده است؟
        $isItemFreeByCoupon = ($couponDiscountForThisItem >= $productBasePrice);

        if ($isItemFreeByCoupon) {
            return self::query()->create([
                'seller_id'        => $product->user_id,
                'order_id'         => $order->id,
                'product_id'       => $cart->product_id,
                'main_price'       => $mainPrice,
                'price'            => $productBasePrice,
                'discount'         => $productInternalDiscount,
                'coupon_discount'  => $couponDiscountForThisItem,
                'status'           => OrderDetailStatus::Waiting,
                'seller_share'     => $sellerShare,
                'site_share'       => $siteShare,
                'platform_subsidy' => $sellerShare, // 🟢 اصلاح اول: سایت فقط سهم فروشنده را سوبسید می‌دهد (هزینه واقعی)
            ]);
        }

        // ۳. سناریوی فروش عادی و نقدی محصول پولی
        return self::query()->create([
            'seller_id'        => $product->user_id,
            'order_id'         => $order->id,
            'product_id'       => $cart->product_id,
            'main_price'       => $mainPrice,
            'price'            => $productBasePrice,
            'discount'         => $productInternalDiscount,
            'coupon_discount'  => 0,
            'status'           => OrderDetailStatus::Waiting,
            'seller_share'     => $sellerShare,
            'site_share'       => $siteShare,
            'platform_subsidy' => 0,
        ]);
    }
}
