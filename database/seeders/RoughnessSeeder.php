<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\RoughnessImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoughnessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/Roughness.xlsx');

        if (file_exists($path)) {
            Excel::import(new RoughnessImport, $path);
            $this->command->info('Roughness data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
