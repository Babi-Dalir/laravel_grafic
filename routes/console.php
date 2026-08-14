<?php

use App\Jobs\CleanExpiredUploadChunksJob;
use App\Jobs\DispatchSellerSettlementsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| 1. Seller Settlements (تسویه‌حساب فروشندگان)
|--------------------------------------------------------------------------
*/
Schedule::job(new DispatchSellerSettlementsJob)
    ->dailyAt('00:30')
    ->onQueue('settlements')
    ->name('dispatch-seller-settlements')
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| 2. Verification Codes (کدهای تایید)
|--------------------------------------------------------------------------
*/
Schedule::command('verification-codes:clean')
    ->everyTenMinutes();

/*
|--------------------------------------------------------------------------
| 3. Campaign Cache (کش کمپین‌ها)
|--------------------------------------------------------------------------
*/
Schedule::command('campaigns:clean-cache')
    ->everyMinute();

/*
|--------------------------------------------------------------------------
| 4. Campaign Expiry (انقضای کمپین‌ها)
|--------------------------------------------------------------------------
*/
Schedule::command('campaigns:check-expiry')
    ->everyMinute();

/*
|--------------------------------------------------------------------------
| 5. Expired Upload Chunks (پاکسازی آپلودهای ناقص)
|--------------------------------------------------------------------------
*/
Schedule::job(new CleanExpiredUploadChunksJob)
    ->dailyAt('03:00')
    ->onQueue('uploads')
    ->name('cleanup-expired-upload-chunks')
    ->withoutOverlapping();
