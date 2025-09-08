<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'naziraayu2003@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // ganti sesuai kebutuhan
                'role_id' => 1, // Atur sesuai kebutuhan
            ]
        );
    }
}
