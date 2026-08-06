<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $services = \App\Models\Service::all()->keyBy('code');

        $users = [
            [
                'name' => 'Responsable Biomédical',
                'email' => 'admin@gmao.local',
                'password' => 'password',
                'service_id' => null,
                'actif' => true,
                'role' => 'Admin',
            ],
            [
                'name' => 'Technicien Biomédical 1',
                'email' => 'tech1@gmao.local',
                'password' => 'password',
                'service_id' => $services['RAD']->id,
                'actif' => true,
                'role' => 'Technicien',
            ],
            [
                'name' => 'Technicien Biomédical 2',
                'email' => 'tech2@gmao.local',
                'password' => 'password',
                'service_id' => $services['BLOC']->id,
                'actif' => true,
                'role' => 'Technicien',
            ],
            [
                'name' => 'Chef Radiologie',
                'email' => 'chef-rad@gmao.local',
                'password' => 'password',
                'service_id' => $services['RAD']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Bloc opératoire',
                'email' => 'chef-bloc@gmao.local',
                'password' => 'password',
                'service_id' => $services['BLOC']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Adjoint Hémodialyse',
                'email' => 'adj-hemo@gmao.local',
                'password' => 'password',
                'service_id' => $services['HEMO']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);
            $data['password'] = Hash::make($data['password']);

            /** @var User $user */
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            $user->syncRoles([$role]);
        }

        // Mise à jour des chefs et adjoints sur les services.
        $services['RAD']->update(['chef_id' => User::where('email', 'chef-rad@gmao.local')->first()->id]);
        $services['BLOC']->update(['chef_id' => User::where('email', 'chef-bloc@gmao.local')->first()->id]);
        $services['HEMO']->update(['adjoint_id' => User::where('email', 'adj-hemo@gmao.local')->first()->id]);
    }
}
