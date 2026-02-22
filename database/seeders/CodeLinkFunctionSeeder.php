<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeLinkFunctionImport;

class CodeLinkFunctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_LinkFunction.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeLinkFunctionImport, $path);
            $this->command->info('CODE_LinkFunction data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
