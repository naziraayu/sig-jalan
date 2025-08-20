<?php

namespace Database\Seeders;

use App\Imports\BalaiImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BalaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Excel::import(new BalaiImport, database_path('seeders/data/Balai.xlsx'));

        $this->command->info("Import Balai.xlsx selesai ✅");
    }
}
