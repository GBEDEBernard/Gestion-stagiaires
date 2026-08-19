<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RappelDepartMail extends Mailable
{
    public function __construct(
        public User $user,
        public string $arrivalTime,
        public int $lateMinutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel : Vous n\'avez pas pointé votre départ',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.rappel_depart');
    }

    public function attachments(): array
    {
        return [];
    }
}
