<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Support\Calendar\InterventionEventFormatter;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterventionCalendarController extends Controller
{
    private const DATE_EXPR = 'COALESCE(date_planifiee, date_demande, created_at)';

    private function authorizeAdminPanel(Request $request): void
    {
        abort_unless($request->user(), 401);

        abort_unless($request->user()->canAccessPanel(Filament::getPanel('admin')), 403);
    }

    public function events(Request $request): JsonResponse
    {
        $this->authorizeAdminPanel($request);

        $query = Intervention::with(['equipement', 'technicien', 'service']);

        if ($request->filled('start')) {
            $query->whereRaw(self::DATE_EXPR.' >= ?', [$request->date('start')]);
        }

        if ($request->filled('end')) {
            $query->whereRaw(self::DATE_EXPR.' < ?', [$request->date('end')]);
        }

        if ($types = $request->query('type')) {
            $query->whereIn('type', (array) $types);
        }

        if ($statuts = $request->query('statut')) {
            $query->whereIn('statut', (array) $statuts);
        }

        if ($technicienIds = $request->query('technicien_id')) {
            $query->whereIn('technicien_id', (array) $technicienIds);
        }

        if ($equipementIds = $request->query('equipement_id')) {
            $query->whereIn('equipement_id', (array) $equipementIds);
        }

        $events = $query->get()
            ->map(fn (Intervention $i) => InterventionEventFormatter::toEvent($i))
            ->filter()
            ->values();

        return response()->json($events);
    }

    public function reschedule(Request $request, Intervention $intervention): JsonResponse
    {
        $this->authorizeAdminPanel($request);

        $data = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        $intervention->update([
            'date_planifiee' => $data['start'],
            'date_fin' => $data['end'] ?? null,
        ]);

        return response()->json(InterventionEventFormatter::toEvent($intervention->fresh(['equipement', 'technicien', 'service'])));
    }
}
