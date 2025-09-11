<?php

namespace App\Imports;

use App\Models\Kabupaten;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KabupatenImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row): void
    {
        // ambil data sebagai array
        $data = $row->toArray();  

        Kabupaten::updateOrCreate(
            ['kabupaten_code' => $data['kabupaten_code'] ?? null], // primary key
            [
                'province_code'     => $data['province_code'] ?? null,
                'kabupaten_name'    => $data['kabupaten_name'] ?? null,
                'balai_code'        => $data['balai_code'] ?? null,
                'island_code'       => $data['island_code'] ?? null,
                'default_kabupaten' => !empty($data['default_kabupaten']),
                'stable'            => $data['stable'] ?? 0,
            ]
        );
    }

}
