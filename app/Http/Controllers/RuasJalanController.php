<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\LinkMaster;
use App\Exports\LinkExport;
use App\Imports\LinkImport;
use Illuminate\Http\Request;
use App\Models\CodeLinkStatus;
use App\Models\CodeLinkFunction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class RuasJalanController extends Controller
{
    /**
     * Helper: Get selected year from session or default to current year
     */
    private function getSelectedYear()
    {
        return session('selected_year', now()->year);
    }

    public function index()
    {
        $selectedYear = $this->getSelectedYear();

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();

        $defaultProvinsi = '35';
        $defaultKabupaten = '09';

        return view('pengaturan_jaringan.ruas_jalan.index', compact(
            'provinsi',
            'kabupaten',
            'defaultProvinsi',
            'defaultKabupaten',
            'selectedYear'
        ));
    }

    public function create()
    {
        // ✅ Default tahun sekarang, tapi user bisa ganti
        $currentYear = now()->year;
        
        // Generate Link No & Code berdasarkan tahun sekarang (akan diupdate via AJAX)
        $newLinkNo = $this->generateLinkNo($currentYear);
        $newLinkCode = $this->generateLinkCode($currentYear);

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();
        $statusList = CodeLinkStatus::orderBy('code_description_ind', 'asc')->get();
        $functionList = CodeLinkFunction::orderBy('code_description_ind', 'asc')->get();

        $defaultProvinsi = '35';
        $defaultKabupaten = '09';

        return view('pengaturan_jaringan.ruas_jalan.create', compact(
            'provinsi',
            'kabupaten',
            'statusList',
            'functionList',
            'defaultProvinsi',
            'defaultKabupaten',
            'newLinkNo',
            'newLinkCode',
            'currentYear'
        ));
    }

    /**
     * ✅ AJAX: Generate Link No & Code berdasarkan tahun
     */
    public function generateCodes(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        return response()->json([
            'link_no' => $this->generateLinkNo($year),
            'link_code' => $this->generateLinkCode($year)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'link_no' => 'required|unique:link,link_no',
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_code' => 'required',
            'link_name' => 'required|string|max:191',
            'status' => 'nullable',
            'function' => 'nullable',
            'class' => 'nullable',
            'link_length_official' => 'nullable|numeric',
            'link_length_actual' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            // 1. Cek apakah link_master sudah ada (berdasarkan link_name + lokasi)
            $linkMaster = LinkMaster::where('link_name', $validated['link_name'])
                                    ->where('province_code', $validated['province_code'])
                                    ->where('kabupaten_code', $validated['kabupaten_code'])
                                    ->first();

            // 2. Jika belum ada, buat link_master baru
            if (!$linkMaster) {
                $linkMaster = LinkMaster::create([
                    'link_name' => $validated['link_name'],
                    'link_no' => $validated['link_no'],
                    'province_code' => $validated['province_code'],
                    'kabupaten_code' => $validated['kabupaten_code'],
                ]);
            }

            // 3. Cek apakah link dengan tahun ini sudah ada
            $existingLink = Link::where('link_master_id', $linkMaster->id)
                               ->where('year', $validated['year'])
                               ->first();

            if ($existingLink) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['year' => "Ruas jalan '{$validated['link_name']}' sudah memiliki data untuk tahun {$validated['year']}."]);
            }

            // 4. Buat Link baru
            $linkData = $request->except('link_name');
            $linkData['link_master_id'] = $linkMaster->id;

            Link::create($linkData);

            DB::commit();

            return redirect()->route('ruas-jalan.index')
                ->with('success', "Ruas jalan '{$validated['link_name']}' berhasil ditambahkan untuk tahun {$validated['year']}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $ruas = Link::with('linkMaster')->findOrFail($id);

        $provinsi = Province::orderBy('province_name', 'asc')->get();
        $kabupaten = Kabupaten::orderBy('kabupaten_name', 'asc')->get();
        $statusList = CodeLinkStatus::orderBy('code_description_ind', 'asc')->get();
        $functionList = CodeLinkFunction::orderBy('code_description_ind', 'asc')->get();

        return view('pengaturan_jaringan.ruas_jalan.edit', compact(
            'ruas', 'provinsi', 'kabupaten', 'statusList', 'functionList'
        ));
    }

    public function update(Request $request, $id)
    {
        $ruas = Link::with('linkMaster')->findOrFail($id);

        $validated = $request->validate([
            'link_no' => 'required|unique:link,link_no,' . $ruas->id . ',id',
            'province_code' => 'required',
            'kabupaten_code' => 'required',
            'link_code' => 'required',
            'link_name' => 'required|string|max:191',
            'status' => 'nullable',
            'function' => 'nullable',
            'link_length_official' => 'nullable|numeric',
            'link_length_actual' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Update link_master jika link_name berubah
            if ($ruas->linkMaster) {
                $ruas->linkMaster->update([
                    'link_name' => $validated['link_name'],
                    'province_code' => $validated['province_code'],
                    'kabupaten_code' => $validated['kabupaten_code'],
                ]);
            }

            // Update link (tanpa year, link_name)
            $linkData = $request->except(['year', 'link_name']);
            $ruas->update($linkData);

            DB::commit();

            return redirect()->route('ruas-jalan.index')
                ->with('success', 'Ruas jalan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mengupdate data: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $ruas = Link::with(['linkMaster', 'province', 'kabupaten', 'statusRelation', 'functionRelation'])
                    ->findOrFail($id);

        return view('pengaturan_jaringan.ruas_jalan.show', compact('ruas'));
    }

    public function destroy($id)
    {
        $ruas = Link::findOrFail($id);
        
        // Cek apakah ada data tahun lain untuk link_master_id yang sama
        $otherYears = Link::where('link_master_id', $ruas->link_master_id)
                         ->where('id', '!=', $id)
                         ->exists();
        
        DB::beginTransaction();
        try {
            // Hapus link
            $ruas->delete();
            
            // Jika tidak ada tahun lain, hapus juga link_master
            if (!$otherYears && $ruas->linkMaster) {
                $ruas->linkMaster->delete();
            }
            
            DB::commit();
            
            return redirect()->route('ruas-jalan.index')
                ->with('success', 'Ruas jalan berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function destroyAll()
    {
        $selectedYear = $this->getSelectedYear();
        
        DB::beginTransaction();
        try {
            // Ambil semua link tahun ini
            $links = Link::where('year', $selectedYear)->get();
            $deleted = $links->count();
            
            // Hapus link
            Link::where('year', $selectedYear)->delete();
            
            // Hapus link_master yang tidak punya link lagi
            $masterIds = $links->pluck('link_master_id')->unique();
            foreach ($masterIds as $masterId) {
                $hasOtherLinks = Link::where('link_master_id', $masterId)->exists();
                if (!$hasOtherLinks) {
                    LinkMaster::find($masterId)?->delete();
                }
            }
            
            DB::commit();
            
            return redirect()->route('ruas-jalan.index')
                ->with('success', "Berhasil menghapus {$deleted} data ruas jalan tahun {$selectedYear}.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus semua data: ' . $e->getMessage()]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new LinkImport, $request->file('file'));
            
            return redirect()->route('ruas-jalan.index')
                ->with('success', 'Data ruas jalan berhasil di import!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal import data: ' . $e->getMessage()]);
        }
    }

    // public function export()
    // {
    //     $selectedYear = $this->getSelectedYear();
        
    //     return Excel::download(
    //         new LinkExport($selectedYear), 
    //         "ruas_jalan_{$selectedYear}.xlsx"
    //     );
    // }

    public function getData(Request $request)
    {
        $selectedYear = $this->getSelectedYear();
        
        /** @var User|null $user */
        $user = Auth::user();

        $query = Link::with(['linkMaster', 'province', 'kabupaten', 'statusRelation', 'functionRelation'])
            ->where('link.year', $selectedYear);

        // Filter
        if ($request->filterProvinsi) {
            $query->where('link.province_code', $request->filterProvinsi);
        }
        if ($request->filterKabupaten) {
            $query->where('link.kabupaten_code', $request->filterKabupaten);
        }

        return DataTables::of($query)
            ->addColumn('link_name', fn($row) => $row->linkMaster?->link_name ?? '-')
            ->addColumn('status_name', fn($row) => $row->statusRelation?->code_description_ind ?? '-')
            ->addColumn('function_name', fn($row) => $row->functionRelation?->code_description_ind ?? '-')
            ->addColumn('province_name', fn($row) => $row->province?->province_name ?? '-')
            ->addColumn('kabupaten_name', fn($row) => $row->kabupaten?->kabupaten_name ?? '-')
            ->addColumn('actions', function ($row) use ($user) {
                $btn = '<div class="d-flex gap-1 justify-content-center">';

                if ($user && $user->hasPermission('detail', 'ruas_jalan')) {
                    $btn .= '<a href="'.route('ruas-jalan.show', $row->id).'" 
                                class="btn btn-info btn-sm" title="Detail Data">
                                <i class="fas fa-eye"></i>
                             </a>';
                }

                if ($user && $user->hasPermission('update', 'ruas_jalan')) {
                    $btn .= '<a href="'.route('ruas-jalan.edit', $row->id).'" 
                                class="btn btn-warning btn-sm" title="Edit Data">
                                <i class="fas fa-edit"></i>
                             </a>';
                }

                if ($user && $user->hasPermission('delete', 'ruas_jalan')) {
                    $btn .= '<form action="'.route('ruas-jalan.destroy', $row->id).'" 
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

    /**
     * Generate Link No based on year
     */
    private function generateLinkNo($year)
    {
        // FORMAT BARU (2025+): YYYYPCKKKK
        if ($year >= 2025) {
            $prefix = $year . '3509';
            
            $lastLink = Link::where('link_no', 'like', $prefix . '%')
                        ->orderBy('link_no', 'desc')
                        ->first();
            
            if ($lastLink) {
                $lastNumber = (int) substr($lastLink->link_no, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
        
        // FORMAT LAMA (< 2025): PCKKKKKKKKKK
        $prefix = '3509';
        
        $lastLink = Link::where('year', $year)
                    ->where('link_no', 'like', $prefix . '%')
                    ->whereRaw('LENGTH(link_no) = 12')
                    ->orderBy('link_no', 'desc')
                    ->first();
        
        if ($lastLink) {
            $newNumber = (string)((int)$lastLink->link_no + 1);
            return str_pad($newNumber, 12, '0', STR_PAD_LEFT);
        }
        
        return '350900000001';
    }
    /**
     * Generate Link Code: 35.09.UUUU
     */
    private function generateLinkCode($year)
    {
        $lastCode = Link::where('year', $year)
                        ->where('link_code', 'like', '35.09.%')
                        ->orderBy('link_code', 'desc')
                        ->first();
        
        if ($lastCode && $lastCode->link_code) {
            $lastNumber = (int) substr($lastCode->link_code, strrpos($lastCode->link_code, '.') + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return '35.09.' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}