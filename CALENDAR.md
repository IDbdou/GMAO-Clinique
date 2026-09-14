# Module Calendrier

Calendrier des interventions du panel Admin, façon Google Calendar : vues
Mois / Semaine / Liste, filtres, création rapide, glisser-déposer.

## Architecture

```
app/Filament/Pages/CalendrierInterventions.php     Page Filament : expose les options de
                                                     filtres (type, statut, technicien,
                                                     équipement) et les URLs consommées par le JS.

app/Http/Controllers/Admin/InterventionCalendarController.php
                                                     Endpoints JSON consommés en fetch() par le
                                                     calendrier :
                                                       GET   /admin/calendrier/events       (liste filtrée)
                                                       PATCH /admin/calendrier/interventions/{id} (drag & drop / resize)

app/Support/Calendar/InterventionEventFormatter.php Convertit une Intervention en évènement
                                                     FullCalendar (date, couleur, extendedProps).

app/Support/Calendar/InterventionColorResolver.php  Résout la couleur Filament (HasColor) d'un
                                                     enum vers une couleur CSS, en réutilisant la
                                                     palette du panel Admin (AdminPanelProvider) —
                                                     aucune couleur n'est redéfinie ici.

resources/views/filament/pages/calendrier-interventions.blade.php
                                                     Barre de filtres + conteneur du calendrier.

resources/js/calendar/index.js                      Initialisation FullCalendar (plugins
                                                     dayGrid/timeGrid/list/interaction), filtres,
                                                     création rapide, drag & drop, swipe mobile.

resources/css/calendar.css                          Habillage visuel + responsive.

routes/web.php                                       Déclare les 2 routes ci-dessus.
```

Le calendrier ne récupère plus les données via un `@json()` injecté dans la
page (comme avant cette refonte) : il les charge en AJAX depuis
`/admin/calendrier/events`, avec la plage de dates visible et les filtres
actifs en paramètres de requête. Cela permet au filtrage et au
changement de vue de ne recharger que les données nécessaires.

## Couleurs

Les couleurs viennent exclusivement des enums Filament existants
(`App\Enums\StatutIntervention`, `PrioriteIntervention`, `TypeIntervention`,
qui implémentent `HasColor`), résolues via `InterventionColorResolver` en
utilisant la palette configurée dans `AdminPanelProvider::colors()`. Pour
changer une couleur, modifier `AdminPanelProvider` ou l'enum concerné —
jamais ce module.

## Où changer les réglages

Tout se règle en haut de `resources/js/calendar/index.js` :

```js
const SLOT_MIN_TIME = '07:00:00';        // borne basse de la vue Semaine/Jour
const SLOT_MAX_TIME = '20:00:00';        // borne haute
const SHOW_WEEKENDS_BY_DEFAULT = false;  // false = Lun->Ven, true = Lun->Dim
const MOBILE_BREAKPOINT = 768;           // largeur sous laquelle Semaine -> Jour
const DEFAULT_EVENT_DURATION = '00:30:00'; // durée affichée si l'intervention n'a pas de date de fin
```

`DEFAULT_EVENT_DURATION` mérite une explication : une intervention sans
`date_fin` (cas très courant, ex. simple signalement) n'a pas de durée
connue. On lui affecte une courte durée par défaut uniquement pour le
rendu (jamais écrite en base) :
- trop courte (0) → FullCalendar n'affiche rien du tout en vue Semaine/Jour
  pour ces évènements (bug constaté pendant le développement) ;
- trop longue (1h, l'ancien défaut de FullCalendar) → une intervention
  démarrant tard le soir (23h14 par ex.) déborde visuellement sur le jour
  suivant en vue Mois.

  30 minutes est le compromis retenu.

## Multi-jours vs. sans fin

`InterventionEventFormatter` distingue deux cas côté backend :
- `date_fin` renseignée sur un jour calendaire différent de `date_planifiee`
  → évènement "toute la journée" couvrant la vraie plage (comme Google
  Calendar), affiché dans le bandeau du haut des vues Semaine/Jour.
- Pas de `date_fin`, ou `date_fin` le même jour → évènement horaire normal.

## Tests

`tests/Feature/Calendar/` :
- `InterventionCalendarEventsTest.php` — accès/autorisation à l'endpoint
  `events`, filtre par statut, couleur renvoyée, cas multi-jours et
  sans date de fin.
- `InterventionRescheduleTest.php` — reprogrammation via l'endpoint PATCH,
  validation (fin avant début refusée), autorisation.

Le placement des blocs et la gestion des chevauchements en vue
Semaine/Jour sont délégués à FullCalendar (bibliothèque déjà testée) ; les
tests PHP couvrent la donnée qu'on lui fournit, pas son rendu pixel.

## Ce qui n'est PAS dans ce module

Le panel Service et le panel Agent n'ont pas de calendrier (ils n'en
avaient pas avant cette refonte) — uniquement le panel Admin.
