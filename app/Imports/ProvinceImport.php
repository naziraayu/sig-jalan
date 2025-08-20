<?php

namespace App\Imports;

use App\Models\Province;
use Maatwebsite\Excel\Concerns\ToModel;

class ProvinceImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip baris header
        if ($row[0] === 'Province_Code' || $row[0] === null) {
            return null;
        }

        return new Province([
            'province_code'    => $row[0],
            'province_name'    => $row[1],
            'default_province' => isset($row[2]) ? (int)$row[2] : 0,
            'stable'           => isset($row[3]) ? (int)$row[3] : null,
        ]);
    }
}
