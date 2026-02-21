<?php

namespace App\Exports;

use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class KabupatenExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // ✅ FIXED: Gunakan select() eksplisit agar urutan kolom pasti sesuai heading
        // Sebelumnya pakai Kabupaten::all() yang urutan kolomnya tidak terjamin
        return Kabupaten::select(
            'province_code',
            'kabupaten_code',
            'kabupaten_name',
            'balai_code',
            'island_code',
            'default_kabupaten',
            'stable'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',   // ✅ FIXED: Typo "Kabuapaten_Code" -> "Kabupaten_Code"
            'Kabupaten_Name',
            'Balai_Code',
            'Island_Code',
            'DefaultKabupaten',
            'Stable',
        ];
    }
}