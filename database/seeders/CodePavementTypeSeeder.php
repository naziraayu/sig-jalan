<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodePavementTypeImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CodePavementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_PavementType.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodePavementTypeImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
