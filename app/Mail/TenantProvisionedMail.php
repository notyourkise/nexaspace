<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantProvisionedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{email: string, password: string}>  $anakKos
     */
    public function __construct(
        public readonly User $juragan,
        public readonly string $juraganPassword,
        public readonly array $anakKos,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nexaspace.site', 'NexaSpace'),
            subject: 'Akun NexaSpace Anda Telah Aktif — ' . $this->juragan->kos_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-provisioned',
            with: [
                'juragan'         => $this->juragan,
                'juraganPassword' => $this->juraganPassword,
                'anakKos'         => $this->anakKos,
                'adminLoginUrl'   => url('/admin/login'),
                'tenantLoginUrl'  => url('/tenant/login'),
            ],
        );
    }
}
