<?php

namespace App\Exports;

use App\Models\LinkKecamatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LinkKecamatanExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return LinkKecamatan::select(
            'province_code',
            'kabupaten_code',
            'link_master_id',
            'drp_from',
            'drp_to',
            'kecamatan_code'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',
            'Link_Master_Id',
            'DRP_From',
            'DRP_To',
            'Kecamatan_Code',
        ];
    }
}