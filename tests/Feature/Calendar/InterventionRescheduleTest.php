<?php

namespace Tests\Feature\Calendar;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterventionRescheduleTest extends TestCase
{
    use InteractsWithCalendarFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpCalendarRoles();
    }

    public function test_un_admin_peut_replanifier_une_intervention_par_glisser_deposer(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());
        $intervention = $this->makeIntervention($equipement, ['date_planifiee' => '2026-09-10 09:00:00']);

        $this->patchJson("/admin/calendrier/interventions/{$intervention->id}", [
            'start' => '2026-09-12T10:00:00',
            'end' => '2026-09-12T11:30:00',
        ])->assertOk();

        $intervention->refresh();

        $this->assertSame('2026-09-12 10:00:00', $intervention->date_planifiee->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-12 11:30:00', $intervention->date_fin->format('Y-m-d H:i:s'));
    }

    public function test_la_replanification_accepte_labsence_de_date_de_fin(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());
        $intervention = $this->makeIntervention($equipement);

        $this->patchJson("/admin/calendrier/interventions/{$intervention->id}", [
            'start' => '2026-09-12T10:00:00',
            'end' => null,
        ])->assertOk();

        $intervention->refresh();

        $this->assertNull($intervention->date_fin);
    }

    public function test_la_replanification_refuse_une_fin_anterieure_au_debut(): void
    {
        $this->actingAs($this->makeUser('Admin'));
        $equipement = $this->makeEquipement($this->makeService());
        $intervention = $this->makeIntervention($equipement);

        $this->patchJson("/admin/calendrier/interventions/{$intervention->id}", [
            'start' => '2026-09-12T10:00:00',
            'end' => '2026-09-12T09:00:00',
        ])->assertStatus(422);
    }

    public function test_un_chef_de_service_ne_peut_pas_replanifier_une_intervention(): void
    {
        $this->actingAs($this->makeUser('Chef de service'));
        $equipement = $this->makeEquipement($this->makeService());
        $intervention = $this->makeIntervention($equipement);

        $this->patchJson("/admin/calendrier/interventions/{$intervention->id}", [
            'start' => '2026-09-12T10:00:00',
        ])->assertForbidden();
    }
}
