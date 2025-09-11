<?php

namespace App\Http\Controllers;

use App\Models\Balai;
use App\Models\Island;
use App\Models\Province;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use App\Exports\KabupatenExport;
use App\Imports\KabupatenImport;
use Maatwebsite\Excel\Facades\Excel;

class KabupatenController extends Controller
{
    public function index()
    {
        $kabupatens = Kabupaten::with(['province','balai'])->get();
        return view('administrasi.kabupaten.index', compact('kabupatens'));
    }

    public function create()
    {
        $provinces = Province::all();
        $balais = Balai::all();
        $islands = Island::all();
        return view('administrasi.kabupaten.create', compact('provinces','balais','islands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_code' => 'required|unique:kabupaten,kabupaten_code',
            'kabupaten_name' => 'required',
            'province_code'  => 'required',
        ]);

        Kabupaten::create($request->all());
        return redirect()->route('kabupaten.index')->with('success','Kabupaten berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kabupaten = Kabupaten::findOrFail($id);
        $provinces = Province::all();
        $balais = Balai::all();
        $islands = Island::all();
        return view('administrasi.kabupaten.edit', compact('kabupaten','provinces','balais','islands'));
    }

    public function update(Request $request, $id)
    {
        $kabupaten = Kabupaten::findOrFail($id);

        $request->validate([
            'kabupaten_name' => 'required',
            'province_code'  => 'required',
        ]);

        $kabupaten->update($request->all());
        return redirect()->route('kabupaten.index')->with('success','Kabupaten berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kabupaten = Kabupaten::findOrFail($id);
        $kabupaten->delete();
        return redirect()->route('kabupaten.index')->with('success','Kabupaten berhasil dihapus.');
    }

    public function destroyAll()
    {
        Kabupaten::query()->delete();
        return redirect()->route('kabupaten.index')->with('success','Semua kabupaten berhasil dihapus.');
    }

    // Import & Export
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new KabupatenImport, $request->file('file'));
        return back()->with('success','Data kabupaten berhasil diimport.');
    }

    public function export()
    {
        return Excel::download(new KabupatenExport, 'kabupaten.xlsx');
    }
}
