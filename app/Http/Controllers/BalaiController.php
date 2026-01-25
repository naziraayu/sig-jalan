<?php

namespace App\Http\Controllers;

use App\Models\Balai;
use App\Models\Province;
use App\Exports\BalaiExport;
use App\Imports\BalaiImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BalaiController extends Controller
{
    public function index(Request $request)
    {
        $balais = Balai::with('province')->get();
        return view('administrasi.balai.index', compact('balais'));
    }

    public function create()
    {
        $provinces = Province::all();
        return view('administrasi.balai.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'balai_code'   => 'required|string|unique:balai,balai_code',
            'province_code'=> 'required|exists:provinces,province_code',
            'balai_name'   => 'required|string|max:255',
        ]);

        Balai::create($request->all());

        return redirect()->route('balai.index')->with('success', 'Balai berhasil ditambahkan.');
    }

    public function edit($balai_code)
    {
        $balai = Balai::findOrFail($balai_code);
        $provinces = Province::all();
        return view('administrasi.balai.edit', compact('balai', 'provinces'));
    }

    public function update(Request $request, $balai_code)
    {
        $balai = Balai::findOrFail($balai_code);

        $request->validate([
            'province_code'=> 'required|exists:provinces,province_code',
            'balai_name'   => 'required|string|max:255',
        ]);

        $balai->update($request->all());

        return redirect()->route('balai.index')->with('success', 'Balai berhasil diperbarui.');
    }

    public function destroy($balai_code)
    {
        $balai = Balai::findOrFail($balai_code);
        $balai->delete();

        return redirect()->route('balai.index')->with('success', 'Balai berhasil dihapus.');
    }

    public function destroyAll()
    {
        // Hapus semua data Balai
        DB::table('balai')->delete();

        return redirect()->route('balai.index')->with('success', 'Semua balai berhasil dihapus.');
    }
}
