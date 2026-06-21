<?php

namespace App\Providers;

use App\Events\ProductFileUploaded;
use App\Listeners\ProcessUploadedProductFile;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductFileUploaded::class => [
            ProcessUploadedProductFile::class,
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
        //
    }
}
