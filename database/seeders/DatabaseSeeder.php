<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important : les rôles doivent exister avant d'assigner des utilisateurs.
        $this->call([
            RoleSeeder::class,
            ServiceSeeder::class,
            DemoUserSeeder::class,
            EquipementSeeder::class,
            DemoGmaoSeeder::class,
            PlanningPreventifSeeder::class,
            ContratMaintenanceSeeder::class,
            SatisfactionInterventionSeeder::class,
        ]);
    }
}
