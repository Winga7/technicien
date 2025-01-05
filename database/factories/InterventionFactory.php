<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InterventionFactory extends Factory
{
    protected $model = \App\Models\Intervention::class;

    public function definition()
    {
        return [
            'titre' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'statut' => $this->faker->randomElement(['en attente', 'en cours', 'terminé']),
            'client_id' => \App\Models\Client::factory(), // Génère un client automatiquement
            'ticket_id' => \App\Models\Ticket::factory(),
            'technicien_id' => \App\Models\User::factory(), // Génère un technicien automatiquement
            'images' => json_encode([$this->faker->imageUrl]),
        ];
    }
}
