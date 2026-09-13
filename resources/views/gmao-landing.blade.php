<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GMAO Clinique — Gestion de Maintenance Assistée par Ordinateur dédiée aux établissements de santé. Suivez vos équipements médicaux, planifiez vos interventions et signalez les pannes en temps réel.">
    <meta name="theme-color" content="#0ea5e9">

    <title>{{ config('app.name', 'GMAO Clinique') }} — Maintenance intelligente pour établissements de santé</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen bg-[var(--color-bg)] text-[var(--color-fg)] antialiased font-sans selection:bg-[var(--color-accent)] selection:text-[var(--color-accent-on)]">

    {{-- ─── Décor de fond ─── --}}
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[520px] w-[520px] rounded-full bg-sky-300/30 blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 h-[520px] w-[520px] rounded-full bg-cyan-300/25 blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 h-[520px] w-[520px] rounded-full bg-emerald-300/20 blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.08),transparent_60%)]"></div>
    </div>

    {{-- ─── Navbar ─── --}}
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/70 dark:bg-zinc-900/70 border-b border-[var(--color-border-soft)]">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-10">
            <a href="/" class="flex items-center gap-3 group">
                <span class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 text-white shadow-lg shadow-sky-500/30 transition-transform group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </span>
                <div class="flex flex-col leading-tight">
                    <span class="text-base font-bold tracking-tight">GMAO Clinique</span>
                    <span class="text-[11px] uppercase tracking-wider text-[var(--color-muted)]">Santé · Maintenance</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm">
                <a href="#features" class="px-3 py-2 rounded-lg text-[var(--color-fg-2)] hover:bg-[var(--color-surface-warm)] hover:text-[var(--color-fg)] transition">Fonctionnalités</a>
                <a href="#roles" class="px-3 py-2 rounded-lg text-[var(--color-fg-2)] hover:bg-[var(--color-surface-warm)] hover:text-[var(--color-fg)] transition">Espaces</a>
                <a href="#demo" class="px-3 py-2 rounded-lg text-[var(--color-fg-2)] hover:bg-[var(--color-surface-warm)] hover:text-[var(--color-fg)] transition">Démo</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="/admin/login" class="hidden sm:inline-flex items-center gap-2 rounded-lg border border-[var(--color-border)] bg-white px-4 py-2 text-sm font-semibold text-[var(--color-fg)] shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    Connexion
                </a>
                <a href="/admin" class="inline-flex items-center gap-2 rounded-lg bg-[var(--color-accent)] px-4 py-2 text-sm font-semibold text-white shadow-md shadow-sky-500/30 hover:bg-[var(--color-accent-hover)] hover:-translate-y-0.5 transition-all">
                    Ouvrir l'app
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </header>

    {{-- ─── Hero ─── --}}
    <section class="relative">
        <div class="mx-auto max-w-7xl px-6 pt-16 pb-20 lg:px-10 lg:pt-24 lg:pb-28">
            <div class="grid gap-12 lg:grid-cols-12 lg:items-center">

                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-sky-500"></span>
                        </span>
                        Plateforme dédiée aux établissements de santé
                    </span>

                    <h1 class="mt-6 text-4xl font-bold tracking-tight text-[var(--color-fg)] sm:text-5xl lg:text-6xl">
                        La maintenance médicale,
                        <span class="block bg-gradient-to-r from-sky-500 via-cyan-500 to-emerald-500 bg-clip-text text-transparent">simplifiée et intelligente.</span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg leading-relaxed text-[var(--color-fg-2)]">
                        Gérez votre parc d'équipements médicaux, planifiez les maintenances préventives, suivez les interventions curatives et signalez les pannes en quelques secondes — le tout depuis une interface moderne pensée pour les cliniques.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="/admin" class="inline-flex items-center gap-2 rounded-xl bg-[var(--color-accent)] px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-[var(--color-accent-hover)] hover:-translate-y-0.5 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            Espace Administrateur / Technicien
                        </a>
                        <a href="/service" class="inline-flex items-center gap-2 rounded-xl border border-[var(--color-border)] bg-white px-6 py-3.5 text-sm font-semibold text-[var(--color-fg)] shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01"/></svg>
                            Espace Chef de service
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm text-[var(--color-muted)]">
                        <div class="flex items-center gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M20 6 9 17l-5-5"/></svg> Traçabilité complète</div>
                        <div class="flex items-center gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M20 6 9 17l-5-5"/></svg> Signalement instantané</div>
                        <div class="flex items-center gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M20 6 9 17l-5-5"/></svg> Tableau de bord temps réel</div>
                    </div>
                </div>

                {{-- Carte UI flottante --}}
                <div class="lg:col-span-5">
                    <div class="relative">
                        <div class="absolute -inset-4 rounded-3xl bg-gradient-to-tr from-sky-400/20 via-cyan-400/20 to-emerald-400/20 blur-2xl"></div>

                        <div class="relative rounded-3xl border border-[var(--color-border-soft)] bg-white/80 p-6 shadow-2xl shadow-sky-500/10 backdrop-blur-xl">
                            <div class="flex items-center justify-between border-b border-[var(--color-border-soft)] pb-4">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500 text-white">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3zM9 9h6v6H9z"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold">Vue clinique</p>
                                        <p class="text-xs text-[var(--color-muted)]">Mardi · {{ now()->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> 96% dispo
                                </span>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-[var(--color-border-soft)] bg-gradient-to-br from-sky-50 to-white p-4">
                                    <p class="text-xs font-medium text-sky-700">Équipements</p>
                                    <p class="mt-1 text-3xl font-bold tracking-tight">128</p>
                                    <p class="mt-1 text-xs text-[var(--color-muted)]">+4 ce mois</p>
                                </div>
                                <div class="rounded-2xl border border-[var(--color-border-soft)] bg-gradient-to-br from-amber-50 to-white p-4">
                                    <p class="text-xs font-medium text-amber-700">Interventions</p>
                                    <p class="mt-1 text-3xl font-bold tracking-tight">17</p>
                                    <p class="mt-1 text-xs text-[var(--color-muted)]">8 en cours · 9 planifiées</p>
                                </div>
                                <div class="rounded-2xl border border-[var(--color-border-soft)] bg-gradient-to-br from-rose-50 to-white p-4">
                                    <p class="text-xs font-medium text-rose-700">Pannes</p>
                                    <p class="mt-1 text-3xl font-bold tracking-tight">3</p>
                                    <p class="mt-1 text-xs text-[var(--color-muted)]">criticité haute</p>
                                </div>
                                <div class="rounded-2xl border border-[var(--color-border-soft)] bg-gradient-to-br from-emerald-50 to-white p-4">
                                    <p class="text-xs font-medium text-emerald-700">Coûts (an)</p>
                                    <p class="mt-1 text-2xl font-bold tracking-tight">42 800 <span class="text-sm font-medium text-[var(--color-muted)]">MAD</span></p>
                                    <p class="mt-1 text-xs text-[var(--color-muted)]">-12% vs N-1</p>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl border border-[var(--color-border-soft)] bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold text-[var(--color-muted)]">ACTIVITÉ RÉCENTE</p>
                                    <span class="text-[10px] uppercase tracking-wider text-sky-600">live</span>
                                </div>
                                <ul class="mt-3 space-y-3 text-sm">
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <div>
                                            <p class="font-medium">Échographe — cardiologie · préventive terminée</p>
                                            <p class="text-xs text-[var(--color-muted)]">il y a 12 min · Tech. Karim</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-block h-2 w-2 rounded-full bg-rose-500"></span>
                                        <div>
                                            <p class="font-medium">Panne signalée · Respirateur — réanimation</p>
                                            <p class="text-xs text-[var(--color-muted)]">il y a 38 min · Service réa.</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-block h-2 w-2 rounded-full bg-sky-500"></span>
                                        <div>
                                            <p class="font-medium">Contrat sterilisation renouvelé</p>
                                            <p class="text-xs text-[var(--color-muted)]">il y a 1h · Admin</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Statistiques ─── --}}
    <section class="border-y border-[var(--color-border-soft)] bg-white/60 backdrop-blur-sm">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-px sm:grid-cols-4 lg:px-10">
            @php
                $stats = [
                    ['label' => 'Équipements suivis', 'value' => '128', 'color' => 'sky'],
                    ['label' => 'Interventions / mois', 'value' => '47', 'color' => 'amber'],
                    ['label' => 'Disponibilité moyenne', 'value' => '96%', 'color' => 'emerald'],
                    ['label' => 'Réduction des pannes', 'value' => '-38%', 'color' => 'rose'],
                ];
            @endphp
            @foreach($stats as $s)
                <div class="flex flex-col items-start gap-1 px-6 py-8 sm:py-10">
                    <p class="text-3xl font-bold tracking-tight text-{{ $s['color'] }}-600">{{ $s['value'] }}</p>
                    <p class="text-xs uppercase tracking-wider text-[var(--color-muted)]">{{ $s['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ─── Fonctionnalités ─── --}}
    <section id="features" class="py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Fonctionnalités</span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Tout ce qu'il faut pour piloter la maintenance.</h2>
                <p class="mt-4 text-base text-[var(--color-fg-2)]">Des outils concrets pour vos techniciens, vos chefs de service et votre direction.</p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $features = [
                        ['icon' => 'M3 3h18v18H3z M9 9h6v6H9z', 'tone' => 'sky',     'title' => 'Parc d\'équipements',     'desc' => 'Référencez, étiquetez et classifiez votre parc médical par service, criticité et contrat.'],
                        ['icon' => 'M8 2v4M16 2v4M3 10h18M5 6h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z', 'tone' => 'amber',   'title' => 'Planning préventif',    'desc' => 'Planifiez automatiquement les maintenances selon la fréquence, la criticité et la disponibilité.'],
                        ['icon' => 'M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01', 'tone' => 'rose',   'title' => 'Signalement de panne', 'desc' => 'Les services signalent en 30 secondes avec photo, localisation et criticité. Notification immédiate.'],
                        ['icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 13h6M9 17h6', 'tone' => 'emerald','title' => 'Comptes-rendus',       'desc' => 'Génération automatique de comptes-rendus PDF horodatés et signés à la fin de chaque intervention.'],
                        ['icon' => 'M3 3v18h18M7 14l4-4 4 4 5-5', 'tone' => 'indigo',  'title' => 'Analyses & KPI',         'desc' => 'Tableaux de bord interactifs : taux de disponibilité, coûts, MTBF, satisfaction techniciens.'],
                        ['icon' => 'M12 2 4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6z',                  'tone' => 'cyan',    'title' => 'Sécurité & rôles',      'desc' => 'Authentification, rôles (admin, tech, chef de service), audit log complet et signatures.'],
                    ];
                @endphp

                @foreach($features as $f)
                    <div class="group relative rounded-2xl border border-[var(--color-border)] bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-500/10">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-{{ $f['tone'] }}-50 text-{{ $f['tone'] }}-600 transition-transform group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $f['icon'] }}"/></svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-[var(--color-fg-2)]">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── Espaces / rôles ─── --}}
    <section id="roles" class="border-t border-[var(--color-border-soft)] bg-gradient-to-b from-white to-[var(--color-surface-warm)] py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Trois espaces, une seule plateforme</span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Chaque rôle trouve son terrain.</h2>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                @php
                    $roles = [
                        [
                            'url'  => '/admin',
                            'tag'  => 'Admin / Technicien',
                            'tone' => 'sky',
                            'icon' => 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z',
                            'title' => 'Pilotage & opérations',
                            'desc'  => 'Gérez le parc, planifiez les interventions, créez les comptes-rendus et analysez les KPIs.',
                            'cta'   => 'Ouvrir /admin',
                        ],
                        [
                            'url'  => '/service',
                            'tag'  => 'Chef de service',
                            'tone' => 'amber',
                            'icon' => 'M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01',
                            'title' => 'Signalement rapide',
                            'desc'  => 'Vos chefs de service signalent une panne en quelques clics et suivent le traitement en temps réel.',
                            'cta'   => 'Ouvrir /service',
                        ],
                        [
                            'url'  => '/admin/login',
                            'tag'  => 'Agent',
                            'tone' => 'emerald',
                            'icon' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z',
                            'title' => 'Accès nominatif',
                            'desc'  => 'Comptes personnels, journal d\'audit, permissions fines et signatures électroniques.',
                            'cta'   => 'Se connecter',
                        ],
                    ];
                @endphp

                @foreach($roles as $r)
                    <a href="{{ $r['url'] }}" class="group relative flex flex-col overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white p-7 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-{{ $r['tone'] }}-500/10">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-{{ $r['tone'] }}-400 to-{{ $r['tone'] }}-600"></div>
                        <span class="inline-flex items-center gap-2 self-start rounded-full bg-{{ $r['tone'] }}-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-{{ $r['tone'] }}-700">{{ $r['tag'] }}</span>
                        <span class="mt-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-{{ $r['tone'] }}-100 text-{{ $r['tone'] }}-600 transition-transform group-hover:rotate-3 group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $r['icon'] }}"/></svg>
                        </span>
                        <h3 class="mt-5 text-xl font-bold">{{ $r['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-[var(--color-fg-2)]">{{ $r['desc'] }}</p>
                        <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-{{ $r['tone'] }}-600">
                            {{ $r['cta'] }}
                            <svg class="transition-transform group-hover:translate-x-1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── Démo ─── --}}
    <section id="demo" class="py-20 lg:py-28">
        <div class="mx-auto max-w-5xl px-6 lg:px-10">
            <div class="overflow-hidden rounded-3xl border border-[var(--color-border)] bg-gradient-to-br from-sky-500 via-cyan-500 to-emerald-500 p-1 shadow-2xl shadow-sky-500/20">
                <div class="rounded-[22px] bg-white p-10 sm:p-14">
                    <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Comptes de démonstration</span>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Testez la plateforme maintenant.</h2>
                    <p class="mt-4 max-w-2xl text-[var(--color-fg-2)]">Accédez à n'importe quel espace avec l'un des comptes ci-dessous. Le mot de passe est identique pour tous.</p>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-[var(--color-border-soft)] bg-[var(--color-surface-warm)] p-4">
                            <p class="text-xs font-medium text-[var(--color-muted)]">Administrateur</p>
                            <p class="mt-1 font-mono text-sm font-semibold">admin@gmao.local</p>
                        </div>
                        <div class="rounded-xl border border-[var(--color-border-soft)] bg-[var(--color-surface-warm)] p-4">
                            <p class="text-xs font-medium text-[var(--color-muted)]">Technicien</p>
                            <p class="mt-1 font-mono text-sm font-semibold">tech@gmao.local</p>
                        </div>
                        <div class="rounded-xl border border-[var(--color-border-soft)] bg-[var(--color-surface-warm)] p-4">
                            <p class="text-xs font-medium text-[var(--color-muted)]">Chef de service</p>
                            <p class="mt-1 font-mono text-sm font-semibold">agent@gmao.local</p>
                        </div>
                    </div>

                    <div class="mt-6 inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><path d="M19 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2zM7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Mot de passe partagé : <strong class="font-mono">password</strong></span>
                    </div>

                    <div class="mt-10 flex flex-wrap gap-3">
                        <a href="/admin" class="inline-flex items-center gap-2 rounded-xl bg-[var(--color-accent)] px-6 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/30 hover:bg-[var(--color-accent-hover)] transition">
                            Démarrer maintenant
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </a>
                        <a href="/service" class="inline-flex items-center gap-2 rounded-xl border border-[var(--color-border)] bg-white px-6 py-3 text-sm font-semibold hover:bg-[var(--color-surface-warm)] transition">
                            Espace chef de service
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Footer ─── --}}
    <footer class="border-t border-[var(--color-border-soft)] bg-white/60 backdrop-blur-sm">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-4 px-6 py-8 sm:flex-row sm:items-center lg:px-10">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </span>
                <div class="text-xs text-[var(--color-muted)]">
                    © {{ date('Y') }} GMAO Clinique · Conçu pour les établissements de santé.
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs text-[var(--color-muted)]">
                <span>v1.0 · Laravel {{ app()->version() }}</span>
            </div>
        </div>
    </footer>
</body>
</html>
