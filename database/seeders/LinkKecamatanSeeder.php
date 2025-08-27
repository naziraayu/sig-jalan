<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\LinkKecamatanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LinkKecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/LinkKecamatan.xlsx');

        if (file_exists($path)) {
            Excel::import(new LinkKecamatanImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
