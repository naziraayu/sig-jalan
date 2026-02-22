<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\LinkClassImport;
use Maatwebsite\Excel\Facades\Excel;


class LinkClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/LinkClass.xlsx');

        if (file_exists($path)) {
            Excel::import(new LinkClassImport, $path);
            $this->command->info('LinkClass data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
