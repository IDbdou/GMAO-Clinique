<?php

namespace Tests\Feature;

use App\Filament\Service\Resources\Signalements\Pages\CreateSignalement;
use App\Filament\Service\Resources\Signalements\SignalementResource;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\Service;
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

        foreach (['Admin', 'Technicien', 'Chef de service'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function makeUser(string $role, bool $actif = true, ?string $email = null, ?Service $service = null): User
    {
        $user = User::create([
            'name' => "Test {$role}",
            'email' => $email ?? strtolower(str_replace(' ', '-', $role)) . '@test.local',
            'password' => 'password',
            'actif' => $actif,
            'service_id' => $service?->id,
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makeService(string $code = 'RAD'): Service
    {
        return Service::create([
            'nom' => "Service {$code}",
            'code' => $code,
        ]);
    }

    private function makeEquipement(Service $service, string $code = 'EQ-1'): Equipement
    {
        return Equipement::create([
            'nom' => "Équipement {$code}",
            'code_inventaire' => $code,
            'service_id' => $service->id,
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
        $this->get('/admin/services')->assertOk();
    }

    public function test_technicien_accede_au_panneau_mais_pas_au_module_utilisateurs(): void
    {
        $this->actingAs($this->makeUser('Technicien'));

        $this->get('/admin/equipements')->assertOk();
        $this->get('/admin/interventions')->assertOk();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/services')->assertForbidden();
    }

    public function test_chef_de_service_ne_peut_pas_acceder_au_panneau_admin(): void
    {
        $service = $this->makeService();
        $this->actingAs($this->makeUser('Chef de service', service: $service));

        $this->get('/admin')->assertStatus(403);
    }

    public function test_compte_inactif_ne_peut_pas_acceder_au_panneau(): void
    {
        $this->actingAs($this->makeUser('Admin', actif: false));

        $this->get('/admin')->assertStatus(403);
    }

    // ----- Panneau /service -----

    public function test_chef_de_service_accede_a_son_espace_signalements(): void
    {
        $service = $this->makeService();
        $this->actingAs($this->makeUser('Chef de service', service: $service));

        $this->get('/service')->assertOk();
        $this->get('/service/signalements')->assertOk();
        $this->get('/service/signalements/create')->assertOk();
        $this->get('/service/equipements')->assertOk();
    }

    public function test_admin_et_technicien_ne_peuvent_pas_acceder_au_panneau_service(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $this->get('/service')->assertStatus(403);

        $this->actingAs($this->makeUser('Technicien'));
        $this->get('/service')->assertStatus(403);
    }

    public function test_chef_de_service_signale_une_panne_cree_une_intervention_corrective_nouvelle(): void
    {
        $service = $this->makeService();
        $chef = $this->makeUser('Chef de service', service: $service);
        $this->actingAs($chef);
        Filament::setCurrentPanel('service');

        $equipement = $this->makeEquipement($service, 'SCAN-1');

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
            'demandeur_id' => $chef->id,
            'service_id' => $service->id,
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'haute',
        ]);

        $intervention = Intervention::first();
        $this->assertNotNull($intervention->titre);
        $this->assertNull($intervention->technicien_id);
    }

    public function test_chef_de_service_ne_voit_que_les_signalements_de_son_service(): void
    {
        $serviceA = $this->makeService('RAD');
        $serviceB = $this->makeService('BLOC');

        $chefA = $this->makeUser('Chef de service', email: 'a@test.local', service: $serviceA);
        $this->makeUser('Chef de service', email: 'b@test.local', service: $serviceB);

        $equipementA = $this->makeEquipement($serviceA, 'EQ-A');
        $equipementB = $this->makeEquipement($serviceB, 'EQ-B');

        Intervention::create([
            'equipement_id' => $equipementA->id,
            'service_id' => $serviceA->id,
            'demandeur_id' => $chefA->id,
            'titre' => 'Signalement A',
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'normale',
            'date_demande' => now(),
        ]);
        Intervention::create([
            'equipement_id' => $equipementB->id,
            'service_id' => $serviceB->id,
            'demandeur_id' => $chefA->id,
            'titre' => 'Signalement B',
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'normale',
            'date_demande' => now(),
        ]);

        $this->actingAs($chefA);
        $serviceIds = SignalementResource::getEloquentQuery()->pluck('service_id')->unique()->all();

        $this->assertSame([$serviceA->id], $serviceIds);
    }
}
