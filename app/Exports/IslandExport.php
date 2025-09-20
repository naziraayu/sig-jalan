<?php

namespace App\Exports;

use App\Models\Island;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class IslandExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function collection()
    {
        return Island::select('province_code', 'island_code', 'island_name', )->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code', 
            'Island_Code', 
            'Island_Name'
        ];
    }
}
