<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MedecinSeeder::class,
            PatientSeeder::class,
            ConsultationSeeder::class,
            MedicamentSeeder::class,
            OrdonnanceSeeder::class,
            DossierMedicalSeeder::class,
        ]);
    }
}