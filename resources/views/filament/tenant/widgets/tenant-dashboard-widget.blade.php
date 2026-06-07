<x-filament-widgets::widget class="nxt-widget">
<style>
    .nxt-root {
        display: flex;
        flex-direction: column;
        gap: 1.45rem;
        width: min(100%, 80rem);
        margin: 0 auto;
    }

    .nxt-card {
        background:
            linear-gradient(135deg, rgba(255,255,255,0.055), rgba(255,255,255,0.02)),
            rgba(15,18,26,0.84);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 0.85rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.055), 0 18px 48px rgba(0,0,0,0.26);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        overflow: hidden;
    }

    /* ── ROW 1: Hero + Info ── */
    .nxt-hero-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.45rem;
    }
    @media (min-width: 1080px) {
        .nxt-hero-row { grid-template-columns: minmax(0, 2.05fr) minmax(20rem, 1fr); }
    }

    .nxt-welcome {
        position: relative;
        min-height: 16.5rem;
        background:
            linear-gradient(105deg, rgba(6,8,12,0.98) 0%, rgba(15,22,20,0.9) 42%, rgba(11,20,13,0.74) 100%),
            radial-gradient(circle at 69% 43%, rgba(124,255,71,0.24), transparent 10rem);
        border-color: rgba(255,255,255,0.16);
    }
    .nxt-glow {
        position: absolute;
        right: 12%; bottom: -7rem;
        width: 30rem; height: 18rem;
        background: radial-gradient(ellipse, rgba(124,255,71,0.16), transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
    }
    .nxt-art {
        position: absolute;
        top: 0; right: 0;
        width: 60%; height: 100%;
        pointer-events: none;
        opacity: 0.9;
        z-index: 0;
    }
    .nxt-welcome-inner {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 16.5rem;
        padding: 2.5rem 2.4rem 1.85rem;
        gap: 1.5rem;
    }
    .nxt-welcome-tag {
        color: #7cff47;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin: 0 0 0.9rem;
    }
    .nxt-welcome-name {
        color: #fff;
        font-size: clamp(2rem, 3.6vw, 3rem);
        font-weight: 800;
        line-height: 1.05;
        margin: 0 0 0.6rem;
        text-shadow: 0 8px 32px rgba(0,0,0,0.35);
    }
    .nxt-welcome-sub {
        color: rgba(228,232,240,0.78);
        font-size: 0.98rem;
        margin: 0;
    }
    .nxt-welcome-foot {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .nxt-meta {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1.25rem;
    }
    .nxt-meta-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(226,232,240,0.78);
        font-size: 0.94rem;
    }
    .nxt-meta-row + .nxt-meta-row {
        padding-left: 1.25rem;
        border-left: 1px solid rgba(255,255,255,0.15);
    }
    .nxt-meta-ico { width: 1.15rem; height: 1.15rem; flex-shrink: 0; color: rgba(226,232,240,0.76); }
    .nxt-signout {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        min-height: 2.75rem;
        padding: 0 1.1rem;
        border-radius: 0.55rem;
        border: 1px solid rgba(255,255,255,0.16);
        background: rgba(255,255,255,0.06);
        color: #fff;
        font-size: 0.96rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.14s ease;
        white-space: nowrap;
        font-family: inherit;
    }
    .nxt-signout:hover { background: rgba(124,255,71,0.12); border-color: rgba(124,255,71,0.38); }
    .nxt-signout svg { width: 0.95rem; height: 0.95rem; color: #7cff47; }

    /* Info Kamar overview */
    .nxt-overview { display: flex; flex-direction: column; }
    .nxt-ov-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1.4rem 1.55rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .nxt-ov-head-ico {
        display: grid;
        place-items: center;
        width: 2.15rem; height: 2.15rem;
        border-radius: 0.55rem;
        color: #7cff47;
        background: rgba(34,197,94,0.10);
        box-shadow: inset 0 0 0 1px rgba(124,255,71,0.22);
        flex: 0 0 auto;
    }
    .nxt-ov-head-ico svg { width: 1.15rem; height: 1.15rem; }
    .nxt-ov-title { color: #fff; font-size: 1.02rem; font-weight: 800; margin: 0; line-height: 1.2; }
    .nxt-ov-sub   { color: rgba(226,232,240,0.6); font-size: 0.82rem; margin: 0.2rem 0 0; }
    .nxt-ov-list { display: flex; flex-direction: column; padding: 0.5rem 1.55rem 1.1rem; flex: 1; }
    .nxt-ov-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 3.1rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .nxt-ov-item:last-child { border-bottom: 0; }
    .nxt-ov-label { display: flex; align-items: center; gap: 0.55rem; color: rgba(226,232,240,0.78); font-size: 0.9rem; }
    .nxt-ov-ico { width: 1.15rem; height: 1.15rem; color: rgba(226,232,240,0.65); flex-shrink: 0; }
    .nxt-ov-val { color: #fff; font-size: 0.9rem; font-weight: 700; text-align: right; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .nxt-ov-val.mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.82rem; }
    .nxt-pill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.25rem 0.65rem; border-radius: 999px;
        font-size: 0.8rem; font-weight: 700;
    }
    .nxt-pill-green { color: #7cff47; background: rgba(34,197,94,0.12); border: 1px solid rgba(124,255,71,0.24); }
    .nxt-pill-red   { color: #f87171; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.24); }
    .nxt-pill-dot { width: 0.45rem; height: 0.45rem; border-radius: 999px; background: currentColor; box-shadow: 0 0 8px currentColor; }

    /* ── ROW 2: Stat cards ── */
    .nxt-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.35rem;
    }
    @media (min-width: 992px) { .nxt-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

    .nxt-stat {
        background:
            linear-gradient(135deg, rgba(255,255,255,0.055), rgba(255,255,255,0.02)),
            rgba(15,18,26,0.84);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 0.8rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.055), 0 18px 48px rgba(0,0,0,0.26);
        backdrop-filter: blur(18px);
        min-height: 8.5rem;
        padding: 1.35rem 1.45rem;
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        grid-template-rows: auto auto auto;
        column-gap: 1.15rem;
        align-content: center;
        transition: border-color 0.15s;
    }
    .nxt-stat:hover { border-color: rgba(124,255,71,0.3); }
    .nxt-stat-warn   { border-color: rgba(234,179,8,0.3); }
    .nxt-stat-green  { border-color: rgba(34,197,94,0.3); }

    .nxt-stat-ico {
        grid-row: 1 / 4;
        display: flex; align-items: center; justify-content: center;
        width: 3.5rem; height: 3.5rem;
        border-radius: 0.65rem;
        background: linear-gradient(135deg, rgba(255,255,255,0.07), rgba(255,255,255,0.025));
        border: 1px solid rgba(255,255,255,0.09);
        flex-shrink: 0;
    }
    .nxt-stat-ico svg { width: 1.65rem; height: 1.65rem; color: #7cff47; stroke-width: 1.75; }
    .nxt-stat-ico-warn  { background: rgba(234,179,8,0.1); }
    .nxt-stat-ico-warn svg { color: #eab308; }
    .nxt-stat-label { color: rgba(226,232,240,0.72); font-size: 0.88rem; font-weight: 500; line-height: 1.3; }
    .nxt-stat-val   { color: #fff; font-size: 1.9rem; font-weight: 800; line-height: 1.05; margin: 0.42rem 0 0; letter-spacing: -0.02em; }
    .nxt-stat-val-sm { font-size: 1.4rem; }
    .nxt-stat-desc  { color: rgba(226,232,240,0.6); font-size: 0.82rem; margin: 0.24rem 0 0; }

    @media (max-width: 880px) {
        .nxt-welcome-foot { align-items: flex-start; flex-direction: column; }
    }
</style>

<div class="nxt-root">

    {{-- ═══ ROW 1 — Hero + Info Kamar ═══ --}}
    <div class="nxt-hero-row">

        {{-- Welcome card --}}
        <div class="nxt-card nxt-welcome">
            <div class="nxt-glow"></div>
            <svg class="nxt-art" viewBox="0 0 800 280" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <filter id="nxtGlow"><feGaussianBlur stdDeviation="2.5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                </defs>
                <path d="M800 45 C680 45 580 125 440 96 C300 67 180 150 0 118"  stroke="#22c55e" stroke-width="1.5" fill="none" opacity="0.42"/>
                <path d="M800 105 C680 105 580 185 440 156 C300 127 180 210 0 178" stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.32"/>
                <path d="M800 165 C680 165 580 245 440 216 C300 187 180 268 0 238" stroke="#22c55e" stroke-width="1"   fill="none" opacity="0.2"/>
                <circle cx="660" cy="140" r="94" stroke="#22c55e" stroke-width="0.8" fill="none" opacity="0.1"/>
                <circle cx="660" cy="140" r="66" stroke="#22c55e" stroke-width="1"   fill="none" opacity="0.16"/>
                <circle cx="660" cy="140" r="41" stroke="#22c55e" stroke-width="0.8" fill="rgba(34,197,94,0.04)" opacity="0.26" filter="url(#nxtGlow)"/>
                <circle cx="660" cy="140" r="11" fill="rgba(34,197,94,0.18)" opacity="0.55"/>
            </svg>

            <div class="nxt-welcome-inner">
                <div>
                    <p class="nxt-welcome-tag">Selamat Datang</p>
                    <h1 class="nxt-welcome-name">{{ $userName }}</h1>
                    <p class="nxt-welcome-sub">
                        @if ($kosName) {{ $kosName }} @else Portal Anak Kos NexaSpace @endif — kelola tagihan & pembayaran Anda.
                    </p>
                </div>
                <div class="nxt-welcome-foot">
                    <div class="nxt-meta">
                        <div class="nxt-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nxt-meta-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span>{{ $dateStr }}</span>
                        </div>
                        <div class="nxt-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nxt-meta-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                    <form method="POST" action="{{ route('filament.tenant.auth.logout') }}">
                        @csrf
                        <button type="submit" class="nxt-signout">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Kamar overview --}}
        <div class="nxt-card nxt-overview">
            <div class="nxt-ov-head">
                <div class="nxt-ov-head-ico">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                </div>
                <div>
                    <p class="nxt-ov-title">Informasi Kamar</p>
                    <p class="nxt-ov-sub">Detail akun & status koneksi Anda</p>
                </div>
            </div>
            <div class="nxt-ov-list">
                <div class="nxt-ov-item">
                    <div class="nxt-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxt-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 9.41a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <span>Email Login</span>
                    </div>
                    <span class="nxt-ov-val mono">{{ $email }}</span>
                </div>
                <div class="nxt-ov-item">
                    <div class="nxt-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxt-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" /></svg>
                        <span>Nomor Kamar</span>
                    </div>
                    <span class="nxt-ov-val">{{ $roomNumber ? 'Kamar ' . $roomNumber : '—' }}</span>
                </div>
                <div class="nxt-ov-item">
                    <div class="nxt-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxt-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                        <span>Nama Kos</span>
                    </div>
                    <span class="nxt-ov-val">{{ $kosName ?? '—' }}</span>
                </div>
                <div class="nxt-ov-item">
                    <div class="nxt-ov-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="nxt-ov-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" /></svg>
                        <span>Status Koneksi</span>
                    </div>
                    @if ($isThrottled)
                        <span class="nxt-pill nxt-pill-red"><span class="nxt-pill-dot"></span>Dibatasi</span>
                    @else
                        <span class="nxt-pill nxt-pill-green"><span class="nxt-pill-dot"></span>Normal</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ROW 2 — Stat Cards ═══ --}}
    <div class="nxt-stats">

        {{-- Total Tagihan Belum Dibayar --}}
        <div class="nxt-stat {{ $unpaidTotal > 0 ? 'nxt-stat-warn' : '' }}">
            <div class="nxt-stat-ico {{ $unpaidTotal > 0 ? 'nxt-stat-ico-warn' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <span class="nxt-stat-label">Total Tagihan Belum Dibayar</span>
            <p class="nxt-stat-val nxt-stat-val-sm">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</p>
            <p class="nxt-stat-desc">{{ $unpaidCount }} tagihan tertunggak</p>
        </div>

        {{-- Tagihan Belum Lunas --}}
        <div class="nxt-stat">
            <div class="nxt-stat-ico">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            </div>
            <span class="nxt-stat-label">Tagihan Belum Lunas</span>
            <p class="nxt-stat-val">{{ $unpaidCount }}</p>
            <p class="nxt-stat-desc">Perlu dibayar</p>
        </div>

        {{-- Tagihan Lunas --}}
        <div class="nxt-stat nxt-stat-green">
            <div class="nxt-stat-ico">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="nxt-stat-label">Tagihan Lunas</span>
            <p class="nxt-stat-val">{{ $paidCount }}</p>
            <p class="nxt-stat-desc">Sepanjang waktu</p>
        </div>

        {{-- Perangkat --}}
        <div class="nxt-stat">
            <div class="nxt-stat-ico">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/></svg>
            </div>
            <span class="nxt-stat-label">Perangkat Terdaftar</span>
            <p class="nxt-stat-val">{{ $deviceTotal }}</p>
            <p class="nxt-stat-desc">{{ $deviceActive }} aktif · {{ $deviceThrottled }} dibatasi</p>
        </div>
    </div>


</div>
</x-filament-widgets::widget>
