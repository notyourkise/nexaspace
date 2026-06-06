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

class JuraganSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $juragan,
        public readonly Subscription $subscription,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nexaspace.site', 'NexaSpace'),
            subject: "Akun {$this->juragan->kos_name} Ditangguhkan — Tagihan Belum Dibayar",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.juragan-suspended',
            with: [
                'juragan'      => $this->juragan,
                'subscription' => $this->subscription,
                'adminUrl'     => url('/admin/login'),
            ],
        );
    }
}
