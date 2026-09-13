<?php

namespace Database\Seeders;

use App\Models\ContratMaintenance;
use App\Models\Equipement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContratMaintenanceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $equipements = Equipement::all()->keyBy('code_inventaire');

        $contrats = [
            [
                'equipement_code' => 'SCAN-CT-001',
                'reference' => 'CT-SIEM-2024-001',
                'type_contrat' => 'Contrat global',
                'fournisseur' => 'Siemens Healthineers',
                'contact' => 'technique.maroc@siemens-healthineers.com / +212 5XX-XXXXXX',
                'date_debut' => '2024-01-15',
                'date_fin' => '2025-01-14',
                'cout_annuel' => 145000.00,
                'notes' => 'Maintenance corrective & préventive incluses. Pièces détachées couvertes hors consommables. Délai intervention max 48h. Hotline 24/7.',
            ],
            [
                'equipement_code' => 'IRM-001',
                'reference' => 'IRM-GE-2023-002',
                'type_contrat' => 'Contrat global',
                'fournisseur' => 'GE Healthcare',
                'contact' => 'service.maroc@gehealthcare.com / +212 5XX-XXXXXX',
                'date_debut' => '2023-06-20',
                'date_fin' => '2026-06-19',
                'cout_annuel' => 220000.00,
                'notes' => 'Contrat 3 ans. Inclut cryogénie complète. Formation techniciens biomédicaux internes prévue trimestriellement.',
            ],
            [
                'equipement_code' => 'RESP-001',
                'reference' => 'RESP-HAM-2024-003',
                'type_contrat' => 'Pièces & main d\'œuvre',
                'fournisseur' => 'Hamilton Medical',
                'contact' => 'support@hamilton-medical.com / +41 XX XXX XX XX',
                'date_debut' => '2024-02-28',
                'date_fin' => '2025-02-27',
                'cout_annuel' => 18000.00,
                'notes' => 'Pièces de rechange incluses sauf consommables. Mise à jour logiciel gratuite. Support à distance inclus.',
            ],
            [
                'equipement_code' => 'GEN-HEMO-001',
                'reference' => 'DIA-FRE-2024-004',
                'type_contrat' => 'Contrat global',
                'fournisseur' => 'Fresenius Medical Care',
                'contact' => 'service.technique@fresenius.com / +212 5XX-XXXXXX',
                'date_debut' => '2024-09-01',
                'date_fin' => '2025-08-31',
                'cout_annuel' => 35000.00,
                'notes' => 'Contrat annuel renouvelable. Maintenance trimestrielle planifiée. Membranes dialyseuses non incluses.',
            ],
            [
                'equipement_code' => 'DEFIB-001',
                'reference' => 'DEF-ZOL-2023-005',
                'type_contrat' => 'Pièces & main d\'œuvre',
                'fournisseur' => 'Zoll Medical',
                'contact' => 'support@zoll.com / +33 X XX XX XX XX',
                'date_debut' => '2023-11-12',
                'date_fin' => '2025-11-11',
                'cout_annuel' => 8500.00,
                'notes' => 'Contrat 2 ans. Batteries remplacées 1x/an. Calibration annuelle incluse.',
            ],
            [
                'equipement_code' => 'AUTO-001',
                'reference' => 'STE-GET-2024-006',
                'type_contrat' => 'Maintenance préventive',
                'fournisseur' => 'Getinge',
                'contact' => 'service.maroc@getinge.com / +212 5XX-XXXXXX',
                'date_debut' => '2024-04-01',
                'date_fin' => '2025-03-31',
                'cout_annuel' => 28000.00,
                'notes' => 'Maintenance préventive 2x/an. Joints et soupapes inclus. Tests Bowie-Dick trimestriels en sus.',
            ],
            [
                'equipement_code' => 'LINAC-001',
                'reference' => 'ONCO-VAR-2022-007',
                'type_contrat' => 'Contrat global',
                'fournisseur' => 'Varian Medical',
                'contact' => 'service.emea@varian.com / +33 X XX XX XX XX',
                'date_debut' => '2022-08-01',
                'date_fin' => '2025-07-31',
                'cout_annuel' => 320000.00,
                'notes' => 'Contrat stratégique 3 ans. Maintenance mensuelle incluse. Dosimétrie externe 2x/an en sus. Formation physicists incluse.',
            ],
            [
                'equipement_code' => 'ECHO-001',
                'reference' => 'RAD-PHI-2024-008',
                'type_contrat' => 'Pièces & main d\'œuvre',
                'fournisseur' => 'Philips Healthcare',
                'contact' => 'support@philips.com / +212 5XX-XXXXXX',
                'date_debut' => '2024-04-10',
                'date_fin' => '2026-04-09',
                'cout_annuel' => 22000.00,
                'notes' => 'Sondes couvertes hors dommages accidentels. Mise à jour logiciel annuelle incluse.',
            ],
            [
                'equipement_code' => 'TABLE-OP-001',
                'reference' => 'BLOC-GET-2024-009',
                'type_contrat' => 'Maintenance préventive',
                'fournisseur' => 'Getinge',
                'contact' => 'service.maroc@getinge.com / +212 5XX-XXXXXX',
                'date_debut' => '2024-03-01',
                'date_fin' => '2025-02-28',
                'cout_annuel' => 12000.00,
                'notes' => 'Vérification semestrielle système hydraulique. Pièces hydrauliques incluses.',
            ],
            [
                'equipement_code' => 'INCUB-001',
                'reference' => 'PED-GE-2023-010',
                'type_contrat' => 'Contrat global',
                'fournisseur' => 'GE Healthcare',
                'contact' => 'neonatal.support@gehealthcare.com / +33 X XX XX XX XX',
                'date_debut' => '2023-06-15',
                'date_fin' => '2026-06-14',
                'cout_annuel' => 18000.00,
                'notes' => 'Contrat 3 ans. Maintenance mensuelle. Capteurs température/humidité remplacés annuellement.',
            ],
            // === SANS CONTRAT (garantie constructeur seule) ===
            [
                'equipement_code' => 'VENT-001',
                'reference' => 'REA-DRA-2023-GAR',
                'type_contrat' => 'Garantie constructeur',
                'fournisseur' => 'Dräger Medical',
                'contact' => 'service@draeger.com / +49 XX XXX XX-0',
                'date_debut' => '2023-05-10',
                'date_fin' => '2025-05-09',
                'cout_annuel' => 0.00,
                'notes' => 'Garantie constructeur 2 ans. Pièces et main d\'œuvre incluses. Extension garantie envisagée.',
            ],
            [
                'equipement_code' => 'POMPE-001',
                'reference' => 'REA-BBR-2024-GAR',
                'type_contrat' => 'Garantie constructeur',
                'fournisseur' => 'B. Braun',
                'contact' => 'technique@bbraun.com / +33 X XX XX XX XX',
                'date_debut' => '2024-01-01',
                'date_fin' => '2026-12-31',
                'cout_annuel' => 0.00,
                'notes' => 'Garantie 3 ans incluse dans l\'achat. Contrat post-garantie à négocier avant fin 2026.',
            ],
            // === CONTRAT EXPIRÉ (pour tester alertes) ===
            [
                'equipement_code' => 'ANAL-BIO-001',
                'reference' => 'LABO-ROC-2022-011',
                'type_contrat' => 'Contrat global',
                'fournisseur' => 'Roche Diagnostics',
                'contact' => 'service@roche.com / +212 5XX-XXXXXX',
                'date_debut' => '2022-02-10',
                'date_fin' => '2024-02-09',
                'cout_annuel' => 55000.00,
                'actif' => false,
                'notes' => 'CONTRAT EXPIRÉ. Renouvellement en cours de négociation. Tarif 2025 en discussion.',
            ],
            [
                'equipement_code' => 'CENTRI-001',
                'reference' => 'LABO-EPP-2023-012',
                'type_contrat' => 'Pièces & main d\'œuvre',
                'fournisseur' => 'Eppendorf',
                'contact' => 'support@eppendorf.com / +49 XX XXX-XX-0',
                'date_debut' => '2023-06-20',
                'date_fin' => '2024-06-19',
                'cout_annuel' => 3500.00,
                'actif' => false,
                'notes' => 'CONTRAT EXPIRÉ. Décision à prendre : renouvellement ou maintenance interne.',
            ],
            // === ALERTE EXPIRATION PROCHE ===
            [
                'equipement_code' => 'FRIGO-PHAR-001',
                'reference' => 'PHAR-LIE-2023-013',
                'type_contrat' => 'Maintenance préventive',
                'fournisseur' => 'Liebherr',
                'contact' => 'service@liebherr.com / +212 5XX-XXXXXX',
                'date_debut' => '2023-02-28',
                'date_fin' => '2025-02-27',
                'cout_annuel' => 4200.00,
                'delai_alerte_mois' => 2,
                'notes' => 'Contrat annuel renouvelable. Maintenance trimestrielle thermostat. Alerte prévue décembre 2024.',
            ],
            [
                'equipement_code' => 'ECG-001',
                'reference' => 'CARD-SCH-2024-014',
                'type_contrat' => 'Pièces & main d\'œuvre',
                'fournisseur' => 'Schiller',
                'contact' => 'technique@schiller.ch / +41 XX XXX XX XX',
                'date_debut' => '2024-04-22',
                'date_fin' => '2025-04-21',
                'cout_annuel' => 3500.00,
                'delai_alerte_mois' => 1,
                'notes' => 'Cables patients et consommables exclus. Calibration annuelle incluse. Alerte prévue mars 2025.',
            ],
        ];

        foreach ($contrats as $data) {
            $eq = $equipements->get($data['equipement_code']);
            if (! $eq) continue;

            unset($data['equipement_code']);
            $data['equipement_id'] = $eq->id;

            ContratMaintenance::firstOrCreate(
                ['reference' => $data['reference']],
                $data,
            );
        }
    }
}
