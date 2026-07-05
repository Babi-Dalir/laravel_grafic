<?php

namespace App\Console\Commands;

use App\Enums\DiscountCampaignStatus;
use App\Models\DiscountCampaign;
use Illuminate\Console\Command;

class CheckExpiredCampaigns extends Command
{
    /**
     * نام دستوری که در آرتیسان صدا زده می‌شود
     */
    protected $signature = 'campaigns:check-expiry';

    /**
     * توضیحات کامند
     */
    protected $description = 'بررسی خودکار کمپین‌های تخفیف و غیرفعال کردن موارد منقضی شده';

    /**
     * منطق اصلی دستور
     */
    public function handle()
    {
        $now = now();

        // واکشی و آپدیت اتمیک کمپین‌هایی که فعال هستند اما تاریخ انقضای آن‌ها گذشته است
        $updatedCount = DiscountCampaign::query()
            ->where('status', DiscountCampaignStatus::Active->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->update([
                'status' => DiscountCampaignStatus::InActive->value
            ]);

        if ($updatedCount > 0) {
            $this->info("تعداد {$updatedCount} کمپین منقضی شده با موفقیت غیرفعال شدند.");
        } else {
            $this->info('هیچ کمپین منقضی شده‌ای برای غیرفعال‌سازی یافت نشد.');
        }
    }
}
