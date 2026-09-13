<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $services = [
            ['nom' => 'Radiologie', 'code' => 'RAD', 'localisation' => 'Aile Est, Rez-de-chaussée'],
            ['nom' => 'Bloc opératoire', 'code' => 'BLOC', 'localisation' => 'Aile Sud, 1er étage'],
            ['nom' => 'Hémodialyse', 'code' => 'HEMO', 'localisation' => 'Aile Nord, Rez-de-chaussée'],
            ['nom' => 'Urgences', 'code' => 'URG', 'localisation' => 'Entrée principale, Rez-de-chaussée'],
            ['nom' => 'Cardiologie', 'code' => 'CARD', 'localisation' => 'Aile Est, 1er étage'],
            ['nom' => 'Réanimation', 'code' => 'REA', 'localisation' => 'Aile Sud, 2ème étage'],
            ['nom' => 'Laboratoire d\'analyses', 'code' => 'LABO', 'localisation' => 'Aile Nord, Sous-sol'],
            ['nom' => 'Maternité', 'code' => 'MAT', 'localisation' => 'Aile Ouest, Rez-de-chaussée'],
            ['nom' => 'Pédiatrie', 'code' => 'PED', 'localisation' => 'Aile Ouest, 1er étage'],
            ['nom' => 'Stérilisation centrale', 'code' => 'STE', 'localisation' => 'Aile Sud, Sous-sol'],
            ['nom' => 'Oncologie', 'code' => 'ONCO', 'localisation' => 'Aile Est, 2ème étage'],
            ['nom' => 'Pharmacie', 'code' => 'PHAR', 'localisation' => 'Entrée principale, Rez-de-chaussée'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['code' => $service['code']],
                array_merge($service, ['actif' => true])
            );
        }
    }
}
