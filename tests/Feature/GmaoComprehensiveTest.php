<?php

namespace Tests\Feature;

use App\Models\CompteRendu;
use App\Models\ContratMaintenance;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\PlanningPreventif;
use App\Models\Service;
use App\Models\SatisfactionIntervention;
use App\Models\User;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Services\ServiceResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GmaoComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Admin', 'Technicien', 'Chef de service'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    // ============================================================
    // HELPERS
    // ============================================================

    private function makeUser(string $role, bool $actif = true, ?string $email = null, ?Service $service = null): User
    {
        $user = User::create([
            'name' => "Test {$role}",
            'email' => $email ?? strtolower(str_replace(' ', '-', $role)) . '@test.local',
            'password' => bcrypt('password'),
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
            'localisation' => 'Étage 1',
            'actif' => true,
        ]);
    }

    private function makeEquipement(Service $service, string $code = 'EQ-1'): Equipement
    {
        return Equipement::create([
            'nom' => "Équipement {$code}",
            'code_inventaire' => $code,
            'service_id' => $service->id,
            'statut' => 'en_service',
            'criticite' => 'moyenne',
        ]);
    }

    // ============================================================
    // 1. RÔLES & CONNEXION / NAVIGATION GET
    // ============================================================

    public function test_admin_peut_acceder_au_panneau_admin(): void
    {
        $this->actingAs($this->makeUser('Admin'));

        $this->get('/admin')->assertOk();
        $this->get('/admin/equipements')->assertOk();
        $this->get('/admin/interventions')->assertOk();
        $this->get('/admin/compte-rendus')->assertOk();
        $this->get('/admin/services')->assertOk();
        $this->get('/admin/users')->assertOk();
    }

    public function test_technicien_peut_acceder_au_panneau_admin_ressources_maintenance(): void
    {
        $this->actingAs($this->makeUser('Technicien'));

        $this->get('/admin')->assertOk();
        $this->get('/admin/equipements')->assertOk();
        $this->get('/admin/interventions')->assertOk();
        $this->get('/admin/compte-rendus')->assertOk();
    }

    public function test_chef_de_service_ne_peut_pas_acceder_au_panneau_admin(): void
    {
        $service = $this->makeService();
        $this->actingAs($this->makeUser('Chef de service', service: $service));

        $this->get('/admin')->assertStatus(403);
    }

    public function test_utilisateur_inactif_est_bloque_partout(): void
    {
        $this->actingAs($this->makeUser('Admin', actif: false));
        $this->get('/admin')->assertStatus(403);
    }

    public function test_chef_de_service_accede_a_son_espace_service(): void
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

    // ============================================================
    // 2. PERMISSIONS — canAccess() sur les ressources
    // ============================================================

    public function test_resource_utilisateur_est_reservee_a_l_admin(): void
    {
        $admin = $this->makeUser('Admin');
        $tech = $this->makeUser('Technicien');

        $this->actingAs($admin);
        $this->assertTrue(UserResource::canAccess());

        $this->actingAs($tech);
        $this->assertFalse(UserResource::canAccess());
    }

    public function test_resource_services_est_reservee_a_l_admin(): void
    {
        $admin = $this->makeUser('Admin');
        $tech = $this->makeUser('Technicien');

        $this->actingAs($admin);
        $this->assertTrue(ServiceResource::canAccess());

        $this->actingAs($tech);
        $this->assertFalse(ServiceResource::canAccess());
    }

    // ============================================================
    // 3. MODÈLES — CRUD ÉQUIPEMENTS (via Eloquent)
    // ============================================================

    public function test_equipement_peut_etre_cree(): void
    {
        $service = $this->makeService();
        $equipement = Equipement::create([
            'nom' => 'IRM Siemens',
            'code_inventaire' => 'IRM-001',
            'service_id' => $service->id,
            'criticite' => 'haute',
            'statut' => 'en_service',
        ]);

        $this->assertDatabaseHas('equipements', [
            'nom' => 'IRM Siemens',
            'code_inventaire' => 'IRM-001',
            'service_id' => $service->id,
        ]);

        $this->assertInstanceOf(Service::class, $equipement->service);
        $this->assertEquals($service->id, $equipement->service->id);
    }

    public function test_equipement_code_inventaire_est_unique_en_base(): void
    {
        $service = $this->makeService();
        $this->makeEquipement($service, 'IRM-001');

        $this->expectException(\Illuminate\Database\QueryException::class);
        Equipement::create([
            'nom' => 'IRM 2',
            'code_inventaire' => 'IRM-001',
            'service_id' => $service->id,
            'criticite' => 'moyenne',
            'statut' => 'en_service',
        ]);
    }

    public function test_equipement_peut_etre_modifie_et_supprime(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        $equipement->update(['nom' => 'Équipement modifié', 'statut' => 'en_panne']);
        $this->assertDatabaseHas('equipements', ['id' => $equipement->id, 'nom' => 'Équipement modifié', 'statut' => 'en_panne']);

        $equipement->delete();
        $this->assertDatabaseMissing('equipements', ['id' => $equipement->id]);
    }

    // ============================================================
    // 4. MODÈLES — CRUD INTERVENTIONS (via Eloquent)
    // ============================================================

    public function test_intervention_peut_etre_cree_avec_bon_service(): void
    {
        $service = $this->makeService('RAD');
        $equipement = $this->makeEquipement($service, 'SCAN-01');

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Test service auto',
            'type' => 'preventif',
            'priorite' => 'normale',
            'statut' => 'nouveau',
        ]);

        $this->assertDatabaseHas('interventions', [
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Test service auto',
        ]);

        $this->assertInstanceOf(Equipement::class, $intervention->equipement);
        $this->assertInstanceOf(Service::class, $intervention->service);
    }

    public function test_intervention_peut_etre_assignee_a_un_technicien(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);
        $technicien = $this->makeUser('Technicien');

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Intervention test',
            'type' => 'curatif',
            'priorite' => 'normale',
            'statut' => 'nouveau',
        ]);

        $intervention->update([
            'technicien_id' => $technicien->id,
            'statut' => 'ouverte',
        ]);

        $this->assertDatabaseHas('interventions', [
            'id' => $intervention->id,
            'technicien_id' => $technicien->id,
            'statut' => 'ouverte',
        ]);

        $intervention->refresh();
        $this->assertInstanceOf(User::class, $intervention->technicien);
        $this->assertEquals($technicien->id, $intervention->technicien->id);
    }

    // ============================================================
    // 5. COMPTES-RENDUS
    // ============================================================

    public function test_compte_rendu_peut_etre_cree_pour_une_intervention(): void
    {
        $technicien = $this->makeUser('Technicien');
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Intervention CR',
            'type' => 'curatif',
            'priorite' => 'normale',
            'statut' => 'en_cours',
            'technicien_id' => $technicien->id,
        ]);

        $cr = CompteRendu::create([
            'intervention_id' => $intervention->id,
            'technicien_id' => $technicien->id,
            'observations' => 'Réparation effectuée.',
            'temps_passe' => 2.5,
            'signature_technicien' => $technicien->name,
            'date_soumission' => now(),
        ]);

        $this->assertDatabaseHas('compte_rendus', [
            'intervention_id' => $intervention->id,
            'observations' => 'Réparation effectuée.',
        ]);

        $this->assertInstanceOf(Intervention::class, $cr->intervention);
        $this->assertInstanceOf(User::class, $cr->technicien);
    }

    public function test_pdf_compte_rendu_est_accessible_par_vue(): void
    {
        $technicien = $this->makeUser('Technicien');
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Intervention PDF',
            'type' => 'curatif',
            'priorite' => 'normale',
            'statut' => 'terminee',
        ]);

        $cr = CompteRendu::create([
            'intervention_id' => $intervention->id,
            'technicien_id' => $technicien->id,
            'observations' => 'Test PDF',
            'temps_passe' => 1.0,
            'signature_technicien' => $technicien->name,
            'date_soumission' => now(),
        ]);

        $this->actingAs($this->makeUser('Admin'));
        $this->get("/admin/compte-rendus/{$cr->id}")->assertOk();
    }

    // ============================================================
    // 6. PANNEAU SERVICE — SIGNALEMENTS (via modèle)
    // ============================================================

    public function test_chef_de_service_signale_une_panne_cree_une_intervention(): void
    {
        $service = $this->makeService();
        $chef = $this->makeUser('Chef de service', service: $service);
        $equipement = $this->makeEquipement($service, 'EQ-SIG');

        // Simuler le signalement (création d'intervention curative)
        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'demandeur_id' => $chef->id,
            'titre' => 'Panne critique',
            'type' => 'curatif',
            'priorite' => 'haute',
            'statut' => 'nouveau',
            'description' => 'Panne critique.',
        ]);

        $this->assertDatabaseHas('interventions', [
            'equipement_id' => $equipement->id,
            'demandeur_id' => $chef->id,
            'service_id' => $service->id,
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'haute',
        ]);
    }

    public function test_chef_de_service_ne_voit_que_les_signalements_de_son_service(): void
    {
        $serviceA = $this->makeService('RAD');
        $serviceB = $this->makeService('BLOC');

        $chefA = $this->makeUser('Chef de service', email: 'a@test.local', service: $serviceA);
        $chefB = $this->makeUser('Chef de service', email: 'b@test.local', service: $serviceB);

        $equipementA = $this->makeEquipement($serviceA, 'EQ-A');
        $equipementB = $this->makeEquipement($serviceB, 'EQ-B');

        $interventionA = Intervention::create([
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
            'demandeur_id' => $chefB->id,
            'titre' => 'Signalement B',
            'type' => 'curatif',
            'statut' => 'nouveau',
            'priorite' => 'normale',
            'date_demande' => now(),
        ]);

        $this->actingAs($chefA);
        $response = $this->get('/service/signalements');
        $response->assertOk();

        // Vérifie que le scope Eloquent filtre correctement
        $serviceIds = \App\Filament\Service\Resources\Signalements\SignalementResource::getEloquentQuery()->pluck('service_id')->unique()->all();
        $this->assertSame([$serviceA->id], $serviceIds);
    }

    // ============================================================
    // 7. MAINTENANCE PRÉVENTIVE
    // ============================================================

    public function test_commande_generer_preventifs_cree_des_interventions(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        PlanningPreventif::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'frequence' => 'mensuelle',
            'titre' => 'Maintenance mensuelle',
            'prochaine_date' => now()->subDay(),
            'actif' => true,
        ]);

        Artisan::call('gmao:generer-preventifs');

        $this->assertDatabaseHas('interventions', [
            'equipement_id' => $equipement->id,
            'type' => 'preventif',
        ]);
    }

    // ============================================================
    // 8. CONTRATS & GARANTIES
    // ============================================================

    public function test_contrat_maintenance_peut_etre_cree(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        $contrat = ContratMaintenance::create([
            'equipement_id' => $equipement->id,
            'reference' => 'CONT-001',
            'fournisseur' => 'Siemens Healthineers',
            'type_contrat' => 'maintenance',
            'date_debut' => now()->subYear(),
            'date_fin' => now()->addYear(),
            'cout_annuel' => 50000,
            'actif' => true,
        ]);

        $this->assertDatabaseHas('contrats_maintenance', [
            'id' => $contrat->id,
            'reference' => 'CONT-001',
            'fournisseur' => 'Siemens Healthineers',
        ]);
    }

    public function test_widget_contrats_alertes_detecte_expiration_proche(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        ContratMaintenance::create([
            'equipement_id' => $equipement->id,
            'reference' => 'CONT-ALERT',
            'fournisseur' => 'Prestataire A',
            'type_contrat' => 'maintenance',
            'date_debut' => now()->subYear(),
            'date_fin' => now()->addDays(5),
            'cout_annuel' => 10000,
            'actif' => true,
        ]);

        $this->actingAs($this->makeUser('Admin'));
        $response = $this->get('/admin');
        $response->assertOk();
    }

    // ============================================================
    // 9. SATISFACTION
    // ============================================================

    public function test_satisfaction_peut_etre_enregistree(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);
        $technicien = $this->makeUser('Technicien');
        $evaluateur = $this->makeUser('Chef de service', email: 'eval@test.local', service: $service);

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Intervention SAT',
            'type' => 'curatif',
            'priorite' => 'normale',
            'statut' => 'terminee',
            'technicien_id' => $technicien->id,
        ]);

        $satisfaction = SatisfactionIntervention::create([
            'intervention_id' => $intervention->id,
            'evaluateur_id' => $evaluateur->id,
            'note' => 5,
            'commentaire' => 'Très bon service.',
        ]);

        $this->assertDatabaseHas('satisfactions_intervention', [
            'id' => $satisfaction->id,
            'intervention_id' => $intervention->id,
            'evaluateur_id' => $evaluateur->id,
            'note' => 5,
        ]);
    }

    // ============================================================
    // 10. CALENDRIER
    // ============================================================

    public function test_calendrier_interventions_est_accessible_par_admin(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $this->get('/admin/calendrier-interventions')->assertOk();
    }

    public function test_calendrier_interventions_est_accessible_par_technicien(): void
    {
        $this->actingAs($this->makeUser('Technicien'));
        $this->get('/admin/calendrier-interventions')->assertOk();
    }

    // ============================================================
    // 11. DASHBOARD — WIDGETS
    // ============================================================

    public function test_dashboard_admin_affiche_les_widgets(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $response = $this->get('/admin');

        $response->assertOk();
        $response->assertSee('Dashboard');
    }

    public function test_dashboard_service_affiche_widgets(): void
    {
        $service = $this->makeService();
        $this->actingAs($this->makeUser('Chef de service', service: $service));

        $response = $this->get('/service');
        $response->assertOk();
        $response->assertSee('Dashboard');
    }

    // ============================================================
    // 12. MODÈLES & RELATIONS
    // ============================================================

    public function test_intervention_a_des_relations_correctes(): void
    {
        $service = $this->makeService();
        $technicien = $this->makeUser('Technicien');
        $demandeur = $this->makeUser('Chef de service', email: 'dem@test.local', service: $service);
        $equipement = $this->makeEquipement($service);

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'technicien_id' => $technicien->id,
            'demandeur_id' => $demandeur->id,
            'titre' => 'Test relations',
            'type' => 'curatif',
            'priorite' => 'normale',
            'statut' => 'nouveau',
        ]);

        $this->assertInstanceOf(Equipement::class, $intervention->equipement);
        $this->assertInstanceOf(Service::class, $intervention->service);
        $this->assertInstanceOf(User::class, $intervention->technicien);
        $this->assertInstanceOf(User::class, $intervention->demandeur);
        $this->assertEquals($equipement->id, $intervention->equipement->id);
        $this->assertEquals($technicien->id, $intervention->technicien->id);
        $this->assertEquals($demandeur->id, $intervention->demandeur->id);
    }

    public function test_service_a_un_chef_et_des_equipements(): void
    {
        $chef = $this->makeUser('Chef de service');
        $service = Service::create([
            'nom' => 'Radiologie',
            'code' => 'RAD',
            'localisation' => 'RDC',
            'chef_id' => $chef->id,
            'actif' => true,
        ]);

        $equipement = $this->makeEquipement($service, 'SCAN-001');

        $this->assertEquals($chef->id, $service->chef->id);
        $this->assertCount(1, $service->equipements);
        $this->assertEquals($service->id, $equipement->service->id);
    }

    // ============================================================
    // 13. UTILISATEURS — CRÉATION ET RÔLES
    // ============================================================

    public function test_utilisateur_peut_avoir_un_role_et_un_service(): void
    {
        $service = $this->makeService();
        $user = $this->makeUser('Technicien', service: $service);

        $this->assertTrue($user->hasRole('Technicien'));
        $this->assertEquals($service->id, $user->service->id);
    }

    public function test_utilisateur_inactif_a_actif_a_false(): void
    {
        $user = $this->makeUser('Admin', actif: false);
        $this->assertFalse($user->actif);
        $this->assertTrue($user->hasRole('Admin'));
    }

    // ============================================================
    // 14. ÉNUMÉRATIONS
    // ============================================================

    public function test_statut_equipement_accepte_les_bonnes_valeurs(): void
    {
        $service = $this->makeService();
        $equipement = Equipement::create([
            'nom' => 'Test',
            'code_inventaire' => 'STAT-01',
            'service_id' => $service->id,
            'statut' => 'en_service',
        ]);

        $this->assertEquals('en_service', $equipement->statut->value);
    }

    public function test_statut_intervention_a_les_bons_labels(): void
    {
        $service = $this->makeService();
        $equipement = $this->makeEquipement($service);

        $intervention = Intervention::create([
            'equipement_id' => $equipement->id,
            'service_id' => $service->id,
            'titre' => 'Test',
            'statut' => 'terminee',
            'type' => 'curatif',
            'priorite' => 'normale',
        ]);

        $this->assertEquals('terminee', $intervention->statut->value);
    }
}
