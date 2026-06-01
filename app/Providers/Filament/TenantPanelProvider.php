<?php

namespace App\Providers\Filament;

use App\Filament\Tenant\Pages\Login;
use App\Filament\Tenant\Widgets\TenantStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('tenant')
            ->login(Login::class)
            ->darkMode(false)
            ->favicon(asset('images/logo-nexa.png'))
            ->brandName('NexaSpace Anak Kos')
            ->brandLogo(asset('images/nexaspace.webp'))
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::hex('#306D29'),
                'success' => Color::hex('#0D530E'),
                'gray'    => Color::hex('#4a4a4a'),
            ])
            ->discoverResources(
                in: app_path('Filament/Tenant/Resources'),
                for: 'App\Filament\Tenant\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/Tenant/Pages'),
                for: 'App\Filament\Tenant\Pages',
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Tenant/Widgets'),
                for: 'App\Filament\Tenant\Widgets',
            )
            ->widgets([
                TenantStatsWidget::class,
                AccountWidget::class,
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
