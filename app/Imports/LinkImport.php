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
        if (empty($row['province_code'])) {
            return null;
        }
        return new Link([
            'year'                 => $row['year'] ?? $row['Year'] ?? null,
            'province_code'        => $row['province_code'] ?? $row['Province_Code'] ?? null,
            'kabupaten_code'       => $row['kabupaten_code'] ?? $row['Kabupaten_Code'] ?? null,
            'link_no'              => $row['link_no'] ?? $row['Link_No'] ?? null,
            'link_master_id'       => $row['link_master_id'] ?? $row['Link_Master_Id'] ?? null,
            'link_code'            => $row['link_code'] ?? $row['Link_Code'] ?? null,
            'status'               => $row['status'] ?? $row['Status'] ?? null,
            'function'             => $row['function'] ?? $row['Function'] ?? null,
            'class'                => $row['class'] ?? $row['Class'] ?? null,
            'project_number'       => $row['project_number'] ?? $row['Project_Number'] ?? null,
            'access_status'        => $row['accessstatus'] ?? $row['AccessStatus'] ?? null,
            'link_length_official' => $row['link_length_official'] ?? $row['Link_Length_Official'] ?? 0,
            'link_length_actual'   => $row['link_length_actual'] ?? $row['Link_Length_Actual'] ?? 0,
            'WTI'                  => $row['wti'] ?? $row['WTI'] ?? 0,
            'MCA2'                 => $row['mca2'] ?? $row['MCA2'] ?? 0,
            'MCA3'                 => $row['mca3'] ?? $row['MCA3'] ?? 0,
            'MCA4'                 => $row['mca4'] ?? $row['MCA4'] ?? 0,
            'MCA5'                 => $row['mca5'] ?? $row['MCA5'] ?? 0,
            'CUMESA'               => $row['cumesa'] ?? $row['CUMESA'] ?? 0,
            'ESA0'                 => $row['esa0'] ?? $row['ESA0'] ?? 0,
            'AADT'                 => $row['aadt'] ?? $row['AADT'] ?? 0,
        ]);
    }
}
