<?php

namespace App\Exports;

use App\Models\LinkClass;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class LinkClassExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return LinkClass::select(
            'province_code',
            'kabupaten_code',
            'link_no',
            'class',
            'kmClass'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',
            'Link_No',
            'Class',
            'KmClass',
        ];
    }
}
