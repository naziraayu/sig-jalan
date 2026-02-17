<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\RoadInventoryImport;
use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoadInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/RoadInventory.xlsx');

        if (file_exists($path)) {
            Excel::import(new RoadInventoryImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
