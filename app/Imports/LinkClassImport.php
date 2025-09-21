<?php

namespace App\Imports;

use App\Models\LinkClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkClassImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new LinkClass([
            'province_code' => $row['province_code'],
            'kabupaten_code' => $row['kabupaten_code'],
            'link_no'       => $row['link_no'],
            'class'         => $row['class'],
            'kmClass'       => $row['kmclass'],
        ]);
    }
}
