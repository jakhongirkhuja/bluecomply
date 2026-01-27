<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChallengeCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Carrier Information (MCS-150)'],
            ['name' => 'Operating Authority (OP-1, OP-2)'],
            ['name' => 'Insurance Information'],
            ['name' => 'Interstate Carrier - Unregistered (No US DOT#)'],
            ['name' => 'Compliance Review'],
            ['name' => 'Safety Audit'],
            ['name' => 'Enforcement Action'],
            ['name' => 'Other'],
        ];

        DB::table('challenge_categories')->insert($categories);
    }
}
