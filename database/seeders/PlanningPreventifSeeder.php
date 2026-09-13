<?php

namespace Database\Seeders;

use App\Enums\FrequencePreventive;
use App\Models\Equipement;
use App\Models\PlanningPreventif;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanningPreventifSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $equipements = Equipement::all()->keyBy('code_inventaire');

        $plannings = [
            // === EN RETARD (alerte rouge) ===
            [
                'equipement_code' => 'RESP-001',
                'titre' => 'Maintenance trimestrielle respirateur',
                'description' => 'Remplacement filtres, contrôle valves, test fuite circuit patient.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->subDays(5),
                'derniere_date' => now()->subMonths(3)->subDays(5),
            ],
            [
                'equipement_code' => 'TABLE-OP-001',
                'titre' => 'Vérification semestrielle table opératoire',
                'description' => 'Vérification hydraulique, électronique, système Trendelenburg.',
                'frequence' => FrequencePreventive::Semestrielle,
                'prochaine_date' => now()->subDays(8),
                'derniere_date' => now()->subMonths(6)->subDays(8),
            ],
            [
                'equipement_code' => 'LINAC-001',
                'titre' => 'Contrôle qualité mensuel accélérateur',
                'description' => 'Dosimétrie, alignement faisceaux, tests sécurités interlocks.',
                'frequence' => FrequencePreventive::Mensuelle,
                'prochaine_date' => now()->subDays(3),
                'derniere_date' => now()->subMonth()->subDays(3),
            ],
            [
                'equipement_code' => 'BIST-001',
                'titre' => 'Vérification trimestrielle bistouri électrique',
                'description' => 'Contrôle puissance, test sécurité patients, nettoyage electrodes.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->subDays(12),
                'derniere_date' => now()->subMonths(3)->subDays(12),
            ],
            [
                'equipement_code' => 'MONI-HEMO-001',
                'titre' => 'Calibration semestrielle moniteur',
                'description' => 'Calibration ECG, SpO2, pression non invasive, alarmes.',
                'frequence' => FrequencePreventive::Semestrielle,
                'prochaine_date' => now()->subDays(2),
                'derniere_date' => now()->subMonths(6)->subDays(2),
            ],

            // === EN ALERTE (proche échéance, orange) ===
            [
                'equipement_code' => 'SCAN-CT-001',
                'titre' => 'Calibration mensuelle Scanner CT',
                'description' => 'Contrôle qualité image, calibration CT number, uniformité et bruit.',
                'frequence' => FrequencePreventive::Mensuelle,
                'prochaine_date' => now()->addDays(2),
                'derniere_date' => now()->subMonth()->addDays(2),
            ],
            [
                'equipement_code' => 'GEN-HEMO-001',
                'titre' => 'Maintenance mensuelle générateur dialyse',
                'description' => 'Vérification pressions, conductivité, calibration pompes.',
                'frequence' => FrequencePreventive::Mensuelle,
                'prochaine_date' => now()->addDays(3),
                'derniere_date' => now()->subMonth()->addDays(3),
            ],
            [
                'equipement_code' => 'DEFIB-001',
                'titre' => 'Test mensuel défibrillateur',
                'description' => 'Test décharge, batterie, charge condensateur et autotest.',
                'frequence' => FrequencePreventive::Mensuelle,
                'prochaine_date' => now()->addDays(4),
                'derniere_date' => now()->subMonth()->addDays(4),
            ],
            [
                'equipement_code' => 'CENTRI-001',
                'titre' => 'Maintenance annuelle centrifugeuse',
                'description' => 'Remplacement amortisseurs, graissage rotor, calibration vitesse.',
                'frequence' => FrequencePreventive::Annuelle,
                'prochaine_date' => now()->addDays(6),
                'derniere_date' => now()->subYear()->addDays(6),
            ],
            [
                'equipement_code' => 'MAMMO-001',
                'titre' => 'Contrôle annuel mammographe',
                'description' => 'Mesure dose, uniformité, résolution spatiale, contrôle compression.',
                'frequence' => FrequencePreventive::Annuelle,
                'prochaine_date' => now()->addDays(7),
                'derniere_date' => now()->subYear()->addDays(7),
            ],

            // === NORMAL (dans le mois, gris) ===
            [
                'equipement_code' => 'IRM-001',
                'titre' => 'Vérification trimestrielle IRM',
                'description' => 'Tests SNR, homogénéité, shim et vérification bobines.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->addDays(12),
                'derniere_date' => now()->subMonths(3)->addDays(12),
            ],
            [
                'equipement_code' => 'ECHO-001',
                'titre' => 'Maintenance trimestrielle échographe',
                'description' => 'Nettoyage sondes, calibration profondeur, vérification Doppler.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->addDays(15),
                'derniere_date' => now()->subMonths(3)->addDays(15),
            ],
            [
                'equipement_code' => 'FRIGO-PHAR-001',
                'titre' => 'Contrôle trimestriel réfrigérateur',
                'description' => 'Vérification thermostat, sondes température, étanchéité joints.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->addDays(18),
                'derniere_date' => now()->subMonths(3)->addDays(18),
            ],
            [
                'equipement_code' => 'ECG-001',
                'titre' => 'Calibration annuelle ECG',
                'description' => 'Calibration tension, dérivations, remplacement cables si nécessaire.',
                'frequence' => FrequencePreventive::Annuelle,
                'prochaine_date' => now()->addDays(20),
                'derniere_date' => now()->subYear()->addDays(20),
            ],
            [
                'equipement_code' => 'AUTO-001',
                'titre' => 'Maintenance annuelle autoclave',
                'description' => 'Remplacement joints, soupapes, calibration sondes, test Bowie-Dick.',
                'frequence' => FrequencePreventive::Annuelle,
                'prochaine_date' => now()->addDays(25),
                'derniere_date' => now()->subYear()->addDays(25),
            ],
            [
                'equipement_code' => 'LAVE-001',
                'titre' => 'Maintenance semestrielle laveur-désinfecteur',
                'description' => 'Détartrage, vérification pompes, remplacement joints porte.',
                'frequence' => FrequencePreventive::Semestrielle,
                'prochaine_date' => now()->addDays(10),
                'derniere_date' => now()->subMonths(6)->addDays(10),
            ],
            [
                'equipement_code' => 'VENT-001',
                'titre' => 'Vérification mensuelle ventilateur',
                'description' => 'Contrôle débits, pressions, alarmes, remplacement filtres.',
                'frequence' => FrequencePreventive::Mensuelle,
                'prochaine_date' => now()->addDays(8),
                'derniere_date' => now()->subMonth()->addDays(8),
            ],
            [
                'equipement_code' => 'POMPE-001',
                'titre' => 'Maintenance trimestrielle pompe à perfusion',
                'description' => 'Calibration débit, test occlusion, remplacement kit piston.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->addDays(14),
                'derniere_date' => now()->subMonths(3)->addDays(14),
            ],
            [
                'equipement_code' => 'INCUB-001',
                'titre' => 'Vérification mensuelle incubateur',
                'description' => 'Calibration température, humidité, test alarmes haute/basse température.',
                'frequence' => FrequencePreventive::Mensuelle,
                'prochaine_date' => now()->addDays(9),
                'derniere_date' => now()->subMonth()->addDays(9),
            ],
            [
                'equipement_code' => 'CTG-001',
                'titre' => 'Maintenance semestrielle cardiotocographe',
                'description' => 'Vérification capteurs FHR/TOCO, calibration signaux, test enregistrement.',
                'frequence' => FrequencePreventive::Semestrielle,
                'prochaine_date' => now()->addDays(22),
                'derniere_date' => now()->subMonths(6)->addDays(22),
            ],
            [
                'equipement_code' => 'AUTO-PHAR-001',
                'titre' => 'Maintenance annuelle automate pharmacie',
                'description' => 'Vérification bras robotique, calibration poids, nettoyage convoyeurs.',
                'frequence' => FrequencePreventive::Annuelle,
                'prochaine_date' => now()->addDays(30),
                'derniere_date' => now()->subYear()->addDays(30),
            ],
            [
                'equipement_code' => 'ANAL-HEM-001',
                'titre' => 'Maintenance trimestrielle analyseur hématologie',
                'description' => 'Nettoyage hydraulique, calibration globules, contrôles internes.',
                'frequence' => FrequencePreventive::Trimestrielle,
                'prochaine_date' => now()->addDays(17),
                'derniere_date' => now()->subMonths(3)->addDays(17),
            ],
        ];

        foreach ($plannings as $data) {
            $eq = $equipements->get($data['equipement_code']);
            if (! $eq) continue;

            unset($data['equipement_code']);
            $data['equipement_id'] = $eq->id;

            PlanningPreventif::firstOrCreate(
                ['equipement_id' => $eq->id, 'titre' => $data['titre']],
                $data,
            );
        }
    }
}
