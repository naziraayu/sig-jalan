<?php

namespace Database\Seeders;

use App\Models\RoadCondition;
use Illuminate\Database\Seeder;
use App\Imports\RoadConditionImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoadConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/RoadCondition.xlsx');

        if (file_exists($path)) {
            Excel::import(new RoadConditionImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
