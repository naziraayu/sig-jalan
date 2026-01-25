<?php

namespace App\Http\Controllers;

use App\Models\DRP;
use App\Models\CodeLinkStatus;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\Link;
use App\Models\CodeDrpType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DRPExport;
use App\Imports\DRPImport;

class DRPController extends Controller
{
    public function index()
    {
        try {
            // Ambil data untuk dropdown
            $statusRuas = CodeLinkStatus::orderBy('order')->get();
            $provinsi = Province::orderBy('province_name')->get();
            
            // Filter kabupaten untuk Jawa Timur saja
            $kabupaten = Kabupaten::where('province_code', '35') 
                        ->orderBy('kabupaten_name')
                        ->get();
            
            // Ambil ruas jalan untuk kabupaten yang dipilih
            $ruasjalan = Link::whereHas('kabupaten', function($query) {
                            $query->where('province_code', '35'); // Jawa Timur
                        })
                        ->orderBy('link_code')
                        ->get();

            return view('pengaturan_jaringan.drp.index', compact('statusRuas', 'provinsi', 'kabupaten', 'ruasjalan'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            // Ambil data untuk dropdown
            $statusRuas = CodeLinkStatus::orderBy('order')->get();
            $provinsi = Province::orderBy('province_name')->get();
            
            // Filter kabupaten untuk Jawa Timur saja
            $kabupaten = Kabupaten::where('province_code', '35') 
                        ->orderBy('kabupaten_name')
                        ->get();
            
            // Ambil tipe DRP
            $drpTypes = CodeDrpType::orderBy('order')->get();

            return view('pengaturan_jaringan.drp.create', compact('statusRuas', 'provinsi', 'kabupaten', 'drpTypes'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
{
    try {
        // Validasi data
        $validated = $request->validate([
            'province_code' => 'required|string|exists:province,province_code',
            'kabupaten_code' => 'required|string|exists:kabupaten,kabupaten_code',
            'link_no' => 'required|string|exists:link,link_no',
            'generation_mode' => 'required|in:manual,auto',
            
            // Fields untuk manual mode
            'drp_order' => 'required_if:generation_mode,manual|integer',
            'drp_length' => 'required_if:generation_mode,manual|numeric',
            'dpr_north_deg' => 'nullable|integer|min:0|max:90',
            'dpr_north_min' => 'nullable|integer|min:0|max:59',
            'dpr_north_sec' => 'nullable|numeric|min:0|max:59.99',
            'dpr_east_deg' => 'nullable|integer|min:0|max:180',
            'dpr_east_min' => 'nullable|integer|min:0|max:59',
            'dpr_east_sec' => 'nullable|numeric|min:0|max:59.99',
            'drp_type' => 'required_if:generation_mode,manual|string|exists:code_drp_type,code',
            'drp_desc' => 'nullable|string|max:500',
            'drp_comment' => 'nullable|string|max:1000',
            'chainage' => 'nullable|numeric',
            
            // Fields untuk auto mode
            'total_length' => 'required_if:generation_mode,auto|numeric|min:1',
            'start_chainage' => 'required_if:generation_mode,auto|numeric|min:0',
        ]);

        DB::beginTransaction();

        if ($validated['generation_mode'] === 'auto') {
            // Auto generate DRP points
            $this->generateDRPPoints($validated);
        } else {
            // Manual single entry
            $this->storeManualDRP($validated);
        }

        DB::commit();

        return redirect()->route('drp.index')
                       ->with('success', 'Data DRP berhasil ditambahkan.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollback();
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
    }
}

/**
 * Generate DRP points automatically based on total length
 */
private function generateDRPPoints($data)
{
    $totalLength = $data['total_length'];
    $startChainage = $data['start_chainage'];
    $linkNo = $data['link_no'];
    
    // Hapus DRP existing jika ada untuk link ini
    DRP::where('link_no', $linkNo)->delete();
    
    $drpPoints = [];
    $currentChainage = $startChainage;
    $order = 1;
    $remainingLength = $totalLength;
    
    // Generate DRP Start Point (Type 1)
    // Hitung panjang segmen pertama sampai kilometer bulat berikutnya
    $firstSegmentLength = 1000 - (($startChainage * 1000) % 1000);
    if ($firstSegmentLength == 1000) {
        $firstSegmentLength = min(1000, $remainingLength);
    } else {
        $firstSegmentLength = min($firstSegmentLength, $remainingLength);
    }
    
    $drpPoints[] = [
        'province_code' => $data['province_code'],
        'kabupaten_code' => $data['kabupaten_code'],
        'link_no' => $linkNo,
        'drp_num' => $order,
        'chainage' => $currentChainage,
        'drp_order' => $order,
        'drp_length' => $firstSegmentLength,
        'dpr_north_deg' => 0,
        'dpr_north_min' => 0,
        'dpr_north_sec' => 0.00,
        'dpr_east_deg' => 0,
        'dpr_east_min' => 0,
        'dpr_east_sec' => 0.00,
        'drp_type' => '1', // Start point
        'drp_desc' => $this->formatChainage($currentChainage),
        'drp_comment' => $data['drp_comment'] ?? null,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    $currentChainage += $firstSegmentLength / 1000;
    $remainingLength -= $firstSegmentLength;
    $order++;
    
    // Generate Middle Points (Type 3)
    while ($remainingLength > 0) {
        $segmentLength = min(1000, $remainingLength);
        
        $drpPoints[] = [
            'province_code' => $data['province_code'],
            'kabupaten_code' => $data['kabupaten_code'],
            'link_no' => $linkNo,
            'drp_num' => $order,
            'chainage' => $currentChainage,
            'drp_order' => $order,
            'drp_length' => $segmentLength,
            'dpr_north_deg' => 0,
            'dpr_north_min' => 0,
            'dpr_north_sec' => 0.00,
            'dpr_east_deg' => 0,
            'dpr_east_min' => 0,
            'dpr_east_sec' => 0.00,
            'drp_type' => '3', // Middle point
            'drp_desc' => $this->formatChainage($currentChainage),
            'drp_comment' => $data['drp_comment'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        $currentChainage += $segmentLength / 1000;
        $remainingLength -= $segmentLength;
        $order++;
    }
    
    // Generate End Point (Type 2)
    $drpPoints[] = [
        'province_code' => $data['province_code'],
        'kabupaten_code' => $data['kabupaten_code'],
        'link_no' => $linkNo,
        'drp_num' => $order,
        'chainage' => $currentChainage,
        'drp_order' => $order,
        'drp_length' => 0.00,
        'dpr_north_deg' => 0,
        'dpr_north_min' => 0,
        'dpr_north_sec' => 0.00,
        'dpr_east_deg' => 0,
        'dpr_east_min' => 0,
        'dpr_east_sec' => 0.00,
        'drp_type' => '2', // End point
        'drp_desc' => $this->formatChainage($currentChainage),
        'drp_comment' => $data['drp_comment'] ?? null,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    // Insert semua DRP points sekaligus
    DRP::insert($drpPoints);
}

/**
 * Store manual DRP entry
 */
private function storeManualDRP($data)
{
    DRP::create([
        'province_code' => $data['province_code'],
        'kabupaten_code' => $data['kabupaten_code'],
        'link_no' => $data['link_no'],
        'drp_num' => $data['drp_order'], // gunakan drp_order sebagai drp_num
        'chainage' => $this->calculateChainage($data),
        'drp_order' => $data['drp_order'],
        'drp_length' => $data['drp_length'],
        'dpr_north_deg' => $data['dpr_north_deg'] ?? 0,
        'dpr_north_min' => $data['dpr_north_min'] ?? 0,
        'dpr_north_sec' => $data['dpr_north_sec'] ?? 0.00,
        'dpr_east_deg' => $data['dpr_east_deg'] ?? 0,
        'dpr_east_min' => $data['dpr_east_min'] ?? 0,
        'dpr_east_sec' => $data['dpr_east_sec'] ?? 0.00,
        'drp_type' => $data['drp_type'],
        'drp_desc' => $data['drp_desc'] ?? $this->generateAutoDescription($data),
        'drp_comment' => $data['drp_comment'] ?? null,
    ]);
}

/**
 * Calculate chainage based on existing DRP order
 */
private function calculateChainage($data)
{
    if (isset($data['chainage']) && $data['chainage'] != '') {
        return $data['chainage'];
    }
    
    // Auto calculate berdasarkan order dan length
    $previousDRP = DRP::where('link_no', $data['link_no'])
                     ->where('drp_order', '<', $data['drp_order'])
                     ->orderBy('drp_order', 'desc')
                     ->first();
    
    if ($previousDRP) {
        return $previousDRP->chainage + ($previousDRP->drp_length / 1000);
    }
    
    return 0; // Jika order pertama
}

/**
 * Generate auto description based on chainage
 */
private function generateAutoDescription($data)
{
    $chainage = $this->calculateChainage($data);
    return $this->formatChainage($chainage);
}
    
    /**
     * Format chainage ke format deskripsi (misal: 1.240 -> "1+240")
     */
    private function formatChainage($chainage)
    {
        $km = floor($chainage);
        $meter = round(($chainage - $km) * 1000);
        
        return sprintf("%d+%03d", $km, $meter);
    }

    public function edit($drpNum)
    {
        try {
            $drp = DRP::where('drp_num', $drpNum)->firstOrFail();
            $provinsi = Province::orderBy('province_name')->get();
            $kabupaten = Kabupaten::orderBy('kabupaten_name')->get();
            $links = Link::orderBy('link_code')->get();
            $drpTypes = CodeDrpType::orderBy('order')->get();

            return view('pengaturan_jaringan.drp.edit', compact('drp', 'provinsi', 'kabupaten', 'links', 'drpTypes'));

        } catch (\Exception $e) {
            return back()->with('error', 'Data DRP tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $drpNum)
    {
        try {
            $drp = DRP::where('drp_num', $drpNum)->firstOrFail();

            $validated = $request->validate([
                'province_code' => 'required|string|exists:province,province_code',
                'kabupaten_code' => 'required|string|exists:kabupaten,kabupaten_code',
                'link_no' => 'required|string|exists:link,link_no',
                'drp_num' => 'required|string|unique:drp,drp_num,' . $drpNum . ',drp_num',
                'chainage' => 'nullable|numeric',
                'drp_order' => 'nullable|integer',
                'drp_length' => 'nullable|numeric',
                'dpr_north_deg' => 'nullable|integer|min:0|max:90',
                'dpr_north_min' => 'nullable|integer|min:0|max:59',
                'dpr_north_sec' => 'nullable|numeric|min:0|max:59.99',
                'dpr_east_deg' => 'nullable|integer|min:0|max:180',
                'dpr_east_min' => 'nullable|integer|min:0|max:59',
                'dpr_east_sec' => 'nullable|numeric|min:0|max:59.99',
                'drp_type' => 'nullable|string|exists:code_drp_type,code',
                'drp_desc' => 'nullable|string|max:500',
                'drp_comment' => 'nullable|string|max:1000',
            ]);

            $drp->update($validated);

            return redirect()->route('drp.index')
                           ->with('success', 'Data DRP berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($drpNum)
    {
        try {
            $drp = DRP::where('drp_num', $drpNum)->firstOrFail();
            $drp->delete();

            return redirect()->route('drp.index')
                           ->with('success', 'Data DRP berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function destroyAll()
    {
        DRP::query()->delete(); 
        return redirect()->route('drp.index')
            ->with('success', 'Semua data DRP berhasil dihapus.');
    }

    // AJAX Methods untuk halaman create dan index
    public function getDetail(Request $request)
    {
        try {
            $linkNo = $request->get('link_no');
            
            if (!$linkNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link number tidak ditemukan'
                ]);
            }

            // Ambil data DRP berdasarkan link_no dengan relasi
            $drpData = DRP::with(['type', 'province', 'kabupaten', 'link'])
                          ->where('link_no', $linkNo)
                          ->orderBy('drp_order')
                          ->get();

            if ($drpData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data DRP tidak ditemukan untuk ruas ini'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $drpData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getLinks(Request $request)
    {
        try {
            $kabupatenCode = $request->get('kabupaten_code');
            
            $links = Link::whereHas('kabupaten', function($query) use ($kabupatenCode) {
                        $query->where('kabupaten_code', $kabupatenCode);
                    })
                    ->orderBy('link_code')
                    ->get();

            return response()->json([
                'success' => true,
                'data' => $links
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

}