<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\CodeLandUseImport;
use Maatwebsite\Excel\Facades\Excel;

class CodeLandUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_LandUse.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeLandUseImport, $path);
            $this->command->info('CODE_LandUse data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
