<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Intervention;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InterventionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_intervention_statut()
    {
        // Créer et authentifier un utilisateur
        $user = User::factory()->create();
        $this->actingAs($user);

        // Créer une intervention
        $intervention = Intervention::factory()->create(['statut' => 'en attente']);

        // Faire la requête de mise à jour
        $response = $this->putJson("/api/interventions/{$intervention->id}/statut", [
            'statut' => 'en cours',
        ]);

        // Vérifier la réponse et la base de données
        $response->assertStatus(200);
        $this->assertDatabaseHas('interventions', ['statut' => 'en cours']);
    }
}
