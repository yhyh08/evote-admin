<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElectionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('elections')->insert([
            [
                'election_topic' => 'Student Council Election 2024',
                'type' => 0,
                'position' => 'President',
                'description' => 'Annual election for selecting student council representatives',
                'start_date' => Carbon::now()->addDays(16),
                'end_date' => Carbon::now()->addDays(29),
                'nominate_period_start' => Carbon::now(),
                'nominate_period_end' => Carbon::now()->addDays(15),
                'result_release_date' => Carbon::now()->addDays(36),
                'status' => 0,
                'org_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'election_topic' => 'Faculty Board Election 2024',
                'type' => 1,
                'position' => 'Member',
                'description' => 'Election for faculty board members',
                'start_date' => Carbon::now()->addDays(60),
                'end_date' => Carbon::now()->addDays(65),
                'nominate_period_start' => Carbon::now()->addDays(46),
                'nominate_period_end' => Carbon::now()->addDays(59),
                'result_release_date' => Carbon::now()->addDays(66),
                'status' => 1,
                'org_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
} 