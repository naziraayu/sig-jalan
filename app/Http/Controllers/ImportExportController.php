<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\DynamicExport;
use App\Imports\DynamicImport;
use Illuminate\Support\Facades\DB;
use App\Exports\RoadConditionExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ImportExportController extends Controller
{
    private function getMenuConfig()
    {
        return [
            'provinsi' => [
                'model' => \App\Models\Province::class,
                'label' => 'Provinsi',
                'permission' => 'provinsi'
            ],
            'balai' => [
                'model' => \App\Models\Balai::class,
                'label' => 'Balai',
                'permission' => 'balai'
            ],
            'pulau' => [
                'model' => \App\Models\Island::class,
                'label' => 'Pulau',
                'permission' => 'pulau'
            ],
            'kabupaten' => [
                'model' => \App\Models\Kabupaten::class,
                'label' => 'Kabupaten',
                'permission' => 'kabupaten'
            ],
            'kecamatan' => [
                'model' => \App\Models\Kecamatan::class,
                'label' => 'Kecamatan',
                'permission' => 'kecamatan'
            ],
            'ruas_jalan' => [
                'model' => \App\Models\Link::class,
                'label' => 'Ruas Jalan',
                'permission' => 'ruas_jalan'
            ],
            'drp' => [
                'model' => \App\Models\Drp::class,
                'label' => 'DRP',
                'permission' => 'drp'
            ],
            'ruas_jalan_kecamatan' => [
                'model' => \App\Models\LinkKecamatan::class,
                'label' => 'Ruas Jalan/Kecamatan',
                'permission' => 'ruas_jalan_kecamatan'
            ],
            'inventarisasi_jalan' => [
                'model' => \App\Models\RoadInventory::class,
                'label' => 'Inventarisasi Jalan',
                'permission' => 'inventarisasi_jalan'
            ],
            'kondisi_jalan' => [
                'model' => \App\Models\RoadCondition::class,
                'label' => 'Kondisi Jalan',
                'permission' => 'kondisi_jalan',
                'use_custom_export' => true
            ],
            'koordinat_gps' => [
                'model' => \App\Models\Alignment::class,
                'label' => 'Import Koordinat GPS',
                'permission' => 'koordinat_gps'
            ],
        ];
    }

    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $menuConfig = $this->getMenuConfig();
        
        $availableMenus = [];
        foreach ($menuConfig as $key => $config) {
            if ($user->hasPermission('detail', $config['permission'])) {
                $availableMenus[$key] = $config;
            }
        }

        return view('admin.import_export.index', compact('availableMenus'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'menu_type' => 'required|string',
            'year' => 'nullable|integer',
            'province_code' => 'nullable|string' // ✅ Tambahan validasi
        ]);

        $menuType = $request->menu_type;
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config = $menuConfig[$menuType];
        $modelClass = $config['model'];
        
        // ✅ IMPROVED: Nama file lebih deskriptif dengan filter
        $fileNameParts = [strtolower(str_replace(' ', '_', $config['label']))];
        if ($request->filled('year')) {
            $fileNameParts[] = 'tahun_' . $request->year;
        }
        if ($request->filled('province_code')) {
            $fileNameParts[] = 'prov_' . $request->province_code;
        }
        $fileNameParts[] = date('YmdHis');
        $fileName = implode('_', $fileNameParts) . '.xlsx';

        try {
            // ✅ Set konfigurasi PHP untuk handle data besar
            set_time_limit(600); // 10 menit
            ini_set('memory_limit', '1024M'); // 1GB
            ini_set('max_execution_time', 600);
            
            // ✅ Disable query log untuk hemat memory
            DB::connection()->disableQueryLog();

            // ✅ Log untuk tracking dengan info filter
            Log::info("Starting export for {$menuType}", [
                'model' => $modelClass,
                'user_id' => Auth::id(),
                'filters' => [
                    'year' => $request->year,
                    'province_code' => $request->province_code
                ]
            ]);

            if (isset($config['use_custom_export']) && $config['use_custom_export']) {
                // ✅ FIXED: Kirim parameter filter ke RoadConditionExport
                $year = $request->input('year');
                $provinceCode = $request->input('province_code');
                
                return Excel::download(
                    new RoadConditionExport($year, $provinceCode), 
                    $fileName
                );
            } else {
                return Excel::download(new DynamicExport($modelClass), $fileName);
            }
            
        } catch (\Throwable $e) {
            Log::error("Export failed for {$menuType}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => [
                    'year' => $request->year ?? 'all',
                    'province_code' => $request->province_code ?? 'all'
                ]
            ]);
            
            return back()->with('error', 'Gagal export data: ' . $e->getMessage() . 
                ' (Jika data terlalu besar, coba filter berdasarkan tahun atau provinsi terlebih dahulu)');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'menu_type' => 'required|string',
            'file' => 'required|mimes:xlsx,xls|max:20480'
        ]);

        $menuType = $request->menu_type;
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config = $menuConfig[$menuType];
        $modelClass = $config['model'];

        try {
            set_time_limit(600);
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 600);
            
            DB::connection()->disableQueryLog();
            
            Log::info("Starting import for {$menuType}", [
                'file' => $request->file('file')->getClientOriginalName(),
                'user_id' => Auth::id()
            ]);
            
            Excel::import(new DynamicImport($modelClass), $request->file('file'));
            
            return back()->with('success', 'Data ' . $config['label'] . ' berhasil diimport!');
            
        } catch (\Throwable $e) {
            Log::error("Import failed for {$menuType}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}