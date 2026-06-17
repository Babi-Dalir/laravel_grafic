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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $categories = Cache::remember(
            'categories',
            now()->addDays(10),
            fn() => Category::query()
                ->with('childCategory.childCategory')
                ->where('parent_id',0)
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
