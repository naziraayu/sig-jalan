<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = config('permissions');

        foreach ($features as $feature => $actions) {
            foreach ($actions as $action) {
                Permission::create([
                    'feature' => $feature,
                    'action' => $action
                ]);
            }
        }
    }
}
