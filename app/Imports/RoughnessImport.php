<?php

namespace App\Imports;

use App\Models\Roughness;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RoughnessImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Roughness([
            'year'             => $row['year'] ?? null,
            'province_code'    => $row['province_code'] ?? null,
            'kabupaten_code'   => $row['kabupaten_code'] ?? null,
            'link_no'          => $row['link_no'] ?? null,
            'chainage_from'    => $row['chainagefrom'] ?? null,
            'chainage_to'      => $row['chainageto'] ?? null,
            'drp_from'         => $row['drp_from'] ?? null,
            'offset_from'      => $row['offset_from'] ?? null,
            'drp_to'           => $row['drp_to'] ?? null,
            'offset_to'        => $row['offset_to'] ?? null,
            'iri'              => $row['iri'] ?? null,
            'analysis_base_year' => $row['analysisbaseyear'] ?? false,
        ]);
    }
}
