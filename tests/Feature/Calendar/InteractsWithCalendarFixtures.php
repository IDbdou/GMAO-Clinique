<?php

namespace Tests\Feature\Calendar;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Role;

trait InteractsWithCalendarFixtures
{
    protected function setUpCalendarRoles(): void
    {
        foreach (['Admin', 'Technicien', 'Chef de service'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function makeUser(string $role, bool $actif = true): User
    {
        $user = User::create([
            'name' => "Test {$role}",
            'email' => strtolower(str_replace(' ', '-', $role)).'-'.uniqid().'@test.local',
            'password' => 'password',
            'actif' => $actif,
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

    private function makeIntervention(Equipement $equipement, array $overrides = []): Intervention
    {
        return Intervention::create(array_merge([
            'equipement_id' => $equipement->id,
            'service_id' => $equipement->service_id,
            'titre' => 'Intervention test',
            'type' => TypeIntervention::Curatif,
            'priorite' => PrioriteIntervention::Normale,
            'statut' => StatutIntervention::Ouverte,
            'date_planifiee' => now()->addDay(),
        ], $overrides));
    }
}
