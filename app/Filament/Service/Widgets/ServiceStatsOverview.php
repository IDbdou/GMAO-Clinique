<?php

namespace App\Filament\Service\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Equipement;
use App\Models\Intervention;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Mon service';

    protected ?string $description = 'Indicateurs de maintenance pour votre service.';

    protected function getStats(): array
    {
        $serviceId = auth()->user()?->service_id;

        return [
            Stat::make('Équipements', Equipement::where('service_id', $serviceId)->count())
                ->description('Dans mon service')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('Signalements', Intervention::where('service_id', $serviceId)
                ->where('type', 'curatif')
                ->count())
                ->description('Pannes déclarées')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('En cours', Intervention::where('service_id', $serviceId)
                ->whereIn('statut', [StatutIntervention::Nouveau, StatutIntervention::Ouverte, StatutIntervention::EnCours])
                ->count())
                ->description('Interventions actives')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),
        ];
    }
}
