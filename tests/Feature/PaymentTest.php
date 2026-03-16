<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_create_payment()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total' => 100
        ]);

        $data = [
            'order_id' => $order->id,
            'amount' => 100,
            'method' => 'card',
            'status' => 'aprobado'
        ];

        $response = $this->postJson('/api/customer/payments', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Payment created successfully'
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 100,
            'status' => 'aprobado'
        ]);
    }

    #[Test]
    public function admin_can_list_payments()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        Payment::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/payments');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Payments retrieved successfully'
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function payment_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/customer/payments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'amount', 'method', 'status']);
    }
}
