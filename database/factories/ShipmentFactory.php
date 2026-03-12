<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'carrier' => $this->faker->randomElement(['DHI', 'FedEx', 'UPS']),
            'tracking' => strtoupper(Str::random(12)),
            'estimated_date' => now()->addDays(rand(2, 7)),
            'status' => 'pendiente'
        ];
    }
}
