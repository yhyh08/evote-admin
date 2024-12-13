<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElectionCommitteeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('election_committees')->insert([
            [
                'com_name' =>'Committees 1',
                'com_phone' =>'016-3456789',
                'com_email' =>'committees1@example.com',
                'isApprove' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'com_name' =>'Committees 2',
                'com_phone' =>'012-3456789',
                'com_email' =>'committees2@example.com',
                'isApprove' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
} 