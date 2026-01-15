<?php

namespace Database\Seeders;

use App\Models\Company\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['id' => 1],
            ['name' => 'Super Admin', 'slug' => 'super-admin']
        );

        Role::updateOrCreate(
            ['id' => 2],
            ['name' => 'Company Owner', 'slug' => 'company-owner']
        );

        $roles = [
            ['slug' => 'operations-manager', 'name' => 'Operations Manager'],
            ['slug' => 'fleet-manager', 'name' => 'Fleet Manager'],
            ['slug' => 'dispatcher', 'name' => 'Dispatcher'],
            ['slug' => 'driver', 'name' => 'Driver'],
            ['slug' => 'warehouse-manager', 'name' => 'Warehouse Manager'],
            ['slug' => 'accountant', 'name' => 'Accountant'],
            ['slug' => 'customer-support', 'name' => 'Customer Support'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );
        }
    }
}
