<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminOrderAccessTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@futsal.com')->first() ?? User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_orders_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/orders');

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
    }

    public function test_orders_page_displays_pending_orders(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/orders?status=pending');

        $response->assertStatus(200);
        $response->assertSee('pending');
    }

    public function test_non_admin_cannot_access_orders_page(): void
    {
        $member = User::where('role', 'member')->first();
        if (!$member) {
            $member = User::factory()->create(['role' => 'member']);
        }

        $response = $this->actingAs($member)
            ->get('/admin/orders');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin/orders');

        $response->assertRedirect('/login');
    }
}
