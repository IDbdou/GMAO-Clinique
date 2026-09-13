<?php

namespace App\Filament\Service\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class StatutSignalementsChart extends ChartWidget
{
    protected ?string $heading = 'Répartition des signalements';

    protected ?string $description = 'État actuel des signalements du service';

    protected ?string $maxHeight = '240px';

    protected int|string|array $columnSpan = ['lg' => 6, 'md' => 6];

    protected static ?int $sort = 12;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $serviceId = auth()->user()?->service_id;

        $nouveau = Intervention::where('service_id', $serviceId)
            ->where('type', 'curatif')
            ->where('statut', StatutIntervention::Nouveau)
            ->count();

        $enCours = Intervention::where('service_id', $serviceId)
            ->where('type', 'curatif')
            ->whereIn('statut', [StatutIntervention::Ouverte, StatutIntervention::EnCours])
            ->count();

        $termine = Intervention::where('service_id', $serviceId)
            ->where('type', 'curatif')
            ->where('statut', StatutIntervention::Terminee)
            ->count();

        $enAttente = Intervention::where('service_id', $serviceId)
            ->where('type', 'curatif')
            ->where('statut', StatutIntervention::EnAttente)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Signalements',
                    'data' => [$nouveau, $enCours, $termine, $enAttente],
                    'backgroundColor' => [
                        '#64748b', // slate
                        '#3b82f6', // blue
                        '#22c55e', // green
                        '#f59e0b', // amber
                    ],
                    'borderColor' => ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Nouveau (' . $nouveau . ')',
                'En cours (' . $enCours . ')',
                'Terminé (' . $termine . ')',
                'En attente (' . $enAttente . ')',
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '65%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
