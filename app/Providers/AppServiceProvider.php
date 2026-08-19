<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\DiscountCampaign;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $categories = Cache::remember(
            'categories',
            now()->addDays(10),
            fn() => Category::query()
                ->with('childCategory.childCategory')
                ->withCount('subProducts') // 🟢 جایگزین شدن متد جدید برای شمارش دقیق زنجیره‌ای
                ->where('parent_id', 0)
                ->get()
        );

        $banners = Cache::remember(
            'banners',
            now()->addDays(10),
            fn() => Banner::query()->get()
        );

        View::share([
            'categories' => $categories,
            'banners' => $banners
        ]);

        Paginator::useBootstrap();

        View::composer('frontend.index', function ($view) {
            $view->with('activeDiscountCampaign', DiscountCampaign::getActiveBannerCampaign());
        });
    }
}
