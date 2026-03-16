<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_cart()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/customer/cart');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Cart retrieved successfully'
            ]);
    }

    #[Test]
    public function user_can_add_product_to_cart()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 1
        ];

        $response = $this->postJson('/api/customer/cart', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product added to cart successfully'
            ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id
        ]);
    }

    #[Test]
    public function user_can_update_cart_item()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id
        ]);

        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->putJson("/api/customer/cart/item/{$item->id}", [
            'quantity' => 3
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Cart updated successfully'
            ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 3
        ]);
    }

    #[Test]
    public function user_can_remove_item_from_cart()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $cart = Cart::factory()->create([
            'user_id' => $user->id
        ]);

        $item = CartItem::factory()->create([
            'cart_id' => $cart->id
        ]);

        $response = $this->deleteJson("/api/customer/cart/item/{$item->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id
        ]);
    }

    #[Test]
    public function user_can_empty_cart()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $cart = Cart::factory()->create([
            'user_id' => $user->id
        ]);

        CartItem::factory()->count(3)->create([
            'cart_id' => $cart->id
        ]);

        $response = $this->deleteJson('/api/customer/cart/empty');

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id
        ]);
    }
}
