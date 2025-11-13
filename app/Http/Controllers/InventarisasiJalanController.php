<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use App\Models\RoadInventory;
use App\Models\CodeLinkStatus;
use App\Exports\RoadInventoryExport;
use App\Imports\RoadInventoryImport;
use Maatwebsite\Excel\Facades\Excel;

class InventarisasiJalanController extends Controller
{
    /**
     * Display listing of road segments
     */
    public function index()
    {
        $selectedYear = session('selected_year');

        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi   = Province::orderBy('province_name')->get();
        $kabupaten  = Kabupaten::orderBy('kabupaten_name')->get();

        // ✅ Hanya ambil ruas yang PUNYA data di road_inventory
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
            ->get()
            ->unique('link_no');

        return view('jalan.inventarisasi-jalan.index', compact(
            'statusRuas', 'provinsi', 'kabupaten', 'ruasjalan', 'selectedYear'
        ));
    }

    /**
     * Get detail inventories for selected link_no
     */
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

    /**
     * Show detail page for specific link
     */
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

    /**
     * Import data from Excel
     */
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls,csv|max:10240',
    //         'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
    //     ]);

    //     try {
    //         // ✅ Ambil year dari input atau session
    //         $year = $request->input('year') ?? session('selected_year') ?? date('Y');
            
    //         Excel::import(new RoadInventoryImport($year), $request->file('file'));
            
    //         return redirect()->back()->with('success', 'Data berhasil diimport untuk tahun ' . $year);
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
    //     }
    // }

    /**
     * Export data to Excel (commented - uncomment when ready)
     */
    // public function export()
    // {
    //     try {
    //         $selectedYear = session('selected_year');
            
    //         if (!$selectedYear) {
    //             return redirect()->back()->with('error', 'Silakan pilih tahun terlebih dahulu');
    //         }
            
    //         return Excel::download(
    //             new RoadInventoryExport($selectedYear), 
    //             'inventarisasi_jalan_' . $selectedYear . '_' . date('Y-m-d_H-i-s') . '.xlsx'
    //         );
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
    //     }
    // }

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