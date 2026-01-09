<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RejectionReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rejection_reasons')->insert([
            [
                'name' => 'Lack of Experience',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Does Not Meet Company Criteria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Applicant Not Interested',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Failed Background Requirements',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Failed Communication',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Position Filled',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
