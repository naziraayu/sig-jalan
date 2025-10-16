<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class ViewServiceProvider extends ServiceProvider
{
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
        // Share selected year ke semua views
        View::composer('*', function ($view) {
            $selectedYear = session('selected_year');
            
            // Jika belum ada tahun di session, ambil tahun terbaru dari database
            if (!$selectedYear) {
                $selectedYear = $this->getLatestYear();
                session(['selected_year' => $selectedYear]);
            }
            
            $view->with('currentYear', $selectedYear);
            $view->with('selectedYear', $selectedYear);
        });
    }
    
    /**
     * Get latest year from database tables
     */
    private function getLatestYear()
    {
        try {
            $latestYear = collect([
                DB::table('link')->max('year'),
                DB::table('road_inventory')->max('year'),
                DB::table('road_condition')->max('year')
            ])->filter()->max();
            
            return $latestYear ?? now()->year;
        } catch (\Exception $e) {
            return now()->year;
        }
    }
}