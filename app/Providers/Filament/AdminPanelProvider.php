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
use App\Filament\Widgets\DeveloperWelcomeWidget;
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
                DeveloperWelcomeWidget::class,
                JuraganOnboardingWidget::class,
                BillingReminderWidget::class,
                StatsOverview::class,
                JuraganQuotaWidget::class,
                RevenueChartWidget::class,
                MikroTikStatusWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                    /* ================================================================
                       NexaSpace — Dark Premium Theme
                       ================================================================ */

                    /* ── Force dark base ── */
                    html, body {
                        background-color: #07090D !important;
                        color-scheme: dark;
                    }
                    body.fi-body {
                        background-color: #07090D !important;
                        color: #e2e8f0 !important;
                    }

                    /* ── Sidebar ── */
                    .fi-sidebar,
                    nav.fi-sidebar {
                        background-color: #0B0F14 !important;
                        border-right: 1px solid rgba(255,255,255,0.05) !important;
                    }
                    .fi-sidebar-header,
                    .fi-sidebar-brand {
                        background-color: #0B0F14 !important;
                        border-bottom: 1px solid rgba(255,255,255,0.04) !important;
                    }
                    .fi-sidebar-nav,
                    .fi-sidebar-nav-groups {
                        background-color: transparent !important;
                    }
                    .fi-sidebar-footer {
                        background-color: #0B0F14 !important;
                        border-top: 1px solid rgba(255,255,255,0.05) !important;
                        color: #94a3b8 !important;
                    }

                    /* ── Nav items ── */
                    .fi-sidebar-item-label {
                        color: #94a3b8 !important;
                        font-size: 0.875rem !important;
                    }
                    .fi-sidebar-item-icon {
                        color: #64748b !important;
                    }
                    .fi-sidebar-item-button:hover {
                        background-color: rgba(255,255,255,0.04) !important;
                    }
                    .fi-sidebar-item-button:hover .fi-sidebar-item-label {
                        color: #cbd5e1 !important;
                    }
                    .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
                        color: #94a3b8 !important;
                    }
                    /* Active item */
                    .fi-sidebar-item-button[aria-current="page"],
                    .fi-sidebar-item-button.fi-active,
                    .fi-sidebar-item-button[class*="active"] {
                        background-color: rgba(34,197,94,0.08) !important;
                    }
                    .fi-sidebar-item-button[aria-current="page"] .fi-sidebar-item-label,
                    .fi-sidebar-item-button.fi-active .fi-sidebar-item-label {
                        color: #22c55e !important;
                        font-weight: 600 !important;
                    }
                    .fi-sidebar-item-button[aria-current="page"] .fi-sidebar-item-icon,
                    .fi-sidebar-item-button.fi-active .fi-sidebar-item-icon {
                        color: #22c55e !important;
                    }
                    /* Nav group labels */
                    .fi-sidebar-group-label {
                        color: #475569 !important;
                        font-size: 0.6875rem !important;
                        letter-spacing: 0.1em !important;
                        text-transform: uppercase !important;
                    }

                    /* ── Topbar ── */
                    .fi-topbar,
                    header.fi-topbar,
                    .fi-topbar-start,
                    .fi-topbar-end,
                    .fi-topbar-header,
                    [class*="fi-topbar"] {
                        background-color: #07090D !important;
                        border-bottom: 1px solid rgba(255,255,255,0.06) !important;
                    }
                    .fi-topbar .fi-breadcrumbs-item,
                    .fi-topbar .fi-breadcrumbs-separator {
                        color: #64748b !important;
                    }
                    .fi-topbar .fi-breadcrumbs-item:last-child {
                        color: #94a3b8 !important;
                    }
                    /* User avatar/dropdown in topbar */
                    .fi-user-avatar {
                        background-color: rgba(34,197,94,0.15) !important;
                        color: #22c55e !important;
                        border: 1px solid rgba(34,197,94,0.25) !important;
                    }
                    .fi-dropdown-trigger-button {
                        color: #cbd5e1 !important;
                    }

                    /* ── Main content ── */
                    main.fi-main,
                    .fi-main {
                        background-color: #07090D !important;
                    }
                    .fi-page,
                    .fi-page-header {
                        background-color: transparent !important;
                    }
                    .fi-header-heading, h1.fi-header-heading {
                        color: #f1f5f9 !important;
                    }
                    .fi-header-subheading {
                        color: #64748b !important;
                    }

                    /* ── Widget card glass ── */
                    .fi-wi-stats-overview-stat,
                    .fi-wi-stats-overview-stat-card {
                        background: rgba(11,15,20,0.75) !important;
                        border: 1px solid rgba(255,255,255,0.07) !important;
                        backdrop-filter: blur(8px) !important;
                        -webkit-backdrop-filter: blur(8px) !important;
                    }
                    .fi-wi-stats-overview-stat-label {
                        color: #94a3b8 !important;
                        font-size: 0.8125rem !important;
                    }
                    .fi-wi-stats-overview-stat-value {
                        color: #f1f5f9 !important;
                        font-size: 1.5rem !important;
                        font-weight: 700 !important;
                    }
                    .fi-wi-stats-overview-stat-description {
                        color: #64748b !important;
                        font-size: 0.75rem !important;
                    }
                    .fi-wi-stats-overview-stat-icon {
                        color: #22c55e !important;
                        opacity: 0.85 !important;
                    }

                    /* ── Section / generic widget card ── */
                    .fi-section,
                    .fi-widget,
                    .fi-wi-chart,
                    .fi-wi-account {
                        background: rgba(11,15,20,0.75) !important;
                        border: 1px solid rgba(255,255,255,0.07) !important;
                        backdrop-filter: blur(8px) !important;
                    }
                    .fi-section-header,
                    .fi-section-header-heading {
                        color: #f1f5f9 !important;
                    }
                    .fi-section-description,
                    .fi-section-header-description {
                        color: #64748b !important;
                    }
                    .fi-section-content {
                        background-color: transparent !important;
                    }

                    /* ── Tables ── */
                    .fi-ta-wrap, .fi-ta {
                        background: transparent !important;
                    }
                    .fi-ta-header {
                        background: rgba(255,255,255,0.02) !important;
                        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
                    }
                    .fi-ta-header-cell {
                        color: #64748b !important;
                        font-size: 0.75rem !important;
                        letter-spacing: 0.05em !important;
                        text-transform: uppercase !important;
                    }
                    .fi-ta-row {
                        border-bottom: 1px solid rgba(255,255,255,0.04) !important;
                    }
                    .fi-ta-row:hover td,
                    .fi-ta-row:hover .fi-ta-cell {
                        background-color: rgba(34,197,94,0.03) !important;
                    }
                    .fi-ta-cell {
                        color: #cbd5e1 !important;
                        background-color: transparent !important;
                    }
                    .fi-ta-empty-state-icon {
                        color: #334155 !important;
                    }
                    .fi-ta-empty-state-heading {
                        color: #64748b !important;
                    }

                    /* ── Forms / inputs ── */
                    .fi-fo-field-wrp-label,
                    .fi-fo-field-wrp label {
                        color: #94a3b8 !important;
                    }
                    .fi-input,
                    .fi-select-input,
                    .fi-textarea,
                    input[type="text"],
                    input[type="email"],
                    input[type="password"],
                    select, textarea {
                        background: rgba(255,255,255,0.04) !important;
                        border-color: rgba(255,255,255,0.1) !important;
                        color: #e2e8f0 !important;
                    }
                    .fi-input:focus,
                    .fi-select-input:focus {
                        border-color: rgba(34,197,94,0.5) !important;
                        box-shadow: 0 0 0 2px rgba(34,197,94,0.15) !important;
                        outline: none !important;
                    }
                    .fi-fo-field-wrp-helper-text {
                        color: #475569 !important;
                    }

                    /* ── Modals ── */
                    .fi-modal-window,
                    .fi-modal-content {
                        background: #0e141f !important;
                        border: 1px solid rgba(255,255,255,0.08) !important;
                    }
                    .fi-modal-header-heading {
                        color: #f1f5f9 !important;
                    }
                    .fi-modal-header-subheading,
                    .fi-modal-description {
                        color: #64748b !important;
                    }
                    .fi-modal-footer {
                        background: rgba(255,255,255,0.02) !important;
                        border-top: 1px solid rgba(255,255,255,0.05) !important;
                    }
                    /* Modal overlay */
                    .fi-modal-overlay {
                        background: rgba(0,0,0,0.65) !important;
                        backdrop-filter: blur(4px) !important;
                    }

                    /* ── Dropdown panels ── */
                    .fi-dropdown-panel {
                        background: #0e141f !important;
                        border: 1px solid rgba(255,255,255,0.08) !important;
                        box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
                    }
                    .fi-dropdown-list-item-label {
                        color: #cbd5e1 !important;
                    }
                    .fi-dropdown-list-item:hover {
                        background: rgba(255,255,255,0.04) !important;
                    }

                    /* ── Notifications ── */
                    .fi-no-notification {
                        background: #0e141f !important;
                        border: 1px solid rgba(255,255,255,0.08) !important;
                    }
                    .fi-no-notification-title {
                        color: #f1f5f9 !important;
                    }
                    .fi-no-notification-body {
                        color: #94a3b8 !important;
                    }

                    /* ── Badges ── */
                    .fi-badge {
                        font-size: 0.6875rem !important;
                        font-weight: 600 !important;
                    }

                    /* ── Buttons ── */
                    .fi-btn-color-gray {
                        background: rgba(255,255,255,0.06) !important;
                        border-color: rgba(255,255,255,0.1) !important;
                        color: #cbd5e1 !important;
                    }
                    .fi-btn-color-gray:hover {
                        background: rgba(255,255,255,0.09) !important;
                    }

                    /* ── Chart widget container ── */
                    .fi-wi-chart canvas {
                        filter: brightness(0.95);
                    }

                    /* ── Filters bar ── */
                    .fi-ta-filters-form {
                        background: transparent !important;
                    }
                    .fi-ta-header-toolbar {
                        background: transparent !important;
                    }

                    /* ── Scrollbar ── */
                    ::-webkit-scrollbar { width: 6px; height: 6px; }
                    ::-webkit-scrollbar-track { background: transparent; }
                    ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
                    ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.18); }

                    /* ── Success toast animation ── */
                    @keyframes nexaCheckPop {
                        0%   { transform: scale(0) rotate(-270deg); opacity: 0; }
                        60%  { transform: scale(1.25) rotate(20deg); opacity: 1; }
                        100% { transform: scale(1) rotate(0deg); opacity: 1; }
                    }
                    .fi-no-notification.fi-color-success .fi-icon,
                    .fi-no-notification.fi-color-success svg {
                        animation: nexaCheckPop 0.6s cubic-bezier(0.22, 1, 0.36, 1);
                        color: #22c55e;
                    }

                    /* ================================================================
                       NexaSpace Custom Widget Components (nexa-*)
                       ================================================================ */

                    .nexa-welcome-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 1rem;
                        margin-bottom: 0;
                    }
                    @media (min-width: 1024px) {
                        .nexa-welcome-grid {
                            grid-template-columns: 2fr 1fr;
                        }
                    }

                    /* ── Hero Card ── */
                    .nexa-hero-card {
                        position: relative;
                        overflow: hidden;
                        border-radius: 1rem;
                        min-height: 220px;
                        background: linear-gradient(135deg, #0a0f1a 0%, #0c1a0f 55%, #07090D 100%);
                        border: 1px solid rgba(34,197,94,0.14);
                    }
                    .nexa-hero-glow {
                        position: absolute;
                        top: -80px;
                        right: -80px;
                        width: 350px;
                        height: 350px;
                        background: radial-gradient(circle, rgba(34,197,94,0.12) 0%, transparent 68%);
                        border-radius: 50%;
                        pointer-events: none;
                    }
                    .nexa-hero-art {
                        position: absolute;
                        inset: 0;
                        width: 65%;
                        height: 100%;
                        right: 0;
                        left: auto;
                        pointer-events: none;
                        opacity: 1;
                    }
                    .nexa-hero-content {
                        position: relative;
                        z-index: 10;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        height: 100%;
                        min-height: 220px;
                        padding: 1.75rem 2rem;
                        gap: 1.5rem;
                    }
                    .nexa-welcome-label {
                        color: #22c55e;
                        font-size: 0.6875rem;
                        font-weight: 700;
                        letter-spacing: 0.15em;
                        text-transform: uppercase;
                        margin: 0 0 0.5rem;
                    }
                    .nexa-welcome-name {
                        color: #ffffff;
                        font-size: clamp(2rem, 4vw, 2.75rem);
                        font-weight: 800;
                        line-height: 1.05;
                        margin: 0 0 0.5rem;
                        letter-spacing: -0.01em;
                    }
                    .nexa-welcome-sub {
                        color: rgba(148,163,184,0.85);
                        font-size: 0.875rem;
                        margin: 0;
                    }
                    .nexa-hero-footer {
                        display: flex;
                        align-items: flex-end;
                        justify-content: space-between;
                        gap: 1rem;
                        flex-wrap: wrap;
                    }
                    .nexa-meta {
                        display: flex;
                        flex-direction: column;
                        gap: 0.375rem;
                    }
                    .nexa-meta-row {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: rgba(148,163,184,0.8);
                        font-size: 0.875rem;
                    }
                    .nexa-meta-icon {
                        width: 1rem;
                        height: 1rem;
                        flex-shrink: 0;
                        color: rgba(100,116,139,0.9);
                    }
                    .nexa-signout-btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 0.5rem 1rem;
                        border-radius: 0.5rem;
                        border: 1px solid rgba(34,197,94,0.25);
                        background: rgba(34,197,94,0.07);
                        color: rgba(203,213,225,0.9);
                        font-size: 0.8125rem;
                        font-weight: 500;
                        cursor: pointer;
                        transition: all 0.15s ease;
                        white-space: nowrap;
                    }
                    .nexa-signout-btn:hover {
                        background: rgba(34,197,94,0.14);
                        border-color: rgba(34,197,94,0.4);
                        color: #e2e8f0;
                    }

                    /* ── System Overview Card ── */
                    .nexa-overview-card {
                        position: relative;
                        overflow: hidden;
                        border-radius: 1rem;
                        background: rgba(11,15,20,0.82);
                        border: 1px solid rgba(255,255,255,0.07);
                        backdrop-filter: blur(12px);
                        -webkit-backdrop-filter: blur(12px);
                    }
                    .nexa-overview-header {
                        display: flex;
                        align-items: flex-start;
                        gap: 0.75rem;
                        padding: 1.25rem 1.25rem 0.875rem;
                        border-bottom: 1px solid rgba(255,255,255,0.05);
                    }
                    .nexa-overview-pulse {
                        width: 8px;
                        height: 8px;
                        border-radius: 50%;
                        background: #22c55e;
                        box-shadow: 0 0 8px #22c55e, 0 0 16px rgba(34,197,94,0.4);
                        flex-shrink: 0;
                        margin-top: 0.35rem;
                        animation: nexaPulse 2s ease-in-out infinite;
                    }
                    @keyframes nexaPulse {
                        0%, 100% { box-shadow: 0 0 8px #22c55e, 0 0 16px rgba(34,197,94,0.4); }
                        50%       { box-shadow: 0 0 12px #22c55e, 0 0 24px rgba(34,197,94,0.6); }
                    }
                    .nexa-overview-title {
                        color: #f1f5f9;
                        font-size: 0.9375rem;
                        font-weight: 600;
                        line-height: 1.3;
                        margin: 0 0 0.125rem;
                    }
                    .nexa-overview-sub {
                        color: rgba(100,116,139,0.9);
                        font-size: 0.75rem;
                        margin: 0;
                    }
                    .nexa-overview-list {
                        display: flex;
                        flex-direction: column;
                        gap: 0;
                        padding: 0.625rem;
                    }
                    .nexa-overview-item {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 0.625rem 0.75rem;
                        border-radius: 0.5rem;
                        border: 1px solid rgba(255,255,255,0.04);
                        background: rgba(255,255,255,0.025);
                        margin-bottom: 0.375rem;
                        transition: background 0.1s;
                    }
                    .nexa-overview-item:last-child { margin-bottom: 0; }
                    .nexa-overview-item:hover { background: rgba(255,255,255,0.04); }
                    .nexa-overview-item-label {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: rgba(148,163,184,0.9);
                        font-size: 0.8125rem;
                    }
                    .nexa-ov-icon {
                        width: 1rem;
                        height: 1rem;
                        color: rgba(100,116,139,0.8);
                        flex-shrink: 0;
                    }
                    .nexa-overview-item-value {
                        display: flex;
                        align-items: center;
                        gap: 0.375rem;
                    }
                    .nexa-dot-pulse {
                        display: inline-block;
                        width: 6px;
                        height: 6px;
                        border-radius: 50%;
                        background: #22c55e;
                        box-shadow: 0 0 6px rgba(34,197,94,0.7);
                    }
                    .nexa-green-text {
                        color: #22c55e;
                        font-size: 0.8125rem;
                        font-weight: 500;
                    }
                    .nexa-fw600 { font-weight: 600 !important; }

                    /* ── MikroTik widget SVG router ── */
                    .nexa-router-svg {
                        opacity: 0.55;
                        margin-top: 0.5rem;
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
