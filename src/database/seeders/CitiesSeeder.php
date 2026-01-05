<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $path = storage_path('app/data/cities_dummy.json');

//        $path = storage_path('app/data/cities.json');
        $data = json_decode(File::get($path), true);

        $states = [];
        $cities = [];

        foreach ($data as $item) {

            $states[$item['state_id']] = [
                'state_id' => $item['state_id'],
                'state_name' => $item['state_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('states')->upsert(array_values($states), ['state_id']);


        foreach ($data as $item) {
            $cities = [
                'state_id'     => $item['state_id'],
                'city'         => $item['city'],
                'county_name'  => $item['county_name'] ?? null,
                'lat'          => $item['lat'],
                'lng'          => $item['lng'],
                'timezone'     => $item['timezone'] ?? null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
            DB::table('cities')->insert($cities);
        }

    }
}
