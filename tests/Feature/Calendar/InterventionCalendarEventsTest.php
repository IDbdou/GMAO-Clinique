<?php

namespace Tests\Feature\Calendar;

use App\Enums\StatutIntervention;
use App\Support\Calendar\InterventionColorResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterventionCalendarEventsTest extends TestCase
{
    use InteractsWithCalendarFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpCalendarRoles();
    }

    public function test_la_page_calendrier_saffiche_pour_un_admin(): void
    {
        $this->actingAs($this->makeUser('Admin'));

        $this->get('/admin/calendrier-interventions')->assertOk();
    }

    public function test_lendpoint_events_est_refuse_a_un_utilisateur_non_autorise_pour_ladmin(): void
    {
        $this->actingAs($this->makeUser('Chef de service'));

        $this->getJson('/admin/calendrier/events')->assertForbidden();
    }

    public function test_lendpoint_events_est_refuse_sans_authentification(): void
    {
        $this->getJson('/admin/calendrier/events')->assertUnauthorized();
    }

    public function test_lendpoint_events_retourne_la_couleur_issue_de_lenum_statut(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $intervention = $this->makeIntervention($equipement, ['statut' => StatutIntervention::EnCours]);

        $this->getJson('/admin/calendrier/events')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $intervention->id,
                'color' => InterventionColorResolver::hex(StatutIntervention::EnCours->getColor()),
            ]);
    }

    public function test_le_filtre_par_statut_ne_retourne_que_les_interventions_correspondantes(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $ouverte = $this->makeIntervention($equipement, ['statut' => StatutIntervention::Ouverte]);
        $terminee = $this->makeIntervention($equipement, ['statut' => StatutIntervention::Terminee]);

        $ids = collect(
            $this->getJson('/admin/calendrier/events?'.http_build_query(['statut' => ['terminee']]))
                ->assertOk()
                ->json()
        )->pluck('id');

        $this->assertTrue($ids->contains($terminee->id));
        $this->assertFalse($ids->contains($ouverte->id));
    }

    public function test_une_intervention_multi_jours_devient_un_evenement_toute_la_journee(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $intervention = $this->makeIntervention($equipement, [
            'date_planifiee' => '2026-09-15 23:00:00',
            'date_fin' => '2026-09-16 02:00:00',
        ]);

        $event = collect($this->getJson('/admin/calendrier/events')->assertOk()->json())
            ->firstWhere('id', $intervention->id);

        $this->assertTrue($event['allDay']);
        $this->assertSame('2026-09-15', $event['start']);
        // Fin exclusive attendue par FullCalendar pour les evenements multi-jours.
        $this->assertSame('2026-09-17', $event['end']);
    }

    public function test_une_intervention_sans_date_de_fin_reste_un_evenement_ponctuel(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $intervention = $this->makeIntervention($equipement, [
            'date_planifiee' => '2026-09-15 23:14:00',
            'date_fin' => null,
        ]);

        $event = collect($this->getJson('/admin/calendrier/events')->assertOk()->json())
            ->firstWhere('id', $intervention->id);

        $this->assertFalse($event['allDay']);
        $this->assertNull($event['end']);
    }

    public function test_une_intervention_sans_date_planifiee_napparait_pas_dans_events(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $intervention = $this->makeIntervention($equipement, ['date_planifiee' => null]);

        $ids = collect($this->getJson('/admin/calendrier/events')->assertOk()->json())->pluck('id');

        $this->assertFalse($ids->contains($intervention->id));
    }

    public function test_lendpoint_unscheduled_est_refuse_a_un_utilisateur_non_autorise(): void
    {
        $this->actingAs($this->makeUser('Chef de service'));

        $this->getJson('/admin/calendrier/unscheduled')->assertForbidden();
    }

    public function test_lendpoint_unscheduled_retourne_uniquement_les_interventions_sans_date_planifiee(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $nonPlanifiee = $this->makeIntervention($equipement, ['date_planifiee' => null]);
        $planifiee = $this->makeIntervention($equipement, ['date_planifiee' => now()->addDay()]);

        $ids = collect($this->getJson('/admin/calendrier/unscheduled')->assertOk()->json())->pluck('id');

        $this->assertTrue($ids->contains($nonPlanifiee->id));
        $this->assertFalse($ids->contains($planifiee->id));
    }

    public function test_programmer_une_intervention_non_planifiee_la_fait_passer_des_non_planifiees_aux_evenements(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());

        $intervention = $this->makeIntervention($equipement, ['date_planifiee' => null]);

        $this->patchJson("/admin/calendrier/interventions/{$intervention->id}", [
            'start' => '2026-09-20T09:00:00',
        ])->assertOk();

        $unscheduledIds = collect($this->getJson('/admin/calendrier/unscheduled')->assertOk()->json())->pluck('id');
        $eventIds = collect($this->getJson('/admin/calendrier/events')->assertOk()->json())->pluck('id');

        $this->assertFalse($unscheduledIds->contains($intervention->id));
        $this->assertTrue($eventIds->contains($intervention->id));
    }
}
