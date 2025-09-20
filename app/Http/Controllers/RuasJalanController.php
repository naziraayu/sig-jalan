<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Exports\LinkExport;
use App\Imports\LinkImport;
use Illuminate\Http\Request;
use App\Models\CodeLinkStatus;
use App\Models\CodeLinkFunction;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RuasJalanController extends Controller
{
    public function index()
    {
        // Ambil data ruas jalan dengan eager loading relasi
        $ruasjalan = Link::with(['province', 'kabupaten', 'statusRelation', 'functionRelation'])->get();

        // Ambil semua provinsi untuk dropdown
        $provinsi = Province::orderBy('province_name', 'asc')->get();

        // Ambil semua kabupaten untuk dropdown (opsional, bisa diisi via AJAX kalau dependent)
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();

         // set default value
        $defaultProvinsi = '35';   // kode Jawa Timur
        $defaultKabupaten = '09'; // kode Jember

        return view('pengaturan_jaringan.ruas_jalan.index', compact('ruasjalan', 'provinsi', 'kabupaten', 'defaultProvinsi', 'defaultKabupaten'));
    }

    /**
     * Form tambah ruas jalan
     */
    public function create()
    {
        // Ambil link_no terakhir
        $lastLink = Link::orderBy('link_no', 'desc')->first();

        // Kalau ada data, increment 1, kalau tidak mulai default
        $newLinkNo = $lastLink ? (string)((int)$lastLink->link_no + 1) : '350900000001';

        // Ambil link_code terakhir
        $lastCode = Link::orderBy('link_code', 'desc')->first();
        if ($lastCode) {
            // Ambil angka terakhir setelah titik
            $lastNumber = (int) substr($lastCode->link_code, strrpos($lastCode->link_code, '.') + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format link_code -> 35.09.XXXX
        $newLinkCode = '35.09.' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();
        $statusList = CodeLinkStatus::orderBy('code_description_ind', 'asc')->get();
        $functionList = CodeLinkFunction::orderBy('code_description_ind', 'asc')->get();

        // set default value
        $defaultProvinsi = '35';   // kode Jawa Timur
        $defaultKabupaten = '09';  // kode Jember


        return view('pengaturan_jaringan.ruas_jalan.create', compact(
        'provinsi',
        'kabupaten',
        'statusList',
        'functionList',
        'defaultProvinsi',
        'defaultKabupaten',
        'newLinkNo',
        'newLinkCode'
    ));
    }

    /**
     * Simpan ruas jalan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'link_no' => 'required|unique:link,link_no',
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_code' => 'required',
            'link_name' => 'required',
        ]);

        Link::create($request->all());

        return redirect()->route('ruas-jalan.index')->with('success', 'Ruas jalan berhasil ditambahkan.');
    }

    /**
     * Form edit ruas jalan
     */
    public function edit($id)
    {
        $ruas = Link::findOrFail($id);

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();
        $statusList = CodeLinkStatus::orderBy('code_description_ind', 'asc')->get();
        $functionList = CodeLinkFunction::orderBy('code_description_ind', 'asc')->get();

        return view('pengaturan_jaringan.ruas_jalan.edit', compact(
            'ruas', 'provinsi', 'kabupaten', 'statusList', 'functionList'
        ));
    }

    /**
     * Update ruas jalan
     */
    public function update(Request $request, $id)
    {
        $ruas = Link::findOrFail($id);

        $request->validate([
            'link_no' => 'required|unique:link,link_no,' . $ruas->link_no . ',link_no',
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_code' => 'required',
            'link_name' => 'required',
        ]);

        $ruas->update($request->all());

        return redirect()->route('ruas-jalan.index')->with('success', 'Ruas jalan berhasil diperbarui.');
    }

    public function show($id)
    {
        $ruas = Link::with(['province', 'kabupaten', 'statusRelation', 'functionRelation'])
                    ->findOrFail($id);

        return view('pengaturan_jaringan.ruas_jalan.show', compact('ruas'));
    }


    /**
     * Hapus ruas jalan
     */
    public function destroy($id)
    {
        $ruas = Link::findOrFail($id);
        $ruas->delete();

        return redirect()->route('ruas-jalan.index')->with('success', 'Ruas jalan berhasil dihapus.');
    }

    /**
     * Hapus semua ruas jalan (opsional)
     */
    public function destroyAll()
    {
        Link::query()->delete(); 
        return redirect()->route('ruas-jalan.index')
            ->with('success', 'Semua data ruas jalan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new LinkImport, $request->file('file'));

        return redirect()->route('ruas-jalan.index')
            ->with('success', 'Data ruas jalan berhasil di import!');
    }

    public function export()
    {
        return Excel::download(new LinkExport, 'ruas_jalan.xlsx');
    }

}
