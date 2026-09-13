<?php

namespace App\Filament\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class InterventionsParStatutChart extends ChartWidget
{
    protected ?string $heading = 'Répartition par statut';

    protected ?string $description = 'Vue d\'ensemble des interventions actives';

    protected ?string $maxHeight = '250px';

    protected int|string|array $columnSpan = ['lg' => 6, 'md' => 6];

    protected static ?int $sort = 12;

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $stats = [
            'Nouveau' => Intervention::where('statut', StatutIntervention::Nouveau)->count(),
            'Ouvert' => Intervention::where('statut', StatutIntervention::Ouverte)->count(),
            'En cours' => Intervention::where('statut', StatutIntervention::EnCours)->count(),
            'En attente' => Intervention::where('statut', StatutIntervention::EnAttente)->count(),
            'Terminé' => Intervention::where('statut', StatutIntervention::Terminee)->count(),
            'Annulé' => Intervention::where('statut', StatutIntervention::Annulee)->count(),
        ];

        $colors = [
            '#94a3b8', // slate-400
            '#0ea5e9', // sky-500
            '#3b82f6', // blue-500
            '#f59e0b', // amber-500
            '#10b981', // emerald-500
            '#f43f5e', // rose-500
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Interventions',
                    'data' => array_values($stats),
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => array_keys($stats),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
