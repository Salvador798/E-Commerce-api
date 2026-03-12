<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(10)->create()->each(function ($order) {

            $items = OrderItem::factory(rand(1, 5))->create([
                'order_id' => $order->id
            ]);

            $total = $items->sum(fn($item) => $item->emit_price * $item->quantity);

            $order->total = $total;
            $order->save();
        });
    }
}
