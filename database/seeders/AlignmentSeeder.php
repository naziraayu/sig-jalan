<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\AlignmentImport;
use Maatwebsite\Excel\Facades\Excel;

class AlignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '1024M');
        $path = database_path('seeders/data/Alignment.xlsx');

        if (file_exists($path)) {
            Excel::import(new AlignmentImport, $path);
            $this->command->info('Alignment data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
