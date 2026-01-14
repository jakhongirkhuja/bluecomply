<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EndorsementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $endorsements = [
            [
                'code' => 'H',
                'name' => 'Hazardous Materials',
                'description' => 'Authorization to transport hazardous materials (TSA background check required).',
            ],
            [
                'code' => 'N',
                'name' => 'Tank Vehicle',
                'description' => 'Authorization to operate tank vehicles.',
            ],
            [
                'code' => 'T',
                'name' => 'Double / Triple Trailers',
                'description' => 'Authorization to pull double or triple trailers.',
            ],
            [
                'code' => 'P',
                'name' => 'Passenger',
                'description' => 'Authorization to operate passenger vehicles.',
            ],
            [
                'code' => 'S',
                'name' => 'School Bus',
                'description' => 'Authorization to operate school buses.',
            ],
            [
                'code' => 'X',
                'name' => 'Hazmat + Tanker',
                'description' => 'Combination of Hazmat (H) and Tank Vehicle (N).',
            ],
        ];

        DB::table('endorsement_types')->upsert(
            array_map(fn ($e) => array_merge($e, [
                'created_at' => $now,
                'updated_at' => $now,
            ]), $endorsements),
            ['code'], // unique key
            ['name', 'description', 'updated_at']
        );
    }
}
