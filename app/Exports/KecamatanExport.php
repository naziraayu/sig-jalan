<?php

namespace App\Exports;

use App\Models\Kecamatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KecamatanExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Kecamatan::select('province_code', 'kabupaten_code', 'kecamatan_code', 'kecamatan_name')->get();
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
