<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DriverVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driverIds = DB::table('drivers')->pluck('id')->toArray();
        $vehicleIds = DB::table('vehicles')->pluck('id')->toArray();

        if (count($driverIds) < 1 || count($vehicleIds) < 1) {
            $this->command->error("Insufficient data in drivers or vehicles tables.");
            return;
        }

        $roles = ['primary', 'secondary', 'relief', 'contractor'];
        $data = [];

        for ($i = 0; $i < 20; $i++) {
            $assignedDate = Carbon::now()->subDays(rand(1, 100));

            $data[] = [
                'driver_id'     => Arr::random($driverIds),
                'vehicle_id'    => Arr::random($vehicleIds),
                'role'          => Arr::random($roles),
                'assigned_at'   => $assignedDate->toDateString(),
                'unassigned_at' => null, // Keeping most active
                'is_active'     => true,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ];
        }

        DB::table('driver_vehicles')->insert($data);
    }
}
