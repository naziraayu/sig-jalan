<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeShoulderConditionImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CodeShoulderConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_ShoulderCondition.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeShoulderConditionImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
