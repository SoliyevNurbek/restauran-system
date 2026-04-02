<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SystemAlertsDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $restaurantName,
        public Collection $upcomingBookings,
        public Collection $lowStockProducts,
        public int $days,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->restaurantName.' - tizim eslatmalari'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.system-alerts-digest'
        );
    }
}
