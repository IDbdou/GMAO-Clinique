<?php

namespace App\Filament\Widgets;

use App\Enums\TypeIntervention;
use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class InterventionsParTypeChart extends ChartWidget
{
    protected ?string $heading = 'Répartition par type';

    protected ?string $description = 'Curatif vs Préventif sur l\'ensemble du parc';

    protected ?string $maxHeight = '250px';

    protected int|string|array $columnSpan = ['lg' => 6, 'md' => 6];

    protected static ?int $sort = 11;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $curatif = Intervention::where('type', TypeIntervention::Curatif)->count();
        $preventif = Intervention::where('type', TypeIntervention::Preventif)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Interventions',
                    'data' => [$curatif, $preventif],
                    'backgroundColor' => [
                        '#f59e0b', // amber-500 (curatif)
                        '#0ea5e9', // sky-500 (préventif)
                    ],
                    'borderColor' => ['#ffffff', '#ffffff'],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Curatif (' . $curatif . ')', 'Préventif (' . $preventif . ')'],
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
