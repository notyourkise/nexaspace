
<div style="display:flex;min-height:100vh;width:100%;background:#ffffff;">

    {{-- LEFT: Login form --}}
    <div style="flex:0 0 50%;display:flex;align-items:center;justify-content:center;padding:2.5rem;overflow-y:auto;background:#ffffff;">
        <div style="width:100%;max-width:22rem;">

            <div style="text-align:center;margin-bottom:2.5rem;">
                <img src="{{ asset('images/logo-nexa.png') }}"
                     alt="NexaSpace"
                     style="height:4rem;width:auto;margin:0 auto 1rem;display:block;object-fit:contain;">
                <div style="font-size:1.5rem;font-weight:700;color:#111827;letter-spacing:-0.025em;">NexaSpace</div>
                <div style="font-size:0.875rem;color:#6b7280;margin-top:0.25rem;">Admin Panel</div>
            </div>

            {{ $this->content }}

            <x-filament-actions::modals />
        </div>
    </div>

    {{-- RIGHT: Gradient panel --}}
    <div style="flex:0 0 50%;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;padding:4rem;background:linear-gradient(135deg,#306D29 0%,#0D530E 60%,#1a3d18 100%);">

        <div style="position:absolute;top:-8rem;right:-8rem;height:24rem;width:24rem;border-radius:9999px;background:#FBF5DD;opacity:0.08;"></div>
        <div style="position:absolute;bottom:-5rem;left:-5rem;height:18rem;width:18rem;border-radius:9999px;background:#E7E1B1;opacity:0.08;"></div>

        <div style="position:relative;z-index:10;max-width:28rem;text-align:center;">

            <div style="margin:0 auto 2rem;display:flex;height:6rem;width:6rem;align-items:center;justify-content:center;border-radius:1.5rem;background:rgba(251,245,221,0.15);">
                <svg style="height:3.5rem;width:3.5rem;opacity:0.9;" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="1.4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H15v-6H9v6H3.75A.75.75 0 013 21V9.75z"/>
                </svg>
            </div>

            <div style="font-size:1.875rem;font-weight:700;color:#ffffff;">Smart Boarding House</div>
            <div style="margin-top:0.75rem;font-size:1.125rem;color:#E7E1B1;">Auto-Throttle WiFi Billing System</div>

            <div style="margin-top:2.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:left;">
                @foreach([
                    ['icon'=>'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z','text'=>'Automated throttling'],
                    ['icon'=>'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z','text'=>'Real-time MikroTik sync'],
                    ['icon'=>'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z','text'=>'Midtrans payments'],
                    ['icon'=>'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z','text'=>'Tenant self-service'],
                ] as $f)
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;border-radius:0.75rem;padding:0.75rem;background:rgba(251,245,221,0.08);">
                        <svg style="margin-top:0.125rem;height:1.25rem;width:1.25rem;flex-shrink:0;opacity:0.8;" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                        </svg>
                        <span style="font-size:0.875rem;font-weight:500;color:#ffffff;opacity:0.9;">{{ $f['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
