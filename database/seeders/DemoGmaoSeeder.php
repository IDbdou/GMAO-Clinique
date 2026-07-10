<?php

namespace Database\Seeders;

use App\Enums\Criticite;
use App\Enums\PrioriteIntervention;
use App\Enums\StatutEquipement;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoGmaoSeeder extends Seeder
{
    public function run(): void
    {
        $technicien = User::where('email', 'technicien@gmao-clinique.local')->first();

        $equipements = [
            [
                'nom' => 'Scanner CT 64 barrettes',
                'code_inventaire' => 'RAD-CT-001',
                'numero_serie' => 'SN-CT64-8842',
                'marque' => 'Siemens',
                'modele' => 'Somatom',
                'service' => 'Radiologie',
                'localisation' => 'Salle 2',
                'criticite' => Criticite::Critique,
                'statut' => StatutEquipement::EnService,
                'date_mise_en_service' => '2022-03-15',
                'fournisseur' => 'Siemens Healthineers',
            ],
            [
                'nom' => 'Générateur de dialyse',
                'code_inventaire' => 'HEM-DIA-004',
                'numero_serie' => 'SN-DIA-1207',
                'marque' => 'Fresenius',
                'modele' => '4008S',
                'service' => 'Hémodialyse',
                'localisation' => 'Poste 4',
                'criticite' => Criticite::Haute,
                'statut' => StatutEquipement::EnPanne,
                'date_mise_en_service' => '2021-09-01',
                'fournisseur' => 'Fresenius Medical Care',
            ],
            [
                'nom' => 'Respirateur de bloc',
                'code_inventaire' => 'BLO-RESP-002',
                'numero_serie' => 'SN-RESP-3391',
                'marque' => 'Dräger',
                'modele' => 'Fabius',
                'service' => 'Bloc opératoire',
                'localisation' => 'Bloc 1',
                'criticite' => Criticite::Critique,
                'statut' => StatutEquipement::EnMaintenance,
                'date_mise_en_service' => '2023-01-20',
                'fournisseur' => 'Dräger Medical',
            ],
        ];

        foreach ($equipements as $data) {
            Equipement::firstOrCreate(
                ['code_inventaire' => $data['code_inventaire']],
                $data,
            );
        }

        $ct = Equipement::where('code_inventaire', 'RAD-CT-001')->first();
        $dia = Equipement::where('code_inventaire', 'HEM-DIA-004')->first();
        $resp = Equipement::where('code_inventaire', 'BLO-RESP-002')->first();

        $interventions = [
            [
                'equipement_id' => $dia->id,
                'technicien_id' => $technicien?->id,
                'titre' => 'Panne pompe à sang',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::EnCours,
                'description' => 'Arrêt intempestif de la pompe pendant une séance.',
                'date_demande' => now()->subDays(1),
                'date_planifiee' => now(),
            ],
            [
                'equipement_id' => $resp->id,
                'technicien_id' => $technicien?->id,
                'titre' => 'Maintenance préventive trimestrielle',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Ouverte,
                'description' => 'Contrôle des valves et remplacement des filtres.',
                'date_demande' => now()->subHours(6),
                'date_planifiee' => now()->addDays(3),
            ],
            [
                'equipement_id' => $ct->id,
                'technicien_id' => $technicien?->id,
                'titre' => 'Calibration annuelle',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::Terminee,
                'description' => 'Calibration et contrôle qualité image.',
                'rapport' => 'Calibration effectuée, écarts dans les tolérances. RAS.',
                'date_demande' => now()->subDays(10),
                'date_debut' => now()->subDays(9),
                'date_fin' => now()->subDays(9),
                'cout' => 4500.00,
            ],
        ];

        foreach ($interventions as $data) {
            Intervention::firstOrCreate(
                ['titre' => $data['titre'], 'equipement_id' => $data['equipement_id']],
                $data,
            );
        }
    }
}
