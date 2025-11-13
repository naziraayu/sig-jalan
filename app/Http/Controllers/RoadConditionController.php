<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use App\Models\RoadCondition;
use App\Models\RoadInventory;
use App\Models\CodeLinkStatus;
use Illuminate\Support\Facades\Log;
use App\Exports\RoadConditionExport;
use App\Imports\RoadConditionImport;
use Maatwebsite\Excel\Facades\Excel;

class RoadConditionController extends Controller
{
    public function index()
    { 
        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi   = Province::orderBy('province_name')->get();
        $kabupaten  = Kabupaten::orderBy('kabupaten_name')->get();

        // Ambil tahun yang dipilih dari session (jika ada)
        $selectedYear = session('selected_year');
        
        // Ambil semua tahun yang ada di road_condition
        $availableYears = RoadCondition::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('jalan.kondisi-jalan.index', compact(
            'statusRuas', 'provinsi', 'kabupaten', 'availableYears', 'selectedYear'
        ));
    }

    /**
     * ✅ PERBAIKAN: Get ruas berdasarkan tahun yang dipilih
     */
    public function getRuasByYear(Request $request)
    {
        $year = $request->get('year');
        
        if (!$year) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak valid',
                'data' => []
            ]);
        }

        try {
            // Step 1: Ambil semua link_no yang UNIK dari road_condition untuk tahun ini
            $linkNos = RoadCondition::where('year', $year)
                ->distinct()
                ->pluck('link_no')
                ->filter() // Hilangkan null values
                ->unique()
                ->values();

            Log::info('Link Nos found:', ['count' => $linkNos->count(), 'data' => $linkNos->toArray()]);

            if ($linkNos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data ruas untuk tahun ' . $year,
                    'data' => []
                ]);
            }

            // Step 2: Ambil detail ruas dari tabel link
            $ruasList = Link::whereIn('link_no', $linkNos)
                ->orderBy('link_code')
                ->get();

            Log::info('Ruas List found:', ['count' => $ruasList->count()]);

            // Jika tidak ada data di tabel link, kembalikan link_no saja
            if ($ruasList->isEmpty()) {
                // Fallback: buat data dari link_no yang ada
                $ruasList = $linkNos->map(function($linkNo) {
                    return [
                        'link_no' => $linkNo,
                        'link_code' => $linkNo,
                        'link_name' => 'Ruas ' . $linkNo,
                    ];
                });
            } else {
                // Format data
                $ruasList = $ruasList->map(function($link) {
                    return [
                        'link_no' => $link->link_no,
                        'link_code' => $link->link_code ?? $link->link_no,
                        'link_name' => $link->link_name ?? 'Ruas ' . $link->link_no,
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $ruasList->values(),
                'count' => $ruasList->count(),
                'year' => $year
            ]);

        } catch (\Exception $e) {
            Log::error('Error getRuasByYear: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getYears(Request $request)
    {
        $linkNo = $request->get('link_no');
        
        if (!$linkNo) {
            return response()->json([
                'success' => false,
                'message' => 'Link No tidak valid'
            ]);
        }

        // Ambil semua tahun yang tersedia untuk ruas ini
        $years = RoadCondition::where('link_no', $linkNo)
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }

    public function getDetail(Request $request)
    {
        $linkNo = $request->get('link_no');
        $year = $request->get('year');

        // Jika tahun tidak dipilih, ambil dari session
        if (!$year) {
            $year = session('selected_year');
        }

        // Validasi input
        if (!$linkNo || !$year) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih ruas dan tahun terlebih dahulu',
            ]);
        }

        try {
            // Ambil data dan urutkan secara numerik
            $conditions = RoadCondition::where('link_no', $linkNo)
                ->where('year', $year)
                ->get()
                ->sortBy(function($item) {
                    // Konversi chainage_from ke float untuk sorting numerik
                    return floatval($item->chainage_from);
                })
                ->values(); // Reset array keys

            if ($conditions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk ruas ' . $linkNo . ' tahun ' . $year,
                ]);
            }

            // Ambil semua inventory untuk link ini
            $inventories = RoadInventory::where('link_no', $linkNo)->get();

            $dataWithSDI = $conditions->map(function($item) use ($inventories) {
                // Cari inventory yang cocok dengan chainage
                $inventory = $inventories->first(function($inv) use ($item) {
                    return $inv->chainage_from <= $item->chainage_from 
                        && $inv->chainage_to >= $item->chainage_to;
                });

                $paveWidth = $inventory ? floatval($inventory->pave_width) : 7.0;

                // Temporary set inventory untuk calculateSDI
                $item->inventory = $inventory;
                
                $sdi = $this->calculateSDI($item);
                
                return [
                    'chainage_from' => $item->chainage_from,
                    'chainage_to' => $item->chainage_to,
                    'year' => intval($item->year),
                    'iri' => $item->iri ? floatval($item->iri) : null,
                    'rci' => $item->rci ? floatval($item->rci) : null,
                    'sdi1' => floatval($sdi['sdi1']),
                    'sdi2' => floatval($sdi['sdi2']),
                    'sdi3' => floatval($sdi['sdi3']),
                    'sdi4' => floatval($sdi['sdi4']),
                    'sdi_final' => floatval($sdi['sdi_final']),
                    'sdi_category' => $sdi['category'],
                    'link_no' => $item->link_no,
                    'pave_width' => $paveWidth,
                    'total_crack_area' => floatval($sdi['total_crack_area']),
                    'crack_percentage' => floatval($sdi['crack_percentage']),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $dataWithSDI,
                'count' => $dataWithSDI->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getDetail: ' . $e->getMessage(), [
                'link_no' => $linkNo,
                'year' => $year,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($link_no)
    {
        $selectedYear = session('selected_year');

        // ✅ PERBAIKAN: Ambil ruas dengan prioritas tahun yang dipilih
        $ruas = Link::with(['province', 'kabupaten'])
            ->where('link_no', $link_no)
            ->when($selectedYear, function($query) use ($selectedYear) {
                // Jika ada selectedYear, prioritaskan tahun tersebut
                return $query->where('year', $selectedYear);
            })
            ->orderBy('year', 'desc') // Ambil tahun terbaru jika tidak ada filter
            ->first();

        // Jika tidak ada data dengan tahun yang dipilih, ambil data tahun terbaru
        if (!$ruas) {
            $ruas = Link::with(['province', 'kabupaten'])
                ->where('link_no', $link_no)
                ->orderBy('year', 'desc')
                ->firstOrFail();
        }

        // Ambil tahun yang tersedia untuk ruas ini
        $availableYears = RoadCondition::where('link_no', $link_no)
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Ambil data kondisi jalan DENGAN FILTER YEAR
        $conditions = RoadCondition::where('link_no', $link_no)
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->where('year', $selectedYear);
            })
            ->get()
            ->sortBy(function($item) {
                return [
                    -intval($item->year),
                    floatval($item->chainage_from)
                ];
            })
            ->values();

        // Load inventories secara manual
        $inventories = RoadInventory::where('link_no', $link_no)->get();

        // Map conditions dengan inventory
        $conditions = $conditions->map(function($condition) use ($inventories) {
            $condition->inventory = $inventories->first(function($inv) use ($condition) {
                return $inv->chainage_from <= $condition->chainage_from 
                    && $inv->chainage_to >= $condition->chainage_to;
            });
            return $condition;
        });

        // Hitung SDI untuk setiap segmen
        $conditionsWithSDI = $conditions->map(function($condition) {
            $sdi = $this->calculateSDI($condition);
            $condition->sdi_data = $sdi;
            return $condition;
        });

        // Statistik keseluruhan
        $statistics = [
            'total_segments' => $conditions->count(),
            'total_length' => $conditions->sum(function($item) {
                return $item->chainage_to - $item->chainage_from;
            }),
            'avg_iri' => $conditions->where('iri', '>', 0)->avg('iri'),
            'avg_rci' => $conditions->where('rci', '>', 0)->avg('rci'),
            'avg_sdi' => $conditionsWithSDI->avg('sdi_data.sdi_final'),
            'good_condition' => $conditionsWithSDI->where('sdi_data.category', 'Baik')->count(),
            'fair_condition' => $conditionsWithSDI->where('sdi_data.category', 'Sedang')->count(),
            'poor_condition' => $conditionsWithSDI->where('sdi_data.category', 'Rusak Ringan')->count(),
            'very_poor_condition' => $conditionsWithSDI->where('sdi_data.category', 'Rusak Berat')->count(),
        ];

        // ✅ Damage Analysis - LENGKAP
        $damage_analysis = [
            'total_crack_area' => $conditions->sum('crack_dep_area') + 
                                $conditions->sum('oth_crack_area') + 
                                $conditions->sum('concrete_cracking_area') + 
                                $conditions->sum('concrete_structural_cracking_area'),
            'total_bleeding_area' => $conditions->sum('bleeding_area'),
            'total_ravelling_area' => $conditions->sum('ravelling_area'),
            'total_desintegration_area' => $conditions->sum('desintegration_area'),
            'total_patching_area' => $conditions->sum('patching_area'),
            'total_pothole_area' => $conditions->sum('pothole_area'),
            'total_pothole_count' => $conditions->sum('pothole_count'),
            'total_potholes' => $conditions->sum('pothole_count'),
            'total_rutting_area' => $conditions->sum('rutting_area'),
            'avg_rutting_depth' => $conditions->avg('rutting_depth'),
            'total_edge_damage' => $conditions->sum('edge_damage_area'),
            'avg_crack_width' => $conditions->avg('crack_width'),
            'segments_with_bleeding' => $conditions->where('bleeding_area', '>', 0)->count(),
        ];

        // ✅ SDI by Year - TAMBAHAN BARU
        $sdi_by_year = $conditionsWithSDI->groupBy('year')->map(function($items, $year) {
            return [
                'avg_sdi' => $items->avg('sdi_data.sdi_final'),
                'min_sdi' => $items->min('sdi_data.sdi_final'),
                'max_sdi' => $items->max('sdi_data.sdi_final'),
                'count' => $items->count(),
            ];
        })->sortKeysDesc();

        return view('jalan.kondisi-jalan.show', compact(
            'ruas',
            'conditions',
            'conditionsWithSDI',
            'statistics',
            'availableYears',
            'damage_analysis',
            'sdi_by_year'
        ));
    }

    /**
     * Get detail segmen untuk modal detail
     */
    public function getSegmentDetail(Request $request)
    {
        $linkNo = $request->get('link_no');
        $chainageFrom = $request->get('chainage_from');
        $chainageTo = $request->get('chainage_to');
        $year = $request->get('year');

        if (!$linkNo || !$chainageFrom || !$chainageTo || !$year) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        try {
            // Ambil data kondisi jalan spesifik
            $condition = RoadCondition::where('link_no', $linkNo)
                ->where('chainage_from', $chainageFrom)
                ->where('chainage_to', $chainageTo)
                ->where('year', $year)
                ->first();

            if (!$condition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // ✅ Load data Link secara manual
            $link = Link::where('link_no', $linkNo)->first();
            
            // ✅ Load inventory secara manual
            $inventory = RoadInventory::where('link_no', $linkNo)
                ->where('chainage_from', '<=', $chainageFrom)
                ->where('chainage_to', '>=', $chainageTo)
                ->first();

            // Set inventory untuk calculateSDI
            $condition->inventory = $inventory;

            // Hitung SDI dengan detail lengkap
            $sdiDetail = $this->calculateSDIWithDetails($condition);

            return response()->json([
                'success' => true,
                'data' => [
                    'condition' => [
                        'link_no' => [
                            'link_code' => $link->link_code ?? $linkNo, // ✅ PERBAIKAN
                            'link_name' => $link->link_name ?? 'Ruas ' . $linkNo, // ✅ PERBAIKAN
                        ],
                        'chainage_from' => $condition->chainage_from,
                        'chainage_to' => $condition->chainage_to,
                        'year' => $condition->year,
                    ],
                    'sdi_detail' => $sdiDetail
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getSegmentDetail', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ METHOD BARU: Public wrapper untuk calculateSDI
     * Method ini bisa dipanggil dari controller lain
     */
    public function calculateSDIPublic($condition)
    {
        return $this->calculateSDI($condition);
    }

    /**
     * ✅ PERBAIKAN: Pastikan method calculateSDI bisa handle data tanpa relationship
     * Fungsi untuk menghitung SDI (Surface Distress Index) sesuai aturan Bina Marga
     */
    private function calculateSDI($condition)
    {
        // ✅ PERBAIKAN: Cek apakah inventory sudah di-load atau perlu di-load manual
        if (!isset($condition->inventory)) {
            $condition->load('inventory');
        }
        
        // Ambil data dari inventory untuk lebar jalan
        $paveWidth = $condition->inventory->pave_width ?? 0;
        
        if ($paveWidth == 0) {
            Log::warning("Pave width is 0 for condition", [
                'link_no' => $condition->link_no,
                'chainage_from' => $condition->chainage_from
            ]);
            
            return [
                'sdi1' => 0,
                'sdi2' => 0,
                'sdi3' => 0,
                'sdi4' => 0,
                'sdi_final' => 0,
                'category' => 'Data Tidak Lengkap',
                'total_crack_area' => 0,
                'crack_percentage' => 0,
            ];
        }

        // Hitung panjang segmen (km → meter)
        $segmentLength = ($condition->chainage_to - $condition->chainage_from) * 1000;
        
        // Normalisasi ke 100m
        $normalizedLength = 100;

        /**
         * 1. Total Area of Cracks (Luas Retak)
         */
        $totalCrackArea = ($condition->crack_dep_area ?? 0) + 
                        ($condition->oth_crack_area ?? 0) + 
                        ($condition->concrete_cracking_area ?? 0) + 
                        ($condition->concrete_structural_cracking_area ?? 0);

        $crackPercentage = ($normalizedLength * $paveWidth > 0) 
            ? ($totalCrackArea / ($normalizedLength * $paveWidth)) * 100 
            : 0;

        $sdi1 = 0;
        if ($crackPercentage == 0) {
            $sdi1 = 0;
        } elseif ($crackPercentage < 10) {
            $sdi1 = 5;
        } elseif ($crackPercentage >= 10 && $crackPercentage <= 30) {
            $sdi1 = 20;
        } else {
            $sdi1 = 40;
        }

        /**
         * 2. Average Crack Width (Lebar Retak)
         */
        $crackWidth = $condition->crack_width ?? 0;
        $sdi2 = $sdi1;

        if ($crackWidth > 0) {
            if ($crackWidth < 1) {
                $sdi2 = $sdi1;
            } elseif ($crackWidth >= 1 && $crackWidth <= 3) {
                $sdi2 = $sdi1;
            } else {
                $sdi2 = $sdi1 * 2;
            }
        }

        /**
         * 3. Total Number of Potholes
         */
        $potholeCount = $condition->pothole_count ?? 0;
        $normalizedPotholes = $segmentLength > 0 
            ? ($potholeCount / $segmentLength) * 100 
            : $potholeCount;

        $sdi3 = $sdi2;
        if ($normalizedPotholes > 0) {
            if ($normalizedPotholes < 10) {
                $sdi3 = $sdi2 + 15;
            } elseif ($normalizedPotholes >= 10 && $normalizedPotholes <= 50) {
                $sdi3 = $sdi2 + 75;
            } else {
                $sdi3 = $sdi2 + 225;
            }
        }

        /**
         * 4. Average Depth of Wheel Rutting
         */
        $ruttingDepth = $condition->rutting_depth ?? 0;
        $sdi4 = $sdi3;

        if ($ruttingDepth > 0) {
            if ($ruttingDepth < 1) {
                $X = 0.5;
                $sdi4 = $sdi3 + (5 * $X);
            } elseif ($ruttingDepth >= 1 && $ruttingDepth <= 3) {
                $X = 2;
                $sdi4 = $sdi3 + (5 * $X);
            } else {
                $sdi4 = $sdi3 + 20;
            }
        }

        // Tentukan kategori SDI
        $category = $this->getSDICategory($sdi4);

        return [
            'sdi1' => round($sdi1, 2),
            'sdi2' => round($sdi2, 2),
            'sdi3' => round($sdi3, 2),
            'sdi4' => round($sdi4, 2),
            'sdi_final' => round($sdi4, 2),
            'category' => $category,
            'total_crack_area' => round($totalCrackArea, 2),
            'crack_percentage' => round($crackPercentage, 2),
            'crack_width' => $crackWidth,
            'pothole_count' => $potholeCount,
            'rutting_depth' => $ruttingDepth,
        ];
    }

    /**
     * Fungsi untuk menentukan kategori SDI
     */
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
     * Calculate SDI dengan penjelasan detail setiap tahap
     */
    private function calculateSDIWithDetails($condition)
    {
        $paveWidth = $condition->inventory->pave_width ?? 0;
        
        if ($paveWidth == 0) {
            return [
                'error' => true,
                'message' => 'Data lebar jalan tidak tersedia'
            ];
        }

        // Konversi ke float untuk memastikan format angka
        $segmentLength = floatval($condition->chainage_to - $condition->chainage_from) * 1000;
        $normalizedLength = 100;

        // ===== TAHAP 1: LUAS RETAK =====
        $crackDepArea = floatval($condition->crack_dep_area ?? 0);
        $othCrackArea = floatval($condition->oth_crack_area ?? 0);
        $concreteCrackingArea = floatval($condition->concrete_cracking_area ?? 0);
        $concreteStructuralArea = floatval($condition->concrete_structural_cracking_area ?? 0);
        
        $totalCrackArea = $crackDepArea + $othCrackArea + $concreteCrackingArea + $concreteStructuralArea;
        
        $totalSegmentArea = $normalizedLength * floatval($paveWidth);
        $crackPercentage = $totalSegmentArea > 0 ? ($totalCrackArea / $totalSegmentArea) * 100 : 0;

        $sdi1 = 0;
        $sdi1_explanation = '';
        
        if ($crackPercentage == 0) {
            $sdi1 = 0;
            $sdi1_explanation = 'Tidak ada retak (0%)';
        } elseif ($crackPercentage < 10) {
            $sdi1 = 5;
            $sdi1_explanation = sprintf('Retak < 10%% (%.2f%%) → SDI1 = 5', $crackPercentage);
        } elseif ($crackPercentage >= 10 && $crackPercentage <= 30) {
            $sdi1 = 20;
            $sdi1_explanation = sprintf('Retak 10-30%% (%.2f%%) → SDI1 = 20', $crackPercentage);
        } else {
            $sdi1 = 40;
            $sdi1_explanation = sprintf('Retak > 30%% (%.2f%%) → SDI1 = 40', $crackPercentage);
        }

        // ===== TAHAP 2: LEBAR RETAK =====
        $crackWidth = floatval($condition->crack_width ?? 0);
        $sdi2 = $sdi1;
        $sdi2_explanation = '';

        if ($crackWidth == 0) {
            $sdi2_explanation = 'Tidak ada data lebar retak → SDI2 = SDI1';
        } elseif ($crackWidth < 1) {
            $sdi2 = $sdi1;
            $sdi2_explanation = sprintf('Retak halus < 1mm (%.2fmm) → SDI2 = SDI1 = %.2f', $crackWidth, $sdi2);
        } elseif ($crackWidth >= 1 && $crackWidth <= 3) {
            $sdi2 = $sdi1;
            $sdi2_explanation = sprintf('Retak sedang 1-3mm (%.2fmm) → SDI2 = SDI1 = %.2f', $crackWidth, $sdi2);
        } else {
            $sdi2 = $sdi1 * 2;
            $sdi2_explanation = sprintf('Retak lebar > 3mm (%.2fmm) → SDI2 = SDI1 × 2 = %.2f', $crackWidth, $sdi2);
        }

        // ===== TAHAP 3: JUMLAH LUBANG =====
        $potholeCount = intval($condition->pothole_count ?? 0);
        $normalizedPotholes = $segmentLength > 0 ? ($potholeCount / $segmentLength) * 100 : $potholeCount;
        
        $sdi3 = $sdi2;
        $sdi3_explanation = '';
        $sdi3_addition = 0;

        if ($normalizedPotholes == 0) {
            $sdi3_explanation = 'Tidak ada lubang → SDI3 = SDI2';
        } elseif ($normalizedPotholes < 10) {
            $sdi3_addition = 15;
            $sdi3 = $sdi2 + $sdi3_addition;
            $sdi3_explanation = sprintf('Lubang < 10/100m (%.2f) → SDI3 = SDI2 + 15 = %.2f', $normalizedPotholes, $sdi3);
        } elseif ($normalizedPotholes >= 10 && $normalizedPotholes <= 50) {
            $sdi3_addition = 75;
            $sdi3 = $sdi2 + $sdi3_addition;
            $sdi3_explanation = sprintf('Lubang 10-50/100m (%.2f) → SDI3 = SDI2 + 75 = %.2f', $normalizedPotholes, $sdi3);
        } else {
            $sdi3_addition = 225;
            $sdi3 = $sdi2 + $sdi3_addition;
            $sdi3_explanation = sprintf('Lubang > 50/100m (%.2f) → SDI3 = SDI2 + 225 = %.2f', $normalizedPotholes, $sdi3);
        }

        // ===== TAHAP 4: KEDALAMAN ALUR RODA =====
        $ruttingDepth = floatval($condition->rutting_depth ?? 0);
        $sdi4 = $sdi3;
        $sdi4_explanation = '';
        $sdi4_addition = 0;

        if ($ruttingDepth == 0) {
            $sdi4_explanation = 'Tidak ada alur roda → SDI4 = SDI3';
        } elseif ($ruttingDepth < 1) {
            $X = 0.5;
            $sdi4_addition = 5 * $X;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur < 1cm (%.2fcm) → SDI4 = SDI3 + (5 × 0.5) = %.2f', $ruttingDepth, $sdi4);
        } elseif ($ruttingDepth >= 1 && $ruttingDepth <= 3) {
            $X = 2;
            $sdi4_addition = 5 * $X;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur 1-3cm (%.2fcm) → SDI4 = SDI3 + (5 × 2) = %.2f', $ruttingDepth, $sdi4);
        } else {
            $sdi4_addition = 20;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur > 3cm (%.2fcm) → SDI4 = SDI3 + 20 = %.2f', $ruttingDepth, $sdi4);
        }

        // Kategori
        $category = $this->getSDICategory($sdi4);

        return [
            'raw_data' => [
                'pave_width' => floatval($paveWidth),
                'segment_length' => floatval($segmentLength / 1000),
                'total_segment_area' => floatval($totalSegmentArea),
                'crack_dep_area' => floatval($crackDepArea),
                'oth_crack_area' => floatval($othCrackArea),
                'concrete_cracking_area' => floatval($concreteCrackingArea),
                'concrete_structural_area' => floatval($concreteStructuralArea),
                'total_crack_area' => floatval($totalCrackArea),
                'crack_percentage' => floatval($crackPercentage),
                'crack_width' => floatval($crackWidth),
                'pothole_count' => intval($potholeCount),
                'normalized_potholes' => floatval($normalizedPotholes),
                'rutting_depth' => floatval($ruttingDepth),
            ],
            'calculations' => [
                'step1' => [
                    'value' => floatval($sdi1),
                    'explanation' => $sdi1_explanation,
                    'formula' => '% Retak = (Total Luas Retak / Luas Segmen) × 100'
                ],
                'step2' => [
                    'value' => floatval($sdi2),
                    'explanation' => $sdi2_explanation,
                    'formula' => 'SDI2 = SDI1 (jika < 3mm) atau SDI1 × 2 (jika > 3mm)'
                ],
                'step3' => [
                    'value' => floatval($sdi3),
                    'explanation' => $sdi3_explanation,
                    'addition' => floatval($sdi3_addition),
                    'formula' => 'SDI3 = SDI2 + (nilai sesuai jumlah lubang per 100m)'
                ],
                'step4' => [
                    'value' => floatval($sdi4),
                    'explanation' => $sdi4_explanation,
                    'addition' => floatval($sdi4_addition),
                    'formula' => 'SDI4 = SDI3 + (nilai sesuai kedalaman alur)'
                ]
            ],
            'final' => [
                'sdi_final' => floatval($sdi4),
                'category' => $category
            ]
        ];
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::queueImport(new RoadConditionImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data sedang diproses, silakan cek nanti!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        try {
            return Excel::download(new RoadConditionExport, 'kondisi_jalan_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function destroyAll()
    {
        RoadCondition::query()->delete();
        
        return redirect()->route('kondisi-jalan.index')
            ->with('success', 'Semua data kondisi jalan berhasil dihapus.');
    }
}