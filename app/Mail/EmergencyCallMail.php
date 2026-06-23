<?php

namespace App\Mail;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmergencyCallMail extends Mailable
{
    use Queueable, SerializesModels;

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
