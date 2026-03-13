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
            'name' => 'Salvador Martinez',
            'email' => 'salmartinezv@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Customer user
        User::create([
            'name' => 'Jesús Martinez',
            'email' => 'salmartinezv@hotmail.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer'
        ]);

        User::create([
            'name' => 'Cruz Martinez',
            'email' => 'cruzsalvadormartinezregnault@gmail.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer'
        ]);

        // Random users
        User::factory()->count(10)->create();
    }
}
