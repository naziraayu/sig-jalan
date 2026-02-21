<?php

namespace App\Exports;

use App\Models\LinkKecamatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // ✅ FIXED: Tambahkan WithHeadings

class LinkKecamatanExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // ✅ FIXED: Gunakan select() eksplisit agar urutan kolom sesuai heading
        return LinkKecamatan::select(
            'province_code',
            'kabupaten_code',
            'link_id',
            'drp_from',
            'drp_to',
            'kecamatan_code'
        )->get();
    }

    // ✅ FIXED: Tambahkan heading agar file Excel punya baris header
    // Sebelumnya tidak ada heading -> saat reimport dengan WithHeadingRow,
    // baris data pertama dijadikan header dan field semua null
    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',
            'Link_Id',
            'DRP_From',
            'DRP_To',
            'Kecamatan_Code',
        ];
    }
}