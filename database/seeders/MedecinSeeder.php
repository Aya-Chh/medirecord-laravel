<?php

namespace Database\Seeders;

use App\Models\Medecin;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedecinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with the medecin role
        $medecinUsers = User::role('medecin')->get();

        foreach ($medecinUsers as $user) {
            // Create medecin record for each medecin user
            $user->medecin()->create([
                'specialite' => fake()->randomElement(['Cardiologie', 'Dermatologie', 'Pédiatrie', 'Gynécologie', 'Neurologie']),
                'numero_ordre' => fake()->unique()->bothify('#######'),
                'hopital' => fake()->randomElement(['Hôpital Général', 'Clinique Privée', 'Centre Hospitalier', null]),
                'bio' => fake()->paragraph(),
                'disponible' => fake()->boolean(80),
            ]);
        }
    }
}