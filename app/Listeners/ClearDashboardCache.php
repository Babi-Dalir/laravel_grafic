<?php

namespace App\Listeners;

use App\Events\OrderPaidEvent; // 🟢 تغییر به رویداد جدید
use App\Enums\DashboardCacheKey;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    /**
     * اجرای عملیات پاکسازی کش به محض پرداخت موفق فاکتور
     */
    public function handle(OrderPaidEvent $event): void
    {
        Cache::forget(DashboardCacheKey::Kpis->value);
        Cache::forget(DashboardCacheKey::MonthlySales->value);
        Cache::forget(DashboardCacheKey::Latest->value);
        Cache::forget(DashboardCacheKey::Insights->value);
    }
}
