<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DepartAutomatiqueMail extends Mailable
{
    public function __construct(
        public User $user,
        public string $arrivalTime,
        public string $departureTime,
        public string $workedHours,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre départ a été enregistré automatiquement',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.depart_automatique');
    }

    public function attachments(): array
    {
        return [];
    }
}
