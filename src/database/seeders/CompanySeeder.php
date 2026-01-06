<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        DB::table('companies')->insert([
            [
                'company_name' => 'Swift Transport LLC',
                'tenet_id' => 'TNT-001',
                'dot_number' => '1234567',
                'user_id' => 1,
                'status' => 'active',
                'der_name' => 'Jane Doe',
                'der_email' => 'jane@swifttransport.com',
                'der_phone' => '(555) 123-4568',
                'last_active' => $now,
                'plan_id' => 1, // Starter
                'drivers' => 45,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Blue Logistics Inc',
                'tenet_id' => 'TNT-002',
                'dot_number' => '7654321',
                'user_id' => 2,
                'status' => 'active',
                'der_name' => 'John Doe',
                'der_email' => 'john@bluelogistics.com',
                'der_phone' => '(555) 234-5678',
                'last_active' => $now,
                'plan_id' => 2, // Professional
                'drivers' => 150,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Mountain Freight Co',
                'tenet_id' => 'TNT-003',
                'dot_number' => '2468135',
                'user_id' => 3,
                'status' => 'suspended',
                'der_name' => 'Alice Smith',
                'der_email' => 'alice@mountainfreight.com',
                'der_phone' => '(555) 345-6789',
                'last_active' => $now,
                'plan_id' => 1, // Starter
                'drivers' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'ACME Trucking',
                'tenet_id' => 'TNT-004',
                'dot_number' => '9876543',
                'user_id' => 4,
                'status' => 'active',
                'der_name' => 'Bob Brown',
                'der_email' => 'bob@acmetracking.com',
                'der_phone' => '(555) 456-7890',
                'last_active' => $now,
                'plan_id' => 3, // Enterprise
                'drivers' => 300,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Rapid Haul Inc',
                'tenet_id' => 'TNT-005',
                'dot_number' => '1122334',
                'user_id' => 5,
                'status' => 'active',
                'der_name' => 'Charlie King',
                'der_email' => 'charlie@rapidhaul.com',
                'der_phone' => '(555) 567-8901',
                'last_active' => $now,
                'plan_id' => 2, // Professional
                'drivers' => 120,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Express Logistics LLC',
                'tenet_id' => 'TNT-006',
                'dot_number' => '2233445',
                'user_id' => 6,
                'status' => 'active',
                'der_name' => 'Diana Prince',
                'der_email' => 'diana@expresslogistics.com',
                'der_phone' => '(555) 678-9012',
                'last_active' => $now,
                'plan_id' => 1, // Starter
                'drivers' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Global Freight Solutions',
                'tenet_id' => 'TNT-007',
                'dot_number' => '3344556',
                'user_id' => 7,
                'status' => 'suspended',
                'der_name' => 'Eve Adams',
                'der_email' => 'eve@globalfreight.com',
                'der_phone' => '(555) 789-0123',
                'last_active' => $now,
                'plan_id' => 3, // Enterprise
                'drivers' => 500,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Summit Hauling',
                'tenet_id' => 'TNT-008',
                'dot_number' => '4455667',
                'user_id' => 8,
                'status' => 'active',
                'der_name' => 'Frank White',
                'der_email' => 'frank@summithauling.com',
                'der_phone' => '(555) 890-1234',
                'last_active' => $now,
                'plan_id' => 2, // Professional
                'drivers' => 180,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Continental Trucking',
                'tenet_id' => 'TNT-009',
                'dot_number' => '5566778',
                'user_id' => 9,
                'status' => 'active',
                'der_name' => 'Grace Lee',
                'der_email' => 'grace@continentaltrucking.com',
                'der_phone' => '(555) 901-2345',
                'last_active' => $now,
                'plan_id' => 3, // Enterprise
                'drivers' => 350,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_name' => 'Summit Express',
                'tenet_id' => 'TNT-010',
                'dot_number' => '6677889',
                'user_id' => 10,
                'status' => 'active',
                'der_name' => 'Hank Miller',
                'der_email' => 'hank@summitexpress.com',
                'der_phone' => '(555) 012-3456',
                'last_active' => $now,
                'plan_id' => 1, // Starter
                'drivers' => 35,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $pivotData = [

            ['company_id' => 2, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],

            ['company_id' => 2, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 2, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 2, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['company_id' => 1, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('company_user')->insert($pivotData);
    }
}
