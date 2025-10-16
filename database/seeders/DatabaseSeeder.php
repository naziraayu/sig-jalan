<?php

namespace Database\Seeders;

use App\Models\CodeLinkStatus;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // panggil semua seeder di sini
        $this->call([
            // RolesSeeder::class,
            PermissionSeeder::class,
            SuperAdminSeeder::class,
            ProvinceSeeder::class,
            BalaiSeeder::class,
            IslandSeeder::class,
            KabupatenSeeder::class,
            KecamatanSeeder::class,
            CodeLinkStatusSeeder::class,
            CodeLinkFunctionSeeder::class,
            CodeLinkClassSeeder::class,
            PopulateLinkMasterSeeder::class,
            LinkSeeder::class,
            CodeDRPTypeSeeder::class,
            DRPSeeder::class,
            LinkKabupatenSeeder::class,
            LinkKecamatanSeeder::class, 
            // AlignmentSeeder::class,
            CodePavementTypeSeeder::class,
            CodeDrainTypeSeeder::class,
            CodeTerrainSeeder::class,
            CodeLandUseSeeder::class,
            CodeImpassableSeeder::class,
            RoadInventorySeeder::class,
            CodeShoulderConditionSeeder::class,
            CodeDrainConditionSeeder::class,
            CodeSlopeConditionSeeder::class,
            CodeFoothpathConditionSeeder::class,
            // RoadConditionSeeder::class,
            // RoughnessSeeder::class,
        ]);
    }
}
