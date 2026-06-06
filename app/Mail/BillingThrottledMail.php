<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingThrottledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name: string, room_number: string}>  $throttledTenants
     */
    public function __construct(
        public readonly User $juragan,
        public readonly array $throttledTenants,
    ) {}

    public function envelope(): Envelope
    {
        $count = count($this->throttledTenants);

        return new Envelope(
            from: new Address('noreply@nexaspace.site', 'NexaSpace'),
            subject: "{$count} Anak Kos di-Throttle — {$this->juragan->kos_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-throttled',
            with: [
                'juragan'          => $this->juragan,
                'throttledTenants' => $this->throttledTenants,
                'adminUrl'         => url('/admin/billings'),
            ],
        );
    }
}
