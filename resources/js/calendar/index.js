import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
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

    // --- Popup d'apercu au survol (avant meme de cliquer) ------------------
    const popupEl = document.getElementById('gmao-event-popup');
    const popupTitleEl = popupEl?.querySelector('[data-popup-title]');
    const popupBodyEl = popupEl?.querySelector('[data-popup-body]');

    function popupRow(label, value) {
        return `<span class="gmao-popup__label">${label}</span><span>${value}</span>`;
    }

    function showEventPopup(info) {
        if (! popupEl) {
            return;
        }

        const props = info.event.extendedProps;
        const color = info.event.backgroundColor || info.event.color;
        const fmt = (date) => (date ? `${date.toLocaleDateString('fr-FR')} à ${formatTime(date)}` : '—');

        popupTitleEl.textContent = info.event.title;
        popupTitleEl.style.borderColor = color;

        popupBodyEl.innerHTML = `
            <div class="gmao-popup__grid">
                ${popupRow('Équipement', escapeHtml(props.equipement))}
                ${popupRow('Service', escapeHtml(props.service))}
                ${popupRow('Technicien', escapeHtml(props.technicien))}
                ${popupRow('Statut', `<span class="gmao-popup__badge" style="background-color:${color}20;color:${color};border:1px solid ${color}40;">${escapeHtml(props.statutLabel)}</span>`)}
                ${popupRow('Priorité', escapeHtml(props.priorite))}
                ${popupRow('Type', escapeHtml(props.typeLabel))}
                ${popupRow('Début', fmt(info.event.start))}
                ${popupRow('Fin', fmt(info.event.end))}
            </div>
            <div class="gmao-popup__description">
                <span class="gmao-popup__label">Description</span>
                <p>${escapeHtml(props.description)}</p>
            </div>
        `;

        positionPopup(info.jsEvent);
        popupEl.hidden = false;
    }

    function positionPopup(jsEvent) {
        const width = 320;
        let left = jsEvent.clientX + 16;
        let top = jsEvent.clientY + 16;

        if (left + width > window.innerWidth - 16) {
            left = jsEvent.clientX - width - 16;
        }

        if (top + 280 > window.innerHeight - 16) {
            top = window.innerHeight - 296;
        }

        if (top < 16) {
            top = 16;
        }

        popupEl.style.left = `${Math.max(16, left)}px`;
        popupEl.style.top = `${top}px`;
    }

    function hideEventPopup() {
        if (popupEl) {
            popupEl.hidden = true;
        }
    }

    // --- Interventions non planifiees (source de drag & drop) --------------
    const unscheduledPanelEl = document.querySelector('[data-unscheduled-panel]');
    const unscheduledListEl = document.querySelector('[data-unscheduled-list]');
    const unscheduledCountEl = document.querySelector('[data-unscheduled-count]');

    function renderUnscheduled(items) {
        if (! unscheduledListEl || ! unscheduledPanelEl) {
            return;
        }

        unscheduledListEl.innerHTML = items.map((item) => `
            <div class="gmao-unscheduled-item" data-id="${item.id}" data-title="${escapeHtml(item.title)}" style="border-left-color:${item.color}">
                <div class="gmao-unscheduled-item__title">${escapeHtml(item.title)}</div>
                <div class="gmao-unscheduled-item__meta">${escapeHtml(item.equipement)} · demandée le ${escapeHtml(item.demandeLabel)}</div>
            </div>
        `).join('');

        if (unscheduledCountEl) {
            unscheduledCountEl.textContent = items.length;
        }

        unscheduledPanelEl.hidden = items.length === 0;
    }

    function reloadUnscheduled() {
        if (! config.unscheduledUrl) {
            return;
        }

        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, values]) => values.forEach((v) => params.append(key, v)));

        fetch(`${config.unscheduledUrl}?${params.toString()}`, { headers: { Accept: 'application/json' } })
            .then((response) => (response.ok ? response.json() : []))
            .then(renderUnscheduled)
            .catch(() => {});
    }

    if (unscheduledListEl) {
        new Draggable(unscheduledListEl, {
            itemSelector: '.gmao-unscheduled-item',
            eventData: (el) => ({
                id: el.dataset.id,
                title: el.dataset.title,
                duration: '00:30',
            }),
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
        droppable: true,
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
        eventReceive: (info) => {
            // Carte "Non planifiee" deposee sur le calendrier : on la
            // programme via l'API, puis on laisse le refetch normal
            // reprendre la main (l'evenement temporaire est retire).
            const start = info.event.allDay ? `${info.event.startStr}T09:00:00` : info.event.startStr;
            const url = config.rescheduleUrlTemplate.replace('__ID__', info.event.id);

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                },
                body: JSON.stringify({ start, end: null }),
            })
                .then((response) => {
                    if (! response.ok) {
                        throw new Error('schedule failed');
                    }

                    info.event.remove();
                    calendar.refetchEvents();
                    reloadUnscheduled();
                })
                .catch(() => {
                    info.event.remove();
                    window.alert("Impossible de programmer l'intervention. Réessayez.");
                });
        },
        eventDidMount: (info) => {
            info.el.setAttribute('title', `${info.event.title} — ${info.event.extendedProps.statutLabel}`);
        },
        eventMouseEnter: showEventPopup,
        eventMouseLeave: hideEventPopup,
        eventsSet: markTodayInListView,
        datesSet: hideEventPopup,
    });

    calendar.render();
    reloadUnscheduled();

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
            reloadUnscheduled();
        });
    });

    return calendar;
}

// ---------------------------------------------------------------------
// Filtres : sur mobile, un menu a cases a cocher remplace le <select
// multiple> natif (peu utilisable au doigt, la multi-selection standard
// exigeant un Ctrl/Cmd-clic). Les cases pilotent le select natif existant
// puis declenchent son evenement "change" : la logique de filtrage du
// calendrier (plus bas) n'a donc pas besoin d'etre dupliquee.
// ---------------------------------------------------------------------
function initFilterDropdowns() {
    const groups = document.querySelectorAll('[data-filter-group]');

    if (! groups.length) {
        return;
    }

    function closeAllPanels() {
        document.querySelectorAll('[data-filter-panel]').forEach((panel) => {
            panel.hidden = true;
        });
    }

    groups.forEach((group) => {
        const select = group.querySelector('[data-filter]');
        const toggle = group.querySelector('[data-filter-toggle]');
        const panel = group.querySelector('[data-filter-panel]');
        const badge = group.querySelector('[data-filter-badge]');
        const checkboxes = panel ? panel.querySelectorAll('input[type="checkbox"]') : [];

        if (! select || ! toggle || ! panel) {
            return;
        }

        function updateBadge() {
            const count = Array.from(checkboxes).filter((checkbox) => checkbox.checked).length;

            if (badge) {
                badge.textContent = String(count);
                badge.hidden = count === 0;
            }
        }

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const willOpen = panel.hidden;
            closeAllPanels();
            panel.hidden = ! willOpen;
        });

        panel.addEventListener('click', (event) => event.stopPropagation());

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                Array.from(select.options).forEach((option) => {
                    if (option.value === checkbox.value) {
                        option.selected = checkbox.checked;
                    }
                });

                select.dispatchEvent(new Event('change', { bubbles: true }));
                updateBadge();
            });
        });

        updateBadge();
    });

    document.addEventListener('click', closeAllPanels);
}

function boot() {
    initFilterDropdowns();

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
