<?php

namespace App\Imports;

use App\Models\LinkMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkMasterImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new LinkMaster([
            'link_no'              => $row['link_no'],
            'link_name'            => $row['link_name'],
            'province_code'        => $row['province_code'],
            'kabupaten_code'       => $row['kabupaten_code'],
        ]);
    }
}
