<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeDrainConditionImport;

class CodeDrainConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_DrainCondition.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeDrainConditionImport, $path);
            $this->command->info('CODE_DrainCondition data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
