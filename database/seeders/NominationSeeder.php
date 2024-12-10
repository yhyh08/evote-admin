<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nominations')->insert([
            [
                'nominee_name' => 'John Doe',
                'nominee_phone' => '123-456-7890',
                'nominee_email' => 'john.doe@example.com',
                'status' => 'pending',
                'status_date' => now(),
                'reason' => 'Outstanding community service',
                'election_id' => 1,
                'candidate_id' => 1,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nominee_name' => 'Jane Smith',
                'nominee_phone' => '098-765-4321',
                'nominee_email' => 'jane.smith@example.com',
                'status' => 'approved',
                'status_date' => now(),
                'reason' => 'Exemplary leadership',
                'election_id' => 1,
                'candidate_id' => 2,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 