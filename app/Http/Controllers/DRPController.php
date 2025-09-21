<?php

namespace App\Http\Controllers;

use App\Models\DRP;
use App\Models\CodeLinkStatus;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\Link;
use App\Models\CodeDrpType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DRPExport;
use App\Imports\DRPImport;

class DRPController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Ambil data untuk dropdown
            $statusRuas = CodeLinkStatus::orderBy('order')->get();
            $provinsi = Province::orderBy('province_name')->get();
            
            // Filter kabupaten untuk Jawa Timur saja (sesuaikan dengan kode provinsi Jawa Timur)
            $kabupaten = Kabupaten::where('province_code', '35') // 35 adalah kode provinsi Jawa Timur
                        ->orderBy('kabupaten_name')
                        ->get();
            
            // Ambil ruas jalan untuk kabupaten yang dipilih
            $ruasjalan = Link::whereHas('kabupaten', function($query) {
                            $query->where('province_code', '35'); // Jawa Timur
                        })
                        ->orderBy('link_code')
                        ->get();

            return view('pengaturan_jaringan.drp.index', compact('statusRuas', 'provinsi', 'kabupaten', 'ruasjalan'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman: ' . $e->getMessage());
        }
    }

    /**
     * Get detail DRP data by link_no via AJAX
     */
    public function getDetail(Request $request)
    {
        try {
            $linkNo = $request->get('link_no');
            
            if (!$linkNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link number tidak ditemukan'
                ]);
            }

            // Ambil data DRP berdasarkan link_no dengan relasi
            $drpData = DRP::with(['type', 'province', 'kabupaten', 'link'])
                          ->where('link_no', $linkNo)
                          ->orderBy('drp_order')
                          ->get();

            if ($drpData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data DRP tidak ditemukan untuk ruas ini'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $drpData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $provinsi = Province::orderBy('province_name')->get();
            $kabupaten = Kabupaten::orderBy('kabupaten_name')->get();
            $links = Link::orderBy('link_code')->get();
            $drpTypes = CodeDrpType::orderBy('order')->get();

            return view('drp.create', compact('provinsi', 'kabupaten', 'links', 'drpTypes'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'province_code' => 'required|string|exists:province,province_code',
                'kabupaten_code' => 'required|string|exists:kabupaten,kabupaten_code',
                'link_no' => 'required|string|exists:link,link_no',
                'drp_num' => 'required|string|unique:drp,drp_num',
                'chainage' => 'nullable|numeric',
                'drp_order' => 'nullable|integer',
                'drp_length' => 'nullable|numeric',
                'dpr_north_deg' => 'nullable|integer|min:0|max:90',
                'dpr_north_min' => 'nullable|integer|min:0|max:59',
                'dpr_north_sec' => 'nullable|numeric|min:0|max:59.99',
                'dpr_east_deg' => 'nullable|integer|min:0|max:180',
                'dpr_east_min' => 'nullable|integer|min:0|max:59',
                'dpr_east_sec' => 'nullable|numeric|min:0|max:59.99',
                'drp_type' => 'nullable|string|exists:code_drp_type,code',
                'drp_desc' => 'nullable|string|max:500',
                'drp_comment' => 'nullable|string|max:1000',
            ]);

            DRP::create($validated);

            return redirect()->route('drp.index')
                           ->with('success', 'Data DRP berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($drpNum)
    {
        try {
            $drp = DRP::where('drp_num', $drpNum)->firstOrFail();
            $provinsi = Province::orderBy('province_name')->get();
            $kabupaten = Kabupaten::orderBy('kabupaten_name')->get();
            $links = Link::orderBy('link_code')->get();
            $drpTypes = CodeDrpType::orderBy('order')->get();

            return view('drp.edit', compact('drp', 'provinsi', 'kabupaten', 'links', 'drpTypes'));

        } catch (\Exception $e) {
            return back()->with('error', 'Data DRP tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $drpNum)
    {
        try {
            $drp = DRP::where('drp_num', $drpNum)->firstOrFail();

            $validated = $request->validate([
                'province_code' => 'required|string|exists:province,province_code',
                'kabupaten_code' => 'required|string|exists:kabupaten,kabupaten_code',
                'link_no' => 'required|string|exists:link,link_no',
                'drp_num' => 'required|string|unique:drp,drp_num,' . $drpNum . ',drp_num',
                'chainage' => 'nullable|numeric',
                'drp_order' => 'nullable|integer',
                'drp_length' => 'nullable|numeric',
                'dpr_north_deg' => 'nullable|integer|min:0|max:90',
                'dpr_north_min' => 'nullable|integer|min:0|max:59',
                'dpr_north_sec' => 'nullable|numeric|min:0|max:59.99',
                'dpr_east_deg' => 'nullable|integer|min:0|max:180',
                'dpr_east_min' => 'nullable|integer|min:0|max:59',
                'dpr_east_sec' => 'nullable|numeric|min:0|max:59.99',
                'drp_type' => 'nullable|string|exists:code_drp_type,code',
                'drp_desc' => 'nullable|string|max:500',
                'drp_comment' => 'nullable|string|max:1000',
            ]);

            $drp->update($validated);

            return redirect()->route('drp.index')
                           ->with('success', 'Data DRP berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($drpNum)
    {
        try {
            $drp = DRP::where('drp_num', $drpNum)->firstOrFail();
            $drp->delete();

            return redirect()->route('drp.index')
                           ->with('success', 'Data DRP berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Remove all resources from storage.
     */
    public function destroyAll()
    {
        DRP::query()->delete(); 
        return redirect()->route('drp.index')
            ->with('success', 'Semua data inventarisasi jalan berhasil dihapus.');
    }

    /**
     * Import data from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new DRPImport, $request->file('file'));
            
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Export data to Excel file.
     */
    public function export()
    {
        try {
            $fileName = 'drp_data_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new DRPExport(), $fileName);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Get kabupaten by province code via AJAX
     */
    public function getKabupaten(Request $request)
    {
        try {
            $provinceCode = $request->get('province_code');
            
            $kabupaten = Kabupaten::where('province_code', $provinceCode)
                                 ->orderBy('kabupaten_name')
                                 ->get();

            return response()->json([
                'success' => true,
                'data' => $kabupaten
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get links by kabupaten code via AJAX
     */
    public function getLinks(Request $request)
    {
        try {
            $kabupatenCode = $request->get('kabupaten_code');
            
            $links = Link::whereHas('kabupaten', function($query) use ($kabupatenCode) {
                        $query->where('kabupaten_code', $kabupatenCode);
                    })
                    ->orderBy('link_code')
                    ->get();

            return response()->json([
                'success' => true,
                'data' => $links
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}