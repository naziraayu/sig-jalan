<?php

namespace App\Imports;

use App\Models\Balai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BalaiImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Balai([
            'province_code' => $row['province_code'], // dari "Province_Code"
            'balai_code'    => $row['balai_code'],    // dari "Balai_Code"
            'balai_name'    => $row['balai_name'],    // dari "Balai_Name"
        ]);
    }
}
