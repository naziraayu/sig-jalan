<?php

namespace App\Exports;

use App\Models\Link;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LinkExport implements FromCollection, WithHeadings
{
    /**
     * Ambil data untuk diexport
     */
    public function collection()
    {
        return Link::select(
            'link_no',
            'province_code',
            'kabupaten_code',
            'link_code',
            'link_name',
            'status',
            'function',
            'class',
            'project_number',
            'access_status',
            'link_length_official',
            'link_length_actual',
            'WTI',
            'MCA2',
            'MCA3',
            'MCA4',
            'MCA5',
            'CUMESA',
            'ESA0',
            'AADT'
        )->get();
    }

    /**
     * Tambah heading (judul kolom di Excel)
     */
    public function headings(): array
    {
        return [
            'Link_No',
            'Province_Code',
            'Kabupaten_Code',
            'Link_Code',
            'Link_Name',
            'Status',
            'Function',
            'Class',
            'Project_Number',
            'Access_Status',
            'Link_Length_Official',
            'Link_Length_Actual',
            'WTI',
            'MCA2',
            'MCA3',
            'MCA4',
            'MCA5',
            'CUMESA',
            'ESA0',
            'AADT'
        ];
    }
}
