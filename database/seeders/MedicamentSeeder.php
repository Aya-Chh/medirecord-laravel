<?php

namespace Database\Seeders;

use App\Models\Medicament;
use Illuminate\Database\Seeder;

class MedicamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 medicaments
        Medicament::factory()->count(15)->create();
    }
}