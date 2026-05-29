<?php

namespace Database\Seeders;

use App\Models\DossierMedical;
use Illuminate\Database\Seeder;

class DossierMedicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 dossiers medicaux
        DossierMedical::factory()->count(10)->create();
    }
}