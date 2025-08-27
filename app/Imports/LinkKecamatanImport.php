<?php

namespace App\Imports;

use App\Models\LinkKecamatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkKecamatanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new LinkKecamatan([
            'province_code'        => $row['province_code'],
            'kabupaten_code'       => $row['kabupaten_code'],
            'link_no'              => $row['link_no'],
            'drp_from'             => $row['drp_from'],
            'drp_to'               => $row['drp_to'],
            'kecamatan_code'       => $row['kecamatan_code'],
        ]);
    }
}
