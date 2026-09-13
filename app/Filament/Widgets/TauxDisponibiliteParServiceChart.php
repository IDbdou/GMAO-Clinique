<?php

namespace App\Filament\Widgets;

use App\Enums\StatutEquipement;
use App\Models\Equipement;
use App\Models\Service;
use Filament\Widgets\ChartWidget;

class TauxDisponibiliteParServiceChart extends ChartWidget
{
    protected ?string $heading = 'Taux de disponibilité par service';

    protected ?string $description = 'Pourcentage d\'équipements opérationnels par service';

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 15;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $services = Service::withCount('equipements')->get();
        $labels = [];
        $dispo = [];
        $panne = [];
        $maintenance = [];

        foreach ($services as $service) {
            $labels[] = $service->code;

            $total = $service->equipements_count;
            if ($total === 0) {
                $dispo[] = 0;
                $panne[] = 0;
                $maintenance[] = 0;
                continue;
            }

            $nbDispo = Equipement::where('service_id', $service->id)
                ->where('statut', StatutEquipement::EnService)
                ->count();

            $nbPanne = Equipement::where('service_id', $service->id)
                ->where('statut', StatutEquipement::EnPanne)
                ->count();

            $nbMaint = Equipement::where('service_id', $service->id)
                ->where('statut', StatutEquipement::EnMaintenance)
                ->count();

            $dispo[] = round(($nbDispo / $total) * 100, 1);
            $panne[] = round(($nbPanne / $total) * 100, 1);
            $maintenance[] = round(($nbMaint / $total) * 100, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Disponible (%)',
                    'data' => $dispo,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'En panne (%)',
                    'data' => $panne,
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'En maintenance (%)',
                    'data' => $maintenance,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
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
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
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
