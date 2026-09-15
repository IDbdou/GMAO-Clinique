<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ServicePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('service')
            ->path('service')
            ->brandName('GMAO Clinique — Chef de service')
            ->viteTheme('resources/css/filament/service/theme.css')
            ->login()
            ->colors([
                'primary' => '#0ea5e9',
                'danger' => '#ef4444',
                'success' => '#10b981',
                'warning' => '#f59e0b',
                'info' => '#6366f1',
            ])
            ->discoverResources(in: app_path('Filament/Service/Resources'), for: 'App\Filament\Service\Resources')
            ->discoverPages(in: app_path('Filament/Service/Pages'), for: 'App\Filament\Service\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Service\Widgets\ServiceStatsOverview::class,
                // ─── Graphiques d'analyse ───
                \App\Filament\Service\Widgets\SignalementsParMoisChart::class,
                \App\Filament\Service\Widgets\StatutSignalementsChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
