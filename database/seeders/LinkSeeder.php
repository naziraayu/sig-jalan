<?php

namespace Database\Seeders;

use App\Imports\LinkImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Path ke file Excel (taruh di database/seeders/excel/links.xlsx)
        $path = database_path('seeders/data/Link.xlsx');

        if (file_exists($path)) {
            Excel::import(new LinkImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
