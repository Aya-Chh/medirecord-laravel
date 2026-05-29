<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use App\Models\{User, Medecin};

class MedecinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void { parent::setUp(); $this->seed(RoleSeeder::class); }

    public function test_admin_can_list_medecins(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Medecin::factory()->count(3)->create();
        $this->actingAs($admin, 'sanctum')->getJson('/api/v1/medecins')
            ->assertOk()->assertJsonStructure(['data', 'meta']);
    }

    public function test_patient_cannot_create_medecin(): void
    {
        $p = User::factory()->create(); $p->assignRole('patient');
        $this->actingAs($p, 'sanctum')->postJson('/api/v1/medecins', [])->assertStatus(403);
    }
}
