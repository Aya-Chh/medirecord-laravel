<?php
namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_register(): void
    {
        $r = $this->postJson('/api/auth/register', [
            'name' => 'Jean Dupont', 'email' => 'jean@test.fr',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);
        $r->assertStatus(201)->assertJsonPath('data.user.role', 'patient');
        $this->assertDatabaseHas('users', ['email' => 'jean@test.fr']);
    }

    public function test_user_can_login(): void
    {
        $u = User::factory()->create(['email' => 'a@b.fr']);
        $u->assignRole('patient');
        $r = $this->postJson('/api/auth/login', ['email' => 'a@b.fr', 'password' => 'password']);
        $r->assertOk()->assertJsonStructure(['data' => ['user', 'token', 'token_type']]);
    }

    public function test_login_fails_with_bad_credentials(): void
    {
        $this->postJson('/api/auth/login', ['email' => 'x@y.fr', 'password' => 'bad'])->assertStatus(401);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        $this->actingAs($u, 'sanctum')->getJson('/api/auth/me')
            ->assertOk()->assertJsonPath('data.role', 'admin');
    }

    public function test_user_can_logout(): void
    {
        $u = User::factory()->create();
        $u->assignRole('patient');
        $token = $u->createToken('t', ['patient'])->plainTextToken;
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout')->assertOk();
    }
}
