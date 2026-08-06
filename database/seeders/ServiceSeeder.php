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
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['code' => $service['code']],
                array_merge($service, ['actif' => true])
            );
        }
    }
}
