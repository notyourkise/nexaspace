<?php

namespace App\Providers\Filament;

use App\Filament\Tenant\Pages\EditProfile;
use App\Filament\Tenant\Pages\Login;
use App\Filament\Tenant\Widgets\TenantDashboardWidget;
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
            ->maxContentWidth(Width::Full)
            ->favicon(asset('images/logo-nexa.png'))
            ->brandName('NEXASPACE')
            ->brandLogo(asset('images/logo-nexa.png'))
            ->brandLogoHeight('2.5rem')
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
                EditProfile::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Tenant/Widgets'),
                for: 'App\Filament\Tenant\Widgets',
            )
            ->widgets([
                TenantDashboardWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => $this->darkThemeCss(),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_LOGO_AFTER,
                fn (): string => '<span class="nxt-brand-name">NEXASPACE</span>',
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

    /**
     * Dark premium theme for the tenant panel — mirrors the admin/developer panel
     * chrome (background, sidebar, topbar, sections, forms, tables) so anak kos
     * see the same look. The dashboard content itself is styled by
     * TenantDashboardWidget (nxt-* classes).
     */
    private function darkThemeCss(): string
    {
        return <<<'HTML'
            <script>(function(){document.documentElement.classList.add('dark');})();</script>
            <style>
            /* ── Force dark base ── */
            html, html.dark, body, body.fi-body {
                background:
                    radial-gradient(circle at 18% 16%, rgba(44,120,62,0.08), transparent 24rem),
                    radial-gradient(circle at 84% 74%, rgba(124,255,71,0.055), transparent 28rem),
                    #05070b !important;
                color: #e2e8f0 !important;
                color-scheme: dark;
            }

            /* ── Override Tailwind light-mode utilities ── */
            .bg-white     { background-color: #0b0f18 !important; }
            .bg-gray-50   { background-color: #080c14 !important; }
            .bg-gray-100  { background-color: #0b1020 !important; }
            .bg-gray-200  { background-color: #111827 !important; }
            .text-gray-950, .text-gray-900 { color: #f1f5f9 !important; }
            .text-gray-800 { color: #e2e8f0 !important; }
            .text-gray-700 { color: #cbd5e1 !important; }
            .text-gray-600 { color: #94a3b8 !important; }
            .text-gray-500 { color: #64748b !important; }
            .border-gray-100 { border-color: rgba(255,255,255,0.06) !important; }
            .border-gray-200 { border-color: rgba(255,255,255,0.08) !important; }
            .divide-gray-100 > * + * { border-color: rgba(255,255,255,0.05) !important; }
            .divide-gray-200 > * + * { border-color: rgba(255,255,255,0.07) !important; }
            .ring-gray-200 { --tw-ring-color: rgba(255,255,255,0.08) !important; }

            /* ── Layout ── */
            .fi-layout { background: transparent !important; }

            /* ── Brand ── */
            .fi-brand-name {
                color: #7cff47 !important;
                font-weight: 800 !important;
                letter-spacing: 0.12em !important;
                text-transform: uppercase !important;
            }
            .fi-sidebar-header, .fi-sidebar-brand { gap: 0.6rem !important; }
            .nxt-brand-name {
                color: #7cff47;
                font-size: 1.05rem;
                font-weight: 800;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                white-space: nowrap;
            }

            /* ── Sidebar ── */
            .fi-sidebar, nav.fi-sidebar {
                background: linear-gradient(180deg, rgba(7,10,15,0.96), rgba(9,14,20,0.96)) !important;
                border-right: 1px solid rgba(255,255,255,0.09) !important;
            }
            .fi-sidebar-header, .fi-sidebar-brand {
                background: rgba(5,7,11,0.72) !important;
                border-bottom: 1px solid rgba(255,255,255,0.09) !important;
            }
            .fi-sidebar-nav, .fi-sidebar-nav-groups { background: transparent !important; }
            .fi-sidebar-item-btn, .fi-sidebar-item-button {
                position: relative !important;
                background: transparent !important;
                border-radius: 0.55rem !important;
                color: #a3a9b7 !important;
                transition: background-color 0.16s ease, color 0.16s ease !important;
            }
            .fi-sidebar-item-label { color: inherit !important; font-weight: 520 !important; }
            .fi-sidebar-item-icon  { color: currentColor !important; }
            .fi-sidebar-item-btn:hover, .fi-sidebar-item-button:hover {
                background: rgba(255,255,255,0.045) !important;
                color: #e7eaf0 !important;
            }
            .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
            .fi-sidebar-item-btn[aria-current],
            .fi-sidebar-item-btn[aria-current="page"],
            .fi-sidebar-item-button[aria-current],
            .fi-sidebar-item-button[aria-current="page"] {
                background: linear-gradient(90deg, rgba(124,255,71,0.14), rgba(124,255,71,0.02) 70%, transparent) !important;
                color: #7cff47 !important;
            }
            .fi-sidebar-item.fi-active > .fi-sidebar-item-btn::before,
            .fi-sidebar-item-btn[aria-current]::before,
            .fi-sidebar-item-btn[aria-current="page"]::before {
                content: "" !important;
                position: absolute !important;
                left: 0 !important; top: 50% !important;
                transform: translateY(-50%) !important;
                width: 3px !important; height: 1.5rem !important;
                border-radius: 0 3px 3px 0 !important;
                background: #7cff47 !important;
                box-shadow: 0 0 10px rgba(124,255,71,0.6) !important;
            }
            .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label,
            .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon,
            .fi-sidebar-item-btn[aria-current] .fi-sidebar-item-label,
            .fi-sidebar-item-btn[aria-current] .fi-sidebar-item-icon {
                color: #7cff47 !important;
            }

            /* ── Topbar ── */
            .fi-topbar-ctn {
                border-bottom: 1px solid rgba(255,255,255,0.09) !important;
                background: rgba(5,7,11,0.82) !important;
                backdrop-filter: blur(16px) !important;
            }
            .fi-topbar, header.fi-topbar, [class*="fi-topbar"] {
                background: transparent !important;
            }
            .fi-topbar .fi-breadcrumbs-item, .fi-topbar .fi-breadcrumbs-separator { color: #64748b !important; }
            .fi-user-avatar, .fi-avatar {
                background: rgba(124,255,71,0.12) !important;
                color: #7cff47 !important;
                border: 1px solid rgba(124,255,71,0.28) !important;
            }
            .fi-dropdown-trigger-button { color: #cbd5e1 !important; }

            /* ── Main content ── */
            main.fi-main, .fi-main, .fi-main-ctn, .fi-page, .fi-page > section, .fi-page-content {
                background: transparent !important;
            }
            .fi-header-heading, h1.fi-header-heading { color: #f1f5f9 !important; }
            .fi-header-subheading { color: #64748b !important; }

            /* ── Sections / cards ── */
            .fi-section, .fi-widget, .fi-wi-chart, .fi-wi-account {
                background: rgba(11,15,20,0.78) !important;
                border: 1px solid rgba(255,255,255,0.08) !important;
                backdrop-filter: blur(10px) !important;
            }
            .fi-section-header, .fi-section-header-heading { color: #f1f5f9 !important; }
            .fi-section-description, .fi-section-header-description { color: #64748b !important; }
            .fi-section-content { background: transparent !important; }

            /* ── Stats overview (fallback) ── */
            .fi-wi-stats-overview-stat {
                background: rgba(11,15,20,0.75) !important;
                border: 1px solid rgba(255,255,255,0.07) !important;
            }
            .fi-wi-stats-overview-stat-label { color: #94a3b8 !important; }
            .fi-wi-stats-overview-stat-value { color: #f1f5f9 !important; }
            .fi-wi-stats-overview-stat-description { color: #64748b !important; }
            .fi-wi-stats-overview-stat-icon { color: #7cff47 !important; }

            /* ── Tables ── */
            .fi-ta-wrap, .fi-ta { background: transparent !important; }
            .fi-ta-header { background: rgba(255,255,255,0.02) !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
            .fi-ta-header-cell { color: #64748b !important; text-transform: uppercase !important; }
            .fi-ta-row { border-bottom: 1px solid rgba(255,255,255,0.04) !important; }
            .fi-ta-row:hover td, .fi-ta-row:hover .fi-ta-cell { background-color: rgba(124,255,71,0.03) !important; }
            .fi-ta-cell { color: #cbd5e1 !important; background: transparent !important; }
            .fi-ta-empty-state-icon { color: #334155 !important; }
            .fi-ta-empty-state-heading { color: #64748b !important; }
            .fi-ta-text-item-label { color: #cbd5e1 !important; }
            input[type="checkbox"], .fi-checkbox-input {
                background-color: #fff !important;
                border-color: rgba(255,255,255,0.4) !important;
            }
            input[type="checkbox"]:checked, .fi-checkbox-input:checked {
                background-color: #7cff47 !important;
                border-color: #7cff47 !important;
            }

            /* ── Forms / inputs ── */
            .fi-fo-field-wrp-label, .fi-fo-field-label, .fi-fo-field-wrp label { color: #cbd5e1 !important; }
            .fi-input, .fi-select-input, .fi-textarea,
            input[type="text"], input[type="email"], input[type="password"], input[type="tel"],
            select, textarea {
                background: rgba(255,255,255,0.04) !important;
                border-color: rgba(255,255,255,0.1) !important;
                color: #e2e8f0 !important;
            }
            .fi-input:focus, .fi-select-input:focus {
                border-color: rgba(124,255,71,0.5) !important;
                box-shadow: 0 0 0 2px rgba(124,255,71,0.15) !important;
            }
            .fi-fo-field-wrp-helper-text { color: #475569 !important; }

            /* ── Modals ── */
            .fi-modal-window, .fi-modal-content { background: #0e141f !important; border: 1px solid rgba(255,255,255,0.08) !important; }
            .fi-modal-header-heading { color: #f1f5f9 !important; }
            .fi-modal-header-subheading, .fi-modal-description { color: #64748b !important; }
            .fi-modal-overlay { background: rgba(0,0,0,0.65) !important; backdrop-filter: blur(4px) !important; }

            /* ── Dropdown / notifications ── */
            .fi-dropdown-panel { background: #0e141f !important; border: 1px solid rgba(255,255,255,0.08) !important; }
            .fi-dropdown-list-item-label { color: #cbd5e1 !important; }
            .fi-dropdown-list-item:hover { background: rgba(255,255,255,0.04) !important; }
            .fi-no-notification { background: #0e141f !important; border: 1px solid rgba(255,255,255,0.08) !important; }
            .fi-no-notification-title { color: #f1f5f9 !important; }
            .fi-no-notification-body { color: #94a3b8 !important; }

            /* ── Buttons ── */
            .fi-btn-color-gray {
                background: rgba(255,255,255,0.06) !important;
                border-color: rgba(255,255,255,0.1) !important;
                color: #cbd5e1 !important;
            }
            .fi-btn-color-gray:hover { background: rgba(255,255,255,0.09) !important; }

            /* ── Scrollbar ── */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.18); }

            /* ════════════════════════════════════════════════
               Modal pembayaran: input putih (teks hitam),
               teks di luar input putih bold
               ════════════════════════════════════════════════ */
            /* Heading + deskripsi modal */
            .fi-modal-window .fi-modal-header-heading,
            .fi-modal-window .fi-modal-heading,
            .fi-modal-window .fi-modal-header-subheading,
            .fi-modal-window .fi-modal-description,
            .fi-modal-window .fi-fo-field-label,
            .fi-modal-window .fi-fo-field-label-content,
            .fi-modal-window .fi-fo-field-wrp-helper-text,
            .fi-modal-window label {
                color: #ffffff !important;
                font-weight: 700 !important;
            }

            /* Input → background putih, teks hitam */
            .fi-modal-window .fi-input,
            .fi-modal-window .fi-select-input,
            .fi-modal-window .fi-input-wrp,
            .fi-modal-window .fi-fo-select-trigger,
            .fi-modal-window input:not([type=checkbox]):not([type=radio]),
            .fi-modal-window select,
            .fi-modal-window textarea {
                background-color: #ffffff !important;
                color: #000000 !important;
                border-color: rgba(0,0,0,0.18) !important;
            }
            .fi-modal-window .fi-input-wrp { box-shadow: none !important; }
            .fi-modal-window .fi-fo-select-trigger,
            .fi-modal-window .fi-fo-select-trigger * {
                color: #000000 !important;
            }
            .fi-modal-window input::placeholder,
            .fi-modal-window textarea::placeholder,
            .fi-modal-window .fi-fo-select-trigger .fi-fo-select-placeholder {
                color: #6b7280 !important;
                font-weight: 600 !important;
            }

            /* Panel opsi select → putih, teks hitam */
            .fi-modal-window [role="listbox"],
            .fi-modal-window .fi-fo-select-options,
            .fi-modal-window .fi-dropdown-panel {
                background-color: #ffffff !important;
                border-color: rgba(0,0,0,0.12) !important;
            }
            .fi-modal-window [role="option"],
            .fi-modal-window .fi-fo-select-option,
            .fi-modal-window .fi-fo-select-option-label,
            .fi-modal-window .fi-dropdown-list-item-label {
                color: #000000 !important;
                font-weight: 600 !important;
            }
            .fi-modal-window [role="option"]:hover,
            .fi-modal-window .fi-fo-select-option:hover,
            .fi-modal-window .fi-dropdown-list-item:hover {
                background-color: rgba(0,0,0,0.06) !important;
            }

            /* FileUpload (FilePond) → area putih, teks gelap */
            .fi-modal-window .filepond--root,
            .fi-modal-window .filepond--panel-root,
            .fi-modal-window .filepond--drop-label {
                background-color: #ffffff !important;
                color: #1f2937 !important;
            }
            .fi-modal-window .filepond--label-action { color: #16a34a !important; font-weight: 800 !important; }
            </style>
            HTML;
    }
}
