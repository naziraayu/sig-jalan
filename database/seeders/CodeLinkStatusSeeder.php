<?php

namespace Database\Seeders;

use App\Models\CodeLinkStatus;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeLinkStatusImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CodeLinkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_LinkStatus.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeLinkStatusImport, $path);
            $this->command->info('Link data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
