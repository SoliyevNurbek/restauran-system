<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmailConnectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $restaurantName,
        public string $notificationEmail,
        public ?string $contactPhone = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->restaurantName.' - notification email ulandi'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification-email-connected'
        );
    }
}
