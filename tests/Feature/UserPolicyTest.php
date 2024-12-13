<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/users');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_view_users()
    {
        $technician = User::factory()->create(['role' => 'technicien']);
        $this->actingAs($technician);

        $response = $this->get('/users');

        $response->assertStatus(403);
    }
}
