<?php

namespace App\Exports;

use App\Models\Kecamatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KecamatanExport implements FromCollection, WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Kecamatan::all();
    }
    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',
            'Kecamatan_Code',
            'Kecamatan_Name',
        ];
    }
}
