<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Login;
use App\Filament\Widgets\BillingReminderWidget;
use App\Filament\Widgets\JuraganDashboardWidget;
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
use Filament\Support\Enums\Width;
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
            ->maxContentWidth(Width::Full)
            ->favicon(asset('images/logo-nexa.png'))
            ->brandName('NEXASPACE')
            ->brandLogo(asset('images/logo-nexa.png'))
            ->brandLogoHeight('3rem')
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
                JuraganDashboardWidget::class,
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
                    <script>
                        (function(){
                            /* Force Tailwind dark-mode class so all dark: variants activate */
                            document.documentElement.classList.add('dark');
                        })();
                    </script>
                    <style>
                    /* ================================================================
                       NexaSpace — Dark Premium Theme
                       ================================================================ */

                    /* ── Force dark base ── */
                    html, html.dark, body {
                        background-color: #07090D !important;
                        color-scheme: dark;
                    }
                    body.fi-body {
                        background-color: #07090D !important;
                        color: #e2e8f0 !important;
                    }

                    /* ── Override Tailwind light-mode utility classes ── */
                    .bg-white     { background-color: #0b0f18 !important; }
                    .bg-gray-50   { background-color: #080c14 !important; }
                    .bg-gray-100  { background-color: #0b1020 !important; }
                    .bg-gray-200  { background-color: #111827 !important; }
                    .text-gray-950,
                    .text-gray-900 { color: #f1f5f9 !important; }
                    .text-gray-800  { color: #e2e8f0 !important; }
                    .text-gray-700  { color: #cbd5e1 !important; }
                    .text-gray-600  { color: #94a3b8 !important; }
                    .text-gray-500  { color: #64748b !important; }
                    .border-gray-100 { border-color: rgba(255,255,255,0.06) !important; }
                    .border-gray-200 { border-color: rgba(255,255,255,0.08) !important; }
                    .divide-gray-100 > * + * { border-color: rgba(255,255,255,0.05) !important; }
                    .divide-gray-200 > * + * { border-color: rgba(255,255,255,0.07) !important; }
                    .ring-gray-200 { --tw-ring-color: rgba(255,255,255,0.08) !important; }

                    /* ── Brand logo + name ── */
                    .fi-brand-name {
                        color: #22c55e !important;
                        font-weight: 800 !important;
                        letter-spacing: 0.12em !important;
                        text-transform: uppercase !important;
                        font-size: 1.0625rem !important;
                    }
                    .fi-sidebar-header,
                    .fi-sidebar-brand {
                        gap: 0.625rem !important;
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

                    /* ── Nav items — strip ALL backgrounds first ── */
                    .fi-sidebar-item-button {
                        background-color: transparent !important;
                        background: none !important;
                        border-left: 2px solid transparent !important;
                        transition: background-color 0.12s, border-color 0.12s !important;
                    }
                    .fi-sidebar-item-label {
                        color: #94a3b8 !important;
                        font-size: 0.875rem !important;
                    }
                    .fi-sidebar-item-icon {
                        color: #64748b !important;
                    }
                    .fi-sidebar-item-button:hover {
                        background-color: rgba(148,163,184,0.12) !important;
                    }
                    .fi-sidebar-item-button:hover .fi-sidebar-item-label {
                        color: #e2e8f0 !important;
                    }
                    .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
                        color: #cbd5e1 !important;
                    }
                    /* Active — Filament v5 sets aria-current on active nav buttons */
                    .fi-sidebar-item-button[aria-current],
                    .fi-sidebar-item-button[aria-current="page"] {
                        background-color: rgba(34,197,94,0.08) !important;
                        border-left-color: #22c55e !important;
                    }
                    .fi-sidebar-item-button[aria-current] .fi-sidebar-item-label,
                    .fi-sidebar-item-button[aria-current="page"] .fi-sidebar-item-label {
                        color: #22c55e !important;
                        font-weight: 600 !important;
                    }
                    .fi-sidebar-item-button[aria-current] .fi-sidebar-item-icon,
                    .fi-sidebar-item-button[aria-current="page"] .fi-sidebar-item-icon {
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
                        max-width: none !important;
                    }
                    .fi-page-header-container,
                    .fi-dashboard-widgets-container {
                        max-width: none !important;
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
                       NexaSpace Developer Dashboard Components (nxd-*)
                       ================================================================ */

                    /* ── Root wrapper ── */
                    .nxd-root {
                        display: flex;
                        flex-direction: column;
                        gap: 1.25rem;
                        width: 100%;
                    }

                    /* ── Base glass card ── */
                    .nxd-card {
                        background: rgba(11,15,22,0.82);
                        border: 1px solid rgba(255,255,255,0.07);
                        border-radius: 1rem;
                        backdrop-filter: blur(14px);
                        -webkit-backdrop-filter: blur(14px);
                        overflow: hidden;
                    }

                    /* ── ROW 1: Hero + Overview (65/35 split) ── */
                    .nxd-hero-row {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 1.25rem;
                    }
                    @media (min-width: 1024px) {
                        .nxd-hero-row { grid-template-columns: 65fr 35fr; }
                    }

                    /* Welcome card */
                    .nxd-welcome {
                        position: relative;
                        min-height: 240px;
                        background: linear-gradient(135deg, #0a0f1a 0%, #091a0e 55%, #07090D 100%) !important;
                        border-color: rgba(34,197,94,0.16) !important;
                    }
                    .nxd-glow {
                        position: absolute;
                        top: -100px; right: -100px;
                        width: 380px; height: 380px;
                        background: radial-gradient(circle, rgba(34,197,94,0.11) 0%, transparent 65%);
                        border-radius: 50%;
                        pointer-events: none;
                        z-index: 0;
                    }
                    .nxd-art {
                        position: absolute;
                        top: 0; right: 0;
                        width: 65%; height: 100%;
                        pointer-events: none;
                        z-index: 0;
                    }
                    .nxd-welcome-inner {
                        position: relative;
                        z-index: 10;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        min-height: 240px;
                        padding: 1.875rem 2rem;
                        gap: 1.5rem;
                    }
                    .nxd-welcome-tag {
                        color: #22c55e;
                        font-size: 0.6875rem;
                        font-weight: 700;
                        letter-spacing: 0.16em;
                        text-transform: uppercase;
                        margin: 0 0 0.5rem;
                    }
                    .nxd-welcome-name {
                        color: #fff;
                        font-size: clamp(1.875rem, 3.5vw, 2.625rem);
                        font-weight: 800;
                        line-height: 1.05;
                        margin: 0 0 0.5rem;
                        letter-spacing: -0.01em;
                    }
                    .nxd-welcome-sub {
                        color: rgba(148,163,184,0.8);
                        font-size: 0.875rem;
                        margin: 0;
                    }
                    /* Welcome card footer */
                    .nxd-welcome-foot {
                        display: flex;
                        align-items: flex-end;
                        justify-content: space-between;
                        gap: 1rem;
                        flex-wrap: wrap;
                    }
                    .nxd-meta { display: flex; flex-direction: column; gap: 0.4rem; }
                    .nxd-meta-row {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: rgba(148,163,184,0.8);
                        font-size: 0.875rem;
                    }
                    .nxd-meta-ico { width: 0.9375rem; height: 0.9375rem; flex-shrink: 0; color: rgba(100,116,139,0.85); }
                    .nxd-signout {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.4375rem;
                        padding: 0.5rem 1rem;
                        border-radius: 0.5rem;
                        border: 1px solid rgba(34,197,94,0.22);
                        background: rgba(34,197,94,0.06);
                        color: rgba(203,213,225,0.9);
                        font-size: 0.8125rem;
                        font-weight: 500;
                        cursor: pointer;
                        transition: all 0.14s ease;
                        white-space: nowrap;
                        font-family: inherit;
                    }
                    .nxd-signout:hover {
                        background: rgba(34,197,94,0.12);
                        border-color: rgba(34,197,94,0.38);
                        color: #e2e8f0;
                    }

                    /* System Overview card */
                    .nxd-overview { display: flex; flex-direction: column; }
                    .nxd-ov-head {
                        display: flex;
                        align-items: flex-start;
                        gap: 0.75rem;
                        padding: 1.25rem 1.25rem 0.875rem;
                        border-bottom: 1px solid rgba(255,255,255,0.05);
                    }
                    .nxd-ov-dot {
                        width: 8px; height: 8px;
                        border-radius: 50%;
                        background: #22c55e;
                        box-shadow: 0 0 8px #22c55e, 0 0 16px rgba(34,197,94,0.4);
                        flex-shrink: 0;
                        margin-top: 0.35rem;
                        animation: nxdPulse 2s ease-in-out infinite;
                    }
                    @keyframes nxdPulse {
                        0%, 100% { box-shadow: 0 0 8px #22c55e, 0 0 16px rgba(34,197,94,0.4); }
                        50%       { box-shadow: 0 0 12px #22c55e, 0 0 24px rgba(34,197,94,0.6); }
                    }
                    .nxd-ov-title { color: #f1f5f9; font-size: 0.9375rem; font-weight: 600; margin: 0 0 0.125rem; }
                    .nxd-ov-sub   { color: rgba(100,116,139,0.9); font-size: 0.75rem; margin: 0; }
                    .nxd-ov-list {
                        display: flex;
                        flex-direction: column;
                        padding: 0.75rem;
                        gap: 0.375rem;
                        flex: 1;
                    }
                    .nxd-ov-item {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 0.625rem 0.75rem;
                        border-radius: 0.5rem;
                        border: 1px solid rgba(255,255,255,0.04);
                        background: rgba(255,255,255,0.025);
                        transition: background 0.1s;
                    }
                    .nxd-ov-item:hover { background: rgba(255,255,255,0.045); }
                    .nxd-ov-label { display: flex; align-items: center; gap: 0.5rem; color: rgba(148,163,184,0.9); font-size: 0.8125rem; }
                    .nxd-ov-ico   { width: 1rem; height: 1rem; color: rgba(100,116,139,0.8); flex-shrink: 0; }
                    .nxd-ov-val   { display: flex; align-items: center; gap: 0.375rem; }
                    .nxd-pulse-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.7); }
                    .nxd-green { color: #22c55e; font-size: 0.8125rem; font-weight: 500; }
                    .nxd-red   { color: #ef4444; font-size: 0.8125rem; font-weight: 500; }
                    .nxd-amber { color: #f59e0b; font-size: 0.8125rem; font-weight: 500; }
                    .nxd-bold  { font-weight: 600 !important; }

                    /* ── ROW 2: Stat cards (4 columns × 2 rows) ── */
                    .nxd-stats {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 1rem;
                    }
                    @media (min-width: 768px) { .nxd-stats { grid-template-columns: repeat(4, 1fr); } }
                    .nxd-stat {
                        background: rgba(11,15,22,0.82);
                        border: 1px solid rgba(255,255,255,0.07);
                        border-radius: 0.875rem;
                        backdrop-filter: blur(14px);
                        padding: 1.125rem 1.25rem 1rem;
                        display: flex;
                        flex-direction: column;
                        gap: 0.375rem;
                        transition: border-color 0.15s, background 0.15s;
                    }
                    .nxd-stat:hover { border-color: rgba(255,255,255,0.12); background: rgba(15,20,30,0.88); }
                    .nxd-stat-warn   { border-color: rgba(234,179,8,0.22) !important;  background: rgba(120,80,0,0.08) !important; }
                    .nxd-stat-info   { border-color: rgba(59,130,246,0.22) !important; background: rgba(10,30,80,0.1) !important; }
                    .nxd-stat-danger { border-color: rgba(239,68,68,0.22) !important;  background: rgba(80,10,10,0.1) !important; }
                    .nxd-stat-top { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.25rem; }
                    .nxd-stat-ico {
                        display: flex; align-items: center; justify-content: center;
                        width: 2rem; height: 2rem;
                        border-radius: 0.5rem;
                        background: rgba(34,197,94,0.1);
                        flex-shrink: 0;
                    }
                    .nxd-stat-ico svg { width: 1rem; height: 1rem; color: #22c55e; }
                    .nxd-stat-ico-warn            { background: rgba(234,179,8,0.1) !important; }
                    .nxd-stat-ico-warn svg        { color: #eab308 !important; }
                    .nxd-stat-ico-info            { background: rgba(59,130,246,0.1) !important; }
                    .nxd-stat-ico-info svg        { color: #3b82f6 !important; }
                    .nxd-stat-ico-danger          { background: rgba(239,68,68,0.1) !important; }
                    .nxd-stat-ico-danger svg      { color: #ef4444 !important; }
                    .nxd-stat-label { color: rgba(148,163,184,0.85); font-size: 0.75rem; font-weight: 500; line-height: 1.3; }
                    .nxd-stat-val   { color: #f1f5f9; font-size: 1.875rem; font-weight: 800; line-height: 1; letter-spacing: -0.02em; }
                    .nxd-stat-val-sm { font-size: 1.125rem !important; letter-spacing: -0.01em !important; }
                    .nxd-stat-desc  { color: rgba(100,116,139,0.8); font-size: 0.75rem; }

                    /* ── ROW 3: MikroTik (40%) + Chart (60%) ── */
                    .nxd-bottom {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 1.25rem;
                    }
                    @media (min-width: 1024px) { .nxd-bottom { grid-template-columns: 40fr 60fr; } }

                    /* MikroTik card */
                    .nxd-mk { display: flex; flex-direction: column; }
                    .nxd-mk-head {
                        display: flex; align-items: center; gap: 0.625rem;
                        padding: 1.125rem 1.25rem 0.875rem;
                        border-bottom: 1px solid rgba(255,255,255,0.05);
                    }
                    .nxd-mk-icon  { width: 1.125rem; height: 1.125rem; color: #22c55e; flex-shrink: 0; }
                    .nxd-mk-title { color: #e2e8f0; font-size: 0.9375rem; font-weight: 600; margin: 0; }
                    .nxd-mk-body  { padding: 1.125rem 1.25rem; display: flex; flex-direction: column; gap: 0.75rem; flex: 1; }
                    .nxd-mk-badge {
                        display: inline-flex; align-items: center; gap: 0.5rem;
                        padding: 0.375rem 0.75rem; border-radius: 99px;
                        font-size: 0.8125rem; font-weight: 600; width: fit-content;
                    }
                    .nxd-mk-badge-green { background: rgba(34,197,94,0.1);   color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
                    .nxd-mk-badge-red   { background: rgba(239,68,68,0.1);   color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
                    .nxd-mk-badge-gray  { background: rgba(100,116,139,0.1); color: #94a3b8; border: 1px solid rgba(100,116,139,0.2); }
                    .nxd-mk-dot       { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
                    .nxd-mk-dot-green { background: #22c55e; box-shadow: 0 0 5px rgba(34,197,94,0.6); }
                    .nxd-mk-dot-red   { background: #ef4444; box-shadow: 0 0 5px rgba(239,68,68,0.6); }
                    .nxd-mk-dot-gray  { background: #64748b; }
                    .nxd-mk-hint     { color: rgba(100,116,139,0.9); font-size: 0.8125rem; margin: 0; line-height: 1.5; }
                    .nxd-mk-hint-red { color: rgba(248,113,113,0.85); }
                    .nxd-mk-table {
                        display: flex; flex-direction: column; gap: 0.375rem;
                        background: rgba(255,255,255,0.025);
                        border: 1px solid rgba(255,255,255,0.05);
                        border-radius: 0.5rem;
                        padding: 0.625rem 0.75rem;
                    }
                    .nxd-mk-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; }
                    .nxd-mk-key { color: rgba(100,116,139,0.8); font-weight: 500; }
                    .nxd-mk-val { color: #cbd5e1; font-weight: 500; font-family: ui-monospace, monospace; font-size: 0.75rem; }
                    .nxd-mk-router { display: flex; justify-content: center; align-items: flex-end; padding-top: 0.5rem; margin-top: auto; opacity: 0.8; }
                    .nxd-code { font-family: ui-monospace, monospace; font-size: 0.75rem; background: rgba(255,255,255,0.06); padding: 0.125rem 0.375rem; border-radius: 0.25rem; color: #94a3b8; }

                    /* Revenue chart card */
                    .nxd-chart { display: flex; flex-direction: column; }
                    .nxd-chart-head {
                        display: flex; align-items: flex-start; justify-content: space-between;
                        padding: 1.125rem 1.25rem 0.875rem;
                        border-bottom: 1px solid rgba(255,255,255,0.05);
                    }
                    .nxd-chart-title { color: #e2e8f0; font-size: 0.9375rem; font-weight: 600; margin: 0 0 0.25rem; }
                    .nxd-chart-sub   { color: rgba(100,116,139,0.85); font-size: 0.75rem; margin: 0; }
                    .nxd-chart-body  { padding: 1rem 0.75rem 0.5rem; flex: 1; }
                    .nxd-chart-body svg { width: 100%; height: auto; display: block; overflow: visible; }
                    </style>
                    HTML,
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_LOGO_AFTER,
                fn (): string => '<div class="nxa-brand-copy"><span>NexaSpace</span><small>Admin Panel</small></div>',
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => <<<'HTML'
                    <a class="nxa-topbar-brand" href="/admin" aria-label="NexaSpace Admin Panel">
                        <img src="/images/logo-nexa.png" alt="" />
                        <span>
                            <strong>NexaSpace</strong>
                            <small>Admin Panel</small>
                        </span>
                    </a>
                    HTML,
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => <<<'HTML'
                    <div class="nxa-sidebar-footer">
                        <div class="nxa-sidebar-shield">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 4.5 6.75v5.625c0 4.142 2.84 8.008 7.5 9.375 4.66-1.367 7.5-5.233 7.5-9.375V6.75L12 3.75Z" />
                            </svg>
                        </div>
                        <div>
                            <strong>NexaSpace Admin</strong>
                            <span>© 2026 NexaSpace</span>
                        </div>
                    </div>
                    HTML,
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_AFTER,
                fn (): string => '<span class="nxa-topbar-name">' . e(auth()->user()?->name ?? '') . '</span><span class="nxa-topbar-chevron" aria-hidden="true">⌄</span>',
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                    /* NexaSpace dashboard reference layout overrides */
                    :root {
                        --nxa-bg: #05070b;
                        --nxa-panel: rgba(15,18,26,0.84);
                        --nxa-panel-2: rgba(18,22,31,0.9);
                        --nxa-line: rgba(255,255,255,0.09);
                        --nxa-line-strong: rgba(255,255,255,0.14);
                        --nxa-text: #f4f7fb;
                        --nxa-muted: #a3a9b7;
                        --nxa-dim: #70798a;
                        --nxa-green: #7cff47;
                        --nxa-green-soft: rgba(124,255,71,0.12);
                    }

                    html, html.dark, body, body.fi-body {
                        background:
                            radial-gradient(circle at 18% 16%, rgba(44,120,62,0.08), transparent 24rem),
                            radial-gradient(circle at 84% 74%, rgba(124,255,71,0.055), transparent 28rem),
                            var(--nxa-bg) !important;
                    }

                    .fi-layout {
                        min-height: 100vh;
                        background: transparent !important;
                    }

                    .fi-sidebar {
                        width: 18.25rem !important;
                        background: linear-gradient(180deg, rgba(7,10,15,0.96), rgba(9,14,20,0.96)) !important;
                        border-right: 1px solid var(--nxa-line) !important;
                        box-shadow: inset -1px 0 0 rgba(124,255,71,0.04), 18px 0 48px rgba(0,0,0,0.22);
                    }

                    .fi-sidebar-header,
                    .fi-sidebar-brand {
                        min-height: 5rem !important;
                        padding: 1.25rem 1.75rem !important;
                        background: rgba(5,7,11,0.72) !important;
                        border-bottom: 1px solid var(--nxa-line) !important;
                    }

                    .fi-sidebar .fi-logo {
                        height: 2.25rem !important;
                        width: 2.25rem !important;
                        object-fit: contain !important;
                        filter: drop-shadow(0 0 16px rgba(124,255,71,0.22));
                    }

                    .nxa-brand-copy {
                        display: flex;
                        flex-direction: column;
                        line-height: 1;
                        margin-left: 0.125rem;
                    }

                    .nxa-brand-copy span {
                        color: #ffffff;
                        font-size: 1.0625rem;
                        font-weight: 750;
                        letter-spacing: 0;
                    }

                    .nxa-brand-copy small {
                        color: rgba(255,255,255,0.62);
                        font-size: 0.625rem;
                        font-weight: 700;
                        letter-spacing: 0.29em;
                        margin-top: 0.33rem;
                        text-transform: uppercase;
                    }

                    .fi-sidebar-nav {
                        padding: 1.1rem 0.85rem 1rem !important;
                    }

                    .fi-sidebar-nav-groups {
                        gap: 0.3rem !important;
                    }

                    /* Precise nav items: fixed icon column so every label aligns
                       on the same vertical line; crisp left accent on active. */
                    .fi-sidebar-item-btn {
                        position: relative !important;
                        min-height: 2.95rem !important;
                        padding: 0 1rem 0 1.1rem !important;
                        gap: 0.85rem !important;
                        border-left: 0 !important;
                        border-radius: 0.55rem !important;
                        background: transparent !important;
                        color: var(--nxa-muted) !important;
                        transition: background-color 0.16s ease, color 0.16s ease !important;
                    }

                    .fi-sidebar-item-btn:hover {
                        background: rgba(255,255,255,0.045) !important;
                        color: #e7eaf0 !important;
                    }

                    .fi-sidebar-item-label {
                        color: inherit !important;
                        font-size: 0.95rem !important;
                        font-weight: 520 !important;
                        line-height: 1 !important;
                    }

                    .fi-sidebar-item-icon {
                        flex: 0 0 1.4rem !important;
                        width: 1.4rem !important;
                        height: 1.4rem !important;
                        color: currentColor !important;
                    }

                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                    .fi-sidebar-item-btn[aria-current],
                    .fi-sidebar-item-btn[aria-current="page"] {
                        background: linear-gradient(90deg, rgba(124,255,71,0.14), rgba(124,255,71,0.02) 70%, transparent) !important;
                        color: var(--nxa-green) !important;
                        box-shadow: none !important;
                    }

                    /* Crisp, vertically-centered accent bar (not a rounded border) */
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn::before,
                    .fi-sidebar-item-btn[aria-current]::before,
                    .fi-sidebar-item-btn[aria-current="page"]::before {
                        content: "" !important;
                        position: absolute !important;
                        left: 0 !important;
                        top: 50% !important;
                        transform: translateY(-50%) !important;
                        width: 3px !important;
                        height: 1.5rem !important;
                        border-radius: 0 3px 3px 0 !important;
                        background: var(--nxa-green) !important;
                        box-shadow: 0 0 10px rgba(124,255,71,0.6) !important;
                    }

                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label,
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon {
                        color: var(--nxa-green) !important;
                    }

                    .fi-sidebar-footer {
                        padding: 0 1.5rem 1.95rem !important;
                        background: transparent !important;
                        border-top: 0 !important;
                    }

                    .nxa-sidebar-footer {
                        display: flex;
                        align-items: center;
                        gap: 0.78rem;
                        padding-top: 1.35rem;
                        border-top: 1px solid rgba(255,255,255,0.07);
                    }

                    .nxa-sidebar-shield {
                        display: grid;
                        place-items: center;
                        width: 2.75rem;
                        height: 2.75rem;
                        border-radius: 0.65rem;
                        background: rgba(255,255,255,0.055);
                        border: 1px solid rgba(255,255,255,0.08);
                        color: #ffffff;
                    }

                    .nxa-sidebar-shield svg {
                        width: 1.35rem;
                        height: 1.35rem;
                    }

                    .nxa-sidebar-footer strong,
                    .nxa-sidebar-footer span {
                        display: block;
                    }

                    .nxa-sidebar-footer strong {
                        color: #ffffff;
                        font-size: 0.9rem;
                        font-weight: 650;
                    }

                    .nxa-sidebar-footer span {
                        color: rgba(255,255,255,0.55);
                        font-size: 0.78rem;
                        margin-top: 0.18rem;
                    }

                    .fi-topbar-ctn {
                        border-bottom: 1px solid var(--nxa-line) !important;
                        background: rgba(5,7,11,0.82) !important;
                        backdrop-filter: blur(16px) !important;
                    }

                    .fi-topbar,
                    header.fi-topbar,
                    [class*="fi-topbar"] {
                        min-height: 5rem !important;
                        background: transparent !important;
                        border-bottom: 0 !important;
                    }

                    .nxa-topbar-brand {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.7rem;
                        min-width: 18.25rem;
                        height: 5rem;
                        padding: 0 1.75rem;
                        border-right: 1px solid rgba(255,255,255,0.08);
                        color: #ffffff;
                        text-decoration: none;
                    }

                    .nxa-topbar-brand img {
                        width: 2.25rem;
                        height: 2.25rem;
                        object-fit: contain;
                        filter: drop-shadow(0 0 16px rgba(124,255,71,0.24));
                    }

                    .nxa-topbar-brand span {
                        display: flex;
                        flex-direction: column;
                        line-height: 1;
                    }

                    .nxa-topbar-brand strong {
                        color: #ffffff;
                        font-size: 1.03rem;
                        font-weight: 760;
                        letter-spacing: 0;
                    }

                    .nxa-topbar-brand small {
                        color: rgba(255,255,255,0.62);
                        font-size: 0.62rem;
                        font-weight: 700;
                        letter-spacing: 0.28em;
                        margin-top: 0.34rem;
                        text-transform: uppercase;
                    }

                    .fi-topbar-start .fi-logo,
                    .fi-topbar-logo {
                        display: none !important;
                    }

                    .fi-user-menu-trigger {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        flex: 0 0 2.875rem !important;
                        width: 2.875rem !important;
                        height: 2.875rem !important;
                        min-width: 2.875rem !important;
                        min-height: 2.875rem !important;
                        padding: 0 !important;
                        aspect-ratio: 1 / 1 !important;
                        border-radius: 999px !important;
                        border: 1.5px solid var(--nxa-green) !important;
                        background: rgba(124,255,71,0.035) !important;
                        box-shadow: 0 0 22px rgba(124,255,71,0.12) !important;
                        overflow: hidden !important;
                    }

                    .fi-user-menu-trigger .fi-avatar,
                    .fi-user-menu-trigger .fi-user-avatar,
                    .fi-user-menu-trigger img {
                        width: 100% !important;
                        height: 100% !important;
                        aspect-ratio: 1 / 1 !important;
                        border-radius: 999px !important;
                        object-fit: cover !important;
                    }

                    .fi-user-avatar,
                    .fi-avatar {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        background: transparent !important;
                        color: #ffffff !important;
                        border: 0 !important;
                        font-weight: 650 !important;
                    }

                    .nxa-topbar-name {
                        color: #ffffff;
                        font-size: 0.98rem;
                        font-weight: 620;
                        margin-left: 0.65rem;
                    }

                    .nxa-topbar-chevron {
                        color: rgba(255,255,255,0.72);
                        font-size: 1rem;
                        margin-left: 0.35rem;
                        transform: translateY(-0.06rem);
                    }

                    .fi-main {
                        background: transparent !important;
                    }

                    .fi-main-ctn,
                    .fi-page,
                    .fi-page > section,
                    .fi-page-content {
                        background: transparent !important;
                    }

                    main.fi-main {
                        padding: 1.45rem 1.45rem 2.35rem !important;
                    }

                    .fi-header {
                        display: none !important;
                    }

                    .fi-dashboard-widgets,
                    .fi-dashboard-widgets-container,
                    .fi-widgets,
                    .fi-wi,
                    .fi-page-content,
                    .fi-page-header-widgets {
                        width: 100% !important;
                        max-width: none !important;
                    }

                    /* Filament 5.6 renders the dashboard widgets inside a schema
                       grid (.fi-sc.fi-grid) — NOT the legacy .fi-wi container.
                       Force the developer welcome widget to span the full grid so
                       it fills the page width instead of a single column. */
                    main.fi-main .fi-sc {
                        width: 100% !important;
                        max-width: none !important;
                    }

                    main.fi-main .fi-sc > .nxd-widget,
                    main.fi-main .fi-sc > .fi-wi-widget:has(.nxd-root),
                    .nxd-widget:has(.nxd-root) {
                        grid-column: 1 / -1 !important;
                        width: 100% !important;
                        min-width: 0 !important;
                    }

                    .fi-widgets {
                        display: block !important;
                    }

                    .fi-widgets > * {
                        width: 100% !important;
                    }

                    .nxd-root {
                        width: min(100%, 90rem);
                        margin: 0 auto;
                        gap: 1.45rem;
                    }

                    .nxd-card,
                    .nxd-stat {
                        background:
                            linear-gradient(135deg, rgba(255,255,255,0.055), rgba(255,255,255,0.02)),
                            var(--nxa-panel) !important;
                        border: 1px solid var(--nxa-line-strong) !important;
                        border-radius: 0.8rem !important;
                        box-shadow: inset 0 1px 0 rgba(255,255,255,0.055), 0 18px 48px rgba(0,0,0,0.26) !important;
                        backdrop-filter: blur(18px) !important;
                    }

                    .nxd-hero-row {
                        gap: 1.45rem !important;
                    }

                    @media (min-width: 1080px) {
                        .nxd-hero-row {
                            grid-template-columns: minmax(0, 2.12fr) minmax(20rem, 1fr) !important;
                        }
                    }

                    .nxd-welcome {
                        min-height: 19.25rem !important;
                        background:
                            linear-gradient(105deg, rgba(6,8,12,0.98) 0%, rgba(15,22,20,0.9) 42%, rgba(11,20,13,0.74) 100%),
                            radial-gradient(circle at 69% 43%, rgba(124,255,71,0.24), transparent 10rem) !important;
                        border-color: rgba(255,255,255,0.16) !important;
                    }

                    .nxd-glow {
                        top: auto !important;
                        right: 12% !important;
                        bottom: -7rem !important;
                        width: 34rem !important;
                        height: 20rem !important;
                        background: radial-gradient(ellipse, rgba(124,255,71,0.16), transparent 70%) !important;
                        border-radius: 50% !important;
                    }

                    .nxd-art {
                        width: 61% !important;
                        height: 100% !important;
                        opacity: 0.9 !important;
                    }

                    .nxd-welcome-inner {
                        min-height: 19.25rem !important;
                        padding: 3.4rem 2.65rem 1.85rem !important;
                    }

                    .nxd-welcome-tag {
                        color: var(--nxa-green) !important;
                        font-size: 0.78rem !important;
                        letter-spacing: 0.04em !important;
                        margin-bottom: 1rem !important;
                    }

                    .nxd-welcome-name {
                        color: #ffffff !important;
                        font-size: clamp(3.25rem, 5vw, 4.65rem) !important;
                        letter-spacing: 0 !important;
                        text-shadow: 0 8px 32px rgba(0,0,0,0.35);
                    }

                    .nxd-welcome-sub {
                        color: rgba(228,232,240,0.76) !important;
                        font-size: 1rem !important;
                    }

                    .nxd-meta {
                        flex-direction: row !important;
                        align-items: center !important;
                        gap: 1.25rem !important;
                    }

                    .nxd-meta-row {
                        color: rgba(226,232,240,0.78) !important;
                        font-size: 0.96rem !important;
                    }

                    .nxd-meta-row + .nxd-meta-row {
                        padding-left: 1.25rem;
                        border-left: 1px solid rgba(255,255,255,0.15);
                    }

                    .nxd-meta-ico {
                        color: rgba(226,232,240,0.76) !important;
                        width: 1.2rem !important;
                        height: 1.2rem !important;
                    }

                    .nxd-signout {
                        min-height: 2.75rem !important;
                        padding: 0 1.1rem !important;
                        border-radius: 0.55rem !important;
                        background: rgba(255,255,255,0.06) !important;
                        border: 1px solid rgba(255,255,255,0.16) !important;
                        color: #ffffff !important;
                        font-size: 0.98rem !important;
                    }

                    .nxd-signout svg {
                        color: var(--nxa-green);
                    }

                    .nxd-overview {
                        min-height: 19.25rem;
                    }

                    .nxd-ov-head {
                        align-items: flex-start !important;
                        padding: 1.75rem 1.65rem 1rem !important;
                        border-bottom: 0 !important;
                        position: relative;
                    }

                    .nxd-ov-head::before {
                        content: "";
                        width: 1.55rem;
                        height: 1.55rem;
                        margin-top: 0.05rem;
                        background: var(--nxa-green);
                        -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='black' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M3 12h3l2.5-7 5 14 2.5-7h5'/%3E%3C/svg%3E") center / contain no-repeat;
                        mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='black' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M3 12h3l2.5-7 5 14 2.5-7h5'/%3E%3C/svg%3E") center / contain no-repeat;
                    }

                    .nxd-ov-head::after {
                        content: "";
                        position: absolute;
                        top: 1.85rem;
                        right: 1.5rem;
                        width: 0.55rem;
                        height: 0.55rem;
                        border-radius: 999px;
                        background: var(--nxa-green);
                        box-shadow: 0 0 14px rgba(124,255,71,0.68);
                    }

                    .nxd-ov-dot {
                        display: none !important;
                    }

                    .nxd-ov-title {
                        color: #ffffff !important;
                        font-size: 1.08rem !important;
                        font-weight: 760 !important;
                    }

                    .nxd-ov-sub {
                        color: rgba(226,232,240,0.66) !important;
                        font-size: 0.9rem !important;
                        margin-top: 0.35rem !important;
                    }

                    .nxd-ov-list {
                        padding: 0.8rem 1.55rem 1.35rem !important;
                        gap: 0 !important;
                    }

                    .nxd-ov-item {
                        min-height: 3.2rem;
                        padding: 0 0.2rem !important;
                        background: transparent !important;
                        border: 0 !important;
                        border-bottom: 1px solid rgba(255,255,255,0.08) !important;
                        border-radius: 0 !important;
                    }

                    .nxd-ov-item:last-child {
                        border-bottom: 0 !important;
                    }

                    .nxd-ov-label {
                        color: rgba(226,232,240,0.78) !important;
                        font-size: 0.94rem !important;
                    }

                    .nxd-ov-ico {
                        width: 1.25rem !important;
                        height: 1.25rem !important;
                        color: rgba(226,232,240,0.7) !important;
                    }

                    .nxd-green {
                        color: var(--nxa-green) !important;
                    }

                    .nxd-pulse-dot {
                        width: 0.45rem !important;
                        height: 0.45rem !important;
                        background: var(--nxa-green) !important;
                        box-shadow: 0 0 12px rgba(124,255,71,0.72) !important;
                    }

                    .nxd-stats {
                        display: grid !important;
                        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                        gap: 1.35rem !important;
                    }

                    .nxd-stat {
                        min-height: 8.8rem !important;
                        padding: 1.38rem 1.48rem !important;
                        display: grid !important;
                        grid-template-columns: auto minmax(0, 1fr) !important;
                        grid-template-rows: auto auto auto !important;
                        column-gap: 1.2rem !important;
                        align-content: center !important;
                    }

                    .nxd-stat-top {
                        display: contents !important;
                    }

                    .nxd-stat-ico {
                        grid-row: 1 / 4 !important;
                        width: 3.65rem !important;
                        height: 3.65rem !important;
                        border-radius: 0.65rem !important;
                        background: linear-gradient(135deg, rgba(255,255,255,0.07), rgba(255,255,255,0.025)) !important;
                        border: 1px solid rgba(255,255,255,0.09) !important;
                    }

                    .nxd-stat-ico svg {
                        width: 1.72rem !important;
                        height: 1.72rem !important;
                        color: var(--nxa-green) !important;
                        stroke-width: 1.75 !important;
                    }

                    .nxd-stat-label {
                        color: rgba(226,232,240,0.72) !important;
                        font-size: 0.92rem !important;
                        font-weight: 520 !important;
                    }

                    .nxd-stat-val {
                        color: #ffffff !important;
                        font-size: 2.15rem !important;
                        margin: 0.42rem 0 0 !important;
                    }

                    .nxd-stat-val-sm {
                        font-size: 1.68rem !important;
                        line-height: 1.15 !important;
                    }

                    .nxd-stat-desc {
                        color: rgba(226,232,240,0.62) !important;
                        font-size: 0.88rem !important;
                        margin: 0.24rem 0 0 !important;
                    }

                    @media (min-width: 1080px) {
                        .nxd-stats .nxd-stat:nth-child(5),
                        .nxd-stats .nxd-stat:nth-child(6),
                        .nxd-stats .nxd-stat:nth-child(7) {
                            grid-column: span 1;
                        }
                    }

                    .nxd-bottom {
                        gap: 1.35rem !important;
                    }

                    @media (min-width: 1080px) {
                        .nxd-bottom {
                            grid-template-columns: minmax(24rem, 0.78fr) minmax(0, 1.22fr) !important;
                        }
                    }

                    .nxd-mk,
                    .nxd-chart {
                        min-height: 19.35rem;
                    }

                    .nxd-mk-head,
                    .nxd-chart-head {
                        padding: 1.55rem 1.55rem 1.1rem !important;
                        border-bottom: 1px solid rgba(255,255,255,0.08) !important;
                    }

                    .nxd-mk-head {
                        position: relative;
                    }

                    .nxd-mk-head::after {
                        content: "⋮";
                        position: absolute;
                        right: 1.45rem;
                        top: 1.35rem;
                        color: rgba(226,232,240,0.58);
                        font-size: 1.35rem;
                        line-height: 1;
                    }

                    .nxd-mk-icon {
                        color: var(--nxa-green) !important;
                    }

                    .nxd-mk-title,
                    .nxd-chart-title {
                        color: #ffffff !important;
                        font-size: 1.1rem !important;
                        font-weight: 750 !important;
                    }

                    .nxd-mk-body {
                        min-height: 13.7rem;
                        padding: 1.45rem !important;
                        display: grid !important;
                        grid-template-columns: 1fr 1fr !important;
                        align-items: end !important;
                        gap: 1.3rem !important;
                    }

                    .nxd-mk-badge,
                    .nxd-mk-hint,
                    .nxd-mk-table {
                        grid-column: 1 !important;
                    }

                    .nxd-mk-badge {
                        align-self: end !important;
                        border-radius: 0.7rem !important;
                        padding: 0.85rem 1rem !important;
                        background: rgba(255,255,255,0.035) !important;
                        border: 1px solid rgba(255,255,255,0.08) !important;
                        color: rgba(255,255,255,0.9) !important;
                        width: 100% !important;
                    }

                    .nxd-mk-dot-green,
                    .nxd-mk-dot-gray {
                        background: var(--nxa-green) !important;
                        box-shadow: 0 0 13px rgba(124,255,71,0.65) !important;
                    }

                    .nxd-mk-hint {
                        color: rgba(226,232,240,0.7) !important;
                        font-size: 0.9rem !important;
                        margin-top: -0.85rem !important;
                    }

                    .nxd-mk-router {
                        grid-column: 2 !important;
                        grid-row: 1 / 4 !important;
                        justify-content: center !important;
                        opacity: 1 !important;
                    }

                    .nxd-mk-router svg {
                        width: 12rem;
                        height: 10rem;
                        filter: drop-shadow(0 16px 30px rgba(124,255,71,0.12));
                    }

                    .nxd-chart-head {
                        align-items: center !important;
                    }

                    .nxd-chart-head::after {
                        content: "6 Bulan⌄";
                        display: inline-flex;
                        align-items: center;
                        height: 2.2rem;
                        padding: 0 0.88rem;
                        border-radius: 0.48rem;
                        border: 1px solid rgba(255,255,255,0.09);
                        background: rgba(255,255,255,0.035);
                        color: rgba(255,255,255,0.86);
                        font-size: 0.86rem;
                    }

                    .nxd-chart-sub {
                        display: none !important;
                    }

                    .nxd-chart-body {
                        padding: 1rem 1.45rem 1.2rem !important;
                    }

                    .nxd-chart-body svg {
                        min-height: 14.2rem;
                    }

                    @media (max-width: 1240px) {
                        .nxd-stats {
                            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                        }

                        .nxd-bottom,
                        .nxd-hero-row {
                            grid-template-columns: 1fr !important;
                        }
                    }

                    @media (max-width: 720px) {
                        main.fi-main {
                            padding: 1rem !important;
                        }

                        .nxd-root {
                            gap: 1rem;
                        }

                        .nxd-welcome-inner {
                            padding: 2rem 1.35rem 1.35rem !important;
                        }

                        .nxd-welcome-name {
                            font-size: 2.6rem !important;
                        }

                        .nxd-meta {
                            flex-direction: column !important;
                            align-items: flex-start !important;
                        }

                        .nxd-meta-row + .nxd-meta-row {
                            border-left: 0;
                            padding-left: 0;
                        }

                        .nxd-stats {
                            grid-template-columns: 1fr !important;
                        }

                        .nxd-mk-body {
                            grid-template-columns: 1fr !important;
                        }

                        .nxd-mk-router {
                            grid-column: 1 !important;
                            grid-row: auto !important;
                        }

                        .nxa-topbar-name,
                        .nxa-topbar-chevron {
                            display: none;
                        }
                    }
                    </style>
                    HTML,
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                    /* NexaSpace global admin table dark mode */
                    .fi-ta,
                    .fi-ta-ctn,
                    .fi-ta-main,
                    .fi-ta-content-ctn,
                    .fi-ta-content,
                    .fi-ta-table,
                    .fi-ta-header-ctn,
                    .fi-ta-header,
                    .fi-ta-header-toolbar,
                    .fi-ta-filters-before-content-ctn,
                    .fi-ta-filters-above-content-ctn,
                    .fi-ta-filters-after-content-ctn,
                    .fi-ta-filters-below-content-ctn,
                    .fi-ta-filters,
                    .fi-ta-filter-indicators,
                    .fi-ta-selection-indicator,
                    .fi-ta-reorder-indicator,
                    .fi-ta-empty-state,
                    .fi-ta-table-loading-ctn {
                        background: #05070b !important;
                        background-color: #05070b !important;
                        color: #ffffff !important;
                        border-color: rgba(255,255,255,0.09) !important;
                    }

                    .fi-ta-ctn {
                        border: 1px solid rgba(255,255,255,0.10) !important;
                        border-radius: 0.9rem !important;
                        overflow: hidden !important;
                        box-shadow: 0 20px 50px rgba(0,0,0,0.28), inset 0 1px 0 rgba(255,255,255,0.04) !important;
                    }

                    .fi-ta-table,
                    .fi-ta-table thead,
                    .fi-ta-table tbody,
                    .fi-ta-table tfoot,
                    .fi-ta-table tr,
                    .fi-ta-table th,
                    .fi-ta-table td,
                    .fi-ta-row,
                    .fi-ta-cell,
                    .fi-ta-cell-content,
                    .fi-ta-col,
                    .fi-ta-text,
                    .fi-ta-text-item,
                    .fi-ta-text-item-label,
                    .fi-ta-cell-label {
                        background: #05070b !important;
                        background-color: #05070b !important;
                        color: #ffffff !important;
                        border-color: rgba(255,255,255,0.08) !important;
                    }

                    .fi-ta-table thead tr,
                    .fi-ta-table thead th,
                    .fi-ta-content-header,
                    .fi-ta-summary-header-row,
                    .fi-ta-summary-row,
                    .fi-ta-group-header,
                    .fi-ta-group-header-cell {
                        background: #080b11 !important;
                        background-color: #080b11 !important;
                        color: #ffffff !important;
                    }

                    .fi-ta-header-cell,
                    .fi-ta-header-cell *,
                    .fi-ta-cell *,
                    .fi-ta-row *,
                    .fi-ta-empty-state *,
                    .fi-ta-filter-indicators *,
                    .fi-ta-selection-indicator *,
                    .fi-ta-reorder-indicator *,
                    .fi-ta-group-heading,
                    .fi-ta-group-description {
                        color: #ffffff !important;
                    }

                    .fi-ta-header-cell,
                    .fi-ta-header-cell-label,
                    .fi-ta-header-heading,
                    .fi-ta-header-description,
                    .fi-ta-col-manager-heading,
                    .fi-ta-filters-heading {
                        color: #ffffff !important;
                    }

                    .fi-ta-row {
                        border-bottom: 1px solid rgba(255,255,255,0.07) !important;
                    }

                    .fi-ta-row:hover,
                    .fi-ta-row:hover > .fi-ta-cell,
                    .fi-ta-row:hover td,
                    .fi-ta-row:hover .fi-ta-cell,
                    .fi-ta-row:hover .fi-ta-col {
                        background: rgba(74,222,81,0.075) !important;
                        background-color: rgba(74,222,81,0.075) !important;
                    }

                    .fi-ta .text-gray-950,
                    .fi-ta .text-gray-900,
                    .fi-ta .text-gray-800,
                    .fi-ta .text-gray-700,
                    .fi-ta .text-gray-600,
                    .fi-ta .text-gray-500,
                    .fi-ta .text-gray-400,
                    .fi-ta [class*="text-gray-"],
                    .fi-ta [class*="dark:text-gray-"],
                    .fi-ta [class*="fi-color-gray"] {
                        color: #ffffff !important;
                    }

                    .fi-ta .bg-white,
                    .fi-ta .bg-gray-50,
                    .fi-ta .bg-gray-100,
                    .fi-ta .bg-gray-200,
                    .fi-ta [class*="bg-gray-"],
                    .fi-ta [class*="dark:bg-gray-"] {
                        background: #05070b !important;
                        background-color: #05070b !important;
                    }

                    .fi-ta-search-field .fi-input-wrp,
                    .fi-ta-search-field input,
                    .fi-ta .fi-input-wrp,
                    .fi-ta .fi-select-input,
                    .fi-ta .fi-input,
                    .fi-ta input,
                    .fi-ta select {
                        background: #0a0d14 !important;
                        background-color: #0a0d14 !important;
                        border-color: rgba(255,255,255,0.15) !important;
                        color: #ffffff !important;
                    }

                    .fi-ta-header-toolbar .fi-btn,
                    .fi-ta-header-toolbar button,
                    .fi-ta-header-toolbar a.fi-btn,
                    .fi-ta-header .fi-btn,
                    .fi-ta-header button,
                    .fi-ta-header a.fi-btn {
                        background: linear-gradient(135deg, rgba(22,101,52,0.96), rgba(34,197,94,0.78)) !important;
                        border: 1px solid rgba(124,255,71,0.42) !important;
                        color: #ffffff !important;
                        font-weight: 900 !important;
                        box-shadow: 0 12px 28px rgba(0,0,0,0.32), 0 0 18px rgba(34,197,94,0.18), inset 0 1px 0 rgba(255,255,255,0.14) !important;
                    }

                    .fi-ta-header-toolbar .fi-btn:hover,
                    .fi-ta-header-toolbar button:hover,
                    .fi-ta-header-toolbar a.fi-btn:hover,
                    .fi-ta-header .fi-btn:hover,
                    .fi-ta-header button:hover,
                    .fi-ta-header a.fi-btn:hover {
                        background: linear-gradient(135deg, rgba(21,128,61,0.98), rgba(74,222,81,0.86)) !important;
                        border-color: rgba(124,255,71,0.68) !important;
                        box-shadow: 0 14px 32px rgba(0,0,0,0.38), 0 0 24px rgba(34,197,94,0.24), inset 0 1px 0 rgba(255,255,255,0.18) !important;
                    }

                    .fi-ta-header-toolbar .fi-btn *,
                    .fi-ta-header-toolbar button *,
                    .fi-ta-header-toolbar a.fi-btn *,
                    .fi-ta-header .fi-btn *,
                    .fi-ta-header button *,
                    .fi-ta-header a.fi-btn * {
                        color: #ffffff !important;
                        font-weight: 900 !important;
                    }

                    .fi-ta-header-toolbar .fi-input-wrp,
                    .fi-ta-header-toolbar input,
                    .fi-ta-header-toolbar select,
                    .fi-ta-header .fi-input-wrp,
                    .fi-ta-header input,
                    .fi-ta-header select {
                        background: #111827 !important;
                        background-color: #111827 !important;
                        border-color: rgba(255,255,255,0.18) !important;
                        color: #ffffff !important;
                    }

                    .fi-ta-search-field input::placeholder,
                    .fi-ta input::placeholder {
                        color: rgba(255,255,255,0.55) !important;
                    }

                    .fi-ta .fi-icon,
                    .fi-ta svg,
                    .fi-ta .fi-icon-btn,
                    .fi-ta .fi-ta-icon,
                    .fi-ta .fi-ta-header-cell-sort-icon {
                        color: #ffffff !important;
                    }

                    .fi-ta .fi-pagination,
                    .fi-ta .fi-pagination *,
                    .fi-ta .fi-pagination-items,
                    .fi-ta .fi-pagination-item,
                    .fi-ta .fi-pagination-overview,
                    .fi-ta .fi-select-input,
                    .fi-ta-footer,
                    .fi-ta-footer * {
                        background-color: #05070b !important;
                        color: #ffffff !important;
                        border-color: rgba(255,255,255,0.12) !important;
                    }

                    .fi-ta .fi-pagination-item.fi-active,
                    .fi-ta .fi-pagination-item[aria-current="page"] {
                        background-color: rgba(74,222,81,0.14) !important;
                        color: #7cff47 !important;
                        border-color: rgba(124,255,71,0.35) !important;
                    }

                    .fi-ta .fi-badge {
                        background-color: rgba(74,222,81,0.14) !important;
                        color: #7cff47 !important;
                        border-color: rgba(124,255,71,0.24) !important;
                    }

                    .fi-ta .fi-checkbox-input,
                    .fi-ta input[type="checkbox"] {
                        background-color: #ffffff !important;
                        border-color: #ffffff !important;
                        color: #22c55e !important;
                        box-shadow: 0 0 0 1px rgba(255,255,255,0.44), 0 0 14px rgba(255,255,255,0.12) !important;
                    }

                    .fi-ta .fi-checkbox-input:checked,
                    .fi-ta input[type="checkbox"]:checked {
                        background-color: #4ade51 !important;
                        border-color: #4ade51 !important;
                        color: #ffffff !important;
                        box-shadow: 0 0 0 1px rgba(124,255,71,0.45), 0 0 16px rgba(34,197,94,0.24) !important;
                    }

                    .fi-ta .fi-checkbox-input:focus,
                    .fi-ta input[type="checkbox"]:focus {
                        outline: none !important;
                        box-shadow: 0 0 0 3px rgba(255,255,255,0.22), 0 0 18px rgba(255,255,255,0.18) !important;
                    }
                    </style>
                    HTML,
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                    /* NexaSpace global admin form and filter dark mode */
                    .fi-page .fi-section,
                    .fi-page .fi-sc,
                    .fi-page .fi-fo,
                    .fi-page .fi-fo-repeater-item,
                    .fi-page .fi-fo-file-upload,
                    .fi-page .fi-fo-file-upload .filepond--panel-root,
                    .fi-page .fi-fo-file-upload .filepond--drop-label,
                    .fi-modal-window,
                    .fi-modal-content {
                        background: #05070b !important;
                        border-color: rgba(255,255,255,0.14) !important;
                        color: #ffffff !important;
                    }

                    .fi-page .fi-section *,
                    .fi-page .fi-sc *,
                    .fi-page .fi-fo *,
                    .fi-modal-window *,
                    .fi-modal-content * {
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    .fi-page .fi-section-header,
                    .fi-page .fi-section-content,
                    .fi-page .fi-section-content-ctn,
                    .fi-page .fi-section-footer,
                    .fi-page .fi-sc-component,
                    .fi-page .fi-fo-field-wrp,
                    .fi-page .fi-fo-field-wrp-content,
                    .fi-page .fi-fo-field-wrp-content-ctn {
                        background: transparent !important;
                    }

                    .fi-page .fi-section-header-description,
                    .fi-page .fi-section-description,
                    .fi-page .fi-fo-field-helper-text,
                    .fi-page .fi-fo-field-wrp-helper-text,
                    .fi-page .fi-hint,
                    .fi-modal-description {
                        color: rgba(255,255,255,0.74) !important;
                        font-weight: 700 !important;
                    }

                    .fi-page .fi-fo-field-label,
                    .fi-page .fi-fo-field-label-content,
                    .fi-page .fi-fo-field-label-ctn,
                    .fi-page .fi-input-wrp-label,
                    .fi-ta-filters .fi-fo-field-label,
                    .fi-ta-filters .fi-fo-field-label-content,
                    .fi-ta-filters .fi-fo-field-label-ctn {
                        color: #ffffff !important;
                        font-weight: 800 !important;
                    }

                    .fi-page .fi-fo-field-label-required-mark,
                    .fi-ta-filters .fi-fo-field-label-required-mark {
                        color: #ff4242 !important;
                    }

                    .fi-page .fi-input-wrp,
                    .fi-page .fi-select-input,
                    .fi-page .fi-textarea,
                    .fi-page .fi-input,
                    .fi-page input,
                    .fi-page textarea,
                    .fi-page select,
                    .fi-ta-filters .fi-input-wrp,
                    .fi-ta-filters .fi-select-input,
                    .fi-ta-filters .fi-textarea,
                    .fi-ta-filters .fi-input,
                    .fi-ta-filters input,
                    .fi-ta-filters textarea,
                    .fi-ta-filters select,
                    .fi-dropdown-panel .fi-input-wrp,
                    .fi-dropdown-panel input,
                    .fi-dropdown-panel textarea,
                    .fi-dropdown-panel select {
                        background-color: #111827 !important;
                        background-image: none !important;
                        border-color: rgba(255,255,255,0.18) !important;
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    .fi-page .fi-input-wrp,
                    .fi-page .fi-select-input,
                    .fi-page .fi-textarea,
                    .fi-page .fi-input,
                    .fi-page input,
                    .fi-page textarea,
                    .fi-page select {
                        background-color: #111827 !important;
                        border-color: rgba(255,255,255,0.20) !important;
                        box-shadow: inset 0 1px 0 rgba(255,255,255,0.05) !important;
                    }

                    .fi-ta-filters .fi-input-wrp,
                    .fi-ta-filters .fi-select-input,
                    .fi-ta-filters .fi-textarea,
                    .fi-ta-filters .fi-input,
                    .fi-ta-filters input,
                    .fi-ta-filters textarea,
                    .fi-ta-filters select,
                    .fi-dropdown-panel .fi-input-wrp,
                    .fi-dropdown-panel input,
                    .fi-dropdown-panel textarea,
                    .fi-dropdown-panel select {
                        background-color: #05070b !important;
                        border-color: rgba(124,255,71,0.36) !important;
                        box-shadow: none !important;
                    }

                    .fi-page .fi-fo-repeater-add .fi-btn,
                    .fi-page .fi-fo-simple-repeater-add .fi-btn,
                    .fi-page .fi-fo-table-repeater-add .fi-btn,
                    .fi-page .fi-fo-key-value-add-action-ctn .fi-btn,
                    .fi-page .fi-fo-repeater-add button,
                    .fi-page .fi-fo-simple-repeater-add button,
                    .fi-page .fi-fo-table-repeater-add button,
                    .fi-page .fi-fo-key-value-add-action-ctn button {
                        background: linear-gradient(135deg, rgba(31,41,55,0.96), rgba(22,101,52,0.52)) !important;
                        border: 1px solid rgba(124,255,71,0.34) !important;
                        color: #ffffff !important;
                        font-weight: 800 !important;
                        box-shadow: 0 12px 28px rgba(0,0,0,0.32), inset 0 1px 0 rgba(255,255,255,0.08) !important;
                    }

                    .fi-page .fi-fo-repeater-add .fi-btn:hover,
                    .fi-page .fi-fo-simple-repeater-add .fi-btn:hover,
                    .fi-page .fi-fo-table-repeater-add .fi-btn:hover,
                    .fi-page .fi-fo-key-value-add-action-ctn .fi-btn:hover,
                    .fi-page .fi-fo-repeater-add button:hover,
                    .fi-page .fi-fo-simple-repeater-add button:hover,
                    .fi-page .fi-fo-table-repeater-add button:hover,
                    .fi-page .fi-fo-key-value-add-action-ctn button:hover {
                        background: linear-gradient(135deg, rgba(55,65,81,0.98), rgba(34,197,94,0.62)) !important;
                        border-color: rgba(124,255,71,0.58) !important;
                        box-shadow: 0 16px 34px rgba(0,0,0,0.38), 0 0 22px rgba(34,197,94,0.16) !important;
                    }

                    .fi-page .fi-fo-repeater-add .fi-btn *,
                    .fi-page .fi-fo-simple-repeater-add .fi-btn *,
                    .fi-page .fi-fo-table-repeater-add .fi-btn *,
                    .fi-page .fi-fo-key-value-add-action-ctn .fi-btn *,
                    .fi-page .fi-fo-repeater-add button *,
                    .fi-page .fi-fo-simple-repeater-add button *,
                    .fi-page .fi-fo-table-repeater-add button *,
                    .fi-page .fi-fo-key-value-add-action-ctn button * {
                        color: #ffffff !important;
                        font-weight: 800 !important;
                    }

                    .fi-page .fi-ac .fi-btn,
                    .fi-page .fi-form-actions .fi-btn,
                    .fi-page form .fi-btn,
                    .fi-page .fi-page-header-actions .fi-btn,
                    .fi-ac .fi-btn.fi-color-gray,
                    .fi-ac .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning),
                    .fi-form-actions .fi-btn.fi-color-gray,
                    .fi-form-actions .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning) {
                        border-radius: 0.65rem !important;
                        font-weight: 900 !important;
                        min-height: 2.625rem !important;
                    }

                    .fi-page .fi-ac .fi-btn.fi-color-primary,
                    .fi-page .fi-ac .fi-btn-color-primary,
                    .fi-page .fi-form-actions .fi-btn.fi-color-primary,
                    .fi-page .fi-form-actions .fi-btn-color-primary,
                    .fi-page form .fi-btn.fi-color-primary,
                    .fi-page form .fi-btn-color-primary,
                    .fi-page .fi-page-header-actions .fi-btn.fi-color-primary,
                    .fi-page .fi-page-header-actions .fi-btn-color-primary,
                    .fi-page button[type="submit"].fi-btn {
                        background: linear-gradient(135deg, rgba(22,101,52,0.96), rgba(34,197,94,0.78)) !important;
                        border: 1px solid rgba(124,255,71,0.44) !important;
                        color: #ffffff !important;
                        box-shadow: 0 14px 30px rgba(0,0,0,0.36), 0 0 18px rgba(34,197,94,0.18), inset 0 1px 0 rgba(255,255,255,0.12) !important;
                    }

                    .fi-page .fi-ac .fi-btn.fi-color-primary:hover,
                    .fi-page .fi-ac .fi-btn-color-primary:hover,
                    .fi-page .fi-form-actions .fi-btn.fi-color-primary:hover,
                    .fi-page .fi-form-actions .fi-btn-color-primary:hover,
                    .fi-page form .fi-btn.fi-color-primary:hover,
                    .fi-page form .fi-btn-color-primary:hover,
                    .fi-page .fi-page-header-actions .fi-btn.fi-color-primary:hover,
                    .fi-page .fi-page-header-actions .fi-btn-color-primary:hover,
                    .fi-page button[type="submit"].fi-btn:hover {
                        background: linear-gradient(135deg, rgba(21,128,61,0.98), rgba(74,222,128,0.84)) !important;
                        border-color: rgba(124,255,71,0.62) !important;
                        box-shadow: 0 16px 34px rgba(0,0,0,0.42), 0 0 24px rgba(34,197,94,0.24), inset 0 1px 0 rgba(255,255,255,0.14) !important;
                    }

                    .fi-page .fi-ac .fi-btn.fi-color-gray,
                    .fi-page .fi-ac .fi-btn-color-gray,
                    .fi-page .fi-form-actions .fi-btn.fi-color-gray,
                    .fi-page .fi-form-actions .fi-btn-color-gray,
                    .fi-page form .fi-btn.fi-color-gray,
                    .fi-page form .fi-btn-color-gray,
                    .fi-page .fi-page-header-actions .fi-btn.fi-color-gray,
                    .fi-page .fi-page-header-actions .fi-btn-color-gray,
                    .fi-ac .fi-btn.fi-color-gray,
                    .fi-form-actions .fi-btn.fi-color-gray,
                    .fi-page .fi-ac .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning),
                    .fi-page .fi-form-actions .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning),
                    .fi-page form .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning),
                    .fi-page .fi-btn.bg-white,
                    .fi-page .fi-btn[class*="bg-white"] {
                        --bg: #111827 !important;
                        --hover-bg: #1f2937 !important;
                        --dark-bg: #111827 !important;
                        --dark-hover-bg: #1f2937 !important;
                        --text: #ffffff !important;
                        --hover-text: #ffffff !important;
                        --dark-text: #ffffff !important;
                        --dark-hover-text: #ffffff !important;
                        background: linear-gradient(135deg, rgba(17,24,39,0.98), rgba(31,41,55,0.94)) !important;
                        background-color: #111827 !important;
                        background-image: linear-gradient(135deg, rgba(17,24,39,0.98), rgba(31,41,55,0.94)) !important;
                        border: 1px solid rgba(255,255,255,0.18) !important;
                        color: #ffffff !important;
                        box-shadow: 0 12px 26px rgba(0,0,0,0.30), inset 0 1px 0 rgba(255,255,255,0.07) !important;
                    }

                    .fi-page .fi-ac .fi-btn.fi-color-gray:hover,
                    .fi-page .fi-ac .fi-btn-color-gray:hover,
                    .fi-page .fi-form-actions .fi-btn.fi-color-gray:hover,
                    .fi-page .fi-form-actions .fi-btn-color-gray:hover,
                    .fi-page form .fi-btn.fi-color-gray:hover,
                    .fi-page form .fi-btn-color-gray:hover,
                    .fi-page .fi-page-header-actions .fi-btn.fi-color-gray:hover,
                    .fi-page .fi-page-header-actions .fi-btn-color-gray:hover,
                    .fi-ac .fi-btn.fi-color-gray:hover,
                    .fi-form-actions .fi-btn.fi-color-gray:hover,
                    .fi-page .fi-ac .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning):hover,
                    .fi-page .fi-form-actions .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning):hover,
                    .fi-page form .fi-btn:not(.fi-color-primary):not(.fi-color-success):not(.fi-color-danger):not(.fi-color-warning):hover,
                    .fi-page .fi-btn.bg-white:hover,
                    .fi-page .fi-btn[class*="bg-white"]:hover {
                        background: linear-gradient(135deg, rgba(31,41,55,1), rgba(55,65,81,0.96)) !important;
                        background-color: #1f2937 !important;
                        background-image: linear-gradient(135deg, rgba(31,41,55,1), rgba(55,65,81,0.96)) !important;
                        border-color: rgba(255,255,255,0.28) !important;
                    }

                    .fi-page .fi-ac .fi-btn *,
                    .fi-page .fi-form-actions .fi-btn *,
                    .fi-page form .fi-btn *,
                    .fi-page .fi-page-header-actions .fi-btn * {
                        color: #ffffff !important;
                        font-weight: 900 !important;
                    }

                    .fi-page .fi-input::placeholder,
                    .fi-page input::placeholder,
                    .fi-page textarea::placeholder,
                    .fi-ta-filters .fi-input::placeholder,
                    .fi-ta-filters input::placeholder,
                    .fi-ta-filters textarea::placeholder {
                        color: rgba(255,255,255,0.55) !important;
                        font-weight: 700 !important;
                    }

                    .fi-page .fi-input-wrp:focus-within,
                    .fi-page input:focus,
                    .fi-page textarea:focus,
                    .fi-page select:focus,
                    .fi-ta-filters .fi-input-wrp:focus-within,
                    .fi-ta-filters input:focus,
                    .fi-ta-filters textarea:focus,
                    .fi-ta-filters select:focus {
                        border-color: rgba(124,255,71,0.9) !important;
                        box-shadow: 0 0 0 1px rgba(124,255,71,0.26), 0 0 22px rgba(34,197,94,0.14) !important;
                        outline: none !important;
                    }

                    .fi-ta-filters,
                    .fi-ta-filters-form,
                    .fi-ta-filters-header,
                    .fi-ta-filters-dropdown,
                    .fi-ta-filters-modal,
                    .fi-dropdown-panel,
                    .fi-dropdown-list,
                    .fi-dropdown-list-item,
                    .fi-select-options,
                    .fi-select-option {
                        background: #05070b !important;
                        border-color: rgba(255,255,255,0.14) !important;
                        color: #ffffff !important;
                    }

                    .fi-ta-filters *,
                    .fi-ta-filters-dropdown *,
                    .fi-ta-filters-modal *,
                    .fi-dropdown-panel *,
                    .fi-select-options *,
                    .fi-select-option * {
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    .fi-ta-filters-heading {
                        color: #ffffff !important;
                        font-weight: 900 !important;
                    }

                    .fi-ta-filters option,
                    .fi-ta-filters select option,
                    .fi-dropdown-panel option,
                    .fi-page select option,
                    .fi-page option {
                        background-color: #05070b !important;
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    .fi-ta-filters option:checked,
                    .fi-ta-filters select option:checked,
                    .fi-page select option:checked {
                        background-color: #12351d !important;
                        color: #ffffff !important;
                    }

                    .fi-dropdown-list-item:hover,
                    .fi-dropdown-list-item:focus,
                    .fi-select-option:hover,
                    .fi-select-option[aria-selected='true'] {
                        background: rgba(124,255,71,0.16) !important;
                        color: #ffffff !important;
                    }

                    .fi-ta-filters .fi-btn.fi-color-danger,
                    .fi-ta-filters .fi-link.fi-color-danger,
                    .fi-ta-filters a.fi-color-danger,
                    .fi-ta-filters button.fi-color-danger {
                        color: #ff4242 !important;
                        font-weight: 900 !important;
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
