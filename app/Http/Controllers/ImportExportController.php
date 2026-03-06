<?php
// FILE: app/Http/Controllers/ImportExportController.php (REPLACE)

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
use Illuminate\Support\Facades\Storage;

class ImportExportController extends Controller
{
    private function getMenuConfig(): array
    {
        return [
            'provinsi' => [
                'model'             => \App\Models\Province::class,
                'label'             => 'Provinsi',
                'permission'        => 'provinsi',
                'export_class'      => \App\Exports\ProvinceExport::class,
                'import_class'      => \App\Imports\ProvinceImport::class,
                'template_class'    => \App\Exports\Templates\ProvinceTemplate::class,
            ],
            'balai' => [
                'model'             => \App\Models\Balai::class,
                'label'             => 'Balai',
                'permission'        => 'balai',
                'export_class'      => \App\Exports\BalaiExport::class,
                'import_class'      => \App\Imports\BalaiImport::class,
                'template_class'    => \App\Exports\Templates\BalaiTemplate::class,
            ],
            'pulau' => [
                'model'             => \App\Models\Island::class,
                'label'             => 'Pulau',
                'permission'        => 'pulau',
                'export_class'      => \App\Exports\IslandExport::class,
                'import_class'      => \App\Imports\IslandImport::class,
                'template_class'    => \App\Exports\Templates\IslandTemplate::class,
            ],
            'kabupaten' => [
                'model'             => \App\Models\Kabupaten::class,
                'label'             => 'Kabupaten',
                'permission'        => 'kabupaten',
                'export_class'      => \App\Exports\KabupatenExport::class,
                'import_class'      => \App\Imports\KabupatenImport::class,
                'template_class'    => \App\Exports\Templates\KabupatenTemplate::class,
            ],
            'kecamatan' => [
                'model'             => \App\Models\Kecamatan::class,
                'label'             => 'Kecamatan',
                'permission'        => 'kecamatan',
                'export_class'      => \App\Exports\KecamatanExport::class,
                'import_class'      => \App\Imports\KecamatanImport::class,
                'template_class'    => \App\Exports\Templates\KecamatanTemplate::class,
            ],
            'ruas_jalan' => [
                'model'             => \App\Models\Link::class,
                'label'             => 'Ruas Jalan',
                'permission'        => 'ruas_jalan',
                'export_class'      => \App\Exports\LinkExport::class,
                'import_class'      => \App\Imports\LinkImport::class,
                'template_class'    => \App\Exports\Templates\LinkTemplate::class,
            ],
            'drp' => [
                'model'             => \App\Models\DRP::class,
                'label'             => 'DRP',
                'permission'        => 'drp',
                'export_class'      => \App\Exports\DRPExport::class,
                'import_class'      => \App\Imports\DRPImport::class,
                'template_class'    => \App\Exports\Templates\DRPTemplate::class,
            ],
            'link_kecamatan' => [
                'model'             => \App\Models\LinkKecamatan::class,
                'label'             => 'Link Kecamatan',
                'permission'        => 'link_kecamatan',
                'export_class'      => \App\Exports\LinkKecamatanExport::class,
                'import_class'      => \App\Imports\LinkKecamatanImport::class,
                'template_class'    => \App\Exports\Templates\LinkKecamatanTemplate::class,
            ],
            'inventarisasi_jalan' => [
                'model'             => \App\Models\RoadInventory::class,
                'label'             => 'Inventarisasi Jalan',
                'permission'        => 'inventarisasi_jalan',
                'export_class'      => \App\Exports\RoadInventoryExport::class,
                'import_class'      => \App\Imports\RoadInventoryImport::class,
                'template_class'    => \App\Exports\Templates\RoadInventoryTemplate::class,
            ],
            'kondisi_jalan' => [
                'model'             => \App\Models\RoadCondition::class,
                'label'             => 'Kondisi Jalan',
                'permission'        => 'kondisi_jalan',
                'use_custom_export' => true,
                'use_custom_import' => true,
                'template_class'    => \App\Exports\Templates\RoadConditionTemplate::class,
            ],
            'koordinat_gps' => [
                'model'             => \App\Models\Alignment::class,
                'label'             => 'Koordinat GPS',
                'permission'        => 'koordinat_gps',
                'import_class'      => \App\Imports\AlignmentImport::class,      // Excel
                'template_class'    => \App\Exports\Templates\AlignmentTemplate::class,
                // ✅ KML support
                'support_kml'       => true,
                'kml_export_class'  => \App\Exports\AlignmentKmlExport::class,
                'kml_import_class'  => \App\Imports\AlignmentKmlImport::class,
            ],
        ];
    }

    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $menuConfig     = $this->getMenuConfig();
        $availableMenus = [];

        foreach ($menuConfig as $key => $config) {
            if ($user->hasPermission('detail', $config['permission'])) {
                $availableMenus[$key] = $config;
            }
        }

        $provinces = \App\Models\Province::select('province_code as code', 'province_name as name')
            ->orderBy('province_name')
            ->get();

        return view('admin.import_export.index', compact('availableMenus', 'provinces'));
    }

    // ============================================================
    // Download Template
    // ============================================================
    public function downloadTemplate(Request $request)
    {
        $request->validate(['menu_type' => 'required|string']);

        $menuType   = $request->menu_type;
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config = $menuConfig[$menuType];

        if (empty($config['template_class'])) {
            return back()->with('error', 'Template untuk menu ini belum tersedia.');
        }

        $fileName = 'template_' . strtolower(str_replace(' ', '_', $config['label'])) . '.xlsx';

        try {
            Log::info("Download template for {$menuType}", ['user_id' => Auth::id()]);
            return Excel::download(new $config['template_class'](), $fileName);
        } catch (\Throwable $e) {
            Log::error("Template download failed for {$menuType}", ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }

    // ============================================================
    // Export
    // ============================================================
    public function export(Request $request)
    {
        $request->validate([
            'menu_type'     => 'required|string',
            'format'        => 'nullable|string|in:xlsx,kml',
            'year'          => 'nullable|integer',
            'province_code' => 'nullable|string',
            'kabupaten_code'=> 'nullable|string',
            'link_no'       => 'nullable|string',
        ]);

        $menuType   = $request->menu_type;
        $format     = $request->input('format', 'xlsx'); // default Excel
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config = $menuConfig[$menuType];

        // ── KML Export ───────────────────────────────────────────
        if ($format === 'kml') {
            if (empty($config['support_kml'])) {
                return back()->with('error', 'Menu ini tidak mendukung format KML.');
            }

            $filters = array_filter([
                'province_code'  => $request->province_code,
                'kabupaten_code' => $request->kabupaten_code,
                'link_no'        => $request->link_no,
                'year'           => $request->year,
            ]);

            $fileNameParts = [strtolower(str_replace(' ', '_', $config['label']))];
            if ($request->filled('link_no'))       $fileNameParts[] = $request->link_no;
            if ($request->filled('year'))          $fileNameParts[] = $request->year;
            if ($request->filled('province_code')) $fileNameParts[] = 'prov_' . $request->province_code;
            $fileNameParts[] = date('YmdHis');
            $fileName = implode('_', $fileNameParts) . '.kml';

            try {
                Log::info("Export KML {$menuType}", ['user_id' => Auth::id(), 'filters' => $filters]);
                $exportClass = $config['kml_export_class'];
                return (new $exportClass($filters))->download($fileName);
            } catch (\Throwable $e) {
                Log::error("KML Export failed {$menuType}", ['error' => $e->getMessage()]);
                return back()->with('error', 'Gagal export KML: ' . $e->getMessage());
            }
        }

        // ── Excel Export ─────────────────────────────────────────
        $fileNameParts = [strtolower(str_replace(' ', '_', $config['label']))];
        if ($request->filled('year'))          $fileNameParts[] = 'tahun_' . $request->year;
        if ($request->filled('province_code')) $fileNameParts[] = 'prov_' . $request->province_code;
        $fileNameParts[] = date('YmdHis');
        $fileName = implode('_', $fileNameParts) . '.xlsx';

        try {
            set_time_limit(600);
            ini_set('memory_limit', '1024M');
            DB::connection()->disableQueryLog();

            Log::info("Export Excel {$menuType}", ['user_id' => Auth::id(), 'year' => $request->year]);

            if (!empty($config['use_custom_export'])) {
                return Excel::download(
                    new RoadConditionExport($request->input('year'), $request->input('province_code')),
                    $fileName
                );
            }

            if (!empty($config['export_class'])) {
                return Excel::download(new $config['export_class'](), $fileName);
            }

            return Excel::download(new DynamicExport($config['model']), $fileName);

        } catch (\Throwable $e) {
            Log::error("Excel Export failed {$menuType}", ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    // ============================================================
    // Import
    // ============================================================
    public function import(Request $request)
    {
        $request->validate([
            'menu_type' => 'required|string',
            'format'    => 'nullable|string|in:xlsx,kml',
            'file'      => 'required|file|max:20480',
        ]);

        $menuType   = $request->menu_type;
        $format     = $request->input('format', 'xlsx');
        $menuConfig = $this->getMenuConfig();

        if (!isset($menuConfig[$menuType])) {
            return back()->with('error', 'Menu tidak valid!');
        }

        $config = $menuConfig[$menuType];

        // ── KML Import ───────────────────────────────────────────
        if ($format === 'kml') {
            if (empty($config['support_kml'])) {
                return back()->with('error', 'Menu ini tidak mendukung format KML.');
            }

            // Validasi ekstensi KML
            $ext = strtolower($request->file('file')->getClientOriginalExtension());
            if (!in_array($ext, ['kml', 'xml'])) {
                return back()->with('error', 'File KML harus berekstensi .kml atau .xml');
            }

            // Simpan sementara
            $tmpPath  = $request->file('file')->store('tmp/kml', 'local');
            $fullPath = storage_path('app/' . $tmpPath);

            try {
                Log::info("Import KML {$menuType}", [
                    'file'    => $request->file('file')->getClientOriginalName(),
                    'user_id' => Auth::id(),
                ]);

                $importClass = $config['kml_import_class'];
                $importer    = new $importClass();
                $importer->import($fullPath);

                Storage::disk('local')->delete($tmpPath);

                $summary = $importer->getSummary();

                $msg = "Import KML {$config['label']} berhasil! {$summary['imported']} titik diimport.";
                if ($summary['skipped'] > 0) {
                    $msg .= " {$summary['skipped']} placemark dilewati.";
                }

                if (!empty($summary['errors'])) {
                    $errorSample = implode('<br>', array_slice($summary['errors'], 0, 5));
                    return back()->with('success', $msg)->with('import_warnings', $errorSample);
                }

                return back()->with('success', $msg);

            } catch (\Throwable $e) {
                Storage::disk('local')->delete($tmpPath);
                Log::error("KML Import failed {$menuType}", ['error' => $e->getMessage()]);
                return back()->with('error', 'Gagal import KML: ' . $e->getMessage());
            }
        }

        // ── Excel Import ─────────────────────────────────────────
        $request->validate(['file' => 'mimes:xlsx,xls']);

        try {
            set_time_limit(600);
            ini_set('memory_limit', '1024M');
            DB::connection()->disableQueryLog();

            Log::info("Import Excel {$menuType}", [
                'file'    => $request->file('file')->getClientOriginalName(),
                'user_id' => Auth::id(),
            ]);

            if (!empty($config['use_custom_import'])) {
                $importer = new RoadConditionImport();
                Excel::import($importer, $request->file('file'));

                $msg = "Data {$config['label']} berhasil diimport! ";
                $msg .= "({$importer->getImportedCount()} berhasil, {$importer->getSkippedCount()} dilewati)";

                if (count($importer->getErrors()) > 0) {
                    $errorSample = implode('<br>', array_slice($importer->getErrors(), 0, 5));
                    return back()->with('success', $msg)->with('import_warnings', $errorSample);
                }

                return back()->with('success', $msg);
            }

            if (!empty($config['import_class'])) {
                $importer = new $config['import_class']();
                Excel::import($importer, $request->file('file'));

                $msg = "Data {$config['label']} berhasil diimport!";
                if (method_exists($importer, 'getSkippedCount') && $importer->getSkippedCount() > 0) {
                    $msg .= " ({$importer->getSkippedCount()} baris dilewati)";

                    if (method_exists($importer, 'getErrors') && count($importer->getErrors()) > 0) {
                        $errorSample = implode('<br>', array_slice($importer->getErrors(), 0, 5));
                        return back()->with('success', $msg)->with('import_warnings', $errorSample);
                    }
                }

                return back()->with('success', $msg);
            }

            Excel::import(new DynamicImport($config['model']), $request->file('file'));
            return back()->with('success', "Data {$config['label']} berhasil diimport!");

        } catch (\Throwable $e) {
            Log::error("Excel Import failed {$menuType}", ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}