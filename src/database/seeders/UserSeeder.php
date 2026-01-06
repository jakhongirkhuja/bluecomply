<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '+1231231231',
            'password' => Hash::make('password123'),
            'role_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);


        $user =  User::create([
            'name' => 'Company Owner Doe',
            'email' => 'company@example.com',
            'phone' => '+12312312312',
            'password' => Hash::make('password123'),
            'role_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('users')->insert([
            [
                'name' => 'Safety Manager Smith',
                'email' => 'safety@example.com',
                'phone' => '+123123124231',
                'password' => Hash::make('password123'),
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Manager Alice',
                'email' => 'driver1@example.com',
                'phone' => '+123123122331',
                'password' => Hash::make('driver123'),
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Manager Bob',
                'email' => 'driver2@example.com',
                'phone' => '+123123123551',
                'password' => Hash::make('driver123'),
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Manager Charlie',
                'email' => 'driver3@example.com',
                'phone' => '+123123123231',
                'password' => Hash::make('driver123'),
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $userToken = $user->createToken('api-token')->plainTextToken;
        echo "User API token: $userToken\n";
    }
}
