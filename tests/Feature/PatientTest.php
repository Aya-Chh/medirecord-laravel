<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use App\Models\{User, Patient};

class PatientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void { parent::setUp(); $this->seed(RoleSeeder::class); }

    public function test_patient_can_only_see_himself(): void
    {
        $u1 = User::factory()->create(); $u1->assignRole('patient');
        $p1 = Patient::factory()->create(['user_id' => $u1->id]);
        $other = Patient::factory()->create();
        $this->actingAs($u1, 'sanctum')->getJson("/api/v1/patients/{$p1->id}")->assertOk();
        $this->actingAs($u1, 'sanctum')->getJson("/api/v1/patients/{$other->id}")->assertStatus(403);
    }
}
