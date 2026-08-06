<?php

namespace App\Filament\Widgets;

use App\Enums\StatutEquipement;
use App\Enums\StatutIntervention;
use App\Models\Equipement;
use App\Models\Intervention;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GmaoStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Vue d\'ensemble de la clinique';

    protected ?string $description = 'Indicateurs clés de la maintenance en temps réel.';

    protected function getStats(): array
    {
        return [
            Stat::make('Équipements', Equipement::count())
                ->description('Parc médical total')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('En panne', Equipement::where('statut', StatutEquipement::EnPanne)->count())
                ->description('Équipements hors service')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Interventions en cours', Intervention::whereIn('statut', [
                StatutIntervention::Nouveau,
                StatutIntervention::Ouverte,
                StatutIntervention::EnCours,
                StatutIntervention::EnAttente,
            ])->count())
                ->description('Dont nouveaux signalements')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Interventions ce mois', Intervention::whereMonth('date_demande', now()->month)
                ->whereYear('date_demande', now()->year)
                ->count())
                ->description('Demandes en ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
