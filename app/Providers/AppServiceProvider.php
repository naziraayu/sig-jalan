<?php

namespace App\Providers;

use App\Models\RoadCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Observers\RoadConditionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Schema::defaultStringLength(191);
        DB::statement("SET NAMES 'utf8mb4'");
        RoadCondition::observe(RoadConditionObserver::class);
    }
}
