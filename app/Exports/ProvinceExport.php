<?php

namespace App\Exports;

use App\Models\Province;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProvinceExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Province::select('province_code', 'province_name', 'default_province', 'stable')
            ->get()
            ->map(function ($province) {
                return [
                    'province_code' => $province->province_code,
                    'province_name' => $province->province_name,
                    'default_province' => $province->default_province ? 'Ya' : 'Tidak',
                    'stable' => $province->stable ? 'Stable' : 'Unstable',
                ];
            });
    }
    public function headings(): array
    {
        return [
            'Province_Code',
            'Province_Name',
            'DefaultProvince',
            'Stable',
        ];
    }
}
