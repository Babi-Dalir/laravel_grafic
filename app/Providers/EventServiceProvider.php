<?php

namespace App\Providers;

use App\Events\OrderPaidEvent;
use App\Events\ProductFileUploaded;
use App\Listeners\ClearDashboardCache;
use App\Listeners\SendOrderSmsListener;
use App\Listeners\ProcessUploadedProductFile;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * نگاشت متمرکز تمام رویدادها و لیسنرهای پروژه گرافیک
     */
    protected $listen = [
        // ۱. مدیریت فایل‌های آپلود شده محصولات دیجیتال
        ProductFileUploaded::class => [
            ProcessUploadedProductFile::class,
        ],

        // ۲. 👑 مدیریت اتمیک رویدادهای پس از پرداخت موفق
        OrderPaidEvent::class => [
            SendOrderSmsListener::class,  // ماموریت اول: ارسال آنی پیامک فاکتور به کاربر
            ClearDashboardCache::class,   // ماموریت دوم: باطل کردن هوشمند کش داشبورد ادمین
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
