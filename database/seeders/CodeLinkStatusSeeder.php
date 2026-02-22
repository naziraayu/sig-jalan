<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeLinkStatusImport;

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
            $this->command->info('CODE_LinkStatus data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
