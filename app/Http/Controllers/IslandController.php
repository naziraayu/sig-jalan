<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Exports\IslandExport;
use App\Imports\IslandImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class IslandController extends Controller
{
    public function index()
    {
        $islands = Island::with('province')->get();
        return view('administrasi.island.index', compact('islands'));
    }

    public function create()
    {
        $provinces = Province::all();
        return view('administrasi.island.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'island_code' => 'required|string|unique:island,island_code',
            'island_name' => 'required|string|max:255',
            'province_code' => 'required|exists:provinces,province_code',
        ]);

        Island::create($request->all());

        return redirect()->route('island.index')->with('success', 'Pulau berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $island = Island::findOrFail($id);
        $provinces = Province::all();
        return view('administrasi.island.edit', compact('island', 'provinces'));
    }

    public function update(Request $request, $id)
    {
        $island = Island::findOrFail($id);

        $request->validate([
            'island_name' => 'required|string|max:255',
            'province_code' => 'required|exists:provinces,province_code',
        ]);

        $island->update($request->all());

        return redirect()->route('island.index')->with('success', 'Pulau berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $island = Island::findOrFail($id);
        $island->delete();

        return redirect()->route('island.index')->with('success', 'Pulau berhasil dihapus.');
    }
    public function destroyAll()
    {
        // Hapus semua data Balai
        DB::table('island')->delete();

        return redirect()->route('island.index')->with('success', 'Semua pulau berhasil dihapus.');
    }

}
