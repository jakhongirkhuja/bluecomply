<?php

namespace Database\Seeders;

use App\Models\Driver\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Driver::create(array_merge([
            'primary_phone' => '+998991234567',
            'status'        => true,
            'company_id'    => 1,
            'first_name'    => 'Jakhongir',
            'middle_name'   => 'Kholkhujaev',
            'last_name'     => 'Test',
            'ssn_sin'       => '123456789',
            'date_of_birth' => '1995-12-18',
        ], ['employee_id' => Driver::generateEmployeeId()]));
    }
}
