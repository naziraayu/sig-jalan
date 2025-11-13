<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class YearFilterMiddleware
{
     /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil tahun dari query parameter atau session
        $year = $request->input('year') ?? session('selected_year');
        
        // Jika ada tahun di request parameter, simpan ke session
        if ($request->has('year')) {
            session(['selected_year' => $year]);
        }
        
        // Jika tidak ada tahun di session, set default (tahun terbaru)
        if (!$year) {
            $year = $this->getLatestYear();
            session(['selected_year' => $year]);
        }
        
        // ✅ Share ke semua views (cukup 1 variable)
        view()->share('selectedYear', $year);
        
        // Tambahkan ke request attributes untuk diakses di controller
        $request->attributes->add(['filtered_year' => $year]);
        
        return $next($request);
    }
    
    /**
     * Get latest year from link table (master data)
     * 
     * @return int
     */
    private function getLatestYear(): int
    {
        try {
            // ✅ Cukup query link table saja (lebih efisien)
            $latestYear = DB::table('link')
                ->whereNotNull('year')
                ->max('year');
            
            return $latestYear ?? now()->year;
        } catch (\Exception $e) {
            // Fallback ke tahun sekarang jika terjadi error
            Log::warning('Failed to get latest year: ' . $e->getMessage());
            return now()->year;
        }
    }
}