<?php

namespace Database\Seeders;

use App\Models\Driver\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\WithFaker;
class DriverSeeder extends Seeder
{
    use WithFaker;
    public function run(): void
    {
        $this->setUpFaker('en_US');
        $this->faker->unique(true);

        for ($i = 0; $i < 70; $i++) {
            Driver::create([
                'primary_phone' => $this->faker->unique()->numerify('##########'),
                'status'        => 'active',
                'company_id'    => 2,

                'first_name'    => $this->faker->firstName,
                'middle_name'   => $this->faker->optional()->firstName,
                'last_name'     => $this->faker->lastName,

                'ssn_sin'       => $this->faker->unique()->ssn,
                'date_of_birth' => $this->faker
                    ->dateTimeBetween('-60 years', '-21 years')
                    ->format('Y-m-d'),

                'employee_id'   => Driver::generateEmployeeId(),
            ]);
        }
    }
}
