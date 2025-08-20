<?php

namespace App\Imports;

use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KabupatenImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       return new Kabupaten([
            'kabupaten_code'   => $row['kabupaten_code'],
            'province_code'    => $row['province_code'],
            'kabupaten_name'   => $row['kabupaten_name'],
            'balai_code'       => $row['balai_code'] ?? null,
            'island_code'      => $row['island_code'] ?? null,
            'default_kabupaten'=> isset($row['defaultkabupaten']) ? (bool)$row['defaultkabupaten'] : false,
            'stable'           => $row['stable'] ?? null,
        ]);
    }
}
