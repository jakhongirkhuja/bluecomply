<?php

namespace Database\Seeders;

use App\Models\Driver\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'type' => 'truck',
                'unit_number' => 'TR-1001',
                'make' => 'Freightliner',
                'vin' => '1FUJA6CK47LW12345',
                'plate' => 'UZ-TRK-101',
                'plate_state' => 'UZ',
            ],
            [
                'type' => 'truck',
                'unit_number' => 'TR-1002',
                'make' => 'Volvo',
                'vin' => '4V4NC9EH1GN123456',
                'plate' => 'UZ-TRK-102',
                'plate_state' => 'UZ',
            ],

            [
                'type' => 'trailer',
                'unit_number' => 'TL-2001',
                'make' => 'Great Dane',
                'vin' => '1GRAA062XBW123456',
                'plate' => 'UZ-TLR-201',
                'plate_state' => 'UZ',
            ],
            [
                'type' => 'trailer',
                'unit_number' => 'TL-2002',
                'make' => 'Wabash',
                'vin' => '1JJV532D7JL123456',
                'plate' => 'UZ-TLR-202',
                'plate_state' => 'UZ',
            ],

            [
                'type' => 'cargo',
                'unit_number' => 'CG-3001',
                'make' => 'Reefer Container',
                'vin' => null,
                'plate' => null,
                'plate_state' => null,
            ],

            [
                'type' => 'equipment',
                'unit_number' => 'EQ-4001',
                'make' => 'Forklift Toyota',
                'vin' => 'EQP-TY-445566',
                'plate' => null,
                'plate_state' => null,
            ],

            [
                'type' => 'other',
                'unit_number' => 'OT-5001',
                'make' => 'Generator',
                'vin' => null,
                'plate' => null,
                'plate_state' => null,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
