<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_inventories()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        Inventory::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/inventories');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Inventories retrieved successfully'
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function admin_can_create_inventory()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'available_quantity' => 50
        ];

        $response = $this->postJson('/api/admin/inventories', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Inventory created successfully'
            ]);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'available_quantity' => 50
        ]);
    }

    #[Test]
    public function admin_can_update_inventory()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $inventory = Inventory::factory()->create([
            'available_quantity' => 10
        ]);

        $response = $this->putJson("/api/admin/inventories/{$inventory->id}", [
            'available_quantity' => 25
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Inventory updated successfully'
            ]);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'available_quantity' => 25
        ]);
    }

    #[Test]
    public function non_admin_cannot_manage_inventories()
    {
        $user = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/inventories');
        $response->assertStatus(403);
    }
}
