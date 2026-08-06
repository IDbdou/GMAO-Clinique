<?php

namespace Database\Seeders;

use App\Models\Equipement;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipementSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $services = Service::all()->keyBy('code');

        $equipements = [
            [
                'nom' => 'Scanner CT',
                'code_inventaire' => 'SCAN-CT-001',
                'numero_serie' => 'SN-CT-123456',
                'marque' => 'Siemens',
                'modele' => 'Somatom Go',
                'service_id' => $services['RAD']->id,
                'localisation' => 'Salle Scanner 1',
                'criticite' => 'critique',
                'statut' => 'en_service',
                'date_mise_en_service' => '2022-01-15',
                'fournisseur' => 'Siemens Healthineers',
            ],
            [
                'nom' => 'IRM',
                'code_inventaire' => 'IRM-001',
                'numero_serie' => 'SN-IRM-789012',
                'marque' => 'GE Healthcare',
                'modele' => 'SIGNA Artist',
                'service_id' => $services['RAD']->id,
                'localisation' => 'Salle IRM 2',
                'criticite' => 'critique',
                'statut' => 'en_service',
                'date_mise_en_service' => '2021-06-20',
                'fournisseur' => 'GE Healthcare',
            ],
            [
                'nom' => 'Table opératoire électrique',
                'code_inventaire' => 'TABLE-OP-001',
                'numero_serie' => 'SN-TOP-345678',
                'marque' => 'Maquet',
                'modele' => 'Magnus',
                'service_id' => $services['BLOC']->id,
                'localisation' => 'Bloc 1',
                'criticite' => 'haute',
                'statut' => 'en_service',
                'date_mise_en_service' => '2020-03-10',
                'fournisseur' => 'Getinge',
            ],
            [
                'nom' => 'Respirateur artificiel',
                'code_inventaire' => 'RESP-001',
                'numero_serie' => 'SN-RESP-901234',
                'marque' => 'Hamilton',
                'modele' => 'C6',
                'service_id' => $services['BLOC']->id,
                'localisation' => 'Bloc 2',
                'criticite' => 'critique',
                'statut' => 'en_service',
                'date_mise_en_service' => '2023-02-28',
                'fournisseur' => 'Hamilton Medical',
            ],
            [
                'nom' => 'Générateur hémodialyse',
                'code_inventaire' => 'GEN-HEMO-001',
                'numero_serie' => 'SN-GH-567890',
                'marque' => 'Fresenius',
                'modele' => '5008',
                'service_id' => $services['HEMO']->id,
                'localisation' => 'Box 1',
                'criticite' => 'haute',
                'statut' => 'en_service',
                'date_mise_en_service' => '2022-09-05',
                'fournisseur' => 'Fresenius Medical Care',
            ],
            [
                'nom' => 'Défibrillateur',
                'code_inventaire' => 'DEFIB-001',
                'numero_serie' => 'SN-DEF-112233',
                'marque' => 'Zoll',
                'modele' => 'X Series',
                'service_id' => $services['URG']->id,
                'localisation' => 'Box Urgences 3',
                'criticite' => 'critique',
                'statut' => 'en_service',
                'date_mise_en_service' => '2023-11-12',
                'fournisseur' => 'Zoll Medical',
            ],
        ];

        foreach ($equipements as $data) {
            Equipement::firstOrCreate(
                ['code_inventaire' => $data['code_inventaire']],
                $data
            );
        }
    }
}
