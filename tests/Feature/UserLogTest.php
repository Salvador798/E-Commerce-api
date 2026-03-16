<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserLogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_user_logs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        UserLog::factory()->count(25)->create();

        $response = $this->getJson('/api/admin/users/log');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);

        // Verificar que hay paginación
        $this->assertArrayHasKey('data', $response->json());
        $this->assertArrayHasKey('data', $response->json('data'));
        $this->assertCount(20, $response->json('data.data')); // la paginación tiene 20 por página
    }

    #[Test]
    public function non_admin_cannot_access_user_logs()
    {
        $user = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/users/log');

        $response->assertStatus(403);
    }
}
