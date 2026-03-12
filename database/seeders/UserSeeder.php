<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Customer user
        User::create([
            'name' => 'Customer',
            'email' => 'customer@email.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer'
        ]);

        // Random users
        User::factory()->count(10)->create();
    }
}
