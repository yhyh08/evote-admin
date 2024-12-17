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
                'nominee_name' => 'Nominee 1',
                'nominee_phone' => '098-765-4321',
                'nominee_email' => 'nominee1@example.com',
                'reason' => 'Exemplary leadership',
                'election_id' => 1,
                'candidate_id' => 1,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nominee_name' => 'Nominee 2',
                'nominee_phone' => '123-456-7890',
                'nominee_email' => 'nominee2@example.com',
                'reason' => 'Outstanding community service',
                'election_id' => 1,
                'candidate_id' => 2,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nominee_name' => 'Nominee 3',
                'nominee_phone' => '098-765-4321',
                'nominee_email' => 'nominee3@example.com',
                'reason' => 'Leadership',
                'election_id' => 2,
                'candidate_id' => 3,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nominee_name' => 'Nominee 4',
                'nominee_phone' => '098-765-4321',
                'nominee_email' => 'nominee4@example.com',
                'reason' => 'Leadership',
                'election_id' => 2,
                'candidate_id' => 4,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('candidates')->insert([
            [
                'candidate_name' => 'Candidate 1',
                'candidate_image' => 'candidate1.jpg',
                'candidate_phone' => '012-3456789',
                'candidate_email' => 'candidate1@example.com',
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
                'status' => 'Pending',
                'reason' => '',
                'receive_date' => Carbon::now()->subDays(30),
                'approve_date' => Carbon::now()->subDays(15),
                'sign' => 'candidate1.jpg',
                'nominee_id' => json_encode([1, 2]),
                'cand_doc_id' => json_encode([1,2]),
                'election_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'candidate_name' => 'Candidate 2',
                'candidate_image' => '',
                'candidate_phone' => '019-8765432',
                'candidate_email' => 'candidate2@example.com',
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
                'reason' => '',
                'receive_date' => Carbon::now()->subDays(20),
                'approve_date' => Carbon::now()->subDays(10),
                'sign' => 'candidate2.jpg',
                'nominee_id' => json_encode([3]),
                'cand_doc_id' => json_encode([3,4]),
                'election_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'candidate_name' => 'Candidate 3',
                'candidate_image' => '',
                'candidate_phone' => '019-8765432',
                'candidate_email' => 'candidate3@example.com',
                'candidate_gender' => 'Male',
                'candidate_ic' => '810315-14-5432',
                'candidate_dob' => '1981-03-15',
                'candidate_address' => '456 Park Avenue, Petaling Jaya',
                'nationality' => 'Malaysian',
                'religion' => 'Buddhist',
                'job' => 'IT Consultant',
                'income' => 6500.00,
                'marriage_status' => 'Married',
                'position' => 'Secretary',
                'short_biography' => 'Business strategist with strong community leadership background.',
                'manifesto' => 'Focus on economic growth and sustainable development.',
                'status' => 'Pending',
                'reason' => '',
                'receive_date' => Carbon::now()->subDays(20),
                'approve_date' => Carbon::now()->subDays(10),
                'sign' => 'candidate3.jpg',
                'nominee_id' => json_encode([4]),
                'cand_doc_id' => json_encode([5,6]),
                'election_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'candidate_name' => 'Candidate 4',
                'candidate_image' => '',
                'candidate_phone' => '012-8765432',
                'candidate_email' => 'candidate4@example.com',
                'candidate_gender' => 'Male',
                'candidate_ic' => '900315-14-5432',
                'candidate_dob' => '1990-03-15',
                'candidate_address' => '910 Park Avenue, Petaling Jaya',
                'nationality' => 'Malaysian',
                'religion' => 'Buddhist',
                'job' => 'IT Consultant',
                'income' => 2500.00,
                'marriage_status' => 'Married',
                'position' => 'Secretary',
                'short_biography' => 'Business strategist with strong community leadership background.',
                'manifesto' => 'Focus on economic growth and sustainable development.',
                'status' => 'Pending',
                'reason' => '',
                'receive_date' => Carbon::now()->subDays(20),
                'approve_date' => Carbon::now()->subDays(10),
                'sign' => 'candidate4.jpg',
                'nominee_id' => json_encode([4]),
                'cand_doc_id' => json_encode([1,4]),
                'election_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
} 