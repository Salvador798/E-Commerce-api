<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLog>
 */
class UserLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['CREAR', 'ACTUALIZAR', 'ELIMINAR', 'LOGIN']),
            'module' => $this->faker->randomElement(['USERS', 'ORDERS', 'PAYMENTS', 'SHIPMENTS']),
            'description' => $this->faker->sentence(),
            'ip' => $this->faker->ipv4()
        ];
    }
}
