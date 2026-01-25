<?php
/**
 * DEBUG SCRIPT
 * File ini untuk membandingkan hasil query Dashboard vs Peta
 * Jalankan dengan: php artisan tinker
 * Atau buat route sementara
 */

namespace App\Http\Controllers;

use App\Models\RoadCondition;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function compareQueries()
    {
        $selectedYear = 2024; // Ganti sesuai tahun yang dipilih
        
        echo "=== DEBUG: PERBANDINGAN QUERY DASHBOARD VS PETA ===\n\n";
        
        // ========================================
        // 1. CEK REFERENCE YEAR
        // ========================================
        echo "1. CEK REFERENCE YEAR:\n";
        $referenceYear = RoadCondition::where('year', $selectedYear)
            ->whereHas('kabupaten', function ($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->value('reference_year');
        
        echo "   Survey Year: $selectedYear\n";
        echo "   Reference Year: " . ($referenceYear ?? 'NULL') . "\n\n";
        
        // ========================================
        // 2. QUERY DASHBOARD (TANPA FILTER)
        // ========================================
        echo "2. QUERY DASHBOARD (Semua data):\n";
        $dashboardAll = RoadCondition::where('year', $selectedYear)
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->selectRaw('
                COUNT(*) as total_segmen,
                COUNT(DISTINCT link_no) as total_ruas,
                SUM(CASE WHEN sdi_category = "Baik" THEN 1 ELSE 0 END) as baik,
                SUM(CASE WHEN sdi_category = "Sedang" THEN 1 ELSE 0 END) as sedang,
                SUM(CASE WHEN sdi_category = "Rusak Ringan" THEN 1 ELSE 0 END) as rusak_ringan,
                SUM(CASE WHEN sdi_category = "Rusak Berat" THEN 1 ELSE 0 END) as rusak_berat
            ')
            ->first();
        
        echo "   Total Segmen: {$dashboardAll->total_segmen}\n";
        echo "   Total Ruas: {$dashboardAll->total_ruas}\n";
        echo "   Baik: {$dashboardAll->baik}\n";
        echo "   Sedang: {$dashboardAll->sedang}\n";
        echo "   Rusak Ringan: {$dashboardAll->rusak_ringan}\n";
        echo "   Rusak Berat: {$dashboardAll->rusak_berat}\n\n";
        
        // ========================================
        // 3. QUERY DENGAN FILTER REFERENCE YEAR
        // ========================================
        echo "3. QUERY dengan filter Reference Year:\n";
        $withRefYear = RoadCondition::where('year', $selectedYear)
            ->where('reference_year', $referenceYear)
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category')
            ->selectRaw('
                COUNT(*) as total_segmen,
                COUNT(DISTINCT link_no) as total_ruas,
                SUM(CASE WHEN sdi_category = "Baik" THEN 1 ELSE 0 END) as baik,
                SUM(CASE WHEN sdi_category = "Sedang" THEN 1 ELSE 0 END) as sedang
            ')
            ->first();
        
        echo "   Total Segmen: {$withRefYear->total_segmen}\n";
        echo "   Total Ruas: {$withRefYear->total_ruas}\n";
        echo "   Baik: {$withRefYear->baik}\n";
        echo "   Sedang: {$withRefYear->sedang}\n\n";
        
        // ========================================
        // 4. QUERY DENGAN FILTER LINK + KECAMATAN
        // ========================================
        echo "4. QUERY dengan filter Link + Kecamatan:\n";
        $withLinkKec = RoadCondition::where('year', $selectedYear)
            ->where('reference_year', $referenceYear)
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereHas('link', function($query) use ($referenceYear, $selectedYear) {
                $query->where(function($q) use ($referenceYear, $selectedYear) {
                    $q->where('year', $referenceYear)
                      ->orWhere('year', $selectedYear);
                })
                ->whereHas('linkKecamatans');
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category')
            ->selectRaw('
                COUNT(*) as total_segmen,
                COUNT(DISTINCT link_no) as total_ruas,
                SUM(CASE WHEN sdi_category = "Baik" THEN 1 ELSE 0 END) as baik,
                SUM(CASE WHEN sdi_category = "Sedang" THEN 1 ELSE 0 END) as sedang
            ')
            ->first();
        
        echo "   Total Segmen: {$withLinkKec->total_segmen}\n";
        echo "   Total Ruas: {$withLinkKec->total_ruas}\n";
        echo "   Baik: {$withLinkKec->baik}\n";
        echo "   Sedang: {$withLinkKec->sedang}\n\n";
        
        // ========================================
        // 5. QUERY PETA (ALIGNMENT STYLE)
        // ========================================
        echo "5. QUERY PETA (sama dengan AlignmentController):\n";
        $petaStyle = RoadCondition::where('year', $selectedYear)
            ->when($referenceYear, function($query) use ($referenceYear) {
                return $query->where(function($q) use ($referenceYear) {
                    $q->where('reference_year', $referenceYear)
                      ->orWhereNull('reference_year');
                });
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category')
            ->whereHas('kabupaten', function ($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereHas('link', function($query) use ($referenceYear, $selectedYear) {
                $query->where(function($q) use ($referenceYear, $selectedYear) {
                    $q->where('year', $referenceYear)
                      ->orWhere('year', $selectedYear);
                })
                ->whereHas('linkKecamatans');
            })
            ->selectRaw('
                COUNT(*) as total_segmen,
                COUNT(DISTINCT link_no) as total_ruas,
                SUM(CASE WHEN sdi_category = "Baik" THEN 1 ELSE 0 END) as baik,
                SUM(CASE WHEN sdi_category = "Sedang" THEN 1 ELSE 0 END) as sedang
            ')
            ->first();
        
        echo "   Total Segmen: {$petaStyle->total_segmen}\n";
        echo "   Total Ruas: {$petaStyle->total_ruas}\n";
        echo "   Baik: {$petaStyle->baik}\n";
        echo "   Sedang: {$petaStyle->sedang}\n\n";
        
        // ========================================
        // 6. CEK DATA YANG HILANG
        // ========================================
        echo "6. ANALISIS DATA YANG HILANG:\n";
        
        // Segmen tanpa link
        $noLink = RoadCondition::where('year', $selectedYear)
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereDoesntHave('link')
            ->count();
        echo "   Segmen tanpa Link: $noLink\n";
        
        // Segmen dengan link tapi tanpa kecamatan
        $noKecamatan = RoadCondition::where('year', $selectedYear)
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereHas('link', function($query) {
                $query->whereDoesntHave('linkKecamatans');
            })
            ->count();
        echo "   Segmen dengan Link tapi tanpa Kecamatan: $noKecamatan\n";
        
        // Segmen dengan reference_year NULL
        $nullRefYear = RoadCondition::where('year', $selectedYear)
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereNull('reference_year')
            ->count();
        echo "   Segmen dengan reference_year NULL: $nullRefYear\n";
        
        // Segmen dengan reference_year tidak sesuai
        $wrongRefYear = RoadCondition::where('year', $selectedYear)
            ->where('reference_year', '!=', $referenceYear)
            ->whereNotNull('reference_year')
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->count();
        echo "   Segmen dengan reference_year salah: $wrongRefYear\n\n";
        
        // ========================================
        // 7. KESIMPULAN
        // ========================================
        echo "=== KESIMPULAN ===\n";
        echo "Dashboard seharusnya menampilkan:\n";
        echo "- Total Segmen: {$petaStyle->total_segmen}\n";
        echo "- Total Ruas: {$petaStyle->total_ruas}\n";
        echo "\nJika masih berbeda, cek:\n";
        echo "1. Apakah cache sudah di-clear?\n";
        echo "2. Apakah file DashboardController sudah ter-replace?\n";
        echo "3. Lihat detail di query #6 untuk data yang hilang\n";
    }
}