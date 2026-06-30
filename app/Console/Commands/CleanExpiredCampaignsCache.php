<?php

namespace App\Console\Commands;

use App\Enums\DiscountCampaignStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CleanExpiredCampaignsCache extends Command
{
    /**
     * اسم و امضای دستور در ترمینال
     */
    protected $signature = 'campaigns:clean-cache';

    /**
     * توضیحات دستور
     */
    protected $description = 'بررسی وضعیت کمپین‌های منقضی شده یا تازه شروع شده و باطل کردن کش صفحه اصلی';

    /**
     * اجرای منطق اصلی کامند
     */
    public function handle(): void
    {
        $now = now();
        $oneMinuteAgo = now()->subMinute();

        // 🟢 بررسی هوشمند: آیا کمپینی وجود دارد که در این دقیقه منقضی شده باشد یا تازه شروع شده باشد؟
        $hasStatusChanged = DB::table('discount_campaigns') // نام جدول کمپین‌های خودت را جایگزین کن
        ->where('status', DiscountCampaignStatus::Active->value)
            ->where(function ($query) use ($now, $oneMinuteAgo) {
                $query->whereBetween('expires_at', [$oneMinuteAgo, $now]) // منقضی شده در این دقیقه
                ->orWhereBetween('starts_at', [$oneMinuteAgo, $now]); // شروع شده در این دقیقه
            })
            ->exists();

        if ($hasStatusChanged) {
            // انفجار اتمیک کش محصولات شگفت‌انگیز صفحه اصلی
            Cache::forget('home.products.special');

            $this->info('کش محصولات شگفت‌انگیز به دلیل تغییر وضعیت کمپین‌ها با موفقیت باطل شد.');
        } else {
            $this->comment('هیچ کمپینی در این دقیقه تغییر وضعیت نداشته است. نیازی به پاکسازی کش نیست.');
        }
    }
}
