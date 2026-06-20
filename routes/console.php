<?php

use App\Services\SellerSettlementService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    SellerSettlementService::run();
})
    ->name('seller-settlement')
    ->withoutOverlapping()
    ->daily();

Schedule::command('verification-codes:clean')
    ->everyTenMinutes();



Schedule::call(function () {
    $disk = Storage::disk('digital_files');

    // آدرس دقیق را با FileManager خود ست کنید. ما اینجا هر دو الگو را ایمن می‌کنیم.
    $targetFolder = 'tmp/products';

    if (!$disk->exists($targetFolder)) {
        return;
    }

    $directories = $disk->directories($targetFolder);

    foreach ($directories as $dir) {
        try {
            // پیدا کردن فایل‌های داخل دایرکتوری برای تشخیص زمان دقیق تغییر
            $files = $disk->files($dir);

            if (empty($files)) {
                // اگر پوشه کاملاً خالی است، آن را حذف کن
                $disk->deleteDirectory($dir);
                continue;
            }

            // بررسی زمان آخرین فایل آپلود شده در این پوشه چانک
            $lastModifiedTime = 0;
            foreach ($files as $file) {
                $time = $disk->lastModified($file);
                if ($time > $lastModifiedTime) {
                    $lastModifiedTime = $time;
                }
            }

            // اگر از آخرین چانک آپلود شده بیش از ۲۴ ساعت گذشته باشد، یعنی آپلود رها شده است
            if (time() - $lastModifiedTime > 86400) {
                $disk->deleteDirectory($dir);
            }
        } catch (\Throwable $e) {
            Log::error("خطا در پاکسازی چانک موقت در مسیر: {$dir}", ['error' => $e->getMessage()]);
        }
    }
})->daily()->name('cleanup-expired-upload-chunks');
