<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use App\Models\{User, Medecin, Patient, Consultation};

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void { parent::setUp(); $this->seed(RoleSeeder::class); }

    public function test_medecin_can_create_consultation(): void
    {
        $u = User::factory()->create(); $u->assignRole('medecin');
        $m = Medecin::factory()->create(['user_id' => $u->id]);
        $p = Patient::factory()->create();
        $this->actingAs($u, 'sanctum')->postJson('/api/v1/consultations', [
            'medecin_id' => $m->id, 'patient_id' => $p->id,
            'date_heure' => now()->addDay()->toIso8601String(),
            'motif' => 'Consultation de routine',
        ])->assertStatus(201);
    }
}
