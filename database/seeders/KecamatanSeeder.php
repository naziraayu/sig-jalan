<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KecamatanSeeder extends Seeder 
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $file = database_path('seeders/data/Kecamatan.xlsx');

        // ambil sheet pertama
        $rows = Excel::toArray([], $file)[0];

        // header row (baris pertama)
        $header = array_map('strtolower', $rows[0]);
        unset($rows[0]);

        foreach ($rows as $row) {
            if (count($row) < 4) {
                continue;
            }

            $rowAssoc = array_combine($header, $row);

            Kecamatan::updateOrInsert(
                ['kecamatan_code' => $rowAssoc['kecamatan_code']],
                [
                    'province_code'  => $rowAssoc['province_code'],
                    'kabupaten_code' => $rowAssoc['kabupaten_code'],
                    'kecamatan_name' => $rowAssoc['kecamatan_name'],
                ]
            );
        }
    }
}
