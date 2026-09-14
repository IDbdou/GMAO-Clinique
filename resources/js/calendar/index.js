import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';

// ---------------------------------------------------------------------
// Reglages du module calendrier. Voir CALENDAR.md pour le detail de
// chaque option (bornes horaires, jours affiches par defaut, etc.)
// ---------------------------------------------------------------------
const SLOT_MIN_TIME = '07:00:00';
const SLOT_MAX_TIME = '20:00:00';
const SHOW_WEEKENDS_BY_DEFAULT = false; // false = semaine Lun -> Ven, true = Lun -> Dim
const MOBILE_BREAKPOINT = 768;
const STATUTS_NEEDING_CONFIRMATION = ['en_cours', 'terminee'];
const SHORT_EVENT_THRESHOLD_MINUTES = 30;
// Duree appliquee aux evenements sans heure de fin : assez courte pour ne
// jamais deborder sur le jour suivant en vue Mois pour un cas realiste,
// assez longue pour rester visible (non nulle) en vue Semaine/Jour.
const DEFAULT_EVENT_DURATION = '00:30:00';

function debounce(fn, wait) {
    let timeout;

    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), wait);
    };
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[char]));
}

function formatTime(date) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

function timezoneLabel() {
    const offsetMinutes = -new Date().getTimezoneOffset();
    const sign = offsetMinutes >= 0 ? '+' : '-';
    const hours = Math.floor(Math.abs(offsetMinutes) / 60);

    return `GMT${sign}${hours}`;
}

/**
 * Initialise le calendrier GMAO sur l'element racine fourni.
 *
 * @param {HTMLElement} root - conteneur contenant [data-calendar-mount] et les filtres [data-filter]
 * @param {{eventsUrl: string, rescheduleUrlTemplate: string, createUrl: string, csrfToken: string}} config
 */
export function initGmaoCalendar(root, config) {
    const calendarEl = root.querySelector('[data-calendar-mount]');

    if (! calendarEl) {
        return null;
    }

    calendarEl.style.setProperty('--gmao-gmt-label', `"${timezoneLabel()}"`);

    const filters = {
        type: [],
        statut: [],
        technicien_id: [],
        equipement_id: [],
    };

    function buildCreateUrl(dateStr) {
        const url = new URL(config.createUrl, window.location.origin);
        url.searchParams.set('date_planifiee', dateStr);

        return url.toString();
    }

    function reschedule(info) {
        const statut = info.event.extendedProps.statut;
        const statutLabel = info.event.extendedProps.statutLabel;

        if (STATUTS_NEEDING_CONFIRMATION.includes(statut)) {
            const confirmed = window.confirm(
                `Cette intervention est "${statutLabel}". Confirmer le déplacement ?`
            );

            if (! confirmed) {
                info.revert();

                return;
            }
        }

        let start = info.event.startStr;
        let end = info.event.endStr || null;

        if (info.event.allDay && end) {
            // FullCalendar utilise une fin exclusive pour les evenements
            // "toute la journee" : on revient a la derniere journee reelle.
            const inclusiveEnd = new Date(info.event.end);
            inclusiveEnd.setUTCDate(inclusiveEnd.getUTCDate() - 1);
            end = `${inclusiveEnd.toISOString().slice(0, 10)}T23:59:59`;
            start = `${start}T00:00:00`;
        }

        const url = config.rescheduleUrlTemplate.replace('__ID__', info.event.id);

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
            },
            body: JSON.stringify({ start, end }),
        })
            .then((response) => {
                if (! response.ok) {
                    throw new Error('reschedule failed');
                }
            })
            .catch(() => {
                info.revert();
                window.alert("Impossible de déplacer l'intervention. Réessayez.");
            });
    }

    function markTodayInListView() {
        const header = calendarEl.querySelector('.fc-list-day.fc-day-today .fc-list-day-text');

        if (header && ! header.dataset.gmaoMarked) {
            header.textContent = `Aujourd'hui · ${header.textContent}`;
            header.dataset.gmaoMarked = '1';
        }
    }

    function eventContent(arg) {
        const isTimeGrid = arg.view.type.startsWith('timeGrid');

        if (! isTimeGrid || arg.event.allDay) {
            return true; // rendu par defaut (Mois, Liste, bandeaux toute-la-journee)
        }

        const start = arg.event.start;
        const end = arg.event.end;
        const durationMinutes = end && start ? (end - start) / 60000 : 0;
        const title = escapeHtml(arg.event.title);

        if (end && durationMinutes > 0 && durationMinutes <= SHORT_EVENT_THRESHOLD_MINUTES) {
            return {
                html: `<div class="gmao-event gmao-event--compact">${title}, ${formatTime(start)}</div>`,
            };
        }

        const timeLabel = end ? `${formatTime(start)} – ${formatTime(end)}` : formatTime(start);

        return {
            html: `
                <div class="gmao-event">
                    <div class="gmao-event__title">${title}</div>
                    <div class="gmao-event__time">${timeLabel}</div>
                </div>
            `,
        };
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        locale: frLocale,
        firstDay: 1,
        height: 'auto',
        initialView: window.innerWidth < MOBILE_BREAKPOINT ? 'timeGridDay' : 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek',
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste',
        },
        noEventsText: 'Aucune intervention sur cette période',
        weekends: SHOW_WEEKENDS_BY_DEFAULT,
        slotMinTime: SLOT_MIN_TIME,
        slotMaxTime: SLOT_MAX_TIME,
        slotDuration: '00:30:00',
        defaultTimedEventDuration: DEFAULT_EVENT_DURATION,
        nowIndicator: true,
        dayMaxEvents: 3,
        editable: true,
        eventStartEditable: true,
        eventDurationEditable: true,
        eventResizableFromStart: false,
        selectable: true,
        navLinks: true,
        navLinkDayClick: (date) => calendar.changeView('timeGridWeek', date),
        moreLinkClick: (arg) => {
            calendar.changeView('timeGridWeek', arg.date);

            return 'timeGrid';
        },
        events: {
            url: config.eventsUrl,
            method: 'GET',
            extraParams: () => filters,
            failure: () => window.console.error('[Calendrier GMAO] Échec du chargement des interventions'),
        },
        eventContent,
        eventClick: (info) => {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        dateClick: (info) => {
            window.location.href = buildCreateUrl(info.dateStr);
        },
        select: (info) => {
            window.location.href = buildCreateUrl(info.startStr);
        },
        eventDrop: reschedule,
        eventResize: reschedule,
        eventDidMount: (info) => {
            info.el.setAttribute('title', `${info.event.title} — ${info.event.extendedProps.statutLabel}`);
        },
        eventsSet: markTodayInListView,
    });

    calendar.render();

    // Bascule Semaine <-> Jour automatiquement au franchissement du
    // breakpoint mobile pendant que la page reste ouverte.
    window.addEventListener('resize', debounce(() => {
        const isMobile = window.innerWidth < MOBILE_BREAKPOINT;
        const current = calendar.view.type;

        if (isMobile && current === 'timeGridWeek') {
            calendar.changeView('timeGridDay');
        }

        if (! isMobile && current === 'timeGridDay') {
            calendar.changeView('timeGridWeek');
        }
    }, 200));

    // Swipe gauche/droite pour naviguer en vue Jour sur mobile.
    let touchStartX = null;
    let touchStartY = null;

    calendarEl.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    calendarEl.addEventListener('touchend', (e) => {
        if (touchStartX === null || calendar.view.type !== 'timeGridDay') {
            touchStartX = null;
            touchStartY = null;

            return;
        }

        const deltaX = e.changedTouches[0].clientX - touchStartX;
        const deltaY = e.changedTouches[0].clientY - touchStartY;

        if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY) * 2) {
            deltaX < 0 ? calendar.next() : calendar.prev();
        }

        touchStartX = null;
        touchStartY = null;
    }, { passive: true });

    // La barre de filtres vit en dehors du conteneur du calendrier dans le
    // Blade (voir calendrier-interventions.blade.php) : recherche globale.
    document.querySelectorAll('[data-filter]').forEach((select) => {
        select.addEventListener('change', () => {
            filters[select.dataset.filter] = Array.from(select.selectedOptions).map((o) => o.value);
            calendar.refetchEvents();
        });
    });

    return calendar;
}

function boot() {
    const root = document.querySelector('[data-gmao-calendar]');

    if (! root) {
        return;
    }

    const config = JSON.parse(root.dataset.gmaoCalendar);
    initGmaoCalendar(root, config);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
