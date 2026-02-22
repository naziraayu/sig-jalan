<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\LinkMasterImport;
use Maatwebsite\Excel\Facades\Excel;

class LinkMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file Excel (taruh di database/seeders/excel/link.xlsx)
        $path = database_path('seeders/data/LinkMaster.xlsx');

        if (file_exists($path)) {
            Excel::import(new LinkMasterImport, $path);
            $this->command->info('LinkMaster data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
