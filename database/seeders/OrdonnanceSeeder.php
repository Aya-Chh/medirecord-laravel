<?php

namespace Database\Seeders;

use App\Models\Ordonnance;
use Illuminate\Database\Seeder;

class OrdonnanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 ordonnances, each with 3 medicaments
        Ordonnance::factory()->count(20)->withMedicaments(3)->create();
    }
}