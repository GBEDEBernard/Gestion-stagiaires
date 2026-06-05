<?php


namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TaskNewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public User $sender,
        public string $messageBody,
        public string $recipientName,
        public string $recipientCivilite,
        public string $greeting,
        public string $taskUrl        // ← reçu, pas recalculé
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message sur la tâche : ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.tasks.new_message');
    }

    public function attachments(): array
    {
        return [];
    }
}
