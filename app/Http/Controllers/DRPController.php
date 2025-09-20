<?php

namespace App\Http\Controllers;

use App\Models\DRP;
use App\Models\Link;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Exports\DRPExport;
use App\Imports\DRPImport;
use Illuminate\Http\Request;
use App\Models\CodeLinkStatus;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DRPController extends Controller
{
    public function index(Request $request)
    {
        // Default filter values
        $defaultStatus    = CodeLinkStatus::where('code_description_ind', 'Kabupaten')->first()->code ?? '';
        $defaultProvinsi  = Province::where('province_name', 'Jawa Timur')->first()->province_code ?? '';
        $defaultKabupaten = Kabupaten::where('kabupaten_name', 'Jember')->first()->kabupaten_code ?? '';
        $defaultLink      = '';

        // Load data untuk dropdown filter
        $status    = CodeLinkStatus::all();
        $provinsi  = Province::all();
        $kabupaten = Kabupaten::all();
        $link      = Link::all();

        if ($request->ajax()) {
            return $this->getDataForDataTables($request);
        }

        return view('pengaturan_jaringan.drp.index', compact(
            'status', 'provinsi', 'kabupaten', 'link',
            'defaultStatus', 'defaultProvinsi', 'defaultKabupaten', 'defaultLink'
        ));
    }
    
    private function getDataForDataTables(Request $request)
    {
        $query = DRP::with(['type', 'province', 'kabupaten', 'link', 'status']);

        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                $actions = '<div class="d-flex gap-1">';
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                
                if ($user && $user->hasPermission('update', 'drp')) {
                    $actions .= '<a href="' . route('drp.edit', $item->drp_num) . '" class="btn btn-warning btn-sm" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                 </a>';
                }
                
                if ($user && $user->hasPermission('delete', 'drp')) {
                    $actions .= '<form action="' . route('drp.destroy', $item->drp_num) . '" method="POST" class="d-inline"
                                    onsubmit="return confirm(\'Yakin ingin menghapus DRP ini?\')">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                 </form>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->addColumn('type_description', function ($item) {
                return $item->type->code_description_ind ?? '-';
            })
            ->addColumn('status_description', function ($item) {
                return $item->status->code_description_ind ?? '-';
            })
            ->editColumn('drp_num', fn($item) => $item->drp_num ?? '')
            ->editColumn('chainage', fn($item) => $item->chainage ?? '')
            ->editColumn('drp_length', fn($item) => $item->drp_length ?? '')
            ->editColumn('drp_desc', fn($item) => $item->drp_desc ?? '')
            ->filter(function ($query) use ($request) {
                if ($request->filled('status_filter') && $request->status_filter !== '') {
                    $query->where('status_code', $request->status_filter);
                }
                if ($request->filled('provinsi_filter') && $request->provinsi_filter !== '') {
                    $query->where('province_code', $request->provinsi_filter);
                }
                if ($request->filled('kabupaten_filter') && $request->kabupaten_filter !== '') {
                    $query->where('kabupaten_code', $request->kabupaten_filter);
                }
                if ($request->filled('ruas_filter') && $request->ruas_filter !== '') {
                    $query->where('link_no', $request->ruas_filter);
                }
            })
            ->filterColumn('type_description', function($query, $keyword) {
                $query->whereHas('type', function($q) use ($keyword) {
                    $q->where('code_description_ind', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('status_description', function($query, $keyword) {
                $query->whereHas('status', function($q) use ($keyword) {
                    $q->where('code_description_ind', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $status    = CodeLinkStatus::all();
        $provinsi  = Province::all();
        $kabupaten = Kabupaten::all();
        $link      = Link::all();

        return view('pengaturan_jaringan.drp.create', compact('status', 'provinsi', 'kabupaten', 'link'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'drp_num'        => 'required|unique:drp,drp_num',
            'province_code'  => 'required',
            'kabupaten_code' => 'required',
            'link_no'        => 'required',
            'status_code'    => 'required',
        ]);

        DRP::create($request->all());

        return redirect()->route('drp.index')->with('success', 'Data DRP berhasil ditambahkan.');
    }

    public function edit($drp_num)
    {
        $drp       = DRP::where('drp_num', $drp_num)->firstOrFail();
        $status    = CodeLinkStatus::all();
        $provinsi  = Province::all();
        $kabupaten = Kabupaten::all();
        $link      = Link::all();

        return view('pengaturan_jaringan.drp.edit', compact('drp', 'status', 'provinsi', 'kabupaten', 'link'));
    }

    public function update(Request $request, $drp_num)
    {
        $drp = DRP::where('drp_num', $drp_num)->firstOrFail();
        
        $request->validate([
            'drp_num'        => 'required|unique:drp,drp_num,' . $drp->drp_num . ',drp_num',
            'province_code'  => 'required',
            'kabupaten_code' => 'required',
            'link_no'        => 'required',
            'status_code'    => 'required',
        ]);

        $drp->update($request->all());

        return redirect()->route('drp.index')->with('success', 'Data DRP berhasil diperbarui.');
    }

    public function destroy($drp_num)
    {
        $drp = DRP::where('drp_num', $drp_num)->firstOrFail();
        $drp->delete();
        return redirect()->route('drp.index')->with('success', 'Data DRP berhasil dihapus.');
    }

    public function destroyAll()
    {
        DRP::truncate();
        return redirect()->route('drp.index')->with('success', 'Semua data DRP berhasil dihapus.');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);

            Excel::import(new DRPImport, $request->file('file'));

            return redirect()->route('drp.index')->with('success', 'Data DRP berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('drp.index')->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            return Excel::download(new DRPExport, 'drp_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->route('drp.index')->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}
