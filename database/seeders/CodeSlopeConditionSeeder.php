<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeSlopeConditionImport;

class CodeSlopeConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_SlopeCondition.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeSlopeConditionImport, $path);
            $this->command->info('CODE_SlopeCondition data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
