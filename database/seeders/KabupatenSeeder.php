<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use Illuminate\Database\Seeder;
use App\Imports\KabupatenImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KabupatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/Kabupaten.xlsx');

        $rows = Excel::toArray([], $file)[0]; // ambil sheet pertama

        $header = array_map('strtolower', $rows[0]); // baris pertama = header
        unset($rows[0]); // hapus header

        foreach ($rows as $row) {
            $rowAssoc = array_combine($header, $row); // mapping kolom -> value

            Kabupaten::updateOrInsert(
                ['kabupaten_code' => $rowAssoc['kabupaten_code']],
                [
                    'province_code'      => $rowAssoc['province_code'],
                    'kabupaten_name'     => $rowAssoc['kabupaten_name'],
                    'balai_code'         => $rowAssoc['balai_code'] ?? null,
                    'island_code'        => $rowAssoc['island_code'] ?? null,
                    'default_kabupaten'  => $rowAssoc['defaultkabupaten'] ?? 0,
                    'stable'             => $rowAssoc['stable'] ?? 1,
                ]
            );
        }
    }
}
