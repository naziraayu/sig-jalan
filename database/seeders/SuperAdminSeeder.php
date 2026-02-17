<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // bikin role Super Admin kalau belum ada
        $role = Role::firstOrCreate(
            ['name' => 'Super Admin']
        );

        // bikin user Super Admin
        $user = User::updateOrCreate(
            ['email' => 'naziraayu2003@gmail.com'],
            [
                'name' => 'Nazira Ayu',
                'password' => Hash::make('password123'),
                'role_id' => $role->id,
            ]
        );

        // kasih semua permission ke Super Admin
        $role->permissions()->sync(\App\Models\Permission::all()->pluck('id'));
    }
}
