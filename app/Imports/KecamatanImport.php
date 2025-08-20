<?php

namespace App\Imports;

use App\Models\Kecamatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KecamatanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Kecamatan([
            'kabupaten_code'   => $row['kabupaten_code'],
            'province_code'    => $row['province_code'],
            'kecamatan_name'   => $row['kecamatan_name'],
            'kecamatan_code'   => $row['kecamatan_code'],
        ]);
    }
}
