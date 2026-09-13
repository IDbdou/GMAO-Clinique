<?php

namespace App\Filament\Service\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class SignalementsParMoisChart extends ChartWidget
{
    protected ?string $heading = 'Signalements sur les 6 derniers mois';

    protected ?string $description = 'Volume de pannes signalées par mois';

    protected ?string $maxHeight = '240px';

    protected int|string|array $columnSpan = ['lg' => 6, 'md' => 6];

    protected static ?int $sort = 11;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = [];
        $creees = [];
        $resolues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $creees[] = Intervention::where('service_id', auth()->user()?->service_id)
                ->where('type', 'curatif')
                ->whereYear('date_demande', $date->year)
                ->whereMonth('date_demande', $date->month)
                ->count();

            $resolues[] = Intervention::where('service_id', auth()->user()?->service_id)
                ->where('type', 'curatif')
                ->whereYear('date_fin', $date->year)
                ->whereMonth('date_fin', $date->month)
                ->where('statut', StatutIntervention::Terminee)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Signalées',
                    'data' => $creees,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Résolues',
                    'data' => $resolues,
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
                    'ticks' => ['precision' => 0],
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
