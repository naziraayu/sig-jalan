<?php

namespace App\Imports;

use App\Models\Link;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class LinkImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       return new Link([
            'link_no'              => $row['link_no'],
            'province_code'        => $row['province_code'],
            'kabupaten_code'       => $row['kabupaten_code'],
            'link_code'            => $row['link_code'],
            'link_name'            => $row['link_name'],
            'status'               => $row['status'],
            'function'             => $row['function'],
            'class'                => $row['class'],
            'project_number'       => $row['project_number'],
            'access_status'        => $row['accessstatus'] ?? null,
            'link_length_official' => $row['link_length_official'],
            'link_length_actual'   => $row['link_length_actual'],
            'WTI'                  => $row['wti'],
            'MCA2'                 => $row['mca2'],
            'MCA3'                 => $row['mca3'],
            'MCA4'                 => $row['mca4'],
            'MCA5'                 => $row['mca5'],
            'CUMESA'               => $row['cumesa'],
            'ESA0'                 => $row['esa0'],
            'AADT'                 => $row['aadt'],
        ]);
    }
}
