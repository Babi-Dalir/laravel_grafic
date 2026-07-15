<?php

namespace App\Console\Commands;

use App\Enums\DiscountCampaignStatus;
use App\Enums\DiscountStatus;
use App\Enums\GiftCartStatus;

// 🟢 اضافه شدن انوم کارت هدیه
use App\Models\DiscountCampaign;
use App\Models\Discount;
use App\Models\GiftCart;

// 🟢 اضافه شدن مدل کارت هدیه
use Illuminate\Console\Command;

class CheckExpiredCampaigns extends Command
{
    protected $signature = 'campaigns:check-expiry';

    protected $description = 'بررسی خودکار کمپین‌ها، کدهای تخفیف و کارت‌های هدیه و غیرفعال کردن موارد منقضی شده';

    public function handle()
    {
        $now = now();

        // ۱. غیرفعال کردن کمپین‌های منقضی شده (هر چه تاریخ انقضای آن از زمان فعلی گذشته است)
        $updatedCampaigns = DiscountCampaign::query()
            ->where('status', DiscountCampaignStatus::Active->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now) // 🟢 مقایسه دقیق با ثانیه جاری سرور
            ->update([
                'status' => DiscountCampaignStatus::InActive->value
            ]);

        // ۲. غیرفعال کردن کدهای تخفیف منقضی شده
        $updatedDiscounts = Discount::query()
            ->where('status', DiscountStatus::Active->value)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', $now) // 🟢 مقایسه دقیق
            ->update([
                'status' => DiscountStatus::InActive->value
            ]);

        // ۳. غیرفعال کردن کارت‌های هدیه منقضی شده
        $updatedGiftCarts = GiftCart::query()
            ->where('status', GiftCartStatus::Active->value)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', $now) // 🟢 مقایسه دقیق
            ->update([
                'status' => GiftCartStatus::InActive->value
            ]);

        // گزارش در ترمینال
        if ($updatedCampaigns > 0) $this->info("تعداد {$updatedCampaigns} کمپین غیرفعال شد.");
        if ($updatedDiscounts > 0) $this->info("تعداد {$updatedDiscounts} کد تخفیف غیرفعال شد.");
        if ($updatedGiftCarts > 0) $this->info("تعداد {$updatedGiftCarts} کارت هدیه منقضی شده غیرفعال شد.");

        if ($updatedCampaigns === 0 && $updatedDiscounts === 0 && $updatedGiftCarts === 0) {
            $this->info('هیچ آیتم منقضی شده‌ای برای غیرفعال‌سازی یافت نشد.');
        }
    }
}
