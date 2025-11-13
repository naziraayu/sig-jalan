<?php

namespace Database\Seeders;

use App\Models\RoadCondition;
use Illuminate\Database\Seeder;
use App\Imports\RoadConditionImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoadConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔧 Tambah batas memory & waktu eksekusi
        ini_set('memory_limit', '1G'); 
        ini_set('max_execution_time', '0'); // biar gak timeout

        $path = database_path('seeders/data/RoadCondition.xlsx');

        if (!file_exists($path)) {
            $this->command->error("❌ File not found: $path");
            return;
        }

        try {
            Excel::import(new RoadConditionImport, $path);
            $this->command->info('✅ RoadCondition data imported successfully!');
        } catch (\Throwable $e) {
            $this->command->error('❌ Import failed: ' . $e->getMessage());
        }
    }
}
