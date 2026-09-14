<x-filament-panels::page>
    <div class="space-y-5 gmao-calendar-shell">

        {{-- En-tête moderne --}}
        <div class="overflow-hidden rounded-2xl border border-sky-200/60 bg-gradient-to-br from-sky-500 via-cyan-500 to-emerald-500 p-1 shadow-xl shadow-sky-500/10">
            <div class="rounded-[14px] bg-white/95 px-6 py-5 backdrop-blur-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-slate-900">Calendrier des interventions</h1>
                        <p class="mt-0.5 text-sm text-slate-500">Visualisez et planifiez l'ensemble des interventions de votre parc.</p>
                    </div>
                    <a href="{{ $this->createUrl }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 transition hover:translate-y-[-1px]" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        Nouvel événement
                    </a>
                </div>
            </div>
        </div>

        {{-- Légende (couleurs des statuts, reprises telles quelles depuis StatutIntervention) --}}
        <div class="flex flex-wrap gap-2">
            @foreach ($this->statutOptions as $statut)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $statut['color'] }};"></span>{{ $statut['label'] }}
                </span>
            @endforeach
        </div>

        {{-- Filtres --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="gmao-calendar-filters">
                <label>
                    Type
                    <select multiple data-filter="type" size="1">
                        @foreach ($this->typeOptions as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Statut
                    <select multiple data-filter="statut" size="1">
                        @foreach ($this->statutOptions as $statut)
                            <option value="{{ $statut['value'] }}">{{ $statut['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Technicien
                    <select multiple data-filter="technicien_id" size="1">
                        @foreach ($this->technicienOptions as $technicien)
                            <option value="{{ $technicien['value'] }}">{{ $technicien['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Équipement
                    <select multiple data-filter="equipement_id" size="1">
                        @foreach ($this->equipementOptions as $equipement)
                            <option value="{{ $equipement['value'] }}">{{ $equipement['label'] }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        {{-- Interventions non planifiées (glisser sur le calendrier pour les programmer) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" data-unscheduled-panel hidden>
            <div class="mb-2 flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-900">Non planifiées</h2>
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500" data-unscheduled-count>0</span>
            </div>
            <p class="mb-3 text-xs text-slate-500">Glissez une carte sur une case du calendrier pour la programmer.</p>
            <div class="gmao-unscheduled-list" data-unscheduled-list></div>
        </div>

        {{-- Calendrier --}}
        @php
            $gmaoCalendarConfig = [
                'eventsUrl' => $this->eventsUrl,
                'unscheduledUrl' => $this->unscheduledUrl,
                'rescheduleUrlTemplate' => $this->rescheduleUrlTemplate,
                'createUrl' => $this->createUrl,
                'csrfToken' => csrf_token(),
            ];
        @endphp
        <div
            data-gmao-calendar='@json($gmaoCalendarConfig)'
            class="rounded-2xl border border-slate-200 bg-white p-4 text-slate-700 shadow-sm sm:p-6"
        >
            <div data-calendar-mount></div>
        </div>

    </div>

    {{-- Popup d'aperçu au survol d'une intervention --}}
    <div id="gmao-event-popup" class="gmao-popup" hidden>
        <div class="gmao-popup__title" data-popup-title></div>
        <div class="gmao-popup__body" data-popup-body></div>
    </div>

    @vite(['resources/css/calendar.css', 'resources/js/calendar/index.js'])
</x-filament-panels::page>
