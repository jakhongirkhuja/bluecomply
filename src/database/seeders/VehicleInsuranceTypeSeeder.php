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
            'Cargo Insurance',
        ];

        foreach ($types as $type) {
            VehicleInsuranceType::firstOrCreate(['name' => $type]);
        }
    }
}
