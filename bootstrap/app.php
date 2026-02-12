<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (){
            \Illuminate\Support\Facades\Route::prefix('admin')->middleware(['web','auth','admin'])->group(base_path('routes/admin.php'));
            \Illuminate\Support\Facades\Route::prefix('seller')->middleware(['web','auth','admin'])->group(base_path('routes/seller.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'=>\App\Http\Middleware\AdminPanelMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
