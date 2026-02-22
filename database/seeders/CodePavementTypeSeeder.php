<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodePavementTypeImport;

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
            $this->command->info('CODE_PavementType data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
