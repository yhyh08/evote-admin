<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organizations')->insert([
            [
                'org_name' => 'Tech Innovators',
                'org_desc' => 'A leading tech company specializing in innovative solutions.',
                'org_cat' => 'Technology',
                'org_address' => '123 Tech Lane, Silicon Valley, CA',
                'org_img' => 'tech_innovators.png',
                'pic_name' => 'John Doe',
                'pic_phone' => '123-456-7890',
                'pic_email' => 'johndoe@techinnovators.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_name' => 'Green Earth',
                'org_desc' => 'An organization dedicated to environmental conservation.',
                'org_cat' => 'Environment',
                'org_address' => '456 Green St, Portland, OR',
                'org_img' => 'green_earth.png',
                'pic_name' => 'Jane Smith',
                'pic_phone' => '987-654-3210',
                'pic_email' => 'janesmith@greenearth.org',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 