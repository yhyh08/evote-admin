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
    public function run()
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'fullname' => 'James Tan',
            'phone' => '0123456789',
            'password' => Hash::make('password')
        ]);

        $this->call([
            ElectionCommitteeSeeder::class,
            ElectionSeeder::class,
            OrganizationSeeder::class,
            NominationSeeder::class,
            CandidateDocsSeeder::class,
        ]);
    }
}
