<?php

namespace App\Http\Controllers;

use App\Models\Drp;
use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Models\LinkKecamatan;
use App\Models\CodeLinkStatus;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\LinkKecamatanExport;
use App\Imports\LinkKecamatanImport;
use Maatwebsite\Excel\Facades\Excel;

class LinkKecamatanController extends Controller
{
    public function index()
    {
        // Get dropdown data
        $statusRuas = CodeLinkStatus::orderBy('order', 'asc')->get();
        $provinces = Province::all();
        $kabupatenList = Kabupaten::where('province_code', '35')->get(); // Default Jawa Timur
        $ruasList = Link::where('kabupaten_code', '09')->get(); // Default Jember
        
        return view('pengaturan_jaringan.ruas_jalan_kecamatan.index', compact('statusRuas', 'provinces', 'kabupatenList', 'ruasList'));
    }

    public function create()
    {
        $statusRuas = CodeLinkStatus::orderBy('order', 'asc')->get();
        $provinces = Province::all();
        $kabupatenList = collect();
        $ruasList = collect();
        $kecamatanList = collect();
        $drpList = collect();
        
        return view('pengaturan_jaringan.ruas_jalan_kecamatan.create', compact('statusRuas', 'provinces', 'kabupatenList', 'ruasList', 'kecamatanList', 'drpList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_no' => 'required',
            'drp_from' => 'required',
            'drp_to' => 'required',
            'kecamatan_code' => 'required',
        ]);

        LinkKecamatan::create($request->all());

        return redirect()->route('ruas-jalan-kecamatan.index')
                        ->with('success', 'Data ruas jalan kecamatan berhasil ditambahkan.');
    }

    public function show($link_no)
    {
        $linkKecamatan = LinkKecamatan::with(['province', 'kabupaten', 'linkNo', 'drpFrom', 'drpTo', 'kecamatan'])
                                    ->where('link_no', $link_no)
                                    ->firstOrFail();
        
        return view('pengaturan_jaringan.ruas_jalan_kecamatan.show', compact('linkKecamatan'));
    }

    public function edit(LinkKecamatan $linkKecamatan)
    {
        $statusRuas = CodeLinkStatus::orderBy('order', 'asc')->get();
        $provinces = Province::all();
        $kabupatenList = Kabupaten::where('province_code', $linkKecamatan->province_code)->get();
        $ruasList = Link::where('kabupaten_code', $linkKecamatan->kabupaten_code)->get();
        $kecamatanList = Kecamatan::where('kabupaten_code', $linkKecamatan->kabupaten_code)->get();
        $drpList = Drp::where('link_no', $linkKecamatan->link_no)->get();
        
        return view('pengaturan_jaringan.ruas_jalan_kecamatan.edit', compact('linkKecamatan', 'statusRuas', 'provinces', 'kabupatenList', 'ruasList', 'kecamatanList', 'drpList'));
    }

    public function update(Request $request, LinkKecamatan $linkKecamatan)
    {
        $request->validate([
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_no' => 'required',
            'drp_from' => 'required',
            'drp_to' => 'required',
            'kecamatan_code' => 'required',
        ]);

        $linkKecamatan->update($request->all());

        return redirect()->route('ruas-jalan-kecamatan.index')
                        ->with('success', 'Data ruas jalan kecamatan berhasil diperbarui.');
    }

        public function getDetail(Request $request)
    {
        $linkNo = $request->link_no;
        
        if (!$linkNo) {
            return response()->json(['success' => false, 'message' => 'Link No diperlukan']);
        }

        try {
            // Query dengan cara yang lebih eksplisit untuk memastikan relasi DRP benar
            $data = LinkKecamatan::with([
                'province',
                'kabupaten', 
                'linkNo',
                'kecamatan'
            ])
            ->where('link_no', $linkNo)
            ->get();

            Log::info('=== getDetail Debug ===');
            Log::info('Query Link No: ' . $linkNo);
            Log::info('Data count: ' . $data->count());

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan untuk link_no: ' . $linkNo
                ]);
            }

            // Format data dengan query manual untuk DRP
            $formattedData = $data->map(function($item) use ($linkNo) {
                Log::info('Processing item:', $item->toArray());
                
                // Query DRP From secara manual dengan kondisi link_no dan drp_num
                $drpFrom = null;
                if ($item->drp_from) {
                    $drpFromData = Drp::where('link_no', $linkNo)
                                    ->where('drp_num', $item->drp_from)
                                    ->first();
                    if ($drpFromData) {
                        $drpFrom = [
                            'drp_num' => $drpFromData->drp_num,
                            'drp_name' => $drpFromData->drp_desc ?? $drpFromData->drp_comment ?? 'DRP-' . $drpFromData->drp_num
                        ];
                    }
                }

                // Query DRP To secara manual dengan kondisi link_no dan drp_num  
                $drpTo = null;
                if ($item->drp_to) {
                    $drpToData = Drp::where('link_no', $linkNo)
                                ->where('drp_num', $item->drp_to)
                                ->first();
                    if ($drpToData) {
                        $drpTo = [
                            'drp_num' => $drpToData->drp_num,
                            'drp_name' => $drpToData->drp_desc ?? $drpToData->drp_comment ?? 'DRP-' . $drpToData->drp_num
                        ];
                    }
                }
                
                return [
                    'id' => $item->id ?? uniqid(),
                    'province_code' => $item->province_code,
                    'kabupaten_code' => $item->kabupaten_code,
                    'link_no' => $item->link_no,
                    'drp_from' => $item->drp_from,
                    'drp_to' => $item->drp_to,
                    'kecamatan_code' => $item->kecamatan_code,
                    'province' => $item->province ? [
                        'province_code' => $item->province->province_code,
                        'province_name' => $item->province->province_name
                    ] : null,
                    'kabupaten' => $item->kabupaten ? [
                        'kabupaten_code' => $item->kabupaten->kabupaten_code,
                        'kabupaten_name' => $item->kabupaten->kabupaten_name
                    ] : null,
                    'linkNo' => $item->linkNo ? [
                        'link_no' => $item->linkNo->link_no,
                        'link_name' => $item->linkNo->link_name,
                        'link_code' => $item->linkNo->link_code
                    ] : null,
                    'drpFrom' => $drpFrom,
                    'drpTo' => $drpTo,
                    'kecamatan' => $item->kecamatan ? [
                        'kecamatan_code' => $item->kecamatan->kecamatan_code,
                        'kecamatan_name' => $item->kecamatan->kecamatan_name
                    ] : null,
                ];
            });

            Log::info('Formatted data: ', $formattedData->toArray());

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'count' => $data->count()
            ]);

        } catch (\Exception $e) {
            Log::error('=== Error in getDetail ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function destroy(LinkKecamatan $linkKecamatan)
    {
        $linkKecamatan->delete();

        return redirect()->route('ruas-jalan-kecamatan.index')
                        ->with('success', 'Data ruas jalan kecamatan berhasil dihapus.');
    }

    public function destroyAll()
    {
        LinkKecamatan::query()->delete(); 
        return redirect()->route('ruas-jalan-kecamatan.index')
            ->with('success', 'Semua data kelas jalan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new LinkKecamatanImport, $request->file('file'));
            
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            return Excel::download(new LinkKecamatanExport, 'ruas-jalan-kecamatan_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}