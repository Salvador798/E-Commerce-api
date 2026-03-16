<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_user_addresses()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Address::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJson('/api/customer/address');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Addresses retrieved successfully'
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_can_create_address()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $data = [
            'street' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA'
        ];

        $response = $this->postJson('/api/customer/address', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Address created successfully'
            ]);

        $this->assertDatabaseHas('addresses', [
            'street' => '123 Main St',
            'postal_code' => '10001',
            'user_id' => $user->id
        ]);
    }

    #[Test]
    public function it_can_update_address()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $address = Address::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->putJson("/api/customer/address/{$address->id}", [
            'city' => 'Los Angeles'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Address updated successfully'
            ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'city' => 'Los Angeles'
        ]);
    }

    #[Test]
    public function it_can_delete_address()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $address = Address::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson("/api/customer/address/{$address->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id
        ]);
    }
}
