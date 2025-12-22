<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 1,
        ]);
        $user = User::create([
            'name' => 'Company Owner Doe',
            'email' => 'company@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,
        ]);
        $userToken = $user->createToken('api-token')->plainTextToken;
        echo "User API token: $userToken\n";
    }
}
