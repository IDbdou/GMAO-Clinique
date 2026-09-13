<?php

namespace Database\Seeders;

use App\Models\Intervention;
use App\Models\SatisfactionIntervention;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatisfactionInterventionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // On récupère les interventions terminées
        $interventions = Intervention::with(['service', 'technicien'])
            ->where('statut', 'terminee')
            ->get();

        // Chefs de service pour les évaluations
        $chefs = User::whereHas('roles', fn ($q) => $q->where('name', 'Chef de service'))->get()->keyBy('service_id');

        // Notes prédéfinies avec réalisme
        $evaluations = [
            // 5 étoiles - excellent
            [
                'titre' => 'Calibration annuelle Scanner CT',
                'note' => 5,
                'commentaire' => 'Intervention réalisée dans les délais. Technicien très professionnel et pédagogue. Explications claires sur les résultats de calibration. Équipement immédiatement opérationnel.',
                'evaluateur_service' => 'RAD',
            ],
            [
                'titre' => 'Remplacement membrane dialyseur',
                'note' => 5,
                'commentaire' => 'Intervention urgente gérée parfaitement. Le technicien est resté jusqu\'à la fin du test de stabilité. Machine remise en service rapidement, aucun patient déprogrammé. Bravo !',
                'evaluateur_service' => 'HEMO',
            ],
            [
                'titre' => 'Maintenance annuelle autoclave',
                'note' => 5,
                'commentaire' => 'Travail impeccable. Respect des protocoles de stérilisation. Documentation complète fournie. Tests Bowie-Dick réussis du premier coup.',
                'evaluateur_service' => 'STE',
            ],
            [
                'titre' => 'Réfrigérateur pharma - alarme température',
                'note' => 5,
                'commentaire' => 'Réactivité exemplaire ! Alarme à 14h, intervention terminée à 16h. Température stabilisée, stocks vaccins sauvegardés. Merci beaucoup.',
                'evaluateur_service' => 'PHAR',
            ],

            // 4 étoiles - très bien
            [
                'titre' => 'Remplacement bobines IRM',
                'note' => 4,
                'commentaire' => 'Travail de qualité. Un peu de retard sur le planning initial (2 jours au lieu de 1) mais résultat impeccable. Tests d\'acceptation conformes.',
                'evaluateur_service' => 'RAD',
            ],
            [
                'titre' => 'Réparation système hydraulique table OP',
                'note' => 4,
                'commentaire' => 'Réparation réussie. Petite nuisance pendant l\'intervention (bruit et odeur d\'huile dans le bloc). Tests de mouvements conformes.',
                'evaluateur_service' => 'BLOC',
            ],
            [
                'titre' => 'Calibration ECG et remplacement cables',
                'note' => 4,
                'commentaire' => 'Intervention rapide et propre. Signal désormais parfait. Manque juste une petite formation rapide du personnel sur la manipulation des nouveaux cables.',
                'evaluateur_service' => 'CARD',
            ],

            // 3 étoiles - moyen
            [
                'titre' => 'Réparation humidificateur incubateur',
                'note' => 3,
                'commentaire' => 'Réparation effectuée mais intervention plus longue que prévu (1 jour supplémentaire). Néonatologie a dû utiliser un incubateur de réserve. Résultat OK finalement.',
                'evaluateur_service' => 'PED',
            ],

            // 2 étoiles - insuffisant
            [
                'titre' => 'Remplacement lampe photomètre biochimie',
                'note' => 2,
                'commentaire' => 'Délai trop long (24h). Labo bloqué pendant ce temps, analyses reportées. De plus, le technicien n\'avait pas la pièce en stock, commande urgente nécessaire. À améliorer.',
                'evaluateur_service' => 'LABO',
            ],

            // 1 étoile - mauvais (intervention annulée)
            [
                'titre' => 'Mise à jour logiciel échographe',
                'note' => 1,
                'commentaire' => 'Intervention annulée sans préavis suffisant. Nous avions bloqué le créneau. Mauvaise communication du service biomédical avec le constructeur.',
                'evaluateur_service' => 'RAD',
            ],
        ];

        foreach ($evaluations as $data) {
            $intervention = $interventions->firstWhere('titre', $data['titre']);
            if (! $intervention) continue;

            $evaluateur = $chefs->get($intervention->service_id);

            SatisfactionIntervention::firstOrCreate(
                ['intervention_id' => $intervention->id],
                [
                    'intervention_id' => $intervention->id,
                    'evaluateur_id' => $evaluateur?->id ?? $intervention->demandeur_id ?? 1,
                    'note' => $data['note'],
                    'commentaire' => $data['commentaire'],
                ]
            );
        }
    }
}
