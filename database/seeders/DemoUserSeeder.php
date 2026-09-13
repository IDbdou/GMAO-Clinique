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
            // Admin
            [
                'name' => 'Responsable Biomédical',
                'email' => 'admin@gmao.local',
                'password' => 'password',
                'service_id' => null,
                'actif' => true,
                'role' => 'Admin',
            ],
            // Techniciens (multiservices)
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
                'name' => 'Technicien Biomédical 3',
                'email' => 'tech3@gmao.local',
                'password' => 'password',
                'service_id' => $services['URG']->id,
                'actif' => true,
                'role' => 'Technicien',
            ],
            [
                'name' => 'Technicien Biomédical 4',
                'email' => 'tech4@gmao.local',
                'password' => 'password',
                'service_id' => $services['REA']->id,
                'actif' => true,
                'role' => 'Technicien',
            ],
            // Chefs de service
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
                'name' => 'Chef Hémodialyse',
                'email' => 'chef-hemo@gmao.local',
                'password' => 'password',
                'service_id' => $services['HEMO']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Urgences',
                'email' => 'chef-urg@gmao.local',
                'password' => 'password',
                'service_id' => $services['URG']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Cardiologie',
                'email' => 'chef-card@gmao.local',
                'password' => 'password',
                'service_id' => $services['CARD']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Réanimation',
                'email' => 'chef-rea@gmao.local',
                'password' => 'password',
                'service_id' => $services['REA']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Laboratoire',
                'email' => 'chef-labo@gmao.local',
                'password' => 'password',
                'service_id' => $services['LABO']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Maternité',
                'email' => 'chef-mat@gmao.local',
                'password' => 'password',
                'service_id' => $services['MAT']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Pédiatrie',
                'email' => 'chef-ped@gmao.local',
                'password' => 'password',
                'service_id' => $services['PED']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Stérilisation',
                'email' => 'chef-ste@gmao.local',
                'password' => 'password',
                'service_id' => $services['STE']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Oncologie',
                'email' => 'chef-onco@gmao.local',
                'password' => 'password',
                'service_id' => $services['ONCO']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            [
                'name' => 'Chef Pharmacie',
                'email' => 'chef-phar@gmao.local',
                'password' => 'password',
                'service_id' => $services['PHAR']->id,
                'actif' => true,
                'role' => 'Chef de service',
            ],
            // Utilisateur inactif (pour tester)
            [
                'name' => 'Ancien Technicien',
                'email' => 'ancien@gmao.local',
                'password' => 'password',
                'service_id' => $services['RAD']->id,
                'actif' => false,
                'role' => 'Technicien',
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

        // Mise à jour des chefs sur les services
        $services['RAD']->update(['chef_id' => User::where('email', 'chef-rad@gmao.local')->first()->id]);
        $services['BLOC']->update(['chef_id' => User::where('email', 'chef-bloc@gmao.local')->first()->id]);
        $services['HEMO']->update(['chef_id' => User::where('email', 'chef-hemo@gmao.local')->first()->id]);
        $services['URG']->update(['chef_id' => User::where('email', 'chef-urg@gmao.local')->first()->id]);
        $services['CARD']->update(['chef_id' => User::where('email', 'chef-card@gmao.local')->first()->id]);
        $services['REA']->update(['chef_id' => User::where('email', 'chef-rea@gmao.local')->first()->id]);
        $services['LABO']->update(['chef_id' => User::where('email', 'chef-labo@gmao.local')->first()->id]);
        $services['MAT']->update(['chef_id' => User::where('email', 'chef-mat@gmao.local')->first()->id]);
        $services['PED']->update(['chef_id' => User::where('email', 'chef-ped@gmao.local')->first()->id]);
        $services['STE']->update(['chef_id' => User::where('email', 'chef-ste@gmao.local')->first()->id]);
        $services['ONCO']->update(['chef_id' => User::where('email', 'chef-onco@gmao.local')->first()->id]);
        $services['PHAR']->update(['chef_id' => User::where('email', 'chef-phar@gmao.local')->first()->id]);
    }
}
