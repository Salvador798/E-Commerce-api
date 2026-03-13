<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/public/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    #[Test]
    public function it_can_create_category()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Electronics'
        ];

        $response = $this->postJson('/api/admin/categories', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Electronics'
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);
    }

    #[Test]
    public function it_can_update_category()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $category = Category::factory()->create();

        $response = $this->putJson("/api/admin/categories/{$category->id}", [
            'name' => 'Updated Category'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category'
        ]);
    }

    #[Test]
    public function it_can_toggle_category_status()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Sanctum::actingAs($admin);

        $category = Category::factory()->create([
            'status' => true,
        ]);

        $response = $this->patchJson("/api/admin/categories/{$category->id}/status");

        $response->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'status' => false
        ]);
    }
}
