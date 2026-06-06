<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $juragan,
        public readonly Subscription $subscription,
        public readonly string $dueDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nexaspace.site', 'NexaSpace'),
            subject: "Langganan NexaSpace Jatuh Tempo {$this->dueDate} — {$this->juragan->kos_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-reminder',
            with: [
                'juragan'      => $this->juragan,
                'subscription' => $this->subscription,
                'dueDate'      => $this->dueDate,
                'adminUrl'     => url('/admin/subscriptions'),
            ],
        );
    }
}
