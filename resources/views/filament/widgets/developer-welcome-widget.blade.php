<x-filament-widgets::widget class="nxd-widget">
<div class="nxd-root">

    {{-- ═══════════════════════════════════════════════════════
         ROW 1 — Hero + System Overview
         ═══════════════════════════════════════════════════════ --}}
    <div class="nxd-hero-row">

        {{-- Welcome card ── --}}
        <div class="nxd-card nxd-welcome">
            {{-- Radial glow --}}
            <div class="nxd-glow"></div>

            {{-- Abstract wave SVG --}}
            <svg class="nxd-art" viewBox="0 0 800 280" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <filter id="nxGlow"><feGaussianBlur stdDeviation="2.5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                </defs>
                <path d="M800 45 C680 45 580 125 440 96 C300 67 180 150 0 118"  stroke="#22c55e" stroke-width="1.5" fill="none" opacity="0.42"/>
                <path d="M800 105 C680 105 580 185 440 156 C300 127 180 210 0 178" stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.32"/>
                <path d="M800 165 C680 165 580 245 440 216 C300 187 180 268 0 238" stroke="#22c55e" stroke-width="1"   fill="none" opacity="0.2"/>
                <circle cx="660" cy="140" r="94" stroke="#22c55e" stroke-width="0.8" fill="none" opacity="0.1"/>
                <circle cx="660" cy="140" r="66" stroke="#22c55e" stroke-width="1"   fill="none" opacity="0.16"/>
                <circle cx="660" cy="140" r="41" stroke="#22c55e" stroke-width="0.8" fill="rgba(34,197,94,0.04)" opacity="0.26" filter="url(#nxGlow)"/>
                <circle cx="660" cy="140" r="11" fill="rgba(34,197,94,0.18)" opacity="0.55"/>
                <circle cx="558" cy="80"  r="2.5" fill="#22c55e" opacity="0.5" filter="url(#nxGlow)"/>
                <circle cx="620" cy="196" r="1.8" fill="#22c55e" opacity="0.4"/>
                <circle cx="714" cy="72"  r="2"   fill="#22c55e" opacity="0.38"/>
                <circle cx="760" cy="152" r="3"   fill="#22c55e" opacity="0.26" filter="url(#nxGlow)"/>
                <circle cx="492" cy="228" r="1.5" fill="#22c55e" opacity="0.46"/>
                <line x1="440" y1="93"  x2="453" y2="93"  stroke="#22c55e" stroke-width="1" opacity="0.35"/>
                <line x1="440" y1="153" x2="453" y2="153" stroke="#22c55e" stroke-width="1" opacity="0.25"/>
            </svg>

            {{-- Content --}}
            <div class="nxd-welcome-inner">
                <div>
                    <p class="nxd-welcome-tag">Welcome Back</p>
                    <h1 class="nxd-welcome-name">{{ $userName }}</h1>
                    <p class="nxd-welcome-sub">Ringkasan sistem dan aktivitas NexaSpace.</p>
                </div>
                <div class="nxd-welcome-foot">
                    <div class="nxd-meta">
                        <div class="nxd-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nxd-meta-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span>{{ $dateStr }}</span>
                        </div>
                        <div class="nxd-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nxd-meta-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span
                                x-data="{
                                    t:'',
                                    init(){
                                        const f={hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false,timeZone:'Asia/Makassar'};
                                        const tick=()=>{this.t=new Intl.DateTimeFormat('id-ID',f).format(new Date());};
                                        tick(); setInterval(tick,1000);
                                    }
                                }"
                                x-text="t+' WITA'"
                            >{{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i:s') }} WITA</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit" class="nxd-signout">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- System Overview ── --}}
        <div class="nxd-card nxd-overview">
            <div class="nxd-ov-head">
                <div class="nxd-ov-dot"></div>
                <div>
                    <p class="nxd-ov-title">System Overview</p>
                    <p class="nxd-ov-sub">Platform health and key system indicators</p>
                </div>
            </div>
            <div class="nxd-ov-list">

                <div class="nxd-ov-item">
                    <div class="nxd-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxd-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        <span>System Status</span>
                    </div>
                    <div class="nxd-ov-val">
                        <span class="nxd-pulse-dot"></span>
                        <span class="nxd-green">Operational</span>
                    </div>
                </div>

                <div class="nxd-ov-item">
                    <div class="nxd-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxd-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 014.5-4.5h13.5a4.5 4.5 0 014.5 4.5" />
                        </svg>
                        <span>Server Uptime</span>
                    </div>
                    <span class="nxd-green nxd-bold">99.98%</span>
                </div>

                <div class="nxd-ov-item">
                    <div class="nxd-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxd-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.25v2.25m0 2.25v2.25" />
                        </svg>
                        <span>Database</span>
                    </div>
                    @if ($dbHealthy)
                        <span class="nxd-green">Healthy</span>
                    @else
                        <span class="nxd-red">Error</span>
                    @endif
                </div>

                <div class="nxd-ov-item">
                    <div class="nxd-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxd-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Backup Status</span>
                    </div>
                    @if ($failedJobs === 0)
                        <span class="nxd-green">Up to date</span>
                    @else
                        <span class="nxd-amber">{{ $failedJobs }} failed</span>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ROW 2 — Stat Cards  (4 × 2)
         ═══════════════════════════════════════════════════════ --}}
    <div class="nxd-stats">

        {{-- Total Juragan --}}
        <div class="nxd-stat">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
                <span class="nxd-stat-label">Total Juragan</span>
            </div>
            <p class="nxd-stat-val">{{ $totalJuragan }}</p>
            <p class="nxd-stat-desc">Akun terdaftar</p>
        </div>

        {{-- Total Anak Kos --}}
        <div class="nxd-stat">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <span class="nxd-stat-label">Total Anak Kos</span>
            </div>
            <p class="nxd-stat-val">{{ $totalAnakKos }}</p>
            <p class="nxd-stat-desc">Pengguna aktif</p>
        </div>

        {{-- Active Devices --}}
        <div class="nxd-stat">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/></svg>
                </div>
                <span class="nxd-stat-label">Active Devices</span>
            </div>
            <p class="nxd-stat-val">{{ $activeDevices }}</p>
            <p class="nxd-stat-desc">Perangkat online</p>
        </div>

        {{-- Unpaid Bills --}}
        <div class="nxd-stat {{ $unpaidBills > 0 ? 'nxd-stat-warn' : '' }}">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico {{ $unpaidBills > 0 ? 'nxd-stat-ico-warn' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <span class="nxd-stat-label">Unpaid Bills</span>
            </div>
            <p class="nxd-stat-val">{{ $unpaidBills }}</p>
            <p class="nxd-stat-desc">Belum dibayar</p>
        </div>

        {{-- Revenue This Month --}}
        <div class="nxd-stat">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
                <span class="nxd-stat-label">Revenue This Month</span>
            </div>
            <p class="nxd-stat-val nxd-stat-val-sm">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="nxd-stat-desc">Pendapatan bulan ini</p>
        </div>

        {{-- Pendaftaran Baru --}}
        <div class="nxd-stat {{ $pendaftaranBaru > 0 ? 'nxd-stat-info' : '' }}">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico {{ $pendaftaranBaru > 0 ? 'nxd-stat-ico-info' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                </div>
                <span class="nxd-stat-label">Pendaftaran Baru</span>
            </div>
            <p class="nxd-stat-val">{{ $pendaftaranBaru }}</p>
            <p class="nxd-stat-desc">Pending bulan ini</p>
        </div>

        {{-- Failed Jobs --}}
        <div class="nxd-stat {{ $failedJobs > 0 ? 'nxd-stat-danger' : '' }}">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico {{ $failedJobs > 0 ? 'nxd-stat-ico-danger' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="nxd-stat-label">Failed Jobs</span>
            </div>
            <p class="nxd-stat-val">{{ $failedJobs }}</p>
            <p class="nxd-stat-desc">Job gagal</p>
        </div>

        {{-- Subscription Aktif --}}
        <div class="nxd-stat {{ $subscriptionAktif === 0 ? 'nxd-stat-warn' : '' }}">
            <div class="nxd-stat-top">
                <div class="nxd-stat-ico {{ $subscriptionAktif === 0 ? 'nxd-stat-ico-warn' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                </div>
                <span class="nxd-stat-label">Subscription Aktif</span>
            </div>
            <p class="nxd-stat-val">{{ $subscriptionAktif }}</p>
            <p class="nxd-stat-desc">Juragan lunas bulan ini</p>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════
         ROW 3 — MikroTik + Revenue Chart
         ═══════════════════════════════════════════════════════ --}}
    <div class="nxd-bottom">

        {{-- MikroTik Status ── --}}
        <div class="nxd-card nxd-mk">
            <div class="nxd-mk-head">
                <svg xmlns="http://www.w3.org/2000/svg" class="nxd-mk-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>
                </svg>
                <p class="nxd-mk-title">Status Router MikroTik</p>
            </div>

            <div class="nxd-mk-body">
                {{-- Status badge --}}
                @if (! $mkConfigured)
                    <div class="nxd-mk-badge nxd-mk-badge-gray">
                        <span class="nxd-mk-dot nxd-mk-dot-gray"></span>
                        Belum Dikonfigurasi
                    </div>
                @elseif ($mkConnected)
                    <div class="nxd-mk-badge nxd-mk-badge-green">
                        <span class="nxd-mk-dot nxd-mk-dot-green"></span>
                        Terhubung
                    </div>
                    <p class="nxd-mk-hint">Router merespons dengan normal.</p>
                    <div class="nxd-mk-table">
                        <div class="nxd-mk-row"><span class="nxd-mk-key">Host</span><span class="nxd-mk-val">{{ $mkHost }}</span></div>
                        <div class="nxd-mk-row"><span class="nxd-mk-key">Port</span><span class="nxd-mk-val">{{ $mkPort }}</span></div>
                        <div class="nxd-mk-row"><span class="nxd-mk-key">User</span><span class="nxd-mk-val">{{ $mkUser }}</span></div>
                    </div>
                @else
                    <div class="nxd-mk-badge nxd-mk-badge-red">
                        <span class="nxd-mk-dot nxd-mk-dot-red"></span>
                        Tidak Terhubung
                    </div>
                    <p class="nxd-mk-hint nxd-mk-hint-red">
                        Router tidak dapat dihubungi. Periksa host, port, dan kredensial.
                    </p>
                @endif

                {{-- SVG Router Illustration --}}
                <div class="nxd-mk-router">
                    <svg width="140" height="110" viewBox="0 0 140 110" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        {{-- Antennas --}}
                        <line x1="40" y1="55" x2="27" y2="16" stroke="#22c55e" stroke-width="1.8" stroke-linecap="round" opacity="0.65"/>
                        <circle cx="27" cy="14" r="3" fill="#22c55e" opacity="0.65"/>
                        <line x1="70" y1="52" x2="70" y2="10" stroke="#22c55e" stroke-width="1.8" stroke-linecap="round" opacity="0.65"/>
                        <circle cx="70" cy="8"  r="3" fill="#22c55e" opacity="0.65"/>
                        <line x1="100" y1="55" x2="113" y2="16" stroke="#22c55e" stroke-width="1.8" stroke-linecap="round" opacity="0.65"/>
                        <circle cx="113" cy="14" r="3" fill="#22c55e" opacity="0.65"/>
                        {{-- Router body --}}
                        <rect x="18" y="55" width="104" height="36" rx="6" fill="rgba(34,197,94,0.06)" stroke="rgba(34,197,94,0.35)" stroke-width="1.4"/>
                        {{-- LED group --}}
                        @if ($mkConnected)
                            <circle cx="33" cy="73" r="3.5" fill="#22c55e" opacity="0.9"><animate attributeName="opacity" values="0.9;0.35;0.9" dur="2s" repeatCount="indefinite"/></circle>
                            <circle cx="44" cy="73" r="3.5" fill="#22c55e" opacity="0.7"><animate attributeName="opacity" values="0.7;0.2;0.7"  dur="2.3s" repeatCount="indefinite"/></circle>
                            <circle cx="55" cy="73" r="3.5" fill="#22c55e" opacity="0.5"><animate attributeName="opacity" values="0.5;0.15;0.5" dur="1.7s" repeatCount="indefinite"/></circle>
                        @elseif (! $mkConfigured)
                            <circle cx="33" cy="73" r="3.5" fill="#475569" opacity="0.55"/>
                            <circle cx="44" cy="73" r="3.5" fill="#475569" opacity="0.4"/>
                            <circle cx="55" cy="73" r="3.5" fill="#475569" opacity="0.3"/>
                        @else
                            <circle cx="33" cy="73" r="3.5" fill="#ef4444" opacity="0.8"/>
                            <circle cx="44" cy="73" r="3.5" fill="#ef4444" opacity="0.5"/>
                            <circle cx="55" cy="73" r="3.5" fill="#475569" opacity="0.3"/>
                        @endif
                        {{-- Port slots --}}
                        <rect x="88"  y="65" width="11" height="7" rx="1.5" fill="rgba(255,255,255,0.07)" stroke="rgba(255,255,255,0.14)" stroke-width="0.8"/>
                        <rect x="103" y="65" width="11" height="7" rx="1.5" fill="rgba(255,255,255,0.07)" stroke="rgba(255,255,255,0.14)" stroke-width="0.8"/>
                        {{-- Base --}}
                        <rect x="28" y="91" width="84" height="5" rx="2.5" fill="rgba(34,197,94,0.07)" stroke="rgba(34,197,94,0.18)" stroke-width="0.9"/>
                        {{-- WiFi arcs --}}
                        @if ($mkConnected)
                            <path d="M 52 40 Q 70 33 88 40" stroke="#22c55e" stroke-width="1.4" fill="none" opacity="0.4" stroke-linecap="round"/>
                            <path d="M 44 32 Q 70 22 96 32" stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.25" stroke-linecap="round"/>
                        @endif
                    </svg>
                </div>
            </div>
        </div>

        {{-- Revenue Chart ── --}}
        <div class="nxd-card nxd-chart">
            <div class="nxd-chart-head">
                <div>
                    <p class="nxd-chart-title">Pendapatan 6 Bulan Terakhir</p>
                    <p class="nxd-chart-sub">Revenue kumulatif dari tagihan anak kos</p>
                </div>
            </div>
            <div class="nxd-chart-body">
                <svg viewBox="0 0 580 200" xmlns="http://www.w3.org/2000/svg" aria-label="Revenue chart">
                    <defs>
                        <linearGradient id="nxAreaGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%"   stop-color="#22c55e" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    {{-- Horizontal grid lines + y-axis labels --}}
                    @foreach ($svgYLabels as $yl)
                        <line x1="{{ $svgCLeft }}" y1="{{ $yl['y'] }}"
                              x2="{{ $svgCRight }}" y2="{{ $yl['y'] }}"
                              stroke="rgba(255,255,255,0.05)" stroke-width="1" stroke-dasharray="4,4"/>
                        <text x="{{ $svgCLeft - 4 }}" y="{{ $yl['y'] + 4 }}"
                              text-anchor="end" font-size="9" fill="rgba(148,163,184,0.6)">{{ $yl['text'] }}</text>
                    @endforeach

                    {{-- Area fill --}}
                    <path d="{{ $svgArea }}" fill="url(#nxAreaGrad)"/>

                    {{-- Line --}}
                    <path d="{{ $svgLine }}" stroke="#22c55e" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>

                    {{-- Data points --}}
                    @foreach ($svgPts as $pt)
                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" fill="#22c55e" stroke="#07090D" stroke-width="1.5"/>
                    @endforeach

                    {{-- X-axis month labels --}}
                    @foreach ($chartLabels as $i => $label)
                        @php
                            $xPos = count($chartLabels) > 1
                                ? $svgCLeft + ($i / (count($chartLabels) - 1)) * ($svgCRight - $svgCLeft)
                                : $svgCLeft;
                        @endphp
                        <text x="{{ round($xPos, 1) }}" y="192"
                              text-anchor="middle" font-size="9.5" fill="rgba(148,163,184,0.7)">{{ $label }}</text>
                    @endforeach
                </svg>
            </div>
        </div>

    </div>
</div>
</x-filament-widgets::widget>
