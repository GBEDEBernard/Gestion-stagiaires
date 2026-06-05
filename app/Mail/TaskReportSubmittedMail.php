<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TaskReportSubmittedMail extends Mailable
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
            subject: 'Rapport soumis : ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.tasks.report_submitted');
    }

    public function attachments(): array
    {
        return [];
    }
}