<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Imports\ProvinceImport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Excel::import(new ProvinceImport, database_path('seeders/data/Province.xlsx'));

        $this->command->info("Import Province.xlsx selesai ✅");
    }
}
