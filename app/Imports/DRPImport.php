<?php

namespace App\Imports;

use App\Models\DRP;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DRPImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new DRP([
            'province_code'   => $row['province_code'],
            'kabupaten_code'  => $row['kabupaten_code'],
            'link_no'         => $row['link_no'],
            'drp_num'         => $row['drp_num'],
            'chainage'        => $row['chainage'],
            'drp_order'       => $row['drp_order'],
            'drp_length'      => $row['drp_length'],
            'dpr_north_deg'   => $row['dpr_north_deg'],
            'dpr_north_min'   => $row['dpr_north_min'],
            'dpr_north_sec'   => $row['dpr_north_sec'],
            'dpr_east_deg'    => $row['dpr_east_deg'],
            'dpr_east_min'    => $row['dpr_east_min'],
            'dpr_east_sec'    => $row['dpr_east_sec'],
            'drp_type'        => $row['drp_type'],
            'drp_desc'        => $row['drp_desc'],
            'drp_comment'     => $row['drp_comment'],
        ]);
    }
}
