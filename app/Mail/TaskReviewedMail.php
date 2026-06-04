<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TaskReviewedMail extends Mailable
{
    use Queueable, SerializesModels;

   
public function __construct(
        public Task $task,
        public User $reviewer,
        public string $action,
        public ?string $comment,
        public string $recipientName,
        public string $taskUrl        // ← reçu, pas recalculé
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: match ($this->action) {
                'approve' => 'Tâche validée : ' . $this->task->title,
                'reject'  => 'Corrections demandées : ' . $this->task->title,
                default   => 'Mise à jour de la tâche : ' . $this->task->title,
            }
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.tasks.reviewed');
    }

    public function attachments(): array { return []; }
}