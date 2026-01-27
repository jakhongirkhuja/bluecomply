<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChallengeType extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Crash'],
            ['name' => 'Inspection'],
            ['name' => 'Other'],
        ];

        DB::table('challenge_types')->insert($categories);
    }
}
