<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Ordonnance;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ordonnance>
 */
class OrdonnanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ordonnance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Get a random consultation or create one if none exist
        $consultation = Consultation::inRandomOrder()->first() ?? Consultation::factory()->create();

        // Get the medecin and patient from the consultation
        $medecin = $consultation->medecin;
        $patient = $consultation->patient;

        $dateEmission = $this->faker->dateTimeBetween('-6 months', 'now');
        $dateExpiration = $this->faker->randomElement([null, $this->faker->dateTimeBetween($dateEmission, '+1 year')]);

        return [
            'consultation_id' => $consultation->id,
            'medecin_id' => $medecin->id,
            'patient_id' => $patient->id,
            'date_emission' => $dateEmission,
            'date_expiration' => $dateExpiration,
            'instructions_generales' => $this->faker->sentence(),
            'statut' => $this->faker->randomElement(['active', 'expiree', 'annulee']),
        ];
    }

    /**
     * Configure the factory to create associated medicaments.
     *
     * @return \Database\Factories\OrdonnanceFactory
     */
    public function withMedicaments(int $count = 3)
    {
        return $this->afterCreating(function (Ordonnance $ordonnance) use ($count) {
            $ordonnance->medicaments()->attach(
                \App\Models\Medicament::inRandomOrder()->limit($count)->get()->pluck('id')->toArray(),
                [
                    'dosage' => fn () => $this->faker->randomElement(['500mg', '1g', '5mg', '10mg', '20mg']),
                    'frequence' => fn () => $this->faker->randomElement(['1 fois/jour', '2 fois/jour', '3 fois/jour']),
                    'duree' => fn () => $this->faker->randomElement([7, 10, 14]),
                    'instructions' => fn () => $this->faker->sentence(),
                ]
            );
        });
    }
}