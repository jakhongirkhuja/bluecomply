<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use App\Models\Driver\Driver;
use App\Models\Driver\EmploymentPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentPeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivers = Driver::all();
        $company = Company::first();

        foreach ($drivers as $driver) {

            EmploymentPeriod::create([
                'driver_id' => $driver->id,
                'company_id' => $company->id,
                'start_date' => now()->subYears(2),
                'end_date' => null,
                'status' => 'active',
                'notes' => 'Currently employed',
                'created_by' => 1,
            ]);

            EmploymentPeriod::create([
                'driver_id' => $driver->id,
                'company_id' => $company->id,
                'start_date' => now()->subYears(4),
                'end_date' => now()->subYears(2)->subDays(1),
                'status' => 'terminated',
                'termination_reason' => 'Voluntary resignation',
                'rehired' => true,
                'notes' => 'Rehired after 6 months',
                'notify_driver' => true,
                'payed_date' => now()->subYears(2)->subDays(1),
                'created_by' => 1,
            ]);
        }
    }
}
