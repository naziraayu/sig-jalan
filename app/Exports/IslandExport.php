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
        return Island::select('island_code', 'island_name', 'province_code')->get();
    }

    public function headings(): array
    {
        return ['Kode Pulau', 'Nama Pulau', 'Kode Provinsi'];
    }
}
