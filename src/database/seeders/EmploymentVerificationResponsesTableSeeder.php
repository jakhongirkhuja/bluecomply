<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use App\Models\Driver\Driver;
use App\Models\Driver\EmploymentVerification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentVerificationResponsesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivers = Driver::all();
        $company = Company::first();

        foreach ($drivers as $driver) {
            // Outgoing verification
            $verification1 = EmploymentVerification::create([
                'driver_id' => $driver->id,
                'company_id' => $company->id,
                'direction' => 'outgoing',
                'method' => 'email',
                'status' => 'pending',
                'created_by' => 1,
            ]);

            // Incoming verification
            $verification2 = EmploymentVerification::create([
                'driver_id' => $driver->id,
                'company_id' => $company->id,
                'direction' => 'incoming',
                'method' => 'fax',
                'status' => 'new',
                'created_by' => 1,
            ]);

            // Add an event
            $verification1->events()->create([
                'type' => 'sent',
                'method' => 'email',
                'note' => 'Initial verification sent vai email',
                'created_by' => 1,
            ]);
            $verification1->events()->create([
                'type' => 'sent',
                'method' => null,
                'note' => 'Verification sent',
                'created_by' => 1,
            ]);
        }
    }
}
