<?php

namespace App\Exports;

use App\Models\LinkClass;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class LinkClassExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // ✅ FIXED: Ganti 'link_no' -> 'link_id' karena di model LinkClass
        // $fillable menggunakan 'link_id', bukan 'link_no'
        // Sebelumnya select 'link_no' yang tidak ada di $fillable -> menghasilkan null
        return LinkClass::select(
            'province_code',
            'kabupaten_code',
            'link_id',      // ✅ FIXED: was 'link_no'
            'class',
            'kmClass'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',
            'Link_Id',          // ✅ FIXED: was 'Link_No'
            'Class',
            'KmClass',
        ];
    }
}