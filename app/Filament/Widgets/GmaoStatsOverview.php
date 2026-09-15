<?php

namespace App\Filament\Widgets;

use App\Enums\StatutEquipement;
use App\Enums\StatutIntervention;
use App\Models\Equipement;
use App\Models\Intervention;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class GmaoStatsOverview extends BaseWidget
{
    protected static ?int $sort = -2;

    protected ?string $heading = null;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $totalEquipements = Equipement::count();
        $enPanne = Equipement::where('statut', StatutEquipement::EnPanne)->count();
        $enMaintenance = Equipement::where('statut', StatutEquipement::EnMaintenance)->count();
        $disponibles = Equipement::where('statut', StatutEquipement::EnService)->count();

        $tauxDispo = $totalEquipements > 0
            ? round(($disponibles / $totalEquipements) * 100, 1)
            : 0;

        $interventionsOuvertes = Intervention::whereIn('statut', [
            StatutIntervention::Nouveau,
            StatutIntervention::Ouverte,
            StatutIntervention::EnCours,
            StatutIntervention::EnAttente,
        ])->count();

        $interventionsTerminees = Intervention::where('statut', StatutIntervention::Terminee)
            ->whereMonth('date_fin', now()->month)
            ->count();

        $coutTotalAnnee = Intervention::whereYear('date_demande', now()->year)
            ->whereIn('statut', [StatutIntervention::Terminee, StatutIntervention::EnCours])
            ->sum('cout') ?? 0;

        $tauxColor = $tauxDispo >= 90 ? 'success' : ($tauxDispo >= 75 ? 'warning' : 'danger');
        $tauxIcon = $tauxDispo >= 90 ? 'heroicon-m-check-circle' : ($tauxDispo >= 75 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-x-circle');

        return [
            Stat::make('Équipements', $totalEquipements)
                ->description("{$disponibles} disponibles · {$enMaintenance} en maintenance · {$enPanne} en panne")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->icon('heroicon-m-cpu-chip')
                ->extraAttributes([
                    'class' => 'fi-stat-with-gradient-primary',
                ])
                ->color('primary'),

            Stat::make('Taux de disponibilité', $tauxDispo . ' %')
                ->description('Parc opérationnel en temps réel')
                ->descriptionIcon($tauxIcon)
                ->icon($tauxIcon)
                ->extraAttributes([
                    'class' => 'fi-stat-with-gradient-' . $tauxColor,
                ])
                ->color($tauxColor),

            Stat::make('Interventions actives', $interventionsOuvertes)
                ->description("{$interventionsTerminees} terminées ce mois")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->icon('heroicon-m-wrench-screwdriver')
                ->extraAttributes([
                    'class' => 'fi-stat-with-gradient-warning',
                ])
                ->color('warning'),

            Stat::make('Coûts ' . now()->format('Y'), number_format((float) $coutTotalAnnee, 0, ',', ' ') . ' MAD')
                ->description('Dépenses maintenance cette année')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-m-banknotes')
                ->extraAttributes([
                    'class' => 'fi-stat-with-gradient-info',
                ])
                ->color('info'),
        ];
    }
}
