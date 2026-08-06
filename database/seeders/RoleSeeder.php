<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Crée les 3 rôles métier de la GMAO Clinique.
     */
    public function run(): void
    {
        foreach (['Admin', 'Technicien', 'Chef de service'] as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web'],
                ['name' => $role, 'guard_name' => 'web']
            );
        }
    }
}
