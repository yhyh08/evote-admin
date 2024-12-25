<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'admin',
        //     'email' => 'admin@admin.com',
        //     'fullname' => 'James Tan',
        //     'phone' => '0123456789',
        //     'role' => 1,
        //     'password' => Hash::make('password')
        // ]);

        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            ElectionCommitteeSeeder::class,
            ElectionSeeder::class,
            OrganizationSeeder::class,
            NominationSeeder::class,
            CandidateDocsSeeder::class,
        ]);
    }
}
