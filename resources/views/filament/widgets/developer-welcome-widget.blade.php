<div class="nexa-welcome-grid">

    {{-- ── Hero Welcome Card ── --}}
    <div class="nexa-hero-card">

        {{-- Radial glow top-right --}}
        <div class="nexa-hero-glow"></div>

        {{-- Abstract SVG wave decoration --}}
        <svg class="nexa-hero-art" viewBox="0 0 800 280" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
                <filter id="nexaGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="2.5" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>
            {{-- Wave lines --}}
            <path d="M 800 50 C 680 50 580 130 440 100 C 300 70 180 155 0 120"
                  stroke="#22c55e" stroke-width="1.4" fill="none" opacity="0.45"/>
            <path d="M 800 110 C 680 110 580 190 440 160 C 300 130 180 215 0 180"
                  stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.35"/>
            <path d="M 800 170 C 680 170 580 250 440 220 C 300 190 180 270 0 240"
                  stroke="#22c55e" stroke-width="1"   fill="none" opacity="0.22"/>
            <path d="M 800 230 C 680 230 580 275 440 260 C 300 245 180 275 0 265"
                  stroke="#22c55e" stroke-width="0.8" fill="none" opacity="0.13"/>

            {{-- Concentric rings --}}
            <circle cx="660" cy="140" r="92" stroke="#22c55e" stroke-width="0.8" fill="none" opacity="0.11"/>
            <circle cx="660" cy="140" r="65" stroke="#22c55e" stroke-width="1"   fill="none" opacity="0.17"/>
            <circle cx="660" cy="140" r="40" stroke="#22c55e" stroke-width="0.8"
                    fill="rgba(34,197,94,0.04)" opacity="0.28" filter="url(#nexaGlow)"/>
            <circle cx="660" cy="140" r="12" fill="rgba(34,197,94,0.18)" opacity="0.55"/>

            {{-- Dot particles --}}
            <circle cx="558" cy="82"  r="2.5" fill="#22c55e" opacity="0.55" filter="url(#nexaGlow)"/>
            <circle cx="620" cy="197" r="1.8" fill="#22c55e" opacity="0.45"/>
            <circle cx="712" cy="73"  r="2"   fill="#22c55e" opacity="0.4"/>
            <circle cx="490" cy="228" r="1.5" fill="#22c55e" opacity="0.5"/>
            <circle cx="746" cy="212" r="1.2" fill="#22c55e" opacity="0.35"/>
            <circle cx="522" cy="49"  r="1.5" fill="#22c55e" opacity="0.45"/>
            <circle cx="762" cy="152" r="3"   fill="#22c55e" opacity="0.28" filter="url(#nexaGlow)"/>

            {{-- Cross-hair tick marks on wave --}}
            <line x1="440" y1="97"  x2="453" y2="97"  stroke="#22c55e" stroke-width="1" opacity="0.38"/>
            <line x1="440" y1="157" x2="453" y2="157" stroke="#22c55e" stroke-width="1" opacity="0.28"/>
        </svg>

        {{-- Card content --}}
        <div class="nexa-hero-content">
            <div>
                <p class="nexa-welcome-label">Welcome Back</p>
                <h1 class="nexa-welcome-name">{{ $userName }}</h1>
                <p class="nexa-welcome-sub">Ringkasan sistem dan aktivitas NexaSpace.</p>
            </div>

            <div class="nexa-hero-footer">
                <div class="nexa-meta">
                    <div class="nexa-meta-row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nexa-meta-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span>{{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                    </div>
                    <div class="nexa-meta-row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nexa-meta-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            x-data="{
                                time: '',
                                init() {
                                    const fmt = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Makassar' };
                                    const tick = () => { this.time = new Intl.DateTimeFormat('id-ID', fmt).format(new Date()); };
                                    tick();
                                    setInterval(tick, 1000);
                                }
                            }"
                            x-text="time + ' WITA'"
                        >{{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i:s') }} WITA</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <button type="submit" class="nexa-signout-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── System Overview Card ── --}}
    <div class="nexa-overview-card">
        <div class="nexa-overview-header">
            <div class="nexa-overview-pulse"></div>
            <div>
                <h3 class="nexa-overview-title">System Overview</h3>
                <p class="nexa-overview-sub">Platform health and key system indicators</p>
            </div>
        </div>

        <div class="nexa-overview-list">

            {{-- System Status --}}
            <div class="nexa-overview-item">
                <div class="nexa-overview-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="nexa-ov-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>System Status</span>
                </div>
                <div class="nexa-overview-item-value">
                    <span class="nexa-dot-pulse"></span>
                    <span class="nexa-green-text">Operational</span>
                </div>
            </div>

            {{-- Server Uptime --}}
            <div class="nexa-overview-item">
                <div class="nexa-overview-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="nexa-ov-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 014.5-4.5h13.5a4.5 4.5 0 014.5 4.5" />
                    </svg>
                    <span>Server Uptime</span>
                </div>
                <span class="nexa-green-text nexa-fw600">99.98%</span>
            </div>

            {{-- Database --}}
            <div class="nexa-overview-item">
                <div class="nexa-overview-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="nexa-ov-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.25v2.25m0 2.25v2.25" />
                    </svg>
                    <span>Database</span>
                </div>
                @if ($dbHealthy)
                    <span class="nexa-green-text">Healthy</span>
                @else
                    <span style="color:#ef4444;font-size:0.8125rem;font-weight:500;">Error</span>
                @endif
            </div>

            {{-- Backup / Failed Jobs --}}
            <div class="nexa-overview-item">
                <div class="nexa-overview-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="nexa-ov-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <span>Backup Status</span>
                </div>
                @if ($failedJobs === 0)
                    <span class="nexa-green-text">Up to date</span>
                @else
                    <span style="color:#f59e0b;font-size:0.8125rem;font-weight:600;">{{ $failedJobs }} failed</span>
                @endif
            </div>

        </div>
    </div>

</div>
