<?php

namespace Database\Factories;

use App\Models\Medicament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicament>
 */
class MedicamentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Medicament::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->randomElement(['Paracétamol', 'Ibuprofène', 'Amoxicilline', 'Atorvastatine', 'Metformine', 'Oméprazole', 'Amlodipine', 'Losartan', 'Sertraline', 'Levothyroxine']),
            'description' => $this->faker->sentence(),
            'forme' => $this->faker->randomElement(['comprime', 'sirop', 'injection', 'creme', 'autre']),
            'dosage_disponible' => $this->faker->randomElement([null, ['500mg', '1g'], ['5mg', '10mg', '20mg'], ['100ml', '200ml']]),
        ];
    }
}