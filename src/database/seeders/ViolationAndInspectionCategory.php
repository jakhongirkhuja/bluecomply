<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolationAndInspectionCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            'Unsafe Driving',
            'HOS Compliance',
            'Driver Fitness',
            'Controlled Substances',
            'Vehicle Maintenance',
            'HM Compliance',
            'Crash Indicator',
        ];

        foreach ($categories as $category) {
            DB::table('violation_categories')->insert([
                'name' => $category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $levels = [
            'Level 1 - Full',
            'Level 2 - Walk Around',
            'Level 3 - Driver Only',
            'Level 4 - Special Study',
            'Level 5 - Terminal',
            'Level 6 - Hazmat Radioactive',
        ];

        foreach ($levels as $level) {
            DB::table('inspection_levels')->insert([
                'name' => $level,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
