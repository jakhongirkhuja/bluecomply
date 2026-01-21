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
            ['name' => 'Truck', 'company_id'=>2],
            ['name' => 'Trailer','company_id'=>2],
            ['name' => 'Cargo','company_id'=>2],
            ['name' => 'Equipment','company_id'=>2],
            ['name' => 'Other','company_id'=>2],
        ];

        foreach ($vehicleTypes as $type) {
            DB::table('vehicle_types')->insert([
                'name' => $type['name'],
                'company_id' => $type['company_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
