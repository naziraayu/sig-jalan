<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;
use App\Exports\ProvinceExport;
use App\Imports\ProvinceImport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProvinceController extends Controller
{
    public function index(Request $request)
    {
        // ambil semua data provinsi
    $provinces = Province::all();

    // kirim ke view
    return view('administrasi.provinsi.index', compact('provinces'));
    }

    public function create()
    {
        return view('administrasi.provinsi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'province_code' => 'required|unique:provinces,province_code',
            'province_name' => 'required|string|max:255',
            'default_province' => 'nullable|boolean',
            'stable' => 'nullable|boolean',
        ]);

        Province::create($validated);

        return redirect()->route('provinces.index')->with('success', 'Province berhasil ditambahkan.');
    }

    public function show(Province $province)
    {
        return view('administrasi.provinsi.show', compact('province'));
    }

    public function edit(Province $province)
    {
        return view('administrasi.provinsi.edit', compact('province'));
    }

    public function update(Request $request, Province $province)
    {
        $validated = $request->validate([
            'province_name' => 'required|string|max:255',
            'default_province' => 'nullable|boolean',
            'stable' => 'nullable|boolean',
        ]);

        $province->update($validated);

        return redirect()->route('provinces.index')->with('success', 'Province berhasil diperbarui.');
    }

    public function destroy(Province $province)
    {
        $province->delete();

        return redirect()->route('provinces.index')->with('success', 'Province berhasil dihapus.');
    }

    public function destroyAll()
    {
        // Hapus semua provinsi
        DB::table('provinces')->delete(); // atau Province::truncate();

        return redirect()->route('provinces.index')
            ->with('success', 'Semua data provinsi berhasil dihapus!');
    }


    // EXPORT
    public function export()
    {
        return Excel::download(new ProvinceExport, 'provinces.xlsx');
    }

    // IMPORT
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new ProvinceImport, $request->file('file'));

        return redirect()->route('provinces.index')->with('success', 'Provinsi berhasil diimport.');
    }
}
