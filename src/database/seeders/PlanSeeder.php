<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        DB::table('plans')->insert([
            [
                'id' => 1,
                'name' => 'Starter',
                'description' => 'For small fleets',
                'price' => 99.00,
                'driver_limit' => 50,
                'features' => json_encode([
                    'Up to 50 drivers',
                    'Basic features',
                    'Email support'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Professional',
                'description' => 'For small fleets',
                'price' => 299.00,
                'driver_limit' => 200,
                'features' => json_encode([
                    'Up to 200 drivers',
                    'All features',
                    'Priority support'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Enterprise',
                'description' => 'For small fleets',
                'price' => 599.00,
                'driver_limit' => 0, // 0 = unlimited
                'features' => json_encode([
                    'Unlimited drivers',
                    'Custom features',
                    'Dedicated support'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
