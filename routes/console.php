<?php

use App\Services\SellerSettlementService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

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

    // دریافت تمام دایرکتوری‌های داخل پوشه چانک‌های موقت
    $directories = $disk->directories('tmp/chunks');

    foreach ($directories as $dir) {
        try {
            // اگر پوشه قدیمی‌تر از ۲۴ ساعت (۸۶۴۰۰ ثانیه) است، حذف شود
            if (time() - $disk->lastModified($dir) > 86400) {
                $disk->deleteDirectory($dir);
            }
        } catch (\Throwable $e) {
            // جلوگیری از متوقف شدن کل فرآیند در صورت بروز خطا برای یک پوشه خاص
            logger()->error("خطا در پاکسازی چانک موقت: {$dir}", ['error' => $e->getMessage()]);
        }
    }
})->daily()->name('cleanup-expired-upload-chunks');
