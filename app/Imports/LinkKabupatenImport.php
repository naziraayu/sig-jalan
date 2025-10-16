<?php

namespace App\Imports;

use App\Models\LinkKabupaten;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkKabupatenImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new LinkKabupaten([
            'province_code'        => $row['province_code'],
            'kabupaten_code'       => $row['kabupaten_code'],
            'link_id'              => $row['link_id'],
            'drp_from'             => $row['drp_from'],
            'drp_to'               => $row['drp_to'],
            'kabupaten'            => $row['kabupaten'], // Assuming kabupaten is also a field in the import
        ]);
    }
}
