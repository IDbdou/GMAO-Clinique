<?php

namespace App\Filament\Widgets;

use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class CoutsParMoisChart extends ChartWidget
{
    protected ?string $heading = 'Coûts de maintenance';

    protected ?string $description = 'Évolution des dépenses sur les 6 derniers mois (MAD)';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = ['lg' => 6, 'md' => 6];

    protected static ?int $sort = 14;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $labels = [];
        $couts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $couts[] = Intervention::whereYear('date_demande', $date->year)
                ->whereMonth('date_demande', $date->month)
                ->sum('cout') ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Coûts (MAD)',
                    'data' => $couts,
                    'backgroundColor' => 'rgba(14, 165, 233, 0.15)',
                    'borderColor' => '#0ea5e9',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#0ea5e9',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7,
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
                        'callback' => 'function(value) { return value.toLocaleString() + " MAD"; }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
