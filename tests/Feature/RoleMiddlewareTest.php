<?php

use App\Models\User;

test('admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/admin');
    $response->assertStatus(200);
});

test('technicien', function () {
    $technicien = User::factory()->create(['role' => 'technicien']);
    $this->actingAs($technicien);

    $response = $this->get('/technicien');
    $response->assertStatus(200);
});
