<?php

namespace App\Exports;

use App\Models\Balai;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class BalaiExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Balai::select('balai_code', 'balai_name', 'province_code')->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code',
            'Balai_Code',
            'Balai_Name',
        ];
    }
}
