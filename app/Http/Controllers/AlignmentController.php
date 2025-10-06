<?php

namespace App\Http\Controllers;

use App\Models\Alignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlignmentController extends Controller
{
    // View peta
    public function showMap()
    {
        return view('peta.kabupaten.index');
    }

    // API JSON untuk data koordinat - dikelompokkan per link_no
    public function getCoords()
    {
        try {
            $data = DB::select("
                SELECT 
                    a.link_no, 
                    a.north as lat,
                    a.east as lng
                FROM alignment a
                INNER JOIN kabupaten k ON a.kabupaten_code = k.kabupaten_code
                WHERE k.kabupaten_name LIKE '%JEMBER%'
                ORDER BY a.link_no ASC, a.chainage ASC
            ");

            // Group manual
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
     * API untuk menampilkan alignment dengan SDI per segmen
     * UPDATED VERSION - Query yang benar
     */
    public function getCoordsWithSDI(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            // 1. Ambil semua segmen road_condition untuk tahun ini di Jember
            $segments = DB::table('road_condition as rc')
                ->join('kabupaten as k', 'rc.kabupaten_code', '=', 'k.kabupaten_code')
                ->where('k.kabupaten_name', 'LIKE', '%JEMBER%')
                ->where('rc.year', $year)
                ->select('rc.link_no', 'rc.chainage_from', 'rc.chainage_to', 'rc.year')
                ->orderBy('rc.link_no')
                ->orderBy('rc.chainage_from')
                ->get();

            $result = [];

            foreach ($segments as $segment) {
                // 2. Ambil koordinat alignment yang masuk dalam segmen ini
                $coords = DB::table('alignment')
                    ->where('link_no', $segment->link_no)
                    ->whereBetween('chainage', [$segment->chainage_from, $segment->chainage_to])
                    ->orderBy('chainage')
                    ->select('north as lat', 'east as lng', 'chainage')
                    ->get();

                // Skip kalau ga ada koordinat untuk segmen ini
                if ($coords->isEmpty()) {
                    Log::warning("No coordinates found for segment", [
                        'link_no' => $segment->link_no,
                        'chainage' => "{$segment->chainage_from} - {$segment->chainage_to}"
                    ]);
                    continue;
                }

                // 3. Hitung SDI untuk segmen ini
                $condition = \App\Models\RoadCondition::where('link_no', $segment->link_no)
                    ->where('chainage_from', $segment->chainage_from)
                    ->where('chainage_to', $segment->chainage_to)
                    ->where('year', $segment->year)
                    ->with('inventory')
                    ->first();

                $sdi = null;
                $category = 'Tidak Ada Data';

                if ($condition) {
                    try {
                        $sdi = app(RoadConditionController::class)->calculateSDIPublic($condition);
                        $category = $sdi['category'];
                    } catch (\Exception $e) {
                        Log::error("Error calculating SDI", [
                            'link_no' => $segment->link_no,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // 4. Format data untuk frontend
                $result[] = [
                    'link_no' => $segment->link_no,
                    'chainage_from' => (float) $segment->chainage_from,
                    'chainage_to' => (float) $segment->chainage_to,
                    'coords' => $coords->map(function($c) {
                        return [
                            'lat' => (float) $c->lat,
                            'lng' => (float) $c->lng
                        ];
                    })->values()->toArray(),
                    'sdi_final' => $sdi ? (float) $sdi['sdi_final'] : null,
                    'category' => $category,
                    'year' => (int) $segment->year
                ];
            }

            Log::info("getCoordsWithSDI completed", [
                'year' => $year,
                'total_segments' => count($result)
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("Error getCoordsWithSDI: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}