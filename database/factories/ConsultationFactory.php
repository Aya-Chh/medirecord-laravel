<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consultation>
 */
class ConsultationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Consultation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $medecin = Medecin::inRandomOrder()->first();
        $patient = Patient::inRandomOrder()->first();

        $dateHeure = $this->faker->dateTimeBetween('-6 months', '+6 months');

        return [
            'medecin_id' => $medecin->id,
            'patient_id' => $patient->id,
            'date_heure' => $dateHeure,
            'motif' => $this->faker->sentence(),
            'diagnostic' => $this->faker->randomElement([null, $this->faker->sentence()]),
            'notes' => $this->faker->randomElement([null, $this->faker->paragraph()]),
            'statut' => $this->faker->randomElement(['planifiee', 'en_cours', 'terminee', 'annulee']),
            'duree_minutes' => $this->faker->randomElement([null, 15, 30, 45, 60]),
        ];
    }
}