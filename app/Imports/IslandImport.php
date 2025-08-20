<?php

namespace App\Imports;

use App\Models\Island;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IslandImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
         return new Island([
            'province_code' => $row['province_code'], // dari "Province_Code"
            'island_code'    => $row['island_code'],    // dari "Balai_Code"
            'island_name'    => $row['island_name'],    // dari "Balai_Name"
        ]);
    }
}
