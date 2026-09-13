<?php

namespace App\Filament\Pages;

use App\Enums\StatutIntervention;
use App\Models\Intervention;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CalendrierInterventions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Calendrier';

    protected static ?string $title = 'Calendrier des interventions';

    protected string $view = 'filament.pages.calendrier-interventions';

    protected static string|\UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 10;

    public array $interventions = [];

    public function mount(): void
    {
        $this->interventions = $this->getInterventionsData();
    }

    protected function getInterventionsData(): array
    {
        return Intervention::with(['equipement', 'technicien', 'service'])
            ->get()
            ->map(function (Intervention $i) {
                $start = $i->date_planifiee ?? $i->date_demande ?? $i->created_at;

                if (! $start) {
                    return null;
                }

                // Si l'intervention se termine un autre jour calendaire que son début,
                // on l'affiche en "toute la journée" confinée à son jour de début
                // (évite que la barre déborde sur les jours suivants en vue Mois).
                // Note : diffInHours() de Carbon renvoie une valeur signée depuis Carbon 3,
                // on compare donc les jours calendaires plutôt qu'un nombre d'heures.
                $end = $i->date_fin;
                if ($end && ! $start->isSameDay($end)) {
                    // All-day : on utilise start (jour courant)
                    return [
                        'id' => $i->id,
                        'title' => $i->titre,
                        'start' => $start->toDateString(),
                        'allDay' => true,
                        'color' => match ($i->statut) {
                            StatutIntervention::Terminee => '#22c55e',
                            StatutIntervention::EnCours => '#f59e0b',
                            StatutIntervention::Ouverte => '#3b82f6',
                            StatutIntervention::EnAttente => '#6b7280',
                            StatutIntervention::Annulee => '#ef4444',
                            default => '#a855f7',
                        },
                        'textColor' => '#fff',
                        'extendedProps' => [
                            'equipement' => $i->equipement?->nom ?? '—',
                            'technicien' => $i->technicien?->name ?? 'Non assigné',
                            'service' => $i->service?->nom ?? '—',
                            'statut' => $i->statut->getLabel(),
                            'priorite' => $i->priorite->getLabel(),
                            'type' => $i->type->getLabel(),
                            'description' => $i->description ?? '—',
                        ],
                    ];
                }

                return [
                    'id' => $i->id,
                    'title' => $i->titre,
                    'start' => $start->toDateTimeString(),
                    'end' => $end?->toDateTimeString(),
                    'color' => match ($i->statut) {
                        StatutIntervention::Terminee => '#22c55e',
                        StatutIntervention::EnCours => '#f59e0b',
                        StatutIntervention::Ouverte => '#3b82f6',
                        StatutIntervention::EnAttente => '#6b7280',
                        StatutIntervention::Annulee => '#ef4444',
                        default => '#a855f7',
                    },
                    'textColor' => '#fff',
                    'extendedProps' => [
                        'equipement' => $i->equipement?->nom ?? '—',
                        'technicien' => $i->technicien?->name ?? 'Non assigné',
                        'service' => $i->service?->nom ?? '—',
                        'statut' => $i->statut->getLabel(),
                        'priorite' => $i->priorite->getLabel(),
                        'type' => $i->type->getLabel(),
                        'description' => $i->description ?? '—',
                    ],
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
