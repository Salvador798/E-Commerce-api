<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_shipments()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        Shipment::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/shipments');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Shipments retrieved successfully'
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function admin_can_create_shipment()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        $customer = User::factory()->create();

        $address = Address::factory()->create([
            'user_id' => $customer->id
        ]);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'address_id' => $address->id,
            'status' => 'pagado'
        ]);

        $data = [
            'order_id' => $order->id,
            'carrier' => 'DHL',
            'tracking' => 'TRACK1234',
            'estimated_date' => now()->addDays(3)->toDateString()
        ];

        $response = $this->postJson('/api/admin/shipments', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Shipment created successfully'
            ]);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'carrier' => 'DHL'
        ]);
    }

    #[Test]
    public function admin_can_update_shipment_status()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        $shipment = Shipment::factory()->create([
            'status' => 'en_transito'
        ]);

        $response = $this->putJson("/api/admin/shipments/{$shipment->id}/status", [
            'status' => 'entregado'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Shipment status updated successfully'
            ]);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'entregado'
        ]);
    }

    #[Test]
    public function shipment_creation_fails_with_invalid_data()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/shipments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'carrier', 'estimated_date']);
    }
}
