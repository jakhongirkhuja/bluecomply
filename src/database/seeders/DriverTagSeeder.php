<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DriverTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyIds = DB::table('companies')->pluck('id')->toArray();
        $driverIds  = DB::table('drivers')->pluck('id')->toArray();
        $userIds    = DB::table('users')->pluck('id')->toArray();

        if (empty($companyIds) || empty($driverIds) || empty($userIds)) {
            $this->command->error("Parent tables are empty. Please seed companies, drivers, and users first.");
            return;
        }

        $tagPool = [
            'Long-Haul', 'Short-Haul', 'Weekend-Only', 'Hazmat', 'Refrigerated',
            'Safety-Award', 'Speeding-Warning', 'Fuel-Efficient', 'New-Hire',
            'Level-1', 'Level-2', 'Level-3', 'Night-Shift', 'Day-Shift',
            'Medical-Clear', 'Documentation-Pending', 'English-Speaker',
            'Spanish-Speaker', 'Heavy-Load', 'Fragile-Expert'
        ];

        $data = [];
        $batchSize = 60;

        for ($i = 0; $i < $batchSize; $i++) {
            $data[] = [
                'company_id' => Arr::random($companyIds), // Matches your schema spelling
                'driver_id'  => Arr::random($driverIds),
                'user_id'    => Arr::random($userIds),
                'tag'        => Arr::random($tagPool),
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
                'updated_at' => Carbon::now(),
            ];
        }

        // Chunking the insert to be safe with database limits
        foreach (array_chunk($data, 20) as $chunk) {
            DB::table('driver_tags')->insert($chunk);
        }
    }
}
