<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodeImpassableImport;

class CodeImpassableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_Impassable.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeImpassableImport, $path);
            $this->command->info('CODE_Impassable data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
