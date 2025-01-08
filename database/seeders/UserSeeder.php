<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'fullname' => 'Admin',
                'phone' => '+6012-3456789',
                'role_id' => 1, // admin role
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Yih Huei',
                'email' => 'user@user.com',
                'fullname' => 'Voter',
                'phone' => '+6019-7725921',
                'role_id' => 2, // user role
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
} 