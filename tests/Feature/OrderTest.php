<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_list_orders()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Order::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJson('/api/customer/orders');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Orders retrieved successfully'
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function user_can_create_order()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $product = Product::factory()->create([
            'price' => 50
        ]);

        Inventory::factory()->create([
            'product_id' => $product->id,
            'available_quantity' => 10
        ]);

        $data = [
            'user_id' => $user->id,
            'total' => 100,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 50
                ]
            ]
        ];

        $response = $this->postJson('/api/customer/orders', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Order created successfully'
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id
        ]);
    }

    #[Test]
    public function user_can_view_order()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJson("/api/customer/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Order retrieved successfully'
            ]);
    }
}
