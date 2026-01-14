<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $vehicleTypes = [
            ['name' => 'Truck'],
            ['name' => 'Trailer'],
            ['name' => 'Cargo'],
            ['name' => 'Equipment'],
            ['name' => 'Other'],
        ];

        foreach ($vehicleTypes as $type) {
            DB::table('vehicle_types')->insert([
                'name' => $type['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
