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
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Création du technicien
        User::factory()->create([
            'name' => 'Tech User',
            'email' => 'tech@example.com',
            'password' => bcrypt('password'),
            'role' => 'technicien',
        ]);
    }
}
