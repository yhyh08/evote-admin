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
                'document' => 'documents/candidate_7/1736158672_wl.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 2,
                'candidate_id' => 1,
                'document' => 'documents/candidate_7/1736158673_my.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 3,
                'candidate_id' => 2,
                'document' => 'documents/1736156639_Profile.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 4,
                'candidate_id' => 2,
                'document' => 'documents/candidate_7/1736158672_vv.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 5,
                'candidate_id' => 3,
                'document' => 'documents/candidate_7/1736158672_wl.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 6,
                'candidate_id' => 3,
                'document' => 'documents/candidate_7/1736158673_my.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 7,
                'candidate_id' => 4,
                'document' => 'documents/candidate_7/1736158672_wl.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cand_doc_id' => 8,
                'candidate_id' => 4,
                'document' => 'documents/candidate_7/1736158673_my.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
} 