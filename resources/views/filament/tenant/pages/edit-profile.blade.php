<x-filament-panels::page>
    <div class="space-y-6 nxt-editprofile">
        {{-- Informasi kamar (read-only) — desain sama dengan "Informasi Platform" developer --}}
        @php($roomUser = auth()->user())

        <style>
            /* ── Semua input background hitam, semua teks putih bold (khusus halaman ini) ── */
            .nxt-editprofile .fi-input,
            .nxt-editprofile .fi-select-input,
            .nxt-editprofile .fi-textarea,
            .nxt-editprofile .fi-input-wrp,
            .nxt-editprofile input,
            .nxt-editprofile select,
            .nxt-editprofile textarea {
                background-color: #000000 !important;
                color: #ffffff !important;
                font-weight: 700 !important;
                border-color: rgba(255,255,255,0.18) !important;
            }
            .nxt-editprofile .fi-input-wrp {
                box-shadow: none !important;
            }
            .nxt-editprofile input::placeholder,
            .nxt-editprofile textarea::placeholder {
                color: rgba(255,255,255,0.55) !important;
                font-weight: 600 !important;
            }

            /* Semua teks pada section form jadi putih bold */
            .nxt-editprofile .fi-section,
            .nxt-editprofile .fi-section * {
                color: #ffffff !important;
            }
            .nxt-editprofile .fi-section :is(p, span, label, div, h1, h2, h3, h4, a, button, li, small, strong) {
                font-weight: 700 !important;
            }

            /* Kartu Informasi Kamar: paksa label/value/judul jadi putih penuh & bold,
               tapi pertahankan ikon hijau */
            .nxt-editprofile .nxp-platform-label,
            .nxt-editprofile .nxp-platform-value,
            .nxt-editprofile .nxp-platform-header h2 {
                color: #ffffff !important;
                font-weight: 800 !important;
            }
            .nxt-editprofile .nxp-platform-icon,
            .nxt-editprofile .nxp-platform-main-icon {
                color: #7cff47 !important;
            }

            /* Judul halaman */
            .fi-header-heading {
                color: #ffffff !important;
                font-weight: 800 !important;
            }

            /* ── Jarak proporsional antar card / form ── */
            .nxt-editprofile { display: flex; flex-direction: column; gap: 2rem; }
            .nxt-editprofile > * + * { margin-top: 0 !important; }
            .nxt-editprofile .fi-section + .fi-section { margin-top: 1.75rem !important; }
        </style>

        <style>
            .nxp-platform-card {
                overflow: hidden;
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 0.85rem;
                background: #05070b;
                box-shadow: 0 20px 50px rgba(0,0,0,0.22);
            }

            .nxp-platform-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.8rem 1rem;
                border-bottom: 1px solid rgba(255,255,255,0.10);
            }

            .nxp-platform-header h2 {
                margin: 0;
                color: #ffffff;
                font-size: 1rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .nxp-platform-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .nxp-platform-item {
                display: flex;
                align-items: center;
                min-width: 0;
                gap: 0.8rem;
                padding: 1rem 1.25rem;
            }

            .nxp-platform-item + .nxp-platform-item {
                border-left: 1px solid rgba(255,255,255,0.10);
            }

            .nxp-platform-icon,
            .nxp-platform-main-icon {
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                color: #7cff47;
                background: rgba(34,197,94,0.10);
                box-shadow: inset 0 0 0 1px rgba(124,255,71,0.22);
            }

            .nxp-platform-main-icon {
                width: 2rem;
                height: 2rem;
                border-radius: 0.55rem;
            }

            .nxp-platform-icon {
                width: 2.65rem;
                height: 2.65rem;
                border-radius: 999px;
            }

            .nxp-platform-label {
                margin: 0;
                color: rgba(255,255,255,0.70);
                font-size: 0.78rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .nxp-platform-value {
                margin: 0.28rem 0 0;
                color: #ffffff;
                font-size: 0.92rem;
                font-weight: 900;
                line-height: 1.25;
            }

            .nxp-platform-value.mono {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            }

            @media (max-width: 920px) {
                .nxp-platform-grid {
                    grid-template-columns: 1fr;
                }

                .nxp-platform-item + .nxp-platform-item {
                    border-left: 0;
                    border-top: 1px solid rgba(255,255,255,0.10);
                }
            }
        </style>

        <section class="nxp-platform-card">
            <div class="nxp-platform-header">
                <div class="nxp-platform-main-icon">
                    <svg style="width:1.1rem;height:1.1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                </div>
                <h2>Informasi Kamar</h2>
            </div>

            <div class="nxp-platform-grid">
                <div class="nxp-platform-item">
                    <div class="nxp-platform-icon">
                        <svg style="width:1.35rem;height:1.35rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615a2.25 2.25 0 0 1-1.07-1.916V7.5" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-platform-label">Email Login</p>
                        <p class="nxp-platform-value mono">{{ $roomUser->email }}</p>
                    </div>
                </div>

                <div class="nxp-platform-item">
                    <div class="nxp-platform-icon">
                        <svg style="width:1.35rem;height:1.35rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-platform-label">Nomor Kamar</p>
                        <p class="nxp-platform-value">{{ $roomUser->room_number ? 'Kamar ' . $roomUser->room_number : '—' }}</p>
                    </div>
                </div>

                <div class="nxp-platform-item">
                    <div class="nxp-platform-icon">
                        <svg style="width:1.35rem;height:1.35rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-platform-label">Nama Kos</p>
                        <p class="nxp-platform-value">{{ $roomUser->juragan?->kos_name ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Profile form --}}
        <form wire:submit="saveProfile">
            {{ $this->profileForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Simpan Profil
                </x-filament::button>
            </div>
        </form>

        {{-- Password form --}}
        <form wire:submit="savePassword">
            {{ $this->passwordForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" color="warning">
                    Ubah Password
                </x-filament::button>
            </div>
        </form>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
