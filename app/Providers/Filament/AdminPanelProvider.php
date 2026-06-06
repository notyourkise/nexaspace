<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Login;
use App\Filament\Widgets\BillingReminderWidget;
use App\Filament\Widgets\JuraganOnboardingWidget;
use App\Filament\Widgets\JuraganQuotaWidget;
use App\Filament\Widgets\MikroTikStatusWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->darkMode(false)
            ->favicon(asset('images/logo-nexa.png'))
            ->brandName('NexaSpace Juragan')
            ->brandLogo(asset('images/nexaspace.webp'))
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::hex('#306D29'),
                'success' => Color::hex('#0D530E'),
                'gray'    => Color::hex('#4a4a4a'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                JuraganOnboardingWidget::class,
                BillingReminderWidget::class,
                StatsOverview::class,
                JuraganQuotaWidget::class,
                RevenueChartWidget::class,
                MikroTikStatusWidget::class,
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            // Lightweight success animation: the green check icon in success toasts
            // pops in and spins briefly when a CRUD/approval action succeeds.
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                        @keyframes nexaCheckPop {
                            0%   { transform: scale(0) rotate(-270deg); opacity: 0; }
                            60%  { transform: scale(1.25) rotate(20deg); opacity: 1; }
                            100% { transform: scale(1) rotate(0deg); opacity: 1; }
                        }
                        .fi-no-notification.fi-color-success .fi-icon,
                        .fi-no-notification.fi-color-success svg {
                            animation: nexaCheckPop 0.6s cubic-bezier(0.22, 1, 0.36, 1);
                            color: #16a34a;
                        }
                    </style>
                    HTML,
            )
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
