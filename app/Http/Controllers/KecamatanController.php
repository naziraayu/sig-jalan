<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Exports\KecamatanExport;
use App\Imports\KecamatanImport;
use Maatwebsite\Excel\Facades\Excel;

class KecamatanController extends Controller
{
    public function index()
    {
        $kecamatans = Kecamatan::with(['province', 'kabupaten'])->get();
        return view('administrasi.kecamatan.index', compact('kecamatans'));
    }

    public function create()
    {
        $provinces = Province::all();
        $kabupatens = Kabupaten::all();
        return view('administrasi.kecamatan.create', compact('provinces','kabupatens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kecamatan_code' => 'required|unique:kecamatan,kecamatan_code',
            'kecamatan_name' => 'required',
            'kabupaten_code' => 'required',
            'province_code' => 'required',
        ]);

        Kecamatan::create($request->all());
        return redirect()->route('kecamatan.index')->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $provinces = Province::all();
        $kabupatens = Kabupaten::all();
        return view('administrasi.kecamatan.edit', compact('kecamatan','provinces','kabupatens'));
    }

    public function update(Request $request, $id)
    {
        $kecamatan = Kecamatan::findOrFail($id);

        $request->validate([
            'kecamatan_code' => 'required|unique:kecamatan,kecamatan_code,'.$kecamatan->kecamatan_code.',kecamatan_code',
            'kecamatan_name' => 'required',
            'kabupaten_code' => 'required',
            'province_code' => 'required',
        ]);

        $kecamatan->update($request->all());
        return redirect()->route('kecamatan.index')->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->delete();
        return redirect()->route('kecamatan.index')->with('success', 'Kecamatan berhasil dihapus.');
    }

    public function destroyAll()
    {
        Kecamatan::truncate();
        return redirect()->route('kecamatan.index')->with('success', 'Semua data kecamatan berhasil dihapus.');
    }
}
