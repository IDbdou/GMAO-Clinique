<?php

namespace Tests\Feature;

use App\Filament\Agent\Resources\Signalements\Pages\CreateSignalement;
use App\Filament\Agent\Resources\Signalements\SignalementResource;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    private function makeUser(string $role, bool $actif = true, ?string $email = null): User
    {
        $user = User::create([
            'name' => "Test {$role}",
            'email' => $email ?? strtolower($role) . '@test.local',
            'password' => 'password',
            'actif' => $actif,
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makeEquipement(string $code = 'EQ-1'): Equipement
    {
        return Equipement::create([
            'nom' => "Équipement {$code}",
            'code_inventaire' => $code,
        ]);
    }

    // ----- Panneau /admin -----

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

    public function test_agent_ne_peut_pas_acceder_au_panneau_admin(): void
    {
        $this->actingAs($this->makeUser('Agent'));

        $this->get('/admin')->assertStatus(403);
    }

    public function test_compte_inactif_ne_peut_pas_acceder_au_panneau(): void
    {
        $this->actingAs($this->makeUser('Admin', actif: false));

        $this->get('/admin')->assertStatus(403);
    }

    // ----- Panneau /agent -----

    public function test_agent_accede_a_son_espace_signalements(): void
    {
        $this->actingAs($this->makeUser('Agent'));

        $this->get('/agent')->assertOk();
        $this->get('/agent/signalements')->assertOk();
        $this->get('/agent/signalements/create')->assertOk();
    }

    public function test_admin_et_technicien_ne_peuvent_pas_acceder_au_panneau_agent(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $this->get('/agent')->assertStatus(403);

        $this->actingAs($this->makeUser('Technicien'));
        $this->get('/agent')->assertStatus(403);
    }

    public function test_agent_signale_une_panne_cree_une_intervention_corrective_nouvelle(): void
    {
        $agent = $this->makeUser('Agent');
        $this->actingAs($agent);
        Filament::setCurrentPanel('agent');

        $equipement = $this->makeEquipement('SCAN-1');

        Livewire::test(CreateSignalement::class)
            ->fillForm([
                'equipement_id' => $equipement->id,
                'priorite' => 'haute',
                'description' => 'L’appareil ne s’allume plus.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('interventions', [
            'equipement_id' => $equipement->id,
            'demandeur_id' => $agent->id,
            'type' => 'curatif',      // corrective
            'statut' => 'nouveau',
            'priorite' => 'haute',
        ]);

        $intervention = Intervention::first();
        $this->assertNotNull($intervention->titre);
        $this->assertNull($intervention->technicien_id);
    }

    public function test_agent_ne_voit_que_ses_propres_signalements(): void
    {
        $agentA = $this->makeUser('Agent', email: 'a@test.local');
        $agentB = $this->makeUser('Agent', email: 'b@test.local');
        $equipement = $this->makeEquipement('EQ-9');

        Intervention::create([
            'equipement_id' => $equipement->id,
            'demandeur_id' => $agentA->id,
            'titre' => 'Signalement A',
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'normale',
            'date_demande' => now(),
        ]);
        Intervention::create([
            'equipement_id' => $equipement->id,
            'demandeur_id' => $agentB->id,
            'titre' => 'Signalement B',
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'normale',
            'date_demande' => now(),
        ]);

        $this->actingAs($agentA);
        $ids = SignalementResource::getEloquentQuery()->pluck('demandeur_id')->unique()->all();

        $this->assertSame([$agentA->id], $ids);
    }
}
