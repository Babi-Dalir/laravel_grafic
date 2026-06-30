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

    protected $dispatchesEvents = [
        'created' => \App\Events\OrderDetailSaved::class,
        'updated' => \App\Events\OrderDetailSaved::class,
        'deleted' => \App\Events\OrderDetailSaved::class,
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderDetailStatus::class, // فعال‌سازی قابلیت اکشن‌محور انوم
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * واکسینه کردن رابطه محصول در برابر حذف‌های موقت دیتابیس
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * اصلاح نام کلاس مدل از جمع به مفرد
     */
    public function download()
    {
        return $this->hasOne(Downloads::class, 'order_detail_id');
    }

    /**
     * ساخت اتمیک جزئیات سفارش با هندل کردن محصولات سافت‌دیلیت شده
     */
    public static function createOrderDetail($order, $cart, Product $product)
    {
        $price = $product->final_price;
        $discount = $product->main_price - $product->final_price;

        // دریافت مستقیم یوزر صاحب محصول (حتی اگر محصول حذف موقت شده باشد)
        $seller = $product->user;

        if ($seller && $seller->hasRole('مدیر')) {
            $siteShare = $price;
            $sellerShare = 0;
        } else {
            // استفاده از آپشنال برای جلوگیری از کرش در صورت نبود کمیسیون دسته بندی
            $commissionPercent = $product->category?->commission?->commission_percent ?? 20;
            $siteShare = ($price * $commissionPercent) / 100;
            $sellerShare = $price - $siteShare;
        }

        return self::query()->create([
            'seller_id' => $product->user_id,
            'order_id' => $order->id,
            'product_id' => $cart->product_id,
            'main_price' => $product->main_price,
            'price' => $price,
            'discount' => $discount,
            'status' => OrderDetailStatus::Waiting, // استفاده مستقیم از شیء انوم
            'seller_share' => $sellerShare,
            'site_share' => $siteShare,
        ]);
    }
}
