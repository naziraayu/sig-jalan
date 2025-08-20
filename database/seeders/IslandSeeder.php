<?php

namespace Database\Seeders;

use App\Imports\IslandImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IslandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Excel::import(new IslandImport, database_path('seeders/data/Island.xlsx'));

        $this->command->info("Import Island.xlsx selesai ✅");
    }
}
