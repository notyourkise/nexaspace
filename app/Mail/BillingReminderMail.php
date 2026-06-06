<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name: string, room_number: string, amount: int}>  $unpaidTenants
     * @param  string  $dueDate  Formatted due date string (e.g. "10 Juni 2026")
     */
    public function __construct(
        public readonly User $juragan,
        public readonly array $unpaidTenants,
        public readonly string $dueDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nexaspace.site', 'NexaSpace'),
            subject: "Pengingat: {$this->juragan->kos_name} — Tagihan Jatuh Tempo {$this->dueDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-reminder',
            with: [
                'juragan'       => $this->juragan,
                'unpaidTenants' => $this->unpaidTenants,
                'dueDate'       => $this->dueDate,
                'adminUrl'      => url('/admin/billings'),
            ],
        );
    }
}
