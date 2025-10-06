<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Exports\LinkExport;
use App\Imports\LinkImport;
use Illuminate\Http\Request;
use App\Models\CodeLinkStatus;
use App\Models\CodeLinkFunction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class RuasJalanController extends Controller
{
    public function index()
    {
        $ruasjalan = Link::with(['province', 'kabupaten', 'statusRelation', 'functionRelation'])->get();
        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();

        $defaultProvinsi = '35';   // Jawa Timur
        $defaultKabupaten = '09'; // Jember

        return view('pengaturan_jaringan.ruas_jalan.index', compact(
            'ruasjalan',
            'provinsi',
            'kabupaten',
            'defaultProvinsi',
            'defaultKabupaten'
        ));
    }

    public function create()
    {
        $lastLink = Link::orderBy('link_no', 'desc')->first();
        $newLinkNo = $lastLink ? (string)((int)$lastLink->link_no + 1) : '350900000001';

        $lastCode = Link::orderBy('link_code', 'desc')->first();
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->link_code, strrpos($lastCode->link_code, '.') + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $newLinkCode = '35.09.' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();
        $statusList = CodeLinkStatus::orderBy('code_description_ind', 'asc')->get();
        $functionList = CodeLinkFunction::orderBy('code_description_ind', 'asc')->get();

        $defaultProvinsi = '35';   // Jawa Timur
        $defaultKabupaten = '09';  // Jember

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

    public function edit($link_no)
    {
        $ruas = Link::findOrFail($link_no);

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();
        $statusList = CodeLinkStatus::orderBy('code_description_ind', 'asc')->get();
        $functionList = CodeLinkFunction::orderBy('code_description_ind', 'asc')->get();

        return view('pengaturan_jaringan.ruas_jalan.edit', compact(
            'ruas', 'provinsi', 'kabupaten', 'statusList', 'functionList'
        ));
    }

    public function update(Request $request, $link_no)
    {
        $ruas = Link::findOrFail($link_no);

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

    public function show($link_no)
    {
        $ruas = Link::with(['province', 'kabupaten', 'statusRelation', 'functionRelation'])
                    ->findOrFail($link_no);

        return view('pengaturan_jaringan.ruas_jalan.show', compact('ruas'));
    }

    public function destroy($link_no)
    {
        $ruas = Link::findOrFail($link_no);
        $ruas->delete();

        return redirect()->route('ruas-jalan.index')->with('success', 'Ruas jalan berhasil dihapus.');
    }

    public function destroyAll()
    {
        Link::query()->delete(); 
        return redirect()->route('ruas-jalan.index')->with('success', 'Semua data ruas jalan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new LinkImport, $request->file('file'));

        return redirect()->route('ruas-jalan.index')->with('success', 'Data ruas jalan berhasil di import!');
    }

    public function export()
    {
        return Excel::download(new LinkExport, 'ruas_jalan.xlsx');
    }

    public function getData(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        $query = Link::with(['province', 'kabupaten', 'statusRelation', 'functionRelation'])
            ->orderBy('link_no', 'asc');;

        if ($request->filterProvinsi) {
            $query->where('province_code', $request->filterProvinsi);
        }
        if ($request->filterKabupaten) {
            $query->where('kabupaten_code', $request->filterKabupaten);
        }

        return DataTables::of($query)
            ->addColumn('status', fn($row) => $row->statusRelation?->code ?? '-')
            ->addColumn('province_code', fn($row) => $row->province?->province_code ?? '-')
            ->addColumn('kabupaten_code', fn($row) => $row->kabupaten?->kabupaten_code ?? '-')
            ->addColumn('actions', function ($row) use ($user) {
                $btn = '<div class="d-flex gap-1">';

                if ($user && $user->hasPermission('detail', 'ruas_jalan')) {
                    $btn .= '<a href="'.route('ruas-jalan.show', $row->link_no).'" 
                                class="btn btn-info btn-sm" title="Detail Data">
                                <i class="fas fa-eye"></i>
                             </a>';
                }

                if ($user && $user->hasPermission('update', 'ruas_jalan')) {
                    $btn .= '<a href="'.route('ruas-jalan.edit', $row->link_no).'" 
                                class="btn btn-warning btn-sm" title="Edit Data">
                                <i class="fas fa-edit"></i>
                             </a>';
                }

                if ($user && $user->hasPermission('delete', 'ruas_jalan')) {
                    $btn .= '<form action="'.route('ruas-jalan.destroy', $row->link_no).'" 
                                    method="POST" 
                                    style="display:inline;" 
                                    onsubmit="return confirm(\'Yakin ingin menghapus ruas jalan ini?\')">
                                    '.csrf_field().method_field('DELETE').'
                                    <button type="submit" 
                                            class="btn btn-danger btn-sm" 
                                            title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
