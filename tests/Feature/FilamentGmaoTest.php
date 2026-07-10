<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentGmaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Admin', 'Technicien', 'Agent'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function makeUser(string $role, bool $actif = true): User
    {
        $user = User::create([
            'name' => "Test {$role}",
            'email' => strtolower($role) . '@test.local',
            'password' => 'password',
            'actif' => $actif,
        ]);
        $user->assignRole($role);

        return $user;
    }

    public function test_admin_peut_ouvrir_les_pages_du_panneau(): void
    {
        $this->actingAs($this->makeUser('Admin'));

        $this->get('/admin')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/users/create')->assertOk();
        $this->get('/admin/equipements')->assertOk();
        $this->get('/admin/equipements/create')->assertOk();
        $this->get('/admin/interventions')->assertOk();
        $this->get('/admin/interventions/create')->assertOk();
    }

    public function test_technicien_accede_au_panneau_mais_pas_au_module_utilisateurs(): void
    {
        $this->actingAs($this->makeUser('Technicien'));

        $this->get('/admin/equipements')->assertOk();
        $this->get('/admin/interventions')->assertOk();
        $this->get('/admin/users')->assertForbidden();
    }

    public function test_agent_ne_peut_pas_acceder_au_panneau(): void
    {
        $this->actingAs($this->makeUser('Agent'));

        // canAccessPanel() refuse l'Agent -> redirection hors du panneau (pas un 200).
        $this->get('/admin')->assertStatus(403);
    }

    public function test_compte_inactif_ne_peut_pas_acceder_au_panneau(): void
    {
        $this->actingAs($this->makeUser('Admin', actif: false));

        $this->get('/admin')->assertStatus(403);
    }
}
