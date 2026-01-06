<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            AgencySeeder::class,
            SituationCategorySeeder::class,
            CitiesSeeder::class,
            VehicleSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            DriverSeeder::class,
            DocumentSeeder::class,
            EmploymentPeriodsTableSeeder::class,
            EmploymentVerificationsTableSeeder::class,
            EmploymentVerificationResponsesTableSeeder::class,
            DamageCategorySeeder::class,

        ]);
    }
}
