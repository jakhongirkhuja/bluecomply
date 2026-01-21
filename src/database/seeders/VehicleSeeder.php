<?php

namespace Database\Seeders;

use App\Models\Driver\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('vehicles')->insert([
                'company_id'    => 2,
                'type_id'       => $faker->randomElement([1, 2]),
                'unit_number'   => 'UNIT-' . $faker->unique()->numberBetween(100, 999),
                'status'        => $faker->randomElement(['active', 'inactive', 'maintenance', 'out_of_service']),
                'make'          => $faker->randomElement(['Freightliner', 'Volvo', 'Peterbilt', 'Kenworth']),
                'model'         => $faker->word(),
                'year'          => $faker->numberBetween(2015, 2025),
                'vin'           => strtoupper(Str::random(17)),
                'plate'         => strtoupper($faker->bothify('??-####')),
                'state_id'      => $faker->numberBetween(1, 50),
                'expire_at'     => now()->addMonths($faker->numberBetween(1, 12)),
                'inspection_at' => now()->subMonths($faker->numberBetween(1, 6)),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
