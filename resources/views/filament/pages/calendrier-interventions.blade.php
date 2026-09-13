<x-filament-panels::page>
    <div class="space-y-5">

        {{-- En-tête moderne --}}
        <div class="overflow-hidden rounded-2xl border border-sky-200/60 bg-gradient-to-br from-sky-500 via-cyan-500 to-emerald-500 p-1 shadow-xl shadow-sky-500/10">
            <div class="rounded-[14px] bg-white/95 px-6 py-5 backdrop-blur-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-slate-900">Calendrier des interventions</h1>
                        <p class="mt-0.5 text-sm text-slate-500">Visualisez et planifiez l'ensemble des interventions de votre parc.</p>
                    </div>
                    <a href="{{ url('/admin/interventions/create') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 transition hover:translate-y-[-1px]" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        Nouvelle intervention
                    </a>
                </div>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-purple-200 bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700">
                <span class="h-2 w-2 rounded-full bg-purple-500"></span>Nouveau
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">
                <span class="h-2 w-2 rounded-full bg-sky-500"></span>Ouvert
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>En cours
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                <span class="h-2 w-2 rounded-full bg-slate-500"></span>En attente
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>Terminée
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">
                <span class="h-2 w-2 rounded-full bg-rose-500"></span>Annulée
            </span>
        </div>

        {{-- Calendrier --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-slate-700 shadow-sm sm:p-6">
            <div id="gmao-calendar"></div>
        </div>

        {{-- Popup --}}
        <div id="event-popup" class="hidden fixed z-50 w-80 rounded-2xl bg-white p-5 shadow-2xl ring-1 ring-slate-200"
             style="top:0; left:0;">
            <div class="mb-3 flex items-start justify-between">
                <h3 id="popup-title" class="mr-2 text-sm font-bold leading-tight text-slate-900 border-l-4 pl-2 border-slate-300"></h3>
                <button onclick="closePopup()" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="popup-content" class="space-y-1.5 text-xs"></div>
            <div class="mt-3 flex justify-end border-t border-slate-100 pt-3">
                <button onclick="closePopup()" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Fermer</button>
            </div>
        </div>

    </div>

    @once
        @push('styles')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
        @endpush
    @endonce

    @once
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var events = @json($interventions);
                    var calendarEl = document.getElementById('gmao-calendar');
                    if (!calendarEl) return;

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        locale: 'fr',
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                        },
                        buttonText: {
                            today: "Aujourd'hui",
                            month: 'Mois',
                            week: 'Semaine',
                            day: 'Jour',
                            list: 'Liste'
                        },
                        events: events,
                        eventClick: function(info) {
                            info.jsEvent.preventDefault();
                            showPopup(info);
                        },
                        height: 'auto',
                        firstDay: 1,
                        slotMinTime: '06:00:00',
                        slotMaxTime: '22:00:00',
                        nowIndicator: true,
                        dayMaxEvents: 4,
                        // Sans "end", ne pas supposer 1h de duree par defaut : evite
                        // qu'un evenement demarrant tard (ex: 23h14) ne deborde sur
                        // le jour suivant dans la vue Mois.
                        defaultTimedEventDuration: '00:00:00',
                    });

                    calendar.render();

                    document.addEventListener('click', function(e) {
                        var popup = document.getElementById('event-popup');
                        if (popup && !popup.classList.contains('hidden')) {
                            if (!popup.contains(e.target) && !e.target.closest('.fc-event')) {
                                popup.classList.add('hidden');
                            }
                        }
                    });
                });

                function showPopup(info) {
                    var popup = document.getElementById('event-popup');
                    var props = info.event.extendedProps;
                    var jsEvent = info.jsEvent;

                    var popupWidth = 320;
                    var left = jsEvent.clientX + 16;
                    var top = jsEvent.clientY - 20;

                    if (left + popupWidth > window.innerWidth - 20) {
                        left = jsEvent.clientX - popupWidth - 16;
                    }
                    if (top + 300 > window.innerHeight - 20) {
                        top = window.innerHeight - 320;
                    }
                    if (top < 20) top = 20;

                    popup.style.left = left + 'px';
                    popup.style.top = top + 'px';

                    var titleEl = document.getElementById('popup-title');
                    titleEl.textContent = info.event.title;
                    titleEl.style.borderColor = info.event.backgroundColor;

                    var fmt = function(d) {
                        if (!d) return '—';
                        var date = new Date(d);
                        return date.toLocaleDateString('fr-FR') + ' à ' + date.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
                    };

                    document.getElementById('popup-content').innerHTML =
                        '<div class="grid grid-cols-[90px_1fr] gap-x-2 gap-y-2">' +
                        '<span class="font-semibold text-slate-500">Équipement :</span><span class="font-medium text-slate-900">' + (props.equipement || '—') + '</span>' +
                        '<span class="font-semibold text-slate-500">Service :</span><span class="text-slate-700">' + (props.service || '—') + '</span>' +
                        '<span class="font-semibold text-slate-500">Technicien :</span><span class="text-slate-700">' + (props.technicien || 'Non assigné') + '</span>' +
                        '<span class="font-semibold text-slate-500">Statut :</span><span><span class="inline-flex rounded-md px-1.5 py-0.5 text-[10px] font-semibold" style="background-color:' + info.event.backgroundColor + '20;color:' + info.event.backgroundColor + ';border:1px solid ' + info.event.backgroundColor + '40;">' + (props.statut || '—') + '</span></span>' +
                        '<span class="font-semibold text-slate-500">Priorité :</span><span class="text-slate-700">' + (props.priorite || '—') + '</span>' +
                        '<span class="font-semibold text-slate-500">Type :</span><span class="text-slate-700">' + (props.type || '—') + '</span>' +
                        '<span class="font-semibold text-slate-500">Début :</span><span class="text-slate-700">' + fmt(info.event.start) + '</span>' +
                        '<span class="font-semibold text-slate-500">Fin :</span><span class="text-slate-700">' + fmt(info.event.end) + '</span>' +
                        '</div>' +
                        '<div class="mt-3 border-t border-slate-100 pt-2">' +
                        '<span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</span>' +
                        '<p class="mt-1 leading-relaxed text-slate-600">' + (props.description || '—') + '</p>' +
                        '</div>';

                    popup.classList.remove('hidden');
                }

                function closePopup() {
                    var popup = document.getElementById('event-popup');
                    if (popup) popup.classList.add('hidden');
                }
            </script>
        @endpush
    @endonce
</x-filament-panels::page>
