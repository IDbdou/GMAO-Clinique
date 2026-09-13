<?php

namespace Database\Seeders;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutCompteRendu;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\CompteRendu;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoGmaoSeeder extends Seeder
{
    public function run(): void
    {
        $tech1 = User::where('email', 'tech1@gmao.local')->first();
        $tech2 = User::where('email', 'tech2@gmao.local')->first();
        $tech3 = User::where('email', 'tech3@gmao.local')->first();
        $tech4 = User::where('email', 'tech4@gmao.local')->first();

        // === INTERVENTIONS EN COURS / OUVERTES ===
        $interventionsEnCours = [
            [
                'equipement_code' => 'GEN-HEMO-002',
                'technicien_id' => $tech1?->id,
                'titre' => 'Panne pompe à sang - Générateur hémodialyse 2',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::EnCours,
                'description' => 'Arrêt intempestif de la pompe pendant une séance. Nécessite remplacement module hydraulique.',
                'date_demande' => now()->subDays(2),
                'date_planifiee' => now(),
            ],
            [
                'equipement_code' => 'MAMMO-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Maintenance préventive mammographe',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Ouverte,
                'description' => "Contrôle annual de la qualité d'image et calibration système de compression.",
                'date_demande' => now()->subHours(6),
                'date_planifiee' => now()->addDays(3),
            ],
            [
                'equipement_code' => 'DEFIB-001',
                'technicien_id' => $tech3?->id,
                'titre' => 'Défibrillateur - erreur batterie',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::EnCours,
                'description' => "Message d'erreur batterie faible persistant malgré remplacement. Vérifier circuit de charge.",
                'date_demande' => now()->subDays(1),
                'date_planifiee' => now()->subHours(4),
            ],
            [
                'equipement_code' => 'POMPE-001',
                'technicien_id' => $tech4?->id,
                'titre' => 'Pompe à perfusion - alarme occlusion',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::EnCours,
                'description' => 'Alarme occlusion récurrente. Contrôle capteur de pression et remplacement kit piston.',
                'date_demande' => now()->subHours(8),
                'date_planifiee' => now()->subHours(2),
            ],
            [
                'equipement_code' => 'RESP-001',
                'technicien_id' => $tech2?->id,
                'titre' => 'Maintenance préventive trimestrielle respirateur',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Ouverte,
                'description' => 'Contrôle des valves, remplacement des filtres et test fuite circuit patient.',
                'date_demande' => now()->subHours(6),
                'date_planifiee' => now()->addDays(2),
            ],
            [
                'equipement_code' => 'CENTRI-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Centrifugeuse - vibration anormale',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::Ouverte,
                'description' => "Vibrations excessives pendant la phase d'accélération. Vérifier rotor et amortisseurs.",
                'date_demande' => now()->subDays(3),
                'date_planifiee' => now()->addDay(),
            ],
            [
                'equipement_code' => 'LINAC-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Contrôle qualité mensuel accélérateur',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::Ouverte,
                'description' => 'Vérification dosimétrie, alignement faisceaux et test sécurités interlocks.',
                'date_demande' => now()->subDays(1),
                'date_planifiee' => now()->addDays(5),
            ],
        ];

        foreach ($interventionsEnCours as $data) {
            $eq = Equipement::where('code_inventaire', $data['equipement_code'])->first();
            if (! $eq) continue;
            unset($data['equipement_code']);
            $data['equipement_id'] = $eq->id;
            $data['service_id'] = $eq->service_id;
            Intervention::firstOrCreate(
                ['titre' => $data['titre'], 'equipement_id' => $eq->id],
                $data,
            );
        }

        // === INTERVENTIONS TERMINÉES (avec comptes-rendus) ===
        $interventionsTerminees = [
            [
                'equipement_code' => 'SCAN-CT-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Calibration annuelle Scanner CT',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::Terminee,
                'description' => "Calibration et contrôle qualité image. Écarts dans les tolérances acceptables.",
                'rapport' => 'Calibration effectuée, écarts dans les tolérances. RAS. Tous les fantômes conformes.',
                'date_demande' => now()->subDays(45),
                'date_debut' => now()->subDays(44),
                'date_fin' => now()->subDays(44),
                'cout' => 4500.00,
                'compte_rendu' => [
                    'observations' => "Calibration complète effectuée selon protocole constructeur. Mesures de CT number, bruit, uniformité, et résolution spatiale conformes. Tests avec fantômes ACR passés avec succès. Aucune dérive détectée depuis la dernière calibration.",
                    'pieces_utilisees' => "Fantôme ACR standard, sources de calibration Siemens (ref. SIEM-CAL-2024), solution ionique 10mg/ml.",
                    'temps_passe' => 8,
                    'cout_main_oeuvre' => 2800.00,
                    'cout_pieces' => 1700.00,
                ],
            ],
            [
                'equipement_code' => 'IRM-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Remplacement bobines IRM',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::Terminee,
                'description' => 'Bobine tête endommagée suite choc. Remplacement nécessaire.',
                'rapport' => 'Remplacement bobine tête effectué. Tests réussis. Patient OK.',
                'date_demande' => now()->subDays(20),
                'date_debut' => now()->subDays(18),
                'date_fin' => now()->subDays(17),
                'cout' => 18500.00,
                'compte_rendu' => [
                    'observations' => "Bobine tête 32 canaux endommagée (fissure carter + coupure câble coaxial) suite à choc lors transport patient urgence. Remplacement par bobine neuve GE Healthcare ref. 5140945. Tests SNR, cartographie de phase et shim passés. Validation IRM fonctionnelle réalisée.",
                    'pieces_utilisees' => "Bobine tête 32 canaux GE SIGNA Artist (ref. 5140945, 15800 MAD), connecteurs SMC, câble blindé coaxial 5m.",
                    'temps_passe' => 12,
                    'cout_main_oeuvre' => 1500.00,
                    'cout_pieces' => 17000.00,
                ],
            ],
            [
                'equipement_code' => 'TABLE-OP-001',
                'technicien_id' => $tech2?->id,
                'titre' => 'Réparation système hydraulique table OP',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::Terminee,
                'description' => "Table ne monte plus en hauteur. Fuite hydraulique suspectée au niveau du vérin principal.",
                'rapport' => 'Joint torique vérin remplacé. Purge circuit. Tests mouvements OK.',
                'date_demande' => now()->subDays(30),
                'date_debut' => now()->subDays(28),
                'date_fin' => now()->subDays(27),
                'cout' => 3200.00,
                'compte_rendu' => [
                    'observations' => "Fuite hydraulique importante au niveau du joint torique du vérin principal (position Trendelenburg). Démontage complet du groupe hydraulique. Remplacement joint torique NBR 70 Shore 120x3mm. Purge et remplissage huile hydraulique ISO VG 32. Tests de montée/descente et inclinaison conformes. Pas de fuite résiduelle après 24h.",
                    'pieces_utilisees' => "Joint torique NBR 120x3mm (x2), huile hydraulique ISO VG 32 (5L), absorbant universel.",
                    'temps_passe' => 6,
                    'cout_main_oeuvre' => 1200.00,
                    'cout_pieces' => 2000.00,
                ],
            ],
            [
                'equipement_code' => 'GEN-HEMO-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Remplacement membrane dialyseur',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::Terminee,
                'description' => 'Alarme fuite membrane détectée par conductivité. Arrêt immédiat séance.',
                'rapport' => 'Membrane dialyseur remplacée. Test étanchéité OK. Machine remise en service.',
                'date_demande' => now()->subDays(15),
                'date_debut' => now()->subDays(15),
                'date_fin' => now()->subDays(14),
                'cout' => 1200.00,
                'compte_rendu' => [
                    'observations' => "Alarme fuite membrane conductivité (code E-37). Arrêt immédiat séance en cours. Démontage module dialyseur : membrane dialyseuse polysulfone 1.4m² perforée au niveau segment proximal. Remplacement par membrane neuve Fresenius F60S. Test d'étanchéité automatique passé. Contrôle conductivité post-réparation OK. Machine remise en service sous surveillance renforcée 24h.",
                    'pieces_utilisees' => "Membrane dialyseuse Fresenius F60S (ref. 5008201, 850 MAD), joints toriques module, solution anticalcaire.",
                    'temps_passe' => 4,
                    'cout_main_oeuvre' => 350.00,
                    'cout_pieces' => 850.00,
                ],
            ],
            [
                'equipement_code' => 'AUTO-001',
                'technicien_id' => $tech2?->id,
                'titre' => 'Maintenance annuelle autoclave',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Terminee,
                'description' => "Entretien annuel avec remplacement joints porte, vérification soupapes sécurité.",
                'rapport' => 'Maintenance effectuée selon procédure constructeur. Test Bowie-Dick OK.',
                'date_demande' => now()->subDays(60),
                'date_debut' => now()->subDays(59),
                'date_fin' => now()->subDays(58),
                'cout' => 2800.00,
                'compte_rendu' => [
                    'observations' => "Maintenance préventive annuelle selon plan constructeur Getinge. Remplacement joints porte (supérieure + inférieure). Vérification soupapes de sécurité pression et température. Nettoyage résistances chauffantes et chambre stérilisation. Calibration sondes PT100. Test Bowie-Dick passé avec succès. Test Helix passé. Documentation constructeur mise à jour.",
                    'pieces_utilisees' => "Kit joints porte Getinge HS6610 (ref. 60666001, 1800 MAD), sonde PT100, pastilles Bowie-Dick (x10), Helix test.",
                    'temps_passe' => 10,
                    'cout_main_oeuvre' => 1000.00,
                    'cout_pieces' => 1800.00,
                ],
            ],
            [
                'equipement_code' => 'FRIGO-PHAR-001',
                'technicien_id' => $tech3?->id,
                'titre' => 'Réfrigérateur pharma - alarme température',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::Terminee,
                'description' => "Alarme température haute +8°C. Risque pour stock vaccins.",
                'rapport' => 'Thermostat défectueux remplacé. Température stabilisée à +5°C. Alerte arrêtée.',
                'date_demande' => now()->subDays(10),
                'date_debut' => now()->subDays(10),
                'date_fin' => now()->subDays(9),
                'cout' => 650.00,
                'compte_rendu' => [
                    'observations' => "Alarme température haute déclenchée à +8.3°C alors que consigne est +5°C. Thermostat électronique Liebherr défectueux (hystérésis anormale). Remplacement par thermostat de rechange. Recalibration plage +2/+8°C. Stabilisation confirmée après 4h de monitoring. Pas de dégradation des stocks vaccins constatée (enregistrement température continu validé).",
                    'pieces_utilisees' => "Thermostat électronique Liebherr MKv (ref. 9590182, 450 MAD).",
                    'temps_passe' => 3,
                    'cout_main_oeuvre' => 200.00,
                    'cout_pieces' => 450.00,
                ],
            ],
            [
                'equipement_code' => 'ECG-001',
                'technicien_id' => $tech3?->id,
                'titre' => 'Calibration ECG et remplacement cables',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Terminee,
                'description' => "Signal bruité sur dérivation V2-V4. Cables usés à remplacer.",
                'rapport' => 'Jeu de 10 cables patients neufs installés. Calibration tension OK.',
                'date_demande' => now()->subDays(25),
                'date_debut' => now()->subDays(24),
                'date_fin' => now()->subDays(24),
                'cout' => 890.00,
                'compte_rendu' => [
                    'observations' => "Signal bruité intermittent sur dérivations précordiales V2 à V4. Diagnostic : cables patients usés (isolation dégradée, micro-coupures). Remplacement complet du jeu de 10 cables Schiller AT-10. Calibration amplitude (1mV = 10mm) conforme. Tests dérivations sur patient simulé passés. Nettoyage boitier et connecteurs.",
                    'pieces_utilisees' => "Jeu cables patients ECG Schiller AT-10 (ref. 2.400111, 690 MAD), alcool isopropylique 70%.",
                    'temps_passe' => 2.5,
                    'cout_main_oeuvre' => 200.00,
                    'cout_pieces' => 690.00,
                ],
            ],
            [
                'equipement_code' => 'INCUB-001',
                'technicien_id' => $tech4?->id,
                'titre' => 'Réparation humidificateur incubateur',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Haute,
                'statut' => StatutIntervention::Terminee,
                'description' => "Niveau humidité ne monte pas au-delà de 40%. Chambre humidification à vérifier.",
                'rapport' => 'Résistance humidification calcifiée remplacée. Niveau 60% atteint.',
                'date_demande' => now()->subDays(8),
                'date_debut' => now()->subDays(7),
                'date_fin' => now()->subDays(7),
                'cout' => 1450.00,
                'compte_rendu' => [
                    'observations' => "Humidification stagnante à 35-40% malgré consigne 60%. Inspection chambre humidification : résistance chauffante calcifiée (eau dure). Remplacement résistance et sonde humidité. Nettoyage détartrage chambre avec solution citrique. Remplissage eau distillée. Tests : montée humidité 35%>60% en 15min. Stabilisation confirmée. Contrôle température peau OK.",
                    'pieces_utilisees' => "Résistance humidification Giraffe OmniBed (ref. 1005-0001-000, 1150 MAD), sonde humidité SHT30, acide citrique.",
                    'temps_passe' => 5,
                    'cout_main_oeuvre' => 300.00,
                    'cout_pieces' => 1150.00,
                ],
            ],
            [
                'equipement_code' => 'ANAL-BIO-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Remplacement lampe photomètre biochimie',
                'type' => TypeIntervention::Curatif,
                'priorite' => PrioriteIntervention::Urgente,
                'statut' => StatutIntervention::Terminee,
                'description' => "Erreur luminosité photomètre. Impossible de valider le blank.",
                'rapport' => 'Lampe halogène remplacée. Calibration 5 niveaux OK. Contrôle interne passé.',
                'date_demande' => now()->subDays(12),
                'date_debut' => now()->subDays(11),
                'date_fin' => now()->subDays(11),
                'cout' => 2100.00,
                'compte_rendu' => [
                    'observations' => "Erreur luminosité photomètre 340nm (intensité < 80%). Impossible de valider le blank H2O. Diagnostic : lampe halogène Philips 12V 20W usée ( filament cassé partiellement). Remplacement lampe neuve. Réalignement optique. Calibration photométrique 5 niveaux (340, 405, 510, 600, 700nm) conformes. Contrôle interne quotidien passé. Contrôle externe Bio-Rad passé.",
                    'pieces_utilisees' => "Lampe halogène Philips 12V 20W (ref. 8407, 1800 MAD), lingettes optiques, contrôles Bio-Rad.",
                    'temps_passe' => 6,
                    'cout_main_oeuvre' => 300.00,
                    'cout_pieces' => 1800.00,
                ],
            ],
        ];

        foreach ($interventionsTerminees as $data) {
            $crData = $data['compte_rendu'] ?? null;
            unset($data['compte_rendu']);

            $eq = Equipement::where('code_inventaire', $data['equipement_code'])->first();
            if (! $eq) continue;
            unset($data['equipement_code']);
            $data['equipement_id'] = $eq->id;
            $data['service_id'] = $eq->service_id;

            $intervention = Intervention::firstOrCreate(
                ['titre' => $data['titre'], 'equipement_id' => $eq->id],
                $data,
            );

            // Créer le compte-rendu associé
            if ($crData && ! $intervention->compteRendu) {
                $crData['intervention_id'] = $intervention->id;
                $crData['technicien_id'] = $intervention->technicien_id;
                $crData['statut'] = StatutCompteRendu::Valide;
                $crData['date_soumission'] = $intervention->date_fin;
                $crData['date_validation'] = $intervention->date_fin?->clone()->addHours(4);
                $crData['signature_technicien'] = $intervention->technicien?->name ?? 'Technicien';
                $crData['signature_chef_service'] = $intervention->service?->chef?->name ?? 'Chef de service';
                $crData['cout_total'] = ($crData['cout_main_oeuvre'] ?? 0) + ($crData['cout_pieces'] ?? 0);
                $crData['commentaire_validation'] = 'Compte-rendu validé. Travaux conformes aux normes en vigueur.';

                CompteRendu::firstOrCreate(
                    ['intervention_id' => $intervention->id],
                    $crData
                );
            }
        }

        // === INTERVENTIONS ANNULÉES ===
        $interventionsAnnulees = [
            [
                'equipement_code' => 'ECHO-001',
                'technicien_id' => $tech1?->id,
                'titre' => 'Mise à jour logiciel échographe',
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Annulee,
                'description' => "Mise à jour firmware prévue. Annulée car constructeur a reporté la release.",
                'rapport' => 'Annulée - nouveau firmware pas encore disponible. Reportée au trimestre prochain.',
                'date_demande' => now()->subDays(40),
                'date_debut' => null,
                'date_fin' => now()->subDays(38),
                'cout' => 0.00,
            ],
        ];

        foreach ($interventionsAnnulees as $data) {
            $eq = Equipement::where('code_inventaire', $data['equipement_code'])->first();
            if (! $eq) continue;
            unset($data['equipement_code']);
            $data['equipement_id'] = $eq->id;
            $data['service_id'] = $eq->service_id;
            Intervention::firstOrCreate(
                ['titre' => $data['titre'], 'equipement_id' => $eq->id],
                $data,
            );
        }
    }
}
