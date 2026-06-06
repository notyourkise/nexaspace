<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  int  $billCount  Number of bills generated for this juragan's kos
     * @param  string  $billingMonth  Human-readable month (e.g. "Juni 2026")
     * @param  int  $totalAmount  Sum of all bills in IDR
     */
    public function __construct(
        public readonly User $juragan,
        public readonly int $billCount,
        public readonly string $billingMonth,
        public readonly int $totalAmount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nexaspace.site', 'NexaSpace'),
            subject: "Tagihan Bulan {$this->billingMonth} Sudah Dibuat — {$this->juragan->kos_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-created',
            with: [
                'juragan'      => $this->juragan,
                'billCount'    => $this->billCount,
                'billingMonth' => $this->billingMonth,
                'totalAmount'  => $this->totalAmount,
                'adminUrl'     => url('/admin/billings'),
            ],
        );
    }
}
