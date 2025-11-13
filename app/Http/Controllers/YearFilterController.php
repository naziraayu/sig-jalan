<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use App\Models\RoadInventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YearFilterController extends Controller
{
   /**
     * Get available years from link table
     */
    public function getAvailableYears()
    {
        try {
            $years = Link::select('year')
                ->distinct()
                ->whereNotNull('year')
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();

            return response()->json([
                'success' => true,
                'years' => $years,
                'current' => session('selected_year')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available years: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data tahun',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get current selected year from session
     */
    public function getCurrentYear()
    {
        $year = session('selected_year', now()->year);
        
        return response()->json([
            'success' => true,
            'year' => $year
        ]);
    }

    /**
     * Set selected year to session
     */
    public function setYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100'
        ]);

        $year = $request->input('year');
        
        // ✅ Cek apakah tahun tersebut ada di database + hitung data
        $linkCount = Link::where('year', $year)->count();
        
        if ($linkCount == 0) {
            return response()->json([
                'success' => false,
                'message' => "Data untuk tahun {$year} tidak ditemukan"
            ], 404);
        }

        // ✅ Hitung jumlah data terkait (optional, untuk info user)
        $inventoryCount = RoadInventory::whereHas('link', function($query) use ($year) {
            $query->where('year', $year);
        })->count();

        // Set ke session
        session(['selected_year' => $year]);

        return response()->json([
            'success' => true,
            'message' => "Filter tahun berhasil diubah ke {$year}",
            'year' => $year,
            'stats' => [
                'links' => $linkCount,
                'inventories' => $inventoryCount
            ]
        ]);
    }

    /**
     * ✅ TAMBAHAN: Clear year filter from session
     */
    public function clearYear()
    {
        session()->forget('selected_year');
        
        return response()->json([
            'success' => true,
            'message' => 'Filter tahun berhasil dihapus. Menampilkan semua data.'
        ]);
    }

    /**
     * ✅ TAMBAHAN: Get statistics for current year
     */
    public function getYearStats()
    {
        $year = session('selected_year');
        
        if (!$year) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun belum dipilih'
            ], 400);
        }

        try {
            $stats = [
                'year' => $year,
                'links' => Link::where('year', $year)->count(),
                'inventories' => RoadInventory::whereHas('link', function($q) use ($year) {
                    $q->where('year', $year);
                })->count(),
                'provinces' => Link::where('year', $year)
                    ->distinct('province_code')
                    ->count('province_code'),
                'kabupatens' => Link::where('year', $year)
                    ->distinct('kabupaten_code')
                    ->count('kabupaten_code'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}