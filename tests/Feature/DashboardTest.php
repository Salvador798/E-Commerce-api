<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_access_dashboard_endpoints()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // Crear datos de ejemplo
        $product = Product::factory()->create();
        Inventory::factory()->create([
            'product_id' => $product->id,
            'available_quantity' => 10
        ]);

        $order = Order::factory()->create(['status' => 'pagado']);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'aprobado',
            'amount' => 100
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $endpoints = [
            '/api/admin/sales/summary',
            '/api/admin/sales/per-month',
            '/api/admin/sales/top-products',
            '/api/admin/orders/summary',
            '/api/admin/orders/per-status',
            '/api/admin/stock/summary',
            '/api/admin/stock/low',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);

            $response->assertStatus(200);
        }
    }

    #[Test]
    public function non_admin_cannot_access_dashboard_endpoints()
    {
        $user = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/sales/summary');
        $response->assertStatus(403);
    }
}
