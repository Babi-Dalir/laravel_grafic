<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
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
        // ۱. بهینه‌سازی لود کاتگوری‌ها با شمارش همزمان محصولات بدون آسیب به دیتابیس
        $categories = Cache::remember(
            'categories',
            now()->addDays(10),
            fn() => Category::query()
                ->with('childCategory.childCategory')
                ->withCount('products') // 🟢 اضافه شدن فیلد اتوماتیک products_count برای جلوگیری از باگ N+1 در بلید
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
    }
}
