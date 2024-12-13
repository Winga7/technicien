<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'nom' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Ajout du mot de passe
        ]);
    }
}
