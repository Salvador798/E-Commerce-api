<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_checkout_successfully()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $product = Product::factory()->create([
            'price' => 100
        ]);

        Inventory::factory()->create([
            'product_id' => $product->id,
            'available_quantity' => 10
        ]);

        $address = Address::factory()->create([
            'user_id' => $user->id
        ]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $data = [
            'address_id' => $address->id,
            'payment_method' => 'card'
        ];

        $response = $this->postJson('/api/customer/checkout', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Checkout completed successfully'
            ]);
    }

    #[Test]
    public function checkout_fails_if_cart_is_empty()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $address = Address::factory()->create([
            'user_id' => $user->id
        ]);

        $data = [
            'address_id' => $address->id,
            'payment_method' => 'card'
        ];

        $response = $this->postJson('/api/customer/checkout', $data);

        $response->assertStatus(404)
            ->assertJson([
                'status' => false
            ]);
    }
}
