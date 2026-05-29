<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\DossierMedical;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DossierMedical>
 */
class DossierMedicalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DossierMedical::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Get a random patient or create one if none exist
        $patient = Patient::inRandomOrder()->first() ?? Patient::factory()->create();

        // 30% chance of being linked to a consultation, 70% chance of being standalone
        $consultation = null;
        if (fake()->boolean(30)) {
            $consultation = Consultation::inRandomOrder()->first() ?? Consultation::factory()->create([
                'patient_id' => $patient->id, // Ensure the consultation belongs to the same patient
            ]);
        }

        return [
            'patient_id' => $patient->id,
            'consultation_id' => $consultation ? $consultation->id : null,
            'titre' => $this->faker->sentence(),
            'contenu' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['rapport', 'analyse', 'imagerie', 'autre']),
            'fichier_path' => $this->faker->randomElement([null, 'uploads/dossiers/' . $this->faker->uuid() . '.pdf']),
        ];
    }
}