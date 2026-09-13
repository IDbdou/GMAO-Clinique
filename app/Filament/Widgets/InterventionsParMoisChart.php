<?php

namespace App\Filament\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class InterventionsParMoisChart extends ChartWidget
{
    protected ?string $heading = 'Volume d\'interventions';

    protected ?string $description = 'Évolution sur les 6 derniers mois (créées vs terminées)';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = ['lg' => 6, 'md' => 6];

    protected static ?int $sort = 13;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = [];
        $creees = [];
        $terminees = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $creees[] = Intervention::whereYear('date_demande', $date->year)
                ->whereMonth('date_demande', $date->month)
                ->count();

            $terminees[] = Intervention::whereYear('date_fin', $date->year)
                ->whereMonth('date_fin', $date->month)
                ->where('statut', StatutIntervention::Terminee)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Créées',
                    'data' => $creees,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Terminées',
                    'data' => $terminees,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'align' => 'end',
                ],
            ],
        ];
    }
}
