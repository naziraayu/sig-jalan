<?php

namespace Database\Seeders;

use App\Imports\LinkMasterImport;
use App\Models\Link;
use App\Models\LinkMaster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PopulateLinkMasterSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/Link.xlsx');

        if (file_exists($path)) {
            Excel::import(new LinkMasterImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}