<?php

namespace Database\Seeders;

use App\Models\General\VehicleDocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Vehicle Registration',
            'Vehicle Title',
            'Special Permit',
            'Annual Inspection',
            'Other',
        ];

        foreach ($types as $type) {
            VehicleDocumentType::create([
                'name' => $type,
            ]);
        }
    }
}
