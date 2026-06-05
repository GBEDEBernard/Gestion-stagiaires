<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TaskCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public string $recipientName,
        public string $recipientCivilite,
        public string $greeting,
        public string $taskUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle tâche : ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.tasks.created');
    }

    public function attachments(): array
    {
        return [];
    }
}