<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    public function user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'token'
            ]);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Credenciales incorrectas'
            ]);
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/customer/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sesion cerrada'
            ]);

        $this->assertCount(0, $user->tokens()->get());
    }
}


