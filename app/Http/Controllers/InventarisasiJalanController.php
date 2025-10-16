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
    public function index()
    {
        $selectedYear = session('selected_year'); // Ambil tahun dari session

        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi   = Province::orderBy('province_name')->get();
        $kabupaten  = Kabupaten::orderBy('kabupaten_name')->get();

        // Ambil hanya 1 data per link_no
        $ruasjalan = RoadInventory::with('linkNo')
            ->select('link_no')
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->where('year', $selectedYear);
            })
            ->groupBy('link_no')
            ->orderBy('link_no')
            ->get();

        return view('jalan.inventarisasi-jalan.index', compact(
            'statusRuas', 'provinsi', 'kabupaten', 'ruasjalan'
        ));
    }

    public function getDetail(Request $request)
    {
        $linkNo = $request->get('link_no');
        $selectedYear = session('selected_year'); // Ambil tahun dari session

        $ruas = RoadInventory::with([
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
        ->where('link_no', $linkNo)
        ->when($selectedYear, function($query) use ($selectedYear) {
            return $query->where('year', $selectedYear);
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
        $selectedYear = session('selected_year'); // Ambil tahun dari session
        // Ambil data ruas berdasarkan link_no
        $ruas = Link::with(['province', 'kabupaten'])
            ->where('link_no', $link_no)
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->where('year', $selectedYear);
            })
            ->firstOrFail();

        // Ambil semua road inventory yang terkait dengan semua relasi
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
        ->where('link_no', $link_no)
        ->when($selectedYear, function($query) use ($selectedYear) {
            return $query->where('year', $selectedYear);
        })
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new RoadInventoryImport, $request->file('file'));
            
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            return Excel::download(new RoadInventoryExport, 'inventarisasi_jalan_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    // public function export()
    // {
    //     try {
    //         $selectedYear = session('selected_year');
            
    //         // Export dengan filter year jika ada
    //         return Excel::download(
    //             new RoadInventoryExport($selectedYear), 
    //             'inventarisasi_jalan_' . ($selectedYear ?? 'all') . '_' . date('Y-m-d_H-i-s') . '.xlsx'
    //         );
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
    //     }
    // }

    public function destroyAll()
    {
        RoadInventory::query()->delete(); 
        return redirect()->route('inventarisasi-jalan.index')
            ->with('success', 'Semua data inventarisasi jalan berhasil dihapus.');
    }
}
