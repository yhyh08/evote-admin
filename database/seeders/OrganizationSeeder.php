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
                'org_address' => '123 Tech Lane, Selangor',
                'org_website' => 'www.techinnovators.com',
                'org_email' => 'info@techinnovators.com',
                'org_size' => '100-500',
                'org_img' => 'tech_innovators.png',
                'pic_name' => 'John Doe',
                'pic_phone' => '012-7536549',
                'pic_email' => 'johndoe@techinnovators.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_name' => 'Green Earth',
                'org_desc' => 'An organization dedicated to environmental conservation.',
                'org_cat' => 'Environment',
                'org_address' => 'Kuala Lumpur',
                'org_website' => 'www.greenearth.org',
                'org_email' => 'info@greenearth.org',
                'org_size' => '100-500',
                'org_img' => 'green_earth.png',
                'pic_name' => 'Jane Smith',
                'pic_phone' => '012-7936549',
                'pic_email' => 'janesmith@greenearth.org',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 