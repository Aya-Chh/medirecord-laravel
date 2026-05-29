<?php

namespace Database\Factories;

use App\Models\Medecin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medecin>
 */
class MedecinFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Medecin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'specialite' => $this->faker->randomElement(['Cardiologie', 'Dermatologie', 'Pédiatrie', 'Gynécologie', 'Neurologie']),
            'numero_ordre' => $this->faker->unique()->bothify('#######'),
            'hopital' => $this->faker->randomElement(['Hôpital Général', 'Clinique Privée', 'Centre Hospitalier', null]),
            'bio' => $this->faker->paragraph(),
            'disponible' => $this->faker->boolean(80),
        ];
    }
}