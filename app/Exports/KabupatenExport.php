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
        return Kabupaten::all();
    }
     public function headings(): array
    {
        return [
            'Province_Code', 
            'Kabuapaten_Code', 
            'Kabupaten_Name',
            'Balai_Code',
            'Island_Code',
            'DefaultKabupaten',
            'Stable'
        ];
    }
}
