<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use App\Models\{User, Medecin, Patient, Consultation, Medicament, Ordonnance};

class OrdonnanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void { parent::setUp(); $this->seed(RoleSeeder::class); }

    public function test_medecin_can_create_ordonnance(): void
    {
        $u = User::factory()->create(); $u->assignRole('medecin');
        $m = Medecin::factory()->create(['user_id' => $u->id]);
        $p = Patient::factory()->create();
        $c = Consultation::factory()->create(['medecin_id' => $m->id, 'patient_id' => $p->id]);
        $med = Medicament::factory()->create();

        $this->actingAs($u, 'sanctum')->postJson('/api/v1/ordonnances', [
            'consultation_id' => $c->id, 'medecin_id' => $m->id, 'patient_id' => $p->id,
            'date_emission' => now()->toDateString(),
            'medicaments' => [[
                'id' => $med->id, 'dosage' => '500mg',
                'frequence' => '3x/jour', 'duree' => '7 jours',
            ]],
        ])->assertStatus(201);
    }

    public function test_pdf_endpoint_returns_pdf(): void
    {
        $u = User::factory()->create(); $u->assignRole('admin');
        $o = Ordonnance::factory()->create();
        $r = $this->actingAs($u, 'sanctum')->get("/api/v1/ordonnances/{$o->id}/pdf");
        $r->assertOk();
        $this->assertStringContainsString('application/pdf', $r->headers->get('Content-Type'));
    }
}
