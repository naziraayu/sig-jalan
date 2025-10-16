<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        // ✅ AKTIFKAN MIDDLEWARE GLOBAL UNTUK WEB
        // Middleware ini akan otomatis dijalankan untuk semua web routes
        $middleware->web(append: [
            \App\Http\Middleware\YearFilterMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        // ✅ TAMBAHKAN VIEW SERVICE PROVIDER
        \App\Providers\ViewServiceProvider::class,
    ])
    ->create();