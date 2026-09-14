<?php

namespace App\Support\Calendar;

use App\Filament\Resources\Interventions\InterventionResource;
use App\Models\Intervention;

/**
 * Convertit une Intervention en evenement FullCalendar (meme source de
 * donnees que le module Interventions existant, aucun champ invente).
 */
class InterventionEventFormatter
{
    public static function toEvent(Intervention $intervention): ?array
    {
        $start = $intervention->date_planifiee;

        // Sans date planifiee, l'intervention n'a pas sa place sur une case
        // du calendrier : elle apparait dans la liste "Non planifiees" a la
        // place (voir InterventionCalendarController::unscheduled()).
        if (! $start) {
            return null;
        }

        $end = $intervention->date_fin;

        // Intervention qui s'etend sur plusieurs jours calendaires : affichee
        // comme un bandeau "toute la journee" couvrant sa vraie plage (a la
        // maniere de Google Calendar), plutot qu'un bloc horaire par jour.
        $isMultiDay = $end && ! $start->isSameDay($end);

        return [
            'id' => $intervention->id,
            'title' => $intervention->titre,
            'start' => $isMultiDay ? $start->toDateString() : $start->toIso8601String(),
            'end' => $isMultiDay
                ? $end->copy()->addDay()->toDateString()
                : $end?->toIso8601String(),
            'allDay' => $isMultiDay,
            'color' => InterventionColorResolver::hex($intervention->statut->getColor()),
            'textColor' => '#fff',
            'url' => InterventionResource::getUrl('edit', ['record' => $intervention->id]),
            'editable' => true,
            'extendedProps' => [
                'equipement' => $intervention->equipement?->nom ?? '—',
                'technicien' => $intervention->technicien?->name ?? 'Non assigné',
                'service' => $intervention->service?->nom ?? '—',
                'statut' => $intervention->statut->value,
                'statutLabel' => $intervention->statut->getLabel(),
                'priorite' => $intervention->priorite->getLabel(),
                'type' => $intervention->type->value,
                'typeLabel' => $intervention->type->getLabel(),
                'description' => $intervention->description ?? '—',
                'hasEnd' => $end !== null,
            ],
        ];
    }

    /**
     * Representation compacte d'une intervention sans date planifiee, pour
     * la liste "Non planifiees" (source de drag & drop vers le calendrier).
     */
    public static function toUnscheduled(Intervention $intervention): array
    {
        $demande = $intervention->date_demande ?? $intervention->created_at;

        return [
            'id' => $intervention->id,
            'title' => $intervention->titre,
            'color' => InterventionColorResolver::hex($intervention->statut->getColor()),
            'equipement' => $intervention->equipement?->nom ?? '—',
            'service' => $intervention->service?->nom ?? '—',
            'statutLabel' => $intervention->statut->getLabel(),
            'priorite' => $intervention->priorite->getLabel(),
            'typeLabel' => $intervention->type->getLabel(),
            'demandeLabel' => $demande?->translatedFormat('d M Y') ?? '—',
        ];
    }
}
