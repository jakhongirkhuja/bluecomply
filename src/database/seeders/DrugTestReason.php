<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrugTestReason extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'PRE-EMPLOYMENT',
            'RANDOM',
            'POST-ACCIDENT',
            'PERIODIC',
            'REASONABLE SUSPICION/CAUSE',
            'FOLLOW-UP',
            'RETURN TO DUTY',
            'PROMOTION',
            'PROBATION',
            'JOB TRANSFER',
            'PRE-SITE ACCESS',
        ];

        foreach ($reasons as $reason) {
            DB::table('drug_test_reasons')->updateOrInsert(
                ['name' => $reason],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
