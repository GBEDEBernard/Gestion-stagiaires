<?php

namespace App\Mail;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class HolidayPublishedMail extends Mailable
{
    public function __construct(
        public User $user,
        public Holiday $holiday,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📅 Jour férié : {$this->holiday->label}",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.holiday_published');
    }

    public function attachments(): array
    {
        return [];
    }
}
