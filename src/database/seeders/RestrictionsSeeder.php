<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestrictionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restrictions = [
            ['code' => 'A', 'name' => 'No Tractor-Trailer (Combination Vehicles)'],
            ['code' => 'B', 'name' => 'No Bus'],
            ['code' => 'C', 'name' => 'No Air Brakes'],
            ['code' => 'D', 'name' => 'No Passenger Vehicle'],
            ['code' => 'E', 'name' => 'No School Bus'],
            ['code' => 'F', 'name' => 'No Tractor-Trailer (Manual Transmission)'],
            ['code' => 'G', 'name' => 'Daylight Only'],
            ['code' => 'H', 'name' => 'No Hazardous Materials'],
            ['code' => 'K', 'name' => 'Intrastate Only'],
            ['code' => 'L', 'name' => 'No Air Brake Endorsement'],
            ['code' => 'M', 'name' => 'Limited to Farm Vehicle'],
            ['code' => 'N', 'name' => 'No Tank Vehicle'],
            ['code' => 'O', 'name' => 'No Passenger Vehicle'],
        ];

        DB::table('restriction_types')->insert($restrictions);
    }
}
