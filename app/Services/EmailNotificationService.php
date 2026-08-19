<?php

namespace App\Services;

use App\Mail\TaskCreatedMail;
use App\Mail\TaskReviewedMail;
use App\Models\Task;
use App\Models\User;
use App\Support\NotificationGreeting;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    public function __construct(
        protected TaskNotificationRecipients $recipients,
        protected TaskEmailUrlService $urlService // ← injecter
    ) {}

    public function notifyTaskCreated(Task $task): void
    {
        foreach ($this->recipients->getAllRecipientsForTask($task) as $recipient) {
            $url = $this->urlService->forRecipient($task, $recipient);

            Mail::to($recipient->getEmailForVerification())
                ->send(new TaskCreatedMail(
                    $task,
                    $recipient->name,
                    NotificationGreeting::civilityForRecipient($recipient),
                    NotificationGreeting::greetingForNow(),
                    $url
                ));
        }
    }

    public function notifyTaskReviewed(Task $task, User $reviewer, string $action, ?string $comment): void
    {
        $owner = $task->owner;

        if ($owner && $owner->id !== $reviewer->id) {
            $url = $this->urlService->forRecipient($task, $owner);

            Mail::to($owner->getEmailForVerification())
                ->send(new TaskReviewedMail(
                    $task,
                    $reviewer,
                    $action,
                    $comment,
                    $owner->name,
                    NotificationGreeting::civilityForRecipient($owner),
                    NotificationGreeting::greetingForNow(),
                    $url
                ));
        }
    }
}
