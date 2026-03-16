<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/public/products');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Products retrieved successfully'
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_can_show_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/public/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product retrieved successfully'
            ]);
    }

    #[Test]
    public function it_can_create_product()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Laptop',
            'price' => 1500,
            'categories' => [$category->id]
        ];

        $response = $this->postJson('/api/admin/products', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product created successfully'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop'
        ]);
    }

    #[Test]
    public function it_can_update_product()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $product = Product::factory()->create();

        $response = $this->putJson("/api/admin/products/{$product->id}", [
            'name' => 'Updated Product'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product updated successfully'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product'
        ]);
    }

    #[Test]
    public function it_can_toggle_product_status()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        $product = Product::factory()->create([
            'status' => true
        ]);

        $response = $this->patchJson("/api/admin/products/{$product->id}/status");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Status updated successfully'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => false
        ]);
    }
}
