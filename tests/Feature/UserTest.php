<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Users retrieved successfully'
            ])
            ->assertJsonCount(4, 'data'); // incluye el admin creado
    }

    #[Test]
    public function admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer'
        ];

        $response = $this->postJson('/api/admin/users', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User created successfully'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    public function admin_can_view_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $user = User::factory()->create();

        $response = $this->getJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User retrieved successfully'
            ]);
    }

    #[Test]
    public function admin_can_update_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $user = User::factory()->create();

        $response = $this->putJson("/api/admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User updated successfully'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);
    }

    #[Test]
    public function admin_can_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $user = User::factory()->create();

        $response = $this->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User deleted successfully'
            ]);

        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    #[Test]
    public function non_admin_cannot_manage_users()
    {
        $user = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(403);
    }
}
