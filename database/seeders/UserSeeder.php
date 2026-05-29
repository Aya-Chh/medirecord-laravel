<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin MediRecord',
            'email' => 'admin@medirecord.com',
            'password' => Hash::make('password'),
            'phone' => '0123456789',
        ]);
        $admin->assignRole('admin');
        $admin->profile()->create([
            'date_naissance' => '1980-01-01',
            'genre' => 'homme',
            'adresse' => '123 Rue de la Paix',
            'ville' => 'Paris',
            'pays' => 'France',
        ]);

        // Create 5 users for medecins (without medecin record yet)
        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'phone' => fake()->phoneNumber(),
            ]);
            $user->assignRole('medecin');
            $user->profile()->create([
                'date_naissance' => fake()->dateTimeBetween('-60 years', '-25 years')->format('Y-m-d'),
                'genre' => fake()->randomElement(['homme', 'femme']),
                'adresse' => fake()->streetAddress(),
                'ville' => fake()->city(),
                'pays' => fake()->country(),
            ]);
        }

        // Create 20 users for patients (without patient record yet)
        for ($i = 0; $i < 20; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'phone' => fake()->phoneNumber(),
            ]);
            $user->assignRole('patient');
            $user->profile()->create([
                'date_naissance' => fake()->dateTimeBetween('-90 years', '-18 years')->format('Y-m-d'),
                'genre' => fake()->randomElement(['homme', 'femme', 'autre']),
                'adresse' => fake()->streetAddress(),
                'ville' => fake()->city(),
                'pays' => fake()->country(),
            ]);
        }
    }
}