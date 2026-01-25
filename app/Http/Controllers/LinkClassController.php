<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\LinkClass;
use Illuminate\Http\Request;
use App\Models\CodeLinkStatus;
use App\Exports\LinkClassExport;
use App\Imports\LinkClassImport;
use Maatwebsite\Excel\Facades\Excel;

class LinkClassController extends Controller
{
    public function index()
    {
        // Ambil data untuk dropdown filter
        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi = Province::orderBy('province_name')->get();
        
        // Ambil kabupaten berdasarkan provinsi Jawa Timur
        $kabupaten = Kabupaten::where('province_code', 'LIKE', '%35%') // Asumsi kode provinsi Jawa Timur mengandung 35
                              ->orderBy('kabupaten_name')
                              ->get();
        
        // Ambil ruas jalan berdasarkan kabupaten Jember
        $ruasjalan = LinkClass::with(['linkNo'])
                             ->whereHas('kabupaten', function($query) {
                                 $query->where('kabupaten_name', 'Jember');
                             })
                             ->get()
                             ->unique('link_no'); // Menghindari duplikat link_no

        return view('pengaturan_jaringan.kelas_jalan.index', compact(
            'statusRuas', 
            'provinsi', 
            'kabupaten', 
            'ruasjalan'
        ));
    }

    public function getDetail(Request $request)
    {
        try {
            $linkNo = $request->get('link_no');
            
            if (!$linkNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link No tidak ditemukan'
                ]);
            }

            // Ambil data Link Class berdasarkan link_no
            $data = LinkClass::with(['province', 'kabupaten', 'linkNo', 'classRelation'])
                            ->where('link_no', $linkNo)
                            ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function show($linkNo)
    {
        $linkClass = LinkClass::with(['province', 'kabupaten', 'linkNo', 'classRelation'])
                             ->where('link_no', $linkNo)
                             ->first();

        if (!$linkClass) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('pengaturan_jaringan.kelas_jalan.show', compact('linkClass'));
    }

    public function create()
    {
        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi = Province::orderBy('province_name')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name')->get();
        $links = Link::orderBy('link_name')->get();
        
        return view('pengaturan_jaringan.kelas_jalan.create', compact('statusRuas', 'provinsi', 'kabupaten', 'links'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_no' => 'required',
            'class' => 'required',
            'kmClass' => 'required|numeric',
        ]);

        LinkClass::create($request->all());

        return redirect()->route('kelas-jalan.index')
                        ->with('success', 'Data Kelas Jalan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $linkClass = LinkClass::findOrFail($id);
        $statusRuas = CodeLinkStatus::orderBy('order')->get();
        $provinsi = Province::orderBy('province_name')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name')->get();
        $links = Link::orderBy('link_name')->get();
        
        return view('pengaturan_jaringan.kelas_jalan.edit', compact('linkClass', 'statusRuas', 'provinsi', 'kabupaten', 'links'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_no' => 'required',
            'class' => 'required',
            'kmClass' => 'required|numeric',
        ]);

        $linkClass = LinkClass::findOrFail($id);
        $linkClass->update($request->all());

        return redirect()->route('kelas-jalan.index')
                        ->with('success', 'Data Kelas Jalan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $linkClass = LinkClass::findOrFail($id);
        $linkClass->delete();

        return redirect()->route('kelas-jalan.index')
                        ->with('success', 'Data Kelas Jalan berhasil dihapus');
    }

    public function destroyAll()
    {
        LinkClass::query()->delete(); 
        return redirect()->route('kelas-jalan.index')
            ->with('success', 'Semua data kelas jalan berhasil dihapus.');
    }
}