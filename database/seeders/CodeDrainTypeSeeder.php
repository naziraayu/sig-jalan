<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\CodeDrainTypeImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CodeDrainTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_DrainType.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeDrainTypeImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
