<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\CodeDRPTypeImport;
use Maatwebsite\Excel\Facades\Excel;

class CodeDRPTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/CODE_DRPType.xlsx');

        if (file_exists($path)) {
            Excel::import(new CodeDRPTypeImport, $path);
            $this->command->info('CODE_DRPType data imported from Excel successfully!');
        } else {
            $this->command->error("File not found: $path");
        }
    }
}
