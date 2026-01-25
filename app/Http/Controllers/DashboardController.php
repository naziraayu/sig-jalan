<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Models\RoadCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $selectedYear = session('selected_year');
            
            // ✅ Cache dashboard data selama 5 menit
            $cacheKey = "dashboard_stats_jember_" . ($selectedYear ?? 'all');
            
            $data = Cache::remember($cacheKey, now()->addMinutes(5), function() use ($selectedYear) {
                return $this->getDashboardData($selectedYear);
            });
            
            return view('layouts.dashboard', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in Dashboard: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return view('layouts.dashboard')->with([
                'error' => 'Terjadi kesalahan saat memuat data dashboard',
                'selectedYear' => session('selected_year'),
            ] + $this->getEmptyData());
        }
    }
    
    private function getDashboardData($selectedYear)
    {
        // ✅ PERBAIKAN: Ambil reference year dari database (SAMA seperti AlignmentController)
        $referenceYear = null;
        if ($selectedYear) {
            $referenceYear = RoadCondition::where('year', $selectedYear)
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->value('reference_year') ?? ($selectedYear - 1);
        }
        
        Log::info('DashboardController getDashboardData', [
            'selected_year' => $selectedYear,
            'reference_year' => $referenceYear
        ]);
        
        // ===== CEK APAKAH ADA DATA =====
        // ✅ PERBAIKAN: Filter STRICT seperti AlignmentController
        $dataCount = RoadCondition::when($selectedYear, function($query) use ($selectedYear, $referenceYear) {
                $query->where('year', $selectedYear);
                // ✅ STRICT: Hanya data dengan reference_year yang benar
                if ($referenceYear) {
                    $query->where('reference_year', $referenceYear);
                }
            })
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category')
            ->count();
        
        if ($dataCount == 0) {
            $emptyMessage = $selectedYear 
                ? "Tidak ada data kondisi jalan untuk Kabupaten Jember tahun {$selectedYear} dengan reference year {$referenceYear}. " 
                : "Tidak ada data kondisi jalan untuk Kabupaten Jember. Silakan jalankan command: php artisan sdi:calculate";
            
            return array_merge([
                'info' => $emptyMessage,
                'selectedYear' => $selectedYear,
            ], $this->getEmptyData());
        }
        
        // ===== STATISTIK UTAMA (Direct Query - Super Fast!) =====
        
        // ✅ PERBAIKAN: Base query STRICT seperti AlignmentController
        $baseQuery = RoadCondition::query()
            ->when($selectedYear, function($query) use ($selectedYear, $referenceYear) {
                $query->where('year', $selectedYear);
                // ✅ STRICT: Hanya reference_year yang benar
                if ($referenceYear) {
                    $query->where('reference_year', $referenceYear);
                }
            })
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category');
        
        // 1. Total Ruas dan Segmen
        $totalLinks = (clone $baseQuery)
            ->distinct('link_no')
            ->count('link_no');
        
        $totalSegments = (clone $baseQuery)->count();
        
        Log::info('Base query counts', [
            'total_links' => $totalLinks,
            'total_segments' => $totalSegments,
            'reference_year' => $referenceYear
        ]);
        
        // 2. Total Panjang
        $totalLength = (clone $baseQuery)
            ->selectRaw('SUM(chainage_to - chainage_from) as total')
            ->value('total') ?? 0;
        
        // 3. Rata-rata SDI
        $avgSDI = (clone $baseQuery)->avg('sdi_value') ?? 0;
        
        // 4. Kategori Kondisi (1 query saja!)
        $categoryStats = (clone $baseQuery)
            ->select('sdi_category', DB::raw('COUNT(*) as count'))
            ->groupBy('sdi_category')
            ->pluck('count', 'sdi_category')
            ->toArray();
        
        $goodCondition = $categoryStats['Baik'] ?? 0;
        $fairCondition = $categoryStats['Sedang'] ?? 0;
        $lightDamage = $categoryStats['Rusak Ringan'] ?? 0;
        $heavyDamage = $categoryStats['Rusak Berat'] ?? 0;
        
        // 5. Persentase
        $totalConditions = $totalSegments;
        $percentGood = $totalConditions > 0 ? ($goodCondition / $totalConditions) * 100 : 0;
        $percentFair = $totalConditions > 0 ? ($fairCondition / $totalConditions) * 100 : 0;
        $percentLight = $totalConditions > 0 ? ($lightDamage / $totalConditions) * 100 : 0;
        $percentHeavy = $totalConditions > 0 ? ($heavyDamage / $totalConditions) * 100 : 0;
        
        // ===== STATISTIK KERUSAKAN =====
        
        $damageStats = (clone $baseQuery)
            ->selectRaw('
                SUM(COALESCE(pothole_count, 0)) as total_potholes,
                SUM(COALESCE(crack_dep_area, 0) + 
                    COALESCE(oth_crack_area, 0) + 
                    COALESCE(concrete_cracking_area, 0) + 
                    COALESCE(concrete_structural_cracking_area, 0)) as total_crack_area,
                SUM(COALESCE(rutting_area, 0)) as total_rutting_area,
                SUM(COALESCE(pothole_area, 0)) as total_pothole_area,
                SUM(COALESCE(patching_area, 0)) as total_patching_area
            ')
            ->first();
        
        $totalPotholes = $damageStats->total_potholes ?? 0;
        $totalCrackArea = $damageStats->total_crack_area ?? 0;
        $totalRuttingArea = $damageStats->total_rutting_area ?? 0;
        $totalPotholeArea = $damageStats->total_pothole_area ?? 0;
        $totalPatchingArea = $damageStats->total_patching_area ?? 0;
        
        // Segmen Kritis
        $criticalSegments = (clone $baseQuery)
            ->where('sdi_value', '>', 150)
            ->count();
        
        // ===== TREND SDI PER TAHUN =====
        // ✅ PERBAIKAN: Query per tahun dengan reference year yang benar
        $sdiByYear = RoadCondition::select('year', 'reference_year')
            ->selectRaw('
                AVG(sdi_value) as avg_sdi,
                MIN(sdi_value) as min_sdi,
                MAX(sdi_value) as max_sdi,
                COUNT(*) as count
            ')
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category')
            ->whereNotNull('year')
            ->whereNotNull('reference_year')
            ->groupBy('year', 'reference_year')
            ->orderBy('year', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'year' => $item->year,
                    'reference_year' => $item->reference_year,
                    'avg_sdi' => round($item->avg_sdi, 2),
                    'min_sdi' => round($item->min_sdi, 2),
                    'max_sdi' => round($item->max_sdi, 2),
                    'count' => $item->count
                ];
            });
        
        // ===== STATISTIK KONDISI JALAN PER TAHUN =====
        $conditionByYear = RoadCondition::select('year', 'reference_year')
            ->selectRaw('
                SUM(CASE WHEN sdi_category = "Baik" THEN 1 ELSE 0 END) as baik,
                SUM(CASE WHEN sdi_category = "Sedang" THEN 1 ELSE 0 END) as sedang,
                SUM(CASE WHEN sdi_category = "Rusak Ringan" THEN 1 ELSE 0 END) as rusak_ringan,
                SUM(CASE WHEN sdi_category = "Rusak Berat" THEN 1 ELSE 0 END) as rusak_berat,
                COUNT(*) as total
            ')
            ->whereHas('kabupaten', function($query) {
                $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
            })
            ->whereNotNull('sdi_value')
            ->whereNotNull('sdi_category')
            ->whereNotNull('year')
            ->whereNotNull('reference_year')
            ->groupBy('year', 'reference_year')
            ->orderBy('year', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'year' => $item->year,
                    'reference_year' => $item->reference_year,
                    'baik' => $item->baik,
                    'sedang' => $item->sedang,
                    'rusak_ringan' => $item->rusak_ringan,
                    'rusak_berat' => $item->rusak_berat,
                    'total' => $item->total
                ];
            });
        
        // ===== TOP 5 RUAS TERBURUK =====
        
        $worstLinks = (clone $baseQuery)
            ->select('link_no')
            ->selectRaw('AVG(sdi_value) as avg_sdi')
            ->selectRaw('SUM(chainage_to - chainage_from) as total_length')
            ->selectRaw('COUNT(*) as segment_count')
            ->groupBy('link_no')
            ->orderByDesc('avg_sdi')
            ->limit(5)
            ->get()
            ->map(function($item) use ($selectedYear, $referenceYear) {
                // ✅ PERBAIKAN: Cari link dengan reference year yang benar
                $link = Link::where('link_no', $item->link_no)
                    ->when($referenceYear, function($query) use ($referenceYear) {
                        $query->where('year', $referenceYear);
                    })
                    ->first();
                
                return [
                    'link_no' => $item->link_no,
                    'link_code' => $link->link_code ?? $item->link_no,
                    'link_name' => $link->link_name ?? 'Ruas ' . $item->link_no,
                    'avg_sdi' => round($item->avg_sdi, 2),
                    'category' => $this->getSDICategory($item->avg_sdi),
                    'total_length' => round($item->total_length, 2),
                    'segment_count' => $item->segment_count,
                    'province' => $link->province->province_name ?? '-',
                    'kabupaten' => $link->kabupaten->kabupaten_name ?? '-',
                ];
            });
        
        // ===== TOP 5 RUAS TERBAIK =====
        
        $bestLinks = (clone $baseQuery)
            ->select('link_no')
            ->selectRaw('AVG(sdi_value) as avg_sdi')
            ->selectRaw('SUM(chainage_to - chainage_from) as total_length')
            ->selectRaw('COUNT(*) as segment_count')
            ->groupBy('link_no')
            ->orderBy('avg_sdi')
            ->limit(5)
            ->get()
            ->map(function($item) use ($selectedYear, $referenceYear) {
                // ✅ PERBAIKAN: Cari link dengan reference year yang benar
                $link = Link::where('link_no', $item->link_no)
                    ->when($referenceYear, function($query) use ($referenceYear) {
                        $query->where('year', $referenceYear);
                    })
                    ->first();
                
                return [
                    'link_no' => $item->link_no,
                    'link_code' => $link->link_code ?? $item->link_no,
                    'link_name' => $link->link_name ?? 'Ruas ' . $item->link_no,
                    'avg_sdi' => round($item->avg_sdi, 2),
                    'category' => $this->getSDICategory($item->avg_sdi),
                    'total_length' => round($item->total_length, 2),
                    'segment_count' => $item->segment_count,
                ];
            });
        
        // ===== STATISTIK PER KECAMATAN =====
        // ✅ PERBAIKAN: Query STRICT dengan reference year yang benar
        $kecamatanStats = DB::table('road_condition as rc')
            ->join('link as l', function($join) use ($referenceYear) {
                $join->on('rc.link_no', '=', 'l.link_no');
                // ✅ STRICT: Link harus dari reference year
                if ($referenceYear) {
                    $join->where('l.year', '=', $referenceYear);
                }
            })
            ->join('link_kecamatan as lk', 'l.id', '=', 'lk.link_id')
            ->join('kecamatan as k', 'lk.kecamatan_code', '=', 'k.kecamatan_code')
            ->join('kabupaten as kab', 'k.kabupaten_code', '=', 'kab.kabupaten_code')
            ->where('kab.kabupaten_name', 'LIKE', '%JEMBER%')
            ->when($selectedYear, function($query) use ($selectedYear, $referenceYear) {
                $query->where('rc.year', $selectedYear);
                // ✅ STRICT: Hanya reference_year yang benar
                if ($referenceYear) {
                    $query->where('rc.reference_year', $referenceYear);
                }
            })
            ->whereNotNull('rc.sdi_value')
            ->whereNotNull('rc.sdi_category')
            ->select('k.kecamatan_name')
            ->selectRaw('COUNT(DISTINCT l.link_no) as total_links')
            ->selectRaw('AVG(rc.sdi_value) as avg_sdi')
            ->selectRaw('SUM(rc.chainage_to - rc.chainage_from) as total_length')
            ->selectRaw('SUM(CASE WHEN rc.sdi_category = "Baik" THEN 1 ELSE 0 END) as good')
            ->selectRaw('SUM(CASE WHEN rc.sdi_category = "Sedang" THEN 1 ELSE 0 END) as fair')
            ->selectRaw('SUM(CASE WHEN rc.sdi_category = "Rusak Ringan" THEN 1 ELSE 0 END) as light')
            ->selectRaw('SUM(CASE WHEN rc.sdi_category = "Rusak Berat" THEN 1 ELSE 0 END) as heavy')
            ->groupBy('k.kecamatan_code', 'k.kecamatan_name')
            ->orderByDesc('avg_sdi')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'kecamatan_name' => $item->kecamatan_name,
                    'total_links' => $item->total_links,
                    'avg_sdi' => round($item->avg_sdi, 2),
                    'category' => $this->getSDICategory($item->avg_sdi),
                    'total_length' => round($item->total_length, 2),
                    'good' => $item->good,
                    'fair' => $item->fair,
                    'light' => $item->light,
                    'heavy' => $item->heavy,
                ];
            });
        
        // ===== DATA TERBARU =====
        
        $recentUpdates = (clone $baseQuery)
            ->with('link')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($condition) {
                $link = $condition->link;
                
                return [
                    'link_code' => $link->link_code ?? $condition->link_no,
                    'link_name' => $link->link_name ?? 'Ruas ' . $condition->link_no,
                    'chainage_from' => $condition->chainage_from,
                    'chainage_to' => $condition->chainage_to,
                    'year' => $condition->year,
                    'sdi_value' => round($condition->sdi_value, 2),
                    'category' => $condition->sdi_category,
                    'updated_at' => $condition->updated_at->format('d M Y'),
                ];
            });
        
        // ===== PANJANG JALAN PER KATEGORI =====
        
        $lengthByCategory = (clone $baseQuery)
            ->select('sdi_category')
            ->selectRaw('SUM(chainage_to - chainage_from) as total_length')
            ->groupBy('sdi_category')
            ->pluck('total_length', 'sdi_category')
            ->toArray();
        
        Log::info('DashboardController completed', [
            'selected_year' => $selectedYear,
            'reference_year' => $referenceYear,
            'total_segments' => $totalSegments,
            'total_links' => $totalLinks
        ]);
        
        return [
            'selectedYear' => $selectedYear,
            'referenceYear' => $referenceYear,
            'totalLinks' => $totalLinks,
            'totalSegments' => $totalSegments,
            'totalLength' => round($totalLength, 2),
            'avgSDI' => round($avgSDI, 2),
            'goodCondition' => $goodCondition,
            'fairCondition' => $fairCondition,
            'lightDamage' => $lightDamage,
            'heavyDamage' => $heavyDamage,
            'percentGood' => round($percentGood, 2),
            'percentFair' => round($percentFair, 2),
            'percentLight' => round($percentLight, 2),
            'percentHeavy' => round($percentHeavy, 2),
            'totalPotholes' => $totalPotholes,
            'totalCrackArea' => round($totalCrackArea, 2),
            'criticalSegments' => $criticalSegments,
            'totalRuttingArea' => round($totalRuttingArea, 2),
            'totalPotholeArea' => round($totalPotholeArea, 2),
            'totalPatchingArea' => round($totalPatchingArea, 2),
            'sdiByYear' => $sdiByYear,
            'conditionByYear' => $conditionByYear,
            'worstLinks' => $worstLinks,
            'bestLinks' => $bestLinks,
            'kecamatanStats' => $kecamatanStats,
            'recentUpdates' => $recentUpdates,
            'lengthByCategory' => [
                'baik' => round($lengthByCategory['Baik'] ?? 0, 2),
                'sedang' => round($lengthByCategory['Sedang'] ?? 0, 2),
                'rusak_ringan' => round($lengthByCategory['Rusak Ringan'] ?? 0, 2),
                'rusak_berat' => round($lengthByCategory['Rusak Berat'] ?? 0, 2),
            ]
        ];
    }
    
    private function getEmptyData()
    {
        return [
            'referenceYear' => null,
            'totalLinks' => 0,
            'totalSegments' => 0,
            'totalLength' => 0,
            'avgSDI' => 0,
            'goodCondition' => 0,
            'fairCondition' => 0,
            'lightDamage' => 0,
            'heavyDamage' => 0,
            'percentGood' => 0,
            'percentFair' => 0,
            'percentLight' => 0,
            'percentHeavy' => 0,
            'totalPotholes' => 0,
            'totalCrackArea' => 0,
            'criticalSegments' => 0,
            'totalRuttingArea' => 0,
            'totalPotholeArea' => 0,
            'totalPatchingArea' => 0,
            'sdiByYear' => collect([]),
            'conditionByYear' => collect([]),
            'worstLinks' => collect([]),
            'bestLinks' => collect([]),
            'kecamatanStats' => collect([]),
            'recentUpdates' => collect([]),
            'lengthByCategory' => [
                'baik' => 0,
                'sedang' => 0,
                'rusak_ringan' => 0,
                'rusak_berat' => 0,
            ]
        ];
    }
    
    private function getSDICategory($sdi)
    {
        if ($sdi < 50) {
            return 'Baik';
        } elseif ($sdi >= 50 && $sdi < 100) {
            return 'Sedang';
        } elseif ($sdi >= 100 && $sdi < 150) {
            return 'Rusak Ringan';
        } else {
            return 'Rusak Berat';
        }
    }
    
    /**
     * Clear dashboard cache
     */
    public function clearCache()
    {
        Cache::forget('dashboard_stats_jember_' . session('selected_year'));
        Cache::forget('dashboard_stats_jember_all');
        
        return redirect()->route('dashboard')
            ->with('success', 'Cache dashboard berhasil dibersihkan');
    }
}
