<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BilanHebdomadaireMail extends Mailable
{
    public function __construct(
        public User $user,
        public array $stats,
        public int $reportsCount,
        public Carbon $weekStart,
        public Carbon $weekEnd,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre bilan hebdomadaire de présence — TECHNOLOGY FOREVER GROUP',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.bilan_hebdomadaire');
    }

    public function attachments(): array
    {
        return [];
    }
}