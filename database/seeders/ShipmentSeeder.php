<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::all()->each(function ($order) {
            Shipment::factory()->create([
                'order_id' => $order->id
            ]);
        });
    }
}
