<?php

use App\Services\SellerSettlementService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    SellerSettlementService::run();
})
    ->name('seller-settlement')
    ->withoutOverlapping()
    ->daily();
