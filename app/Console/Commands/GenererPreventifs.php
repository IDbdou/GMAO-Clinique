<?php

namespace App\Console\Commands;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\Intervention;
use App\Models\PlanningPreventif;
use Illuminate\Console\Command;

class GenererPreventifs extends Command
{
    protected $signature = 'gmao:generer-preventifs';

    protected $description = 'Génère automatiquement les interventions préventives dont la date est atteinte ou dépassée';

    public function handle(): int
    {
        $plannings = PlanningPreventif::with('equipement')
            ->where('actif', true)
            ->where('prochaine_date', '<=', now()->toDateString())
            ->get();

        if ($plannings->isEmpty()) {
            $this->info('Aucune maintenance préventive à générer.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($plannings as $planning) {
            $equipement = $planning->equipement;
            if (! $equipement) continue;

            // Vérifie qu'une intervention identique n'existe pas déjà en cours
            $dejaExistant = Intervention::where('equipement_id', $equipement->id)
                ->where('titre', $planning->titre)
                ->whereIn('statut', [
                    StatutIntervention::Nouveau->value,
                    StatutIntervention::Ouverte->value,
                    StatutIntervention::EnCours->value,
                    StatutIntervention::EnAttente->value,
                ])
                ->whereDate('date_demande', '>=', $planning->prochaine_date)
                ->exists();

            if ($dejaExistant) continue;

            Intervention::create([
                'equipement_id' => $equipement->id,
                'service_id' => $equipement->service_id,
                'titre' => $planning->titre,
                'type' => TypeIntervention::Preventif,
                'priorite' => PrioriteIntervention::Normale,
                'statut' => StatutIntervention::Ouverte,
                'description' => $planning->description,
                'date_demande' => now(),
                'date_planifiee' => $planning->prochaine_date,
            ]);

            // Reporter la prochaine date
            $planning->reporterProchaineDate();
            $count++;
        }

        $this->info("{$count} intervention(s) préventive(s) générée(s).");
        return self::SUCCESS;
    }
}
