<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Création de l'administrateur
        User::factory()->create([
            'nom' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Création du technicien
        User::factory()->create([
            'nom' => 'Tech User',
            'email' => 'tech@example.com',
            'password' => bcrypt('password'),
            'role' => 'technicien',
        ]);
    }
}
