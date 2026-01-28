<?php

namespace Database\Seeders;

use App\Models\General\VehicleInsuranceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleInsuranceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Commercial Auto Policy',
            'Liability Insurance',
            'Physical Damage',
            'Cargo Insurance',
            'Trailer Interchange',
            'Cyber',
            'Other',
        ];

        foreach ($types as $type) {
            VehicleInsuranceType::firstOrCreate(['name' => $type]);
        }
    }
}
