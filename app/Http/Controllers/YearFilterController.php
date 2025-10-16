<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'years' => $years
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data tahun',
                'error' => $e->getMessage()
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
        
        // Cek apakah tahun tersebut ada di database
        $exists = Link::where('year', $year)->exists();
        
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => "Data untuk tahun {$year} tidak ditemukan"
            ], 404);
        }

        // Set ke session
        session(['selected_year' => $year]);

        return response()->json([
            'success' => true,
            'message' => "Filter tahun berhasil diubah ke {$year}",
            'year' => $year
        ]);
    }
}