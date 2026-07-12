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
    /**
     * ساخت اتمیک جزئیات سفارش با توزیع هوشمند کد تخفیف و تفکیک کامل محصول سایت/فروشنده
     */
    public static function createOrderDetail($order, $cart, Product $product, $allocatedCouponDiscount = 0)
    {
        $seller = $product->user;
        $commissionPercent = $product->category?->commission?->commission_percent ?? 20;

        $mainPrice = $product->main_price;
        $productBasePrice = $product->final_price;
        $productInternalDiscount = $mainPrice - $productBasePrice;

        // ۱. سناریوی محصولی که از ابتدا توسط فروشنده یا سایت رایگان منتشر شده است (قیمت پایه صفر)
        if ($productBasePrice <= 0) {
            return self::query()->create([
                'seller_id'        => $product->user_id,
                'order_id'         => $order->id,
                'product_id'       => $cart->product_id,
                'main_price'       => $mainPrice,
                'price'            => 0,
                'discount'         => $productInternalDiscount,
                'coupon_discount'  => 0,
                'status'           => OrderDetailStatus::Waiting->value,
                'seller_share'     => 0,
                'site_share'       => 0,
                'platform_subsidy' => 0,
            ]);
        }

        // تشخیص اینکه آیا محصول متعلق به خود سایت (مدیر) است یا فروشنده مارکت‌پلیس
        $isManagerProduct = ($seller && $seller->hasRole('مدیر'));

        // ۲. محاسبات پچ شده در صورت وجود کد تخفیف برای این آیتم پولی
        if ($allocatedCouponDiscount > 0) {
            $isItemFreeByCoupon = ($allocatedCouponDiscount >= $productBasePrice);

            if ($isManagerProduct) {
                // 🟢 سهم محصول سایت: فروشنده صفر، سایت کل پرداختی واقعی را می‌گیرد
                $finalPrice = max(0, $productBasePrice - $allocatedCouponDiscount);
                $sellerShare = 0;
                $siteShare = $finalPrice;
                $platformSubsidy = 0;
            } else {
                // 🟢 سهم محصول فروشندگان مارکت‌پلیس
                $siteShareStandard = ($productBasePrice * $commissionPercent) / 100;
                $sellerShareStandard = $productBasePrice - $siteShareStandard;

                if ($isItemFreeByCoupon) {
                    // اگر تخفیف کل قیمت محصول را پوشش داد: سهم فروشنده تضمین شده و پلتفرم سوبسید می‌دهد
                    $sellerShare = $sellerShareStandard;
                    $siteShare = 0;
                    $platformSubsidy = $sellerShareStandard;
                } else {
                    // 🟢 پچ طلایی تخفیف جزئی: قیمت واقعی پرداختی خریدار ملاک قرار می‌گیرد
                    $finalPrice = $productBasePrice - $allocatedCouponDiscount;

                    // سهم سایت به اندازه درصد کمیسیون از "مبلغ پرداختی واقعی" محاسبه می‌شود
                    $siteShare = ($finalPrice * $commissionPercent) / 100;
                    // مابقی مبلغ پرداختی واقعی سهم فروشنده می‌شود
                    $sellerShare = $finalPrice - $siteShare;
                    $platformSubsidy = 0;
                }
            }

            return self::query()->create([
                'seller_id'        => $product->user_id,
                'order_id'         => $order->id,
                'product_id'       => $cart->product_id,
                'main_price'       => $mainPrice,
                'price'            => $productBasePrice - $allocatedCouponDiscount,
                'discount'         => $productInternalDiscount,
                'coupon_discount'  => $allocatedCouponDiscount,
                'status'           => OrderDetailStatus::Waiting->value,
                'seller_share'     => $sellerShare,
                'site_share'       => $siteShare,
                'platform_subsidy' => $platformSubsidy,
            ]);
        }

        // ۳. سناریوی فروش عادی و نقدی بدون کوپن تخفیف
        if ($isManagerProduct) {
            $siteShare = $productBasePrice;
            $sellerShare = 0;
        } else {
            $siteShare = ($productBasePrice * $commissionPercent) / 100;
            $sellerShare = $productBasePrice - $siteShare;
        }

        return self::query()->create([
            'seller_id'        => $product->user_id,
            'order_id'         => $order->id,
            'product_id'       => $cart->product_id,
            'main_price'       => $mainPrice,
            'price'            => $productBasePrice,
            'discount'         => $productInternalDiscount,
            'coupon_discount'  => 0,
            'status'           => OrderDetailStatus::Waiting->value,
            'seller_share'     => $sellerShare,
            'site_share'       => $siteShare,
            'platform_subsidy' => 0,
        ]);
    }
}
