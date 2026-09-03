<?php

namespace App\Mail;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EmergencyCallMail extends Mailable
{
    public function __construct(
        public User $user,
        public Holiday $holiday,
        public string $customMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🚨 Intervention urgente - {$this->holiday->label}",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.emergency_call');
    }

    public function attachments(): array
    {
        return [];
    }
}
