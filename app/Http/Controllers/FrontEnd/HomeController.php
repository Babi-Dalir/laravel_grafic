<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\CartType;
use App\Enums\DiscountCampaignStatus;
use App\Enums\ProductStatus;
use App\Enums\SliderStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Slider;
use App\Models\UserCart;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function home()
    {
        $now = now()->toDateTimeString();

        // ۱. کش اسلایدرها -> افزایش به ۳۰ روز (کاملاً ایستا)
        $sliders = Cache::remember('home.sliders', 60 * 60 * 24 * 30, function () {
            return Slider::query()
                ->where('status', SliderStatus::Active->value)
                ->select(['id', 'image', 'link'])
                ->get();
        });

        // ۲. کش محصولات پرفروش -> افزایش به ۱۲ ساعت (به ثانیه: 43200)
        $most_sold = Cache::remember('home.products.most_sold', 43200, function () {
            return Product::query()
                ->with(['category:id,name', 'campaignTargets.campaign'])
                ->where('status', ProductStatus::Approved->value)
                ->orderBy('sold', 'DESC')
                ->take(10)
                ->get();
        });

        // ۳. کش جدیدترین محصولات -> افزایش به ۲۴ ساعت (به ثانیه: 86400)
        $newest_products = Cache::remember('home.products.newest_products', 86400, function () {
            return Product::query()
                ->with(['category:id,name', 'campaignTargets.campaign'])
                ->where('status', ProductStatus::Approved->value)
                ->orderBy('created_at', 'DESC')
                ->take(10)
                ->get();
        });

        // ۴. کش محصولات شگفت‌انگیز -> افزایش به ۶ ساعت (به ثانیه: 21600)
        $spacial_products = Cache::remember('home.products.special', 21600, function () use ($now) {
            return Product::query()
                ->with(['category:id,name', 'campaignTargets.campaign'])
                ->where('status', ProductStatus::Approved->value)
                ->where(function ($q) use ($now) {
                    $q->whereHas('campaignTargets.campaign', function ($query) use ($now) {
                        $query->where('status', DiscountCampaignStatus::Active->value)
                            ->where('starts_at', '<=', $now)
                            ->where(function ($e) use ($now) {
                                $e->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                            });
                    })
                        ->orWhere('main_price', '>', 0);
                })
                ->get()
                ->filter(fn($product) => $product->hasDiscount())
                ->take(10);
        });

        // ۵. پیشنهاد لحظه‌ای -> افزایش به ۱ ساعت (به ثانیه: 3600)
        $instant_offers = Cache::remember('home.products.instant_offers', 3600, function () {
            return Product::smartOffer()
                ->with(['category:id,name'])
                ->limit(9)
                ->get();
        });

        return view('frontend.index', compact(
            'sliders',
            'most_sold',
            'spacial_products',
            'newest_products',
            'instant_offers'
        ));
    }

    public function userCart()
    {
        // سبد خرید کاربر نیازی به کش سراسری ندارد چون دیتای داینامیک شخصی است
        $carts = UserCart::query()
            ->with('product')
            ->where('user_id', auth()->id())
            ->where('type', CartType::Main->value)
            ->get();

        return view('frontend.user_cart', compact('carts'));
    }
}
