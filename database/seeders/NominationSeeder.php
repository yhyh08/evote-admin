<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NominationSeeder extends Seeder
{
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

        DB::table('candidates')->insert([
            [
                'candidate_name' => 'John Smith',
                'candidate_image' => 'john-smith.jpg',
                'candidate_phone' => '012-3456789',
                'candidate_email' => 'john.smith@example.com',
                'candidate_gender' => 'Male',
                'candidate_ic' => '901201-14-5567',
                'candidate_dob' => '1990-12-01',
                'candidate_address' => '123 Main Street, Kuala Lumpur',
                'nationality' => 'Malaysian',
                'religion' => 'Islam',
                'job' => 'Software Engineer',
                'income' => 5000.00,
                'marriage_status' => 'Single',
                'position' => 'President',
                'short_biography' => 'Experienced leader with 10 years in community service.',
                'manifesto' => 'Committed to technological advancement and community development.',
                'status' => 'Approved',
                'receive_date' => Carbon::now()->subDays(30),
                'approve_date' => Carbon::now()->subDays(15),
                'sign' => 'john-signature.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'candidate_name' => 'Sarah Lee',
                'candidate_image' => 'sarah-lee.jpg',
                'candidate_phone' => '019-8765432',
                'candidate_email' => 'sarah.lee@example.com',
                'candidate_gender' => 'Female',
                'candidate_ic' => '880315-14-5432',
                'candidate_dob' => '1988-03-15',
                'candidate_address' => '456 Park Avenue, Petaling Jaya',
                'nationality' => 'Malaysian',
                'religion' => 'Buddhist',
                'job' => 'Business Consultant',
                'income' => 6500.00,
                'marriage_status' => 'Married',
                'position' => 'Vice President',
                'short_biography' => 'Business strategist with strong community leadership background.',
                'manifesto' => 'Focus on economic growth and sustainable development.',
                'status' => 'Pending',
                'receive_date' => Carbon::now()->subDays(20),
                'approve_date' => Carbon::now()->subDays(10),
                'sign' => 'sarah-signature.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
} 