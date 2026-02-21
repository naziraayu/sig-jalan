<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\DynamicExport;
use App\Imports\DynamicImport;
use Illuminate\Support\Facades\DB;
use App\Exports\RoadConditionExport;
use App\Imports\RoadConditionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ImportExportController extends Controller
{
    private function getMenuConfig()
    {
        return [
            'provinsi' => [
                'model'        => \App\Models\Province::class,
                'label'        => 'Provinsi',
                'permission'   => 'provinsi',
                'export_class' => \App\Exports\ProvinceExport::class,
                'import_class' => \App\Imports\ProvinceImport::class,
            ],
            'balai' => [
                'model'        => \App\Models\Balai::class,
                'label'        => 'Balai',
                'permission'   => 'balai',
                'export_class' => \App\Exports\BalaiExport::class,
                'import_class' => \App\Imports\BalaiImport::class,
            ],
            'pulau' => [
                'model'        => \App\Models\Island::class,
                'label'        => 'Pulau',
                'permission'   => 'pulau',
                'export_class' => \App\Exports\IslandExport::class,
                'import_class' => \App\Imports\IslandImport::class,
            ],
            'kabupaten' => [
                'model'        => \App\Models\Kabupaten::class,
                'label'        => 'Kabupaten',
                'permission'   => 'kabupaten',
                'export_class' => \App\Exports\KabupatenExport::class,
                'import_class' => \App\Imports\KabupatenImport::class,
            ],
            'kecamatan' => [
                'model'        => \App\Models\Kecamatan::class,
                'label'        => 'Kecamatan',
                'permission'   => 'kecamatan',
                'export_class' => \App\Exports\KecamatanExport::class,
                'import_class' => \App\Imports\KecamatanImport::class,
            ],
            'ruas_jalan' => [
                'model'        => \App\Models\Link::class,
                'label'        => 'Ruas Jalan',
                'permission'   => 'ruas_jalan',
                'export_class' => \App\Exports\LinkExport::class,
                'import_class' => \App\Imports\LinkImport::class,
            ],
            'drp' => [
                'model'        => \App\Models\DRP::class,
                'label'        => 'DRP',
                'permission'   => 'drp',
                'export_class' => \App\Exports\DRPExport::class,
                'import_class' => \App\Imports\DRPImport::class,
            ],
            'ruas_jalan_kecamatan' => [
                'model'        => \App\Models\LinkKecamatan::class,
                'label'        => 'Ruas Jalan/Kecamatan',
                'permission'   => 'ruas_jalan_kecamatan',
                'export_class' => \App\Exports\LinkKecamatanExport::class,
                'import_class' => \App\Imports\LinkKecamatanImport::class,
            ],
            'inventarisasi_jalan' => [
                'model'        => \App\Models\RoadInventory::class,
                'label'        => 'Inventarisasi Jalan',
                'permission'   => 'inventarisasi_jalan',
                'export_class' => \App\Exports\RoadInventoryExport::class,
                'import_class' => \App\Imports\RoadInventoryImport::class,
            ],
            'kondisi_jalan' => [
                'model'            => \App\Models\RoadCondition::class,
                'label'            => 'Kondisi Jalan',
                'permission'       => 'kondisi_jalan',
                'use_custom_export' => true,
                'use_custom_import' => true,
            ],
            'koordinat_gps' => [
                'model'        => \App\Models\Alignment::class,
                'label'        => 'Import Koordinat GPS',
                'permission'   => 'koordinat_gps',
                'import_class' => \App\Imports\AlignmentImport::class,
                // koordinat_gps tidak perlu export_class khusus, pakai DynamicExport
            ],
        ];
    }

    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $menuConfig   = $this->getMenuConfig();
        $availableMenus = [];

        foreach ($menuConfig as $key => $config) {
            if ($user->hasPermission('detail', $config['permission'])) {
                $availableMenus[$key] = $config;
            }
        }

        // ✅ FIXED: Kirim data provinsi agar filter di view berfungsi
        $provinces = \App\Models\Province::select('province_code as code', 'province_name as name')
            ->orderBy('province_name')
            ->get();

        return view('admin.import_export.index', compact('availableMenus', 'provinces'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'menu_type'     => 'required|string',
            'year'          => 'nullable|integer',
            'province_code' => 'nullable|string',
        ]);

        $menuType   = $request->menu_type;
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config     = $menuConfig[$menuType];
        $modelClass = $config['model'];

        // Buat nama file deskriptif
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
            set_time_limit(600);
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 600);

            DB::connection()->disableQueryLog();

            Log::info("Starting export for {$menuType}", [
                'model'   => $modelClass,
                'user_id' => Auth::id(),
                'filters' => [
                    'year'          => $request->year,
                    'province_code' => $request->province_code,
                ],
            ]);

            // ✅ FIXED: Kondisi jalan pakai RoadConditionExport (custom)
            if (!empty($config['use_custom_export'])) {
                return Excel::download(
                    new RoadConditionExport($request->input('year'), $request->input('province_code')),
                    $fileName
                );
            }

            // ✅ FIXED: Pakai export_class spesifik jika tersedia
            if (!empty($config['export_class'])) {
                return Excel::download(new $config['export_class'](), $fileName);
            }

            // Fallback ke DynamicExport
            return Excel::download(new DynamicExport($modelClass), $fileName);

        } catch (\Throwable $e) {
            Log::error("Export failed for {$menuType}", [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'filters' => [
                    'year'          => $request->year ?? 'all',
                    'province_code' => $request->province_code ?? 'all',
                ],
            ]);

            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'menu_type' => 'required|string',
            'file'      => 'required|mimes:xlsx,xls|max:20480',
        ]);

        $menuType   = $request->menu_type;
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config     = $menuConfig[$menuType];
        $modelClass = $config['model'];

        try {
            set_time_limit(600);
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 600);

            DB::connection()->disableQueryLog();

            Log::info("Starting import for {$menuType}", [
                'file'    => $request->file('file')->getClientOriginalName(),
                'user_id' => Auth::id(),
            ]);

            // ✅ FIXED: Kondisi jalan pakai RoadConditionImport (custom)
            if (!empty($config['use_custom_import'])) {
                Excel::import(new RoadConditionImport(), $request->file('file'));
                return back()->with('success', 'Data ' . $config['label'] . ' berhasil diimport!');
            }

            // ✅ FIXED: Pakai import_class spesifik jika tersedia
            if (!empty($config['import_class'])) {
                Excel::import(new $config['import_class'](), $request->file('file'));
                return back()->with('success', 'Data ' . $config['label'] . ' berhasil diimport!');
            }

            // Fallback ke DynamicImport
            Excel::import(new DynamicImport($modelClass), $request->file('file'));
            return back()->with('success', 'Data ' . $config['label'] . ' berhasil diimport!');

        } catch (\Throwable $e) {
            Log::error("Import failed for {$menuType}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}