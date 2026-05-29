<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'date_naissance' => $this->faker->date(),
            'genre' => $this->faker->randomElement(['homme', 'femme', 'autre']),
            'adresse' => $this->faker->streetAddress(),
            'ville' => $this->faker->city(),
            'pays' => $this->faker->country(),
        ];
    }
}