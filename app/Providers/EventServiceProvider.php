<?php

namespace App\Providers;

use App\Events\OrderPaidEvent; // 🟢 اضافه شد
use App\Events\ProductFileUploaded;
use App\Listeners\SendOrderSmsListener; // 🟢 اضافه شد
use App\Listeners\ProcessUploadedProductFile;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider; // 🟢 اصلاح ارث‌بری برای فعال شدن مپینگ فریم‌ورک

class EventServiceProvider extends ServiceProvider
{
    /**
     * نگاشت متمرکز تمام رویدادها و لیسنرهای پروژه
     */
    protected $listen = [
        ProductFileUploaded::class => [
            ProcessUploadedProductFile::class,
        ],
        // 🟢 ثبت رویداد پرداخت موفق در کنار سایر رویدادهای سیستم
        OrderPaidEvent::class => [
            SendOrderSmsListener::class,
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
        parent::boot(); // 🟢 الزامی برای اعمال منطق نگاشت لاراول
    }
}
