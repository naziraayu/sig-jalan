<?php

namespace App\Http\Controllers;

use App\Exports\RoadInventoryExport;
use App\Http\Controllers\Controller;
use App\Imports\RoadInventoryImport;
use App\Models\CodeImpassable;
use App\Models\CodeLinkStatus;
use App\Models\CodePavementType;
use App\Models\CodeTerrain;
use App\Models\Kabupaten;
use App\Models\Link;
use App\Models\Province;
use App\Models\RoadInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InventarisasiJalanController extends Controller
{
    public function index()
    {
        $selectedYear = session('selected_year');

        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi   = Province::orderBy('province_name')->get();
        $kabupaten  = Kabupaten::orderBy('kabupaten_name')->get();

        // ✅ Ambil semua ruas yang PUNYA data di road_inventory untuk tahun terpilih
        $ruasjalan = Link::with('linkMaster')
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->where('year', $selectedYear);
            })
            // ✅ Filter: Hanya yang ada di road_inventory
            ->whereHas('roadInventories', function($query) use ($selectedYear) {
                if ($selectedYear) {
                    $query->whereHas('link', function($q) use ($selectedYear) {
                        $q->where('year', $selectedYear);
                    });
                }
            })
            ->orderBy('link_no')
            ->get()
            ->unique('link_no');

        return view('jalan.inventarisasi-jalan.index', compact(
            'statusRuas', 'provinsi', 'kabupaten', 'ruasjalan', 'selectedYear'
        ));
    }

    public function create()
    {
        $selectedYear = session('selected_year');
        
        // ✅ Validasi: Harus pilih tahun dulu
        if (!$selectedYear) {
            return redirect()->route('inventarisasi-jalan.index')
                ->with('error', 'Silakan pilih tahun terlebih dahulu!');
        }
        
        // ✅ Ambil ruas jalan yang tersedia untuk tahun yang dipilih
        $ruasJalan = Link::with('linkMaster')
            ->where('year', $selectedYear)
            ->orderBy('link_no')
            ->get()
            ->unique('link_no');
        
        // ✅ Ambil data dropdown untuk step 2
        $pavementTypes = CodePavementType::orderBy('order')->get();
        $terrainTypes = CodeTerrain::orderBy('order')->get();
        $impassableReasons = CodeImpassable::orderBy('order')->get();
        
        return view('jalan.inventarisasi-jalan.create', compact(
            'ruasJalan', 
            'selectedYear',
            'pavementTypes',
            'terrainTypes',
            'impassableReasons'
        ));
    }

    public function getRuasDetail($linkNo)
    {
        $selectedYear = session('selected_year');
        
        $link = Link::with('linkMaster')
            ->where('link_no', $linkNo)
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->where('year', $selectedYear);
            })
            ->first();
        
        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Ruas tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'link_name' => $link->linkMaster->link_name ?? 'Tidak ada nama',
            'link_length_official' => $link->linkMaster->link_length_official ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        try {
            // ✅ Ambil data dari request JSON
            $surveySetup = $request->input('survey_setup');
            $inventoryData = $request->input('inventory_data');
            
            // ✅ Validasi basic
            if (empty($inventoryData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data inventarisasi tidak boleh kosong'
                ], 422);
            }
            
            // ✅ Validasi link_id harus ada
            if (empty($surveySetup['link_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link ID tidak ditemukan'
                ], 422);
            }
            
            // ✅ Prepare data untuk batch insert
            $dataToInsert = [];
            foreach ($inventoryData as $item) {
                $dataToInsert[] = [
                    'province_code' => $surveySetup['province_code'] ?? null,
                    'kabupaten_code' => $surveySetup['kabupaten_code'] ?? null,
                    'link_id' => $surveySetup['link_id'],
                    'link_no' => $surveySetup['link_no'],
                    'year' => $surveySetup['year'],
                    'chainage_from' => $item['chainage_from'],
                    'chainage_to' => $item['chainage_to'],
                    'pave_type' => $item['pave_type'] ?? null,
                    'pave_width' => $item['pave_width'] ?? null,
                    'row' => $item['row'] ?? null,
                    'terrain' => $item['terrain'] ?? null,
                    'impassable' => $item['impassable'] ?? 0,
                    'impassable_reason' => $item['impassable_reason'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // ✅ Batch insert ke database
            RoadInventory::insert($dataToInsert);
            
            return response()->json([
                'success' => true,
                'message' => count($dataToInsert) . ' segmen inventarisasi berhasil disimpan!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving road inventory: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetail(Request $request)
    {
        $linkNo = $request->get('link_no');
        $selectedYear = session('selected_year');

        // ✅ PERBAIKAN: Query dengan whereHas yang benar
        $ruas = RoadInventory::with([
            'link.linkMaster',
            'province',
            'kabupaten',
            'pavementType',
            'shoulderTypeL',
            'shoulderTypeR',
            'drainTypeL',
            'drainTypeR',
            'terrainType',
            'landUseL',
            'landUseR',
            'impassableReason',
        ])
        ->whereHas('link', function($query) use ($linkNo, $selectedYear) {
            $query->where('link_no', $linkNo);
            
            if ($selectedYear) {
                $query->where('year', $selectedYear);
            }
        })
        ->orderBy('chainage_from')
        ->get();

        if ($ruas->count()) {
            return response()->json([
                'success' => true,
                'data'    => $ruas,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan',
        ]);
    }

    public function show($link_no)
    {
        $selectedYear = session('selected_year');
        
        // ✅ PERBAIKAN: Ambil link berdasarkan link_no dan year dengan fallback
        $ruas = Link::with(['linkMaster', 'province', 'kabupaten'])
            ->where('link_no', $link_no)
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->where('year', $selectedYear);
            }, function($query) {
                // ✅ Fallback: Ambil tahun terbaru kalau tidak ada filter
                return $query->orderBy('year', 'desc');
            })
            ->firstOrFail();

        // ✅ PERBAIKAN: Ambil inventories berdasarkan link_id (bukan link_no)
        $inventories = RoadInventory::with([
            'province',
            'kabupaten',
            'pavementType',
            'shoulderTypeL',
            'shoulderTypeR',
            'drainTypeL',
            'drainTypeR',
            'terrainType',
            'landUseL',
            'landUseR',
            'impassableReason'
        ])
        ->where('link_id', $ruas->id)
        ->orderBy('chainage_from')
        ->get();

        // Hitung statistik untuk summary cards
        $statistics = [
            'total_length' => $inventories->sum(function($item) {
                return $item->chainage_to - $item->chainage_from;
            }),
            'average_width' => $inventories->where('pave_width', '>', 0)->avg('pave_width'),
            'passable_count' => $inventories->where('impassable', 0)->count(),
            'impassable_count' => $inventories->where('impassable', 1)->count(),
            'total_segments' => $inventories->count(),
            'average_row' => $inventories->where('row', '>', 0)->avg('row'),
            'pavement_types' => $inventories->groupBy('pave_type')->map->count(),
            'terrain_types' => $inventories->groupBy('terrain')->map->count(),
        ];

        // Analisis kondisi bahu jalan
        $shoulder_analysis = [
            'left_shoulder_exists' => $inventories->where('should_with_L', '>', 0)->count(),
            'right_shoulder_exists' => $inventories->where('should_with_R', '>', 0)->count(),
            'avg_left_shoulder' => $inventories->where('should_with_L', '>', 0)->avg('should_with_L'),
            'avg_right_shoulder' => $inventories->where('should_with_R', '>', 0)->avg('should_with_R'),
        ];

        // Analisis sistem drainase
        $drainage_analysis = [
            'left_drainage_types' => $inventories->whereNotNull('drain_type_L')
                ->groupBy('drain_type_L')->map->count(),
            'right_drainage_types' => $inventories->whereNotNull('drain_type_R')
                ->groupBy('drain_type_R')->map->count(),
        ];

        // Analisis penggunaan lahan
        $landuse_analysis = [
            'left_landuse_types' => $inventories->whereNotNull('land_use_L')
                ->groupBy('land_use_L')->map->count(),
            'right_landuse_types' => $inventories->whereNotNull('land_use_R')
                ->groupBy('land_use_R')->map->count(),
        ];

        // Segmen bermasalah (impassable)
        $problematic_segments = $inventories->where('impassable', 1);
        $impassable_reasons = $problematic_segments->groupBy('impassable_reason')->map->count();

        return view('jalan.inventarisasi-jalan.show', compact(
            'ruas', 
            'inventories', 
            'statistics',
            'shoulder_analysis',
            'drainage_analysis',
            'landuse_analysis',
            'problematic_segments',
            'impassable_reasons'
        ));
    }

    public function edit($link_no)
    {
        $selectedYear = session('selected_year');
        
        if (!$selectedYear) {
            return redirect()->route('inventarisasi-jalan.index')
                ->with('error', 'Silakan pilih tahun terlebih dahulu!');
        }
        
        // Ambil data link berdasarkan link_no dan year
        $link = Link::with('linkMaster')
            ->where('link_no', $link_no)
            ->where('year', $selectedYear)
            ->firstOrFail();
        
        // Ambil data inventories yang sudah ada
        $existingData = RoadInventory::with([
            'pavementType',
            'terrainType',
            'impassableReason'
        ])
        ->where('link_id', $link->id)
        ->orderBy('chainage_from')
        ->get();
        
        if ($existingData->isEmpty()) {
            return redirect()->route('inventarisasi-jalan.index')
                ->with('error', 'Data inventarisasi tidak ditemukan untuk ruas ini!');
        }
        
        // Ambil data dropdown untuk form
        $pavementTypes = CodePavementType::orderBy('order')->get();
        $terrainTypes = CodeTerrain::orderBy('order')->get();
        $impassableReasons = CodeImpassable::orderBy('order')->get();
        
        // Ambil semua ruas jalan untuk dropdown (meskipun disabled, tetap perlu untuk tampilan)
        $ruasJalan = Link::with('linkMaster')
            ->where('year', $selectedYear)
            ->orderBy('link_no')
            ->get()
            ->unique('link_no');
        
        return view('jalan.inventarisasi-jalan.edit', compact(
            'link',
            'existingData',
            'ruasJalan',
            'selectedYear',
            'pavementTypes',
            'terrainTypes',
            'impassableReasons'
        ));
    }

    public function update(Request $request, $link_no)
    {
        try {
            $surveySetup = $request->input('survey_setup');
            $inventoryData = $request->input('inventory_data');
            
            if (empty($inventoryData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data inventarisasi tidak boleh kosong'
                ], 422);
            }
            
            if (empty($surveySetup['link_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link ID tidak ditemukan'
                ], 422);
            }
            
            // Hapus data lama untuk link_id ini
            RoadInventory::where('link_id', $surveySetup['link_id'])->delete();
            
            // Prepare data untuk batch insert
            $dataToInsert = [];
            foreach ($inventoryData as $item) {
                $dataToInsert[] = [
                    'province_code' => $surveySetup['province_code'] ?? null,
                    'kabupaten_code' => $surveySetup['kabupaten_code'] ?? null,
                    'link_id' => $surveySetup['link_id'],
                    'link_no' => $surveySetup['link_no'],
                    'year' => $surveySetup['year'],
                    'chainage_from' => $item['chainage_from'],
                    'chainage_to' => $item['chainage_to'],
                    'pave_type' => $item['pave_type'] ?? null,
                    'pave_width' => $item['pave_width'] ?? null,
                    'row' => $item['row'] ?? null,
                    'terrain' => $item['terrain'] ?? null,
                    'impassable' => $item['impassable'] ?? 0,
                    'impassable_reason' => $item['impassable_reason'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Batch insert data baru
            RoadInventory::insert($dataToInsert);
            
            return response()->json([
                'success' => true,
                'message' => count($dataToInsert) . ' segmen inventarisasi berhasil diperbarui!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating road inventory: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete all inventories for selected year
     */
    public function destroyAll()
    {
        $selectedYear = session('selected_year');
        
        // ✅ VALIDASI: Pastikan year sudah dipilih
        if (!$selectedYear) {
            return redirect()->back()->with('error', 'Pilih tahun terlebih dahulu!');
        }
        
        // ✅ PERBAIKAN: Hapus hanya data pada tahun yang dipilih
        $deleted = RoadInventory::whereHas('link', function($query) use ($selectedYear) {
            $query->where('year', $selectedYear);
        })->delete();
        
        return redirect()->route('inventarisasi-jalan.index')
            ->with('success', "Berhasil menghapus {$deleted} data inventarisasi tahun {$selectedYear}.");
    }
}