<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SituationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('citation_categories')->insert([
            ['name' => 'Speeding'],
            ['name' => 'Overweight'],
            ['name' => 'Equipment'],
            ['name' => 'Lane Violation'],
            ['name' => 'Reckless / Careless Driving'],
            ['name' => 'Permit / Routing Violation'],
            ['name' => 'Seatbelt'],
            ['name' => 'Other'],
        ]);
    }
}
