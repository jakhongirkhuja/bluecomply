<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['company_name' => 'GreenTech Solutions', 'status' => true, 'user_id' => 2],
            ['company_name' => 'Redwave Industries', 'status' => true, 'user_id' => 2],
            ['company_name' => 'Silverline Corp', 'status' => true, 'user_id' => 2],
            ['company_name' => 'Quantum Innovations', 'status' => true, 'user_id' => 2],
        ];
        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
