<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function assertReportPayload($payload): void
    {
        $this->assertIsArray($payload);

        $data = $payload['data'] ?? $payload;
        $this->assertIsArray($data);

        if (empty($data)) {
            return;
        }

        $values = array_values($data);
        $first = $values[0] ?? null;

        $this->assertIsArray($first);
        $this->assertArrayHasKey('label', $first);
        $this->assertArrayHasKey('value', $first);
        $this->assertArrayHasKey('extra', $first);
        $this->assertIsArray($first['extra']);
    }

    #[Test]
    public function admin_can_access_reports()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $endpoints = [
            '/api/admin/sales/day',
            '/api/admin/sales/category',
            '/api/admin/sales/customer',
            '/api/admin/sales/product',
            '/api/admin/sales/method',
            '/api/admin/sales/status',
            '/api/admin/sales/region'
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);

            $response->assertStatus(200);

            $this->assertReportPayload($response->json());
        }
    }

    #[Test]
    public function admin_can_access_sales_by_range()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/sales/range?from=2026-01-01&until=2026-01-31');

        $response->assertStatus(200);

        $this->assertReportPayload($response->json());
    }

    #[Test]
    public function non_admin_cannot_access_reports()
    {
        $user = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/sales/day');
        $response->assertStatus(403);
    }

    #[Test]
    public function sales_by_range_requires_dates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/sales/range');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['from', 'until']);
    }
}
