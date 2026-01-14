<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $equipments = [
            [
                'code' => 'GEN_DRY_VAN',
                'name' => 'Tractor-Trailer (Dry Van)',
                'category' => 'General',
                'description' => 'Standard freight trailer, non-specialized.',
                'requires_endorsement' => false,
            ],
            [
                'code' => 'GEN_BOX_TRUCK',
                'name' => 'Box Truck / Straight Truck',
                'category' => 'General',
                'description' => 'Medium to heavy-duty straight trucks.',
                'requires_endorsement' => false,
            ],
            [
                'code' => 'GEN_FLATBED',
                'name' => 'Flatbed (Standard)',
                'category' => 'General',
                'description' => 'Flatbed without oversize or special permits.',
                'requires_endorsement' => false,
            ],
            [
                'code' => 'GEN_CARGO_VAN',
                'name' => 'Cargo Van',
                'category' => 'General',
                'description' => 'Light commercial cargo vans.',
                'requires_endorsement' => false,
            ],

            [
                'code' => 'REEFER',
                'name' => 'Refrigerated Trailer (Reefer)',
                'category' => 'Specialized',
                'description' => 'Temperature-controlled freight.',
                'requires_endorsement' => false,
            ],
            [
                'code' => 'TANKER',
                'name' => 'Tanker Truck',
                'category' => 'Specialized',
                'description' => 'Liquid or gaseous bulk cargo.',
                'requires_endorsement' => true, // N or X
            ],
            [
                'code' => 'HAZMAT',
                'name' => 'Hazardous Materials Vehicle',
                'category' => 'Specialized',
                'description' => 'Transportation of hazardous materials.',
                'requires_endorsement' => true, // H or X
            ],
            [
                'code' => 'DOUBLES_TRIPLES',
                'name' => 'Double / Triple Trailers',
                'category' => 'Specialized',
                'description' => 'Multiple trailers configuration.',
                'requires_endorsement' => true, // T
            ],
            [
                'code' => 'CAR_HAULER',
                'name' => 'Car Hauler',
                'category' => 'Specialized',
                'description' => 'Automobile transport trailers.',
                'requires_endorsement' => false,
            ],
            [
                'code' => 'OVERSIZE',
                'name' => 'Oversize / Overweight Load',
                'category' => 'Specialized',
                'description' => 'Loads requiring permits.',
                'requires_endorsement' => false,
            ],
            [
                'code' => 'HEAVY_EQUIPMENT',
                'name' => 'Heavy Equipment Transport',
                'category' => 'Heavy',
                'description' => 'Lowboy or step deck equipment hauling.',
                'requires_endorsement' => false,
            ],
        ];

        DB::table('equipment_types')->upsert(
            array_map(fn ($e) => array_merge($e, [
                'created_at' => $now,
                'updated_at' => $now,
            ]), $equipments),
            ['code'],
            ['name', 'category', 'description', 'requires_endorsement', 'updated_at']
        );
    }
}
