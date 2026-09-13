<?php

namespace App\Filament\Service\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Equipement;
use App\Models\Intervention;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceStatsOverview extends BaseWidget
{
    protected ?string $heading = null;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $serviceId = auth()->user()?->service_id;
        $serviceNom = auth()->user()?->service?->nom ?? 'mon service';

        return [
            Stat::make('Équipements', Equipement::where('service_id', $serviceId)->count())
                ->description("Suivis dans {$serviceNom}")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->extraAttributes(['class' => 'fi-stat-with-gradient-primary'])
                ->color('primary'),

            Stat::make('Signalements', Intervention::where('service_id', $serviceId)
                ->where('type', 'curatif')
                ->count())
                ->description('Pannes déclarées')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->extraAttributes(['class' => 'fi-stat-with-gradient-warning'])
                ->color('warning'),

            Stat::make('En cours', Intervention::where('service_id', $serviceId)
                ->whereIn('statut', [
                    StatutIntervention::Nouveau,
                    StatutIntervention::Ouverte,
                    StatutIntervention::EnCours,
                ])
                ->count())
                ->description('Interventions actives')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->extraAttributes(['class' => 'fi-stat-with-gradient-danger'])
                ->color('danger'),
        ];
    }
}
