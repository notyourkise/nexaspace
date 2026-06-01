{{-- Midtrans Snap payment pop-up, triggered automatically when this view renders --}}

@if ($snap_token)
    <div
        x-data="{ booted: false }"
        x-init="
            if (!document.getElementById('snap-js')) {
                const s = document.createElement('script');
                s.id  = 'snap-js';
                s.src = '{{ $snap_url }}';
                s.setAttribute('data-client-key', '{{ $client_key }}');
                document.head.appendChild(s);
                s.onload = () => { booted = true; triggerSnap(); };
            } else {
                booted = true;
                triggerSnap();
            }

            function triggerSnap() {
                window.snap.pay('{{ $snap_token }}', {
                    onSuccess:  (result) => { $wire.dispatch('payment-success'); },
                    onPending:  (result) => { $wire.dispatch('payment-pending'); },
                    onError:    (result) => { $wire.dispatch('payment-error');   },
                    onClose:    ()       => { $wire.dispatch('close-modal', { id: 'pay-now' }); },
                });
            }
        "
        class="flex flex-col items-center gap-4 py-4"
    >
        <div x-show="!booted" class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Loading payment gateway…
        </div>

        <div x-show="booted" class="text-center text-sm text-gray-500">
            A payment pop-up should appear. If it doesn't,
            <button
                type="button"
                class="text-primary-600 underline"
                x-on:click="triggerSnap()"
            >click here</button>.
        </div>
    </div>
@else
    <div class="rounded-lg bg-danger-50 p-4 text-sm text-danger-700">
        Could not reach the payment gateway. Please try again or contact support.
    </div>
@endif
