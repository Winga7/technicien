<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_client()
    {
        $user = \App\Models\User::factory()->create(); // Créer un utilisateur
        $this->actingAs($user); // Simuler la connexion de l'utilisateur

        $response = $this->postJson('/clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'telephone' => '123456789',
            'addresse' => '123 Test Street',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('clients', ['email' => 'test@example.com']);
    }
}
