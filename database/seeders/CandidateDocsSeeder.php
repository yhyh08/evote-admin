<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CandidateDocsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('candidate_docs')->insert([
            [
                'cand_doc_id' => 1,
                'candidate_id' => 1,
                'document' => 'Profile.pdf',
                'description' => 'Candidate Personal Profile',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 2,
                'candidate_id' => 1,
                'document' => 'IC.jpg',
                'description' => 'Identity Card',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 3,
                'candidate_id' => 2,
                'document' => 'Resume.pdf',
                'description' => 'Professional Resume',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 4,
                'candidate_id' => 2,
                'document' => 'Certification.pdf',
                'description' => 'Professional Certifications',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 5,
                'candidate_id' => 3,
                'document' => 'Education.pdf',
                'description' => 'Educational Certificates',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 6,
                'candidate_id' => 3,
                'document' => 'Experience.pdf',
                'description' => 'Work Experience Documents',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 7,
                'candidate_id' => 4,
                'document' => 'school.pdf',
                'description' => 'School Documents',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 8,
                'candidate_id' => 4,
                'document' => 'FYP.pdf',
                'description' => 'Final Year Project Documents',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
} 