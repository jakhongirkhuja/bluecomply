<?php

namespace Database\Seeders;

use App\Models\General\VehicleMaintenanceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleMaintenanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maintenances = [
            'Oil Change',
            'Repair',
            'Tire Service',
            'Inspection',
            'Other Maintenance',
        ];

        foreach ($maintenances as $name) {
            VehicleMaintenanceType::firstOrCreate(['name' => $name]);
        }
    }
}
