<?php

namespace App\Exports;

use App\Models\LinkMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // ✅ FIXED: Tambahkan WithHeadings

class LinkMasterExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // ✅ FIXED: Gunakan select() eksplisit agar urutan kolom sesuai heading
        return LinkMaster::select(
            'link_no',
            'link_name',
            'province_code',
            'kabupaten_code'
        )->get();
    }

    // ✅ FIXED: Tambahkan heading agar file Excel punya baris header
    // Sebelumnya tidak ada heading -> saat reimport dengan WithHeadingRow,
    // baris data pertama dijadikan header dan field semua null
    public function headings(): array
    {
        return [
            'Link_No',
            'Link_Name',
            'Province_Code',
            'Kabupaten_Code',
        ];
    }
}