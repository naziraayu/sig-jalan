<?php

namespace Database\Seeders;

use App\Imports\DRPImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DRPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/DRP.xlsx');

        if (file_exists($path)) {
            Excel::import(new DRPImport, $path);
            $this->command->info('DRP data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
