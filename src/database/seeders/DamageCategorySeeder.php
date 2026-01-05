<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DamageCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Weather' => [
                'Front Bumper', 'Rear Bumper', 'Left Front (LF)', 'Right Front (RF)',
                'Left Rear (LR)', 'Right Rear (RR)', 'Windshield / Glass',
                'Lights', 'Tires / Wheels', 'Doors', 'Roof / Hood / Trunk',
                'Chassis / Frame', 'Engine / Transmission'
            ],
            'Vandalism/Theft' => [
                'Damaged Packaging', 'Spilled Load', 'Crushed Load',
                'Contaminated Cargo', 'Lost / Missing Items'
            ],
            'Warehouse/Facility Damage' => [
                'Fence / Barrier', 'Road Sign / Traffic Equipment',
                'Building / Structure', 'Other Property'
            ],
            'Driver Damaged' => [
                'Lift / Forklift', 'Straps / Chains', 'Loading Dock / Ramp', 'Tools / Devices'
            ],
            'Other' => [
                'Enter Description'
            ]
        ];

        foreach ($categories as $category => $specifics) {
            $categoryId = DB::table('damage_categories')->insertGetId([
                'name' => $category,
                'created_at' => now(),
                'updated_at' => now()
            ]);


        }
    }
}
