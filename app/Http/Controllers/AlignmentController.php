<?php

namespace App\Http\Controllers;

use App\Models\Alignment;
use Illuminate\Http\Request;
use App\Models\RoadCondition;
use App\Models\RoadInventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AlignmentController extends Controller
{
    // View peta
    public function showMap()
    {
        return view('peta.kabupaten.index');
    }

    // API JSON untuk data koordinat - dikelompokkan per link_no
    public function getCoords(Request $request)
    {
        try {
            $selectedYear = $request->get('year') ?? session('selected_year');

            $query = "
                SELECT 
                    a.link_no, 
                    a.north as lat,
                    a.east as lng
                FROM alignment a
                INNER JOIN kabupaten k ON a.kabupaten_code = k.kabupaten_code
            ";

            $conditions = ["k.kabupaten_name LIKE '%JEMBER%'"];
            $bindings = [];

            if ($selectedYear) {
                $query .= " INNER JOIN link l ON a.link_no = l.link_no";
                $conditions[] = "l.year = ?";
                $bindings[] = $selectedYear;
            }

            $query .= " WHERE " . implode(" AND ", $conditions);
            $query .= " ORDER BY a.link_no ASC, a.chainage ASC";

            $data = DB::select($query, $bindings);

            $grouped = collect($data)->groupBy('link_no')->map(function ($items, $linkNo) {
                return [
                    'link_no' => $linkNo,
                    'coords' => $items->map(function ($item) {
                        return [
                            'lat' => (float) $item->lat,
                            'lng' => (float) $item->lng,
                        ];
                    })->values()
                ];
            })->values();

            return response()->json($grouped);

        } catch (\Exception $e) {
            Log::error("Error getCoords: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get available years untuk dropdown
     */
    public function getAvailableYears()
    {
        try {
            $years = DB::table('road_condition as rc')
                ->join('kabupaten as k', 'rc.kabupaten_code', '=', 'k.kabupaten_code')
                ->where('k.kabupaten_name', 'LIKE', '%JEMBER%')
                ->select('rc.year')
                ->distinct()
                ->orderBy('rc.year', 'desc')
                ->pluck('year');

            return response()->json([
                'success' => true,
                'years' => $years
            ]);
        } catch (\Exception $e) {
            Log::error("Error getAvailableYears: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ FINAL FIX: API untuk menampilkan alignment dengan SDI per segmen
     * Tanpa pakai kolom ID, langsung pakai composite key
     */
    public function getCoordsWithSDI(Request $request)
{
    try {
        set_time_limit(600); // izinkan eksekusi sampai 10 menit
        ini_set('memory_limit', '1G');


        $year = $request->get('year') ?? session('selected_year') ?? date('Y');
        Log::info("getCoordsWithSDI called", ['year' => $year]);

        // 💾 Gunakan cache agar cepat diakses ulang
        return Cache::remember("coords_sdi_{$year}", 3600, function () use ($year) {
            $segments = RoadCondition::where('year', $year)
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->orderBy('link_no')
                ->orderBy('chainage_from')
                ->get();

            Log::info("Segments found", ['count' => $segments->count()]);

            if ($segments->isEmpty()) {
                Log::warning("No segments found for year", ['year' => $year]);
                return [];
            }

            // 🔹 Kumpulkan semua link_no unik dari segmen
            $linkNos = $segments->pluck('link_no')->unique();

            // 🔹 Ambil semua koordinat sekaligus
            $allCoords = DB::table('alignment')
                ->whereIn('link_no', $linkNos)
                ->select('link_no', 'chainage', 'north as lat', 'east as lng')
                ->orderBy('link_no')
                ->orderBy('chainage')
                ->get()
                ->groupBy('link_no');

            Log::info("Alignment coords loaded", ['link_count' => $allCoords->count()]);

            // 🔹 Ambil semua inventory sekaligus
            $inventories = RoadInventory::whereIn('link_no', $linkNos)->get()
                ->groupBy('link_no');

            $roadConditionController = app(RoadConditionController::class);
            $result = [];

            foreach ($segments as $condition) {
                $coordsAll = $allCoords[$condition->link_no] ?? collect();

                // Filter chainage sesuai segmen
                $coords = $coordsAll->filter(function ($c) use ($condition) {
                    return $c->chainage >= $condition->chainage_from && $c->chainage <= $condition->chainage_to;
                })->values();

                if ($coords->isEmpty()) {
                    Log::warning("No coordinates found", [
                        'link_no' => $condition->link_no,
                        'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}"
                    ]);
                    continue;
                }

                // Ambil inventory yang sesuai
                $invGroup = $inventories[$condition->link_no] ?? collect();
                $inventory = $invGroup->first(function ($inv) use ($condition) {
                    return $inv->chainage_from <= $condition->chainage_from
                        && $inv->chainage_to >= $condition->chainage_to;
                });

                $condition->inventory = $inventory;

                // 🔹 Hitung SDI
                $sdi = null;
                $category = 'Tidak Ada Data';
                try {
                    $sdi = $roadConditionController->calculateSDIPublic($condition);
                    $category = $sdi['category'];

                    Log::info("SDI OK", [
                        'link_no' => $condition->link_no,
                        'sdi_final' => $sdi['sdi_final'],
                        'category' => $category,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error calculating SDI", [
                        'link_no' => $condition->link_no,
                        'error' => $e->getMessage()
                    ]);
                }

                // 🔹 Format output untuk frontend
                $result[] = [
                    'link_no' => $condition->link_no,
                    'chainage_from' => (float) $condition->chainage_from,
                    'chainage_to' => (float) $condition->chainage_to,
                    'coords' => $coords->map(fn($c) => [
                        'lat' => (float) $c->lat,
                        'lng' => (float) $c->lng,
                    ])->values()->toArray(),
                    'sdi_final' => $sdi ? (float) $sdi['sdi_final'] : null,
                    'category' => $category,
                    'year' => (int) $condition->year
                ];
            }

            Log::info("getCoordsWithSDI completed", [
                'year' => $year,
                'segments_processed' => count($result),
                'segments_found' => $segments->count()
            ]);

            return $result;
        });

    } catch (\Exception $e) {
        Log::error("Error getCoordsWithSDI: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

}