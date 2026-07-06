<?php

use App\Services\SellerSettlementService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduler
|--------------------------------------------------------------------------
*/

// دستور پیش‌فرض لایت‌ویت لاراول
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * ۱. جاب روزانه تسویه‌حساب اتوماتیک کیف‌پول فروشندگان
 */
Schedule::call(function () {
    SellerSettlementService::run();
})
    ->name('seller-settlement')
    ->withoutOverlapping()
    ->daily();

/**
 * ۲. پاکسازی کدهای تایید منقضی شده از دیتابیس (هر ۱۰ دقیقه)
 */
Schedule::command('verification-codes:clean')
    ->everyTenMinutes();

/**
 * ۳. 👑 کنترلر هوشمند کش کمپین‌های شگفت‌انگیز صفحه اصلی (هر دقیقه)
 */
Schedule::command('campaigns:clean-cache')
    ->everyMinute();

/**
 * ۴. جاب روزانه پاکسازی فایل‌های چانک آپلود‌های رها شده و ناقص
 */
Schedule::call(function () {
    $disk = Storage::disk('digital_files');
    $targetFolder = 'tmp/products';

    if (!$disk->exists($targetFolder)) {
        return;
    }

    $directories = $disk->directories($targetFolder);

    foreach ($directories as $dir) {
        try {
            $files = $disk->files($dir);

            if (empty($files)) {
                $disk->deleteDirectory($dir);
                continue;
            }

            $lastModifiedTime = 0;
            foreach ($files as $file) {
                $time = $disk->lastModified($file);
                if ($time > $lastModifiedTime) {
                    $lastModifiedTime = $time;
                }
            }

            // اگر ۲۴ ساعت از آخرین چانک رها شده گذشته باشد
            if (time() - $lastModifiedTime > 86400) {
                $disk->deleteDirectory($dir);
            }
        } catch (\Throwable $e) {
            Log::error("خطا در پاکسازی چانک موقت در مسیر: {$dir}", ['error' => $e->getMessage()]);
        }
    }
})
    ->daily()
    ->name('cleanup-expired-upload-chunks')
    ->withoutOverlapping(); // 🟢 گارد امنیتی عدم همپوشانی برای جلوگیری از درگیری شدید I/O سرور


// اجرای کامند بررسی انقضای کمپین‌ها به صورت هر ساعت
Schedule::command('campaigns:check-expiry')->hourly();
