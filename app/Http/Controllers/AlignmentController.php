<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoadCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlignmentController extends Controller
{
    // View peta
    public function showMap()
    {
        return view('peta.kabupaten.index');
    }

    public function getCoords(Request $request)
    {
        try {
            $selectedYear = $request->get('year') ?? session('selected_year');
            
            // ✅ Ambil reference year dari database (SAMA seperti Dashboard)
            $referenceYear = RoadCondition::where('year', $selectedYear)
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->value('reference_year') ?? ($selectedYear - 1);

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

            if ($referenceYear) {
                // ✅ Join ke link dengan reference year
                $query .= " INNER JOIN link l ON a.link_no = l.link_no";
                $conditions[] = "l.year = ?";
                $bindings[] = $referenceYear;
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

            Log::info("getCoords completed", [
                'selected_year' => $selectedYear,
                'reference_year' => $referenceYear,
                'link_count' => $grouped->count()
            ]);

            return response()->json($grouped);

        } catch (\Exception $e) {
            Log::error("Error getCoords: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

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

    public function getCoordsWithSDI(Request $request)
    {
        try {
            set_time_limit(600);
            ini_set('memory_limit', '1G');

            $surveyYear = $request->get('year') ?? session('selected_year') ?? date('Y');
            
            // ✅ Ambil reference year dari database
            $referenceYear = RoadCondition::where('year', $surveyYear)
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->value('reference_year');
            
            if (!$referenceYear) {
                $referenceYear = $surveyYear - 1;
            }
            
            Log::info("getCoordsWithSDI called", [
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear
            ]);

            // ✅ PERBAIKAN: JANGAN gunakan cache untuk endpoint ini
            // Karena ini digunakan untuk SEMUA kecamatan tanpa filter
            // Cache hanya berguna jika ada parameter yang konsisten
            
            // ✅ STRICT FILTER seperti Dashboard
            $segments = RoadCondition::where('year', $surveyYear)
                ->where('reference_year', $referenceYear)
                ->whereNotNull('sdi_value')
                ->whereNotNull('sdi_category')
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->with(['link.linkMaster:id,link_no,link_name'])
                ->orderBy('link_no')
                ->orderBy('chainage_from')
                ->get();

            Log::info("Segments found", [
                'count' => $segments->count(),
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear
            ]);

            if ($segments->isEmpty()) {
                Log::warning("No segments with SDI found", [
                    'survey_year' => $surveyYear,
                    'reference_year' => $referenceYear
                ]);
                
                // ✅ PERBAIKAN: Return format yang konsisten
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Tidak ada data untuk tahun yang dipilih'
                ]);
            }

            $linkNos = $segments->pluck('link_no')->unique();

            // Ambil SEMUA koordinat untuk link yang ada
            $allCoords = DB::table('alignment')
                ->whereIn('link_no', $linkNos)
                ->select('link_no', 'chainage', 'north as lat', 'east as lng')
                ->orderBy('link_no')
                ->orderBy('chainage')
                ->get()
                ->groupBy('link_no');

            Log::info("Alignment coords loaded", ['link_count' => $allCoords->count()]);

            $result = [];
            $skippedCount = 0;
            $fallbackCount = 0;
            $rangeCount = 0;

            foreach ($segments as $condition) {
                $coordsAll = $allCoords[$condition->link_no] ?? collect();

                // Skip kalau link ini tidak punya koordinat sama sekali
                if ($coordsAll->isEmpty()) {
                    $skippedCount++;
                    Log::warning("No coordinates found for link", [
                        'link_no' => $condition->link_no
                    ]);
                    continue;
                }

                // ===== OPSI 1B: STEP 1 - Coba ambil SEMUA koordinat dalam range =====
                $coords = $coordsAll->filter(function ($c) use ($condition) {
                    return $c->chainage >= $condition->chainage_from 
                        && $c->chainage <= $condition->chainage_to;
                })->values();

                // ===== STEP 2: Kalau dapat >= 2 titik, pakai semua titik dalam range =====
                if ($coords->count() >= 2) {
                    $rangeCount++;
                    Log::debug("Using coordinates in range", [
                        'link_no' => $condition->link_no,
                        'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                        'points_found' => $coords->count(),
                        'chainages' => $coords->pluck('chainage')->implode(', ')
                    ]);
                } 
                // ===== STEP 3: Kalau dapat < 2 titik, pakai FALLBACK (cari terdekat) =====
                else {
                    $fallbackCount++;
                    
                    // Cari koordinat terdekat ke chainage_from
                    $nearestStart = $coordsAll->sortBy(function($c) use ($condition) {
                        return abs($c->chainage - $condition->chainage_from);
                    })->first();
                    
                    // Cari koordinat terdekat ke chainage_to
                    $nearestEnd = $coordsAll->sortBy(function($c) use ($condition) {
                        return abs($c->chainage - $condition->chainage_to);
                    })->first();
                    
                    // Kalau start dan end sama (titik yang sama), ambil 2 titik berurutan
                    if ($nearestStart && $nearestEnd && $nearestStart->chainage == $nearestEnd->chainage) {
                        // Cari index titik ini dalam collection
                        $index = $coordsAll->search(function($c) use ($nearestStart) {
                            return $c->chainage == $nearestStart->chainage 
                                && $c->lat == $nearestStart->lat 
                                && $c->lng == $nearestStart->lng;
                        });
                        
                        // Ambil titik ini dan titik berikutnya (atau sebelumnya)
                        if ($index !== false) {
                            if ($index < $coordsAll->count() - 1) {
                                // Ambil current dan next
                                $coords = collect([$coordsAll[$index], $coordsAll[$index + 1]]);
                            } else if ($index > 0) {
                                // Ambil previous dan current
                                $coords = collect([$coordsAll[$index - 1], $coordsAll[$index]]);
                            } else {
                                // Hanya ada 1 titik total untuk link ini
                                $coords = collect([$nearestStart]);
                            }
                        } else {
                            $coords = collect([$nearestStart]);
                        }
                    } else {
                        // Start dan end berbeda, pakai keduanya
                        $coords = collect([$nearestStart, $nearestEnd]);
                    }
                    
                    Log::debug("Using fallback coordinates", [
                        'link_no' => $condition->link_no,
                        'chainage_requested' => "{$condition->chainage_from} - {$condition->chainage_to}",
                        'chainage_used' => $coords->pluck('chainage')->implode(', '),
                        'points_in_range' => $coordsAll->filter(function ($c) use ($condition) {
                            return $c->chainage >= $condition->chainage_from 
                                && $c->chainage <= $condition->chainage_to;
                        })->count(),
                        'distance_from' => $nearestStart ? abs($nearestStart->chainage - $condition->chainage_from) : 'N/A',
                        'distance_to' => $nearestEnd ? abs($nearestEnd->chainage - $condition->chainage_to) : 'N/A'
                    ]);
                }

                // ===== STEP 4: Final check - pastikan minimal ada 2 titik =====
                if ($coords->count() < 2) {
                    $skippedCount++;
                    Log::warning("Insufficient coordinates for segment", [
                        'link_no' => $condition->link_no,
                        'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                        'points_available' => $coords->count(),
                        'total_coords_for_link' => $coordsAll->count()
                    ]);
                    continue;
                }

                // ===== STEP 5: Masukkan ke result =====
                $result[] = [
                    'link_no' => $condition->link_no,
                    'link_name' => $condition->link->linkMaster->link_name ?? 'Nama ruas tidak tersedia',
                    'chainage_from' => (float) $condition->chainage_from,
                    'chainage_to' => (float) $condition->chainage_to,
                    'coords' => $coords->map(fn($c) => [
                        'lat' => (float) $c->lat,
                        'lng' => (float) $c->lng,
                    ])->values()->toArray(),
                    'sdi_final' => (float) $condition->sdi_value,
                    'category' => $condition->sdi_category,
                    'year' => (int) $condition->year,
                    'reference_year' => (int) $condition->reference_year
                ];
            }

            Log::info("getCoordsWithSDI completed", [
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear,
                'total_segments' => $segments->count(),
                'segments_processed' => count($result),
                'segments_with_range_coords' => $rangeCount,
                'segments_with_fallback' => $fallbackCount,
                'segments_skipped' => $skippedCount,
                'success_rate' => $segments->count() > 0 ? round((count($result) / $segments->count()) * 100, 2) . '%' : '0%'
            ]);

            // ✅ PERBAIKAN: Return format yang konsisten
            return response()->json([
                'success' => true,
                'data' => $result,
                'count' => count($result),
                'stats' => [
                    'total' => $segments->count(),
                    'displayed' => count($result),
                    'range_coords' => $rangeCount,
                    'fallback' => $fallbackCount,
                    'skipped' => $skippedCount,
                    'success_rate' => $segments->count() > 0 ? round((count($result) / $segments->count()) * 100, 2) . '%' : '0%'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error getCoordsWithSDI: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getKecamatanList(Request $request)
    {
        try {
            $surveyYear = $request->get('year') ?? session('selected_year') ?? date('Y');
            
            // ✅ Ambil reference year dari database
            $referenceYear = RoadCondition::where('year', $surveyYear)
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->value('reference_year') ?? ($surveyYear - 1);
            
            Log::info("getKecamatanList called", [
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear
            ]);
            
            // ✅ STRICT FILTER seperti Dashboard
            $kecamatans = DB::table('link_kecamatan as lk')
                // ✅ BENAR - join lewat link_master
                ->join('link_master as lm', 'lk.link_master_id', '=', 'lm.id')
                ->join('link as l', function($join) use ($referenceYear) {
                    $join->on('lm.id', '=', 'l.link_master_id');
                    if ($referenceYear) {
                        $join->where('l.year', '=', $referenceYear);
                    }
                })
                ->join('kecamatan as k', 'lk.kecamatan_code', '=', 'k.kecamatan_code')
                ->join('kabupaten as kab', 'lk.kabupaten_code', '=', 'kab.kabupaten_code')
                ->join('road_condition as rc', function($join) use ($surveyYear, $referenceYear) {
                    $join->on('l.link_no', '=', 'rc.link_no')
                         ->where('rc.year', '=', $surveyYear);
                    if ($referenceYear) {
                        $join->where('rc.reference_year', '=', $referenceYear);
                    }
                })
                ->where('kab.kabupaten_name', 'LIKE', '%JEMBER%')
                ->whereNotNull('rc.sdi_value')
                ->whereNotNull('rc.sdi_category')
                ->select(
                    'k.kecamatan_code',
                    'k.kecamatan_name',
                    DB::raw('COUNT(DISTINCT l.id) as total_links'),
                    DB::raw('COUNT(DISTINCT CONCAT(rc.link_no, "-", rc.chainage_from, "-", rc.chainage_to)) as total_segments')
                )
                ->groupBy('k.kecamatan_code', 'k.kecamatan_name')
                ->orderBy('k.kecamatan_name')
                ->get();

            Log::info("getKecamatanList completed", [
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear,
                'kecamatan_count' => $kecamatans->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $kecamatans,
                'year' => $surveyYear,
                'reference_year' => $referenceYear
            ]);

        } catch (\Exception $e) {
            Log::error("Error getKecamatanList: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCoordsWithSDIByKecamatan(Request $request)
    {
        try {
            // ✅ PERBAIKAN 1: VALIDASI INPUT (CRITICAL!)
            $validated = $request->validate([
                'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'kecamatan_codes' => 'required|array|min:1',
                'kecamatan_codes.*' => 'string|max:10'
            ], [
                'year.required' => 'Tahun harus diisi',
                'year.integer' => 'Tahun harus berupa angka',
                'kecamatan_codes.required' => 'Pilih minimal 1 kecamatan',
                'kecamatan_codes.min' => 'Pilih minimal 1 kecamatan'
            ]);

            set_time_limit(600); // ✅ 10 menit
            ini_set('memory_limit', '1G');

            $surveyYear = $validated['year'];
            $kecamatanCodes = $validated['kecamatan_codes'];
            
            // ✅ Ambil reference year dari database
            $referenceYear = RoadCondition::where('year', $surveyYear)
                ->whereHas('kabupaten', function ($query) {
                    $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                })
                ->value('reference_year') ?? ($surveyYear - 1);
            
            Log::info('getCoordsWithSDIByKecamatan called', [
                'survey_year' => $surveyYear,
                'reference_year' => $referenceYear,
                'kecamatan_codes' => $kecamatanCodes,
                'kecamatan_count' => count($kecamatanCodes)
            ]);

            // ✅ PERBAIKAN 2: CACHE KEY yang BENAR (include kecamatan!)
            sort($kecamatanCodes); // Supaya [1,2,3] = [3,2,1]
            $kecKey = implode('_', $kecamatanCodes);
            $cacheKey = "coords_sdi_kec_{$surveyYear}_ref_{$referenceYear}_{$kecKey}_v3";
            
            // ✅ PERBAIKAN 3: Gunakan cache dengan key yang benar
            return Cache::remember($cacheKey, 3600, function () use ($surveyYear, $referenceYear, $kecamatanCodes) {
                
                // ✅ STRICT FILTER seperti Dashboard
                $segments = RoadCondition::where('year', $surveyYear)
                    ->where('reference_year', $referenceYear)
                    ->whereNotNull('sdi_value')
                    ->whereNotNull('sdi_category')
                    ->whereHas('kabupaten', function ($query) {
                        $query->where('kabupaten_name', 'LIKE', '%JEMBER%');
                    })
                    // ✅ BENAR - lewat linkMaster
                    ->whereHas('link.linkMaster', function($query) use ($kecamatanCodes) {
                        $query->whereHas('linkKecamatans', function($q) use ($kecamatanCodes) {
                            $q->whereIn('kecamatan_code', $kecamatanCodes);
                        });
                    })
                    ->with([
                        'link.linkMaster:id,link_no,link_name',
                        'link.roadInventories:link_id,pave_width'
                    ])
                    ->orderBy('link_no')
                    ->orderBy('chainage_from')
                    ->get();
                
                Log::info('Segments found', [
                    'count' => $segments->count(),
                    'survey_year' => $surveyYear,
                    'reference_year' => $referenceYear
                ]);

                if ($segments->isEmpty()) {
                    return [
                        'success' => true,
                        'data' => [],
                        'message' => 'Tidak ada data untuk kecamatan yang dipilih',
                        'count' => 0,
                        'stats' => [
                            'total' => 0,
                            'displayed' => 0,
                            'range_coords' => 0,
                            'fallback' => 0,
                            'skipped' => 0,
                            'success_rate' => '0%'
                        ]
                    ];
                }

                $linkNos = $segments->pluck('link_no')->unique();

                $allCoords = DB::table('alignment')
                    ->whereIn('link_no', $linkNos)
                    ->select('link_no', 'chainage', 'north as lat', 'east as lng')
                    ->orderBy('link_no')
                    ->orderBy('chainage')
                    ->get()
                    ->groupBy('link_no');

                Log::info('Alignment coords loaded', ['link_count' => $allCoords->count()]);

                $result = [];
                $skippedCount = 0;
                $fallbackCount = 0;
                $rangeCount = 0;

                foreach ($segments as $condition) {
                    $coordsAll = $allCoords[$condition->link_no] ?? collect();

                    // Skip kalau link ini tidak punya koordinat sama sekali
                    if ($coordsAll->isEmpty()) {
                        $skippedCount++;
                        Log::warning("No coordinates found for link", [
                            'link_no' => $condition->link_no
                        ]);
                        continue;
                    }

                    // ===== OPSI 1B: STEP 1 - Coba ambil SEMUA koordinat dalam range =====
                    $coords = $coordsAll->filter(function ($c) use ($condition) {
                        return $c->chainage >= $condition->chainage_from 
                            && $c->chainage <= $condition->chainage_to;
                    })->values();

                    // ===== STEP 2: Kalau dapat >= 2 titik, pakai semua titik dalam range =====
                    if ($coords->count() >= 2) {
                        $rangeCount++;
                        Log::debug("Using coordinates in range", [
                            'link_no' => $condition->link_no,
                            'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                            'points_found' => $coords->count(),
                            'chainages' => $coords->pluck('chainage')->implode(', ')
                        ]);
                    } 
                    // ===== STEP 3: Kalau dapat < 2 titik, pakai FALLBACK (cari terdekat) =====
                    else {
                        $fallbackCount++;
                        
                        // Cari koordinat terdekat ke chainage_from
                        $nearestStart = $coordsAll->sortBy(function($c) use ($condition) {
                            return abs($c->chainage - $condition->chainage_from);
                        })->first();
                        
                        // Cari koordinat terdekat ke chainage_to
                        $nearestEnd = $coordsAll->sortBy(function($c) use ($condition) {
                            return abs($c->chainage - $condition->chainage_to);
                        })->first();
                        
                        // Kalau start dan end sama (titik yang sama), ambil 2 titik berurutan
                        if ($nearestStart && $nearestEnd && $nearestStart->chainage == $nearestEnd->chainage) {
                            // Cari index titik ini dalam collection
                            $index = $coordsAll->search(function($c) use ($nearestStart) {
                                return $c->chainage == $nearestStart->chainage 
                                    && $c->lat == $nearestStart->lat 
                                    && $c->lng == $nearestStart->lng;
                            });
                            
                            // Ambil titik ini dan titik berikutnya (atau sebelumnya)
                            if ($index !== false) {
                                if ($index < $coordsAll->count() - 1) {
                                    // Ambil current dan next
                                    $coords = collect([$coordsAll[$index], $coordsAll[$index + 1]]);
                                } else if ($index > 0) {
                                    // Ambil previous dan current
                                    $coords = collect([$coordsAll[$index - 1], $coordsAll[$index]]);
                                } else {
                                    // Hanya ada 1 titik total untuk link ini
                                    $coords = collect([$nearestStart]);
                                }
                            } else {
                                $coords = collect([$nearestStart]);
                            }
                        } else {
                            // Start dan end berbeda, pakai keduanya
                            $coords = collect([$nearestStart, $nearestEnd]);
                        }
                        
                        Log::debug("Using fallback coordinates", [
                            'link_no' => $condition->link_no,
                            'chainage_requested' => "{$condition->chainage_from} - {$condition->chainage_to}",
                            'chainage_used' => $coords->pluck('chainage')->implode(', '),
                            'points_in_range' => $coordsAll->filter(function ($c) use ($condition) {
                                return $c->chainage >= $condition->chainage_from 
                                    && $c->chainage <= $condition->chainage_to;
                            })->count(),
                            'distance_from' => $nearestStart ? abs($nearestStart->chainage - $condition->chainage_from) : 'N/A',
                            'distance_to' => $nearestEnd ? abs($nearestEnd->chainage - $condition->chainage_to) : 'N/A'
                        ]);
                    }

                    // ===== STEP 4: Final check - pastikan minimal ada 2 titik =====
                    if ($coords->count() < 2) {
                        $skippedCount++;
                        Log::warning("Insufficient coordinates for segment", [
                            'link_no' => $condition->link_no,
                            'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                            'points_available' => $coords->count(),
                            'total_coords_for_link' => $coordsAll->count()
                        ]);
                        continue;
                    }

                    // ===== STEP 5: Masukkan ke result =====
                    $result[] = [
                        'link_no' => $condition->link_no,
                        'link_name' => $condition->link->linkMaster->link_name ?? 'Nama ruas tidak tersedia',
                        'chainage_from' => (float) $condition->chainage_from,
                        'chainage_to' => (float) $condition->chainage_to,
                        'coords' => $coords->map(fn($c) => [
                            'lat' => (float) $c->lat,
                            'lng' => (float) $c->lng,
                        ])->values()->toArray(),
                        'sdi_final' => (float) $condition->sdi_value,
                        'category' => $condition->sdi_category,
                        'year' => (int) $condition->year,
                        'reference_year' => (int) $condition->reference_year,
                        'pave_width' => (float) ($condition->link->roadInventories->first()->pave_width ?? 6)
                    ];
                }

                Log::info('getCoordsWithSDIByKecamatan completed', [
                    'survey_year' => $surveyYear,
                    'reference_year' => $referenceYear,
                    'total_segments' => $segments->count(),
                    'segments_processed' => count($result),
                    'segments_with_range_coords' => $rangeCount,
                    'segments_with_fallback' => $fallbackCount,
                    'segments_skipped' => $skippedCount,
                    'success_rate' => $segments->count() > 0 ? round((count($result) / $segments->count()) * 100, 2) . '%' : '0%'
                ]);

                // ✅ PERBAIKAN 4: Return format yang konsisten
                return [
                    'success' => true,
                    'data' => $result,
                    'count' => count($result),
                    'stats' => [
                        'total' => $segments->count(),
                        'displayed' => count($result),
                        'range_coords' => $rangeCount,
                        'fallback' => $fallbackCount,
                        'skipped' => $skippedCount,
                        'success_rate' => $segments->count() > 0 ? round((count($result) / $segments->count()) * 100, 2) . '%' : '0%'
                    ]
                ];
            }); // End Cache::remember - ini akan return response dari cache

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ✅ Handle validation error
            Log::warning("Validation failed in getCoordsWithSDIByKecamatan", [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'details' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error("Error getCoordsWithSDIByKecamatan: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}