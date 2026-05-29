<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with the patient role
        $patientUsers = User::role('patient')->get();

        foreach ($patientUsers as $user) {
            // Create patient record for each patient user
            $user->patient()->create([
                'groupe_sanguin' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'allergies' => fake()->randomElement([null, 'Pollen', 'Arachides', 'Lactose', 'Pénicilline']),
                'antecedents' => fake()->randomElement([null, 'Diabète', 'Hypertension', 'Asthme', 'Antécédents cardiaques']),
            ]);
        }
    }
}