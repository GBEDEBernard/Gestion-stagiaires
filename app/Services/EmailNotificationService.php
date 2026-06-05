<?php

namespace App\Services;

use App\Mail\TaskCreatedMail;
use App\Mail\TaskNewMessageMail;
use App\Mail\TaskReportSubmittedMail;
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
                ->queue(new TaskCreatedMail(
                    $task,
                    $recipient->name,
                    NotificationGreeting::civilityForRecipient($recipient),
                    NotificationGreeting::greetingForNow(),
                    $url
                ));
        }
    }

    public function notifyReportSubmitted(Task $task): void
    {
        foreach ($this->recipients->getAllRecipientsForTask($task) as $recipient) {
            $url = $this->urlService->forRecipient($task, $recipient);

            Mail::to($recipient->getEmailForVerification())
                ->queue(new TaskReportSubmittedMail(
                    $task,
                    $recipient->name,
                    NotificationGreeting::civilityForRecipient($recipient),
                    NotificationGreeting::greetingForNow(),
                    $url
                ));
        }
    }
    
    public function notifyNewMessage(Task $task, User $sender, string $messageBody): void
    {
        if ($task->owner_id === $sender->id) {
            // Propriétaire écrit → notifier superviseur + admins
            foreach ($this->recipients->getAllRecipientsForTask($task) as $recipient) {
                $url = $this->urlService->forRecipient($task, $recipient);

                Mail::to($recipient->getEmailForVerification())
                    ->queue(new TaskNewMessageMail(
                        $task,
                        $sender,
                        $messageBody,
                        $recipient->name,
                        NotificationGreeting::civilityForRecipient($recipient),
                        NotificationGreeting::greetingForNow(),
                        $url
                    ));
            }

            return;
        }

        // Superviseur/admin écrit → notifier le propriétaire
        $owner = $task->owner;

        if ($owner) {
            $url = $this->urlService->forRecipient($task, $owner);

            Mail::to($owner->getEmailForVerification())
                ->queue(new TaskNewMessageMail(
                    $task,
                    $sender,
                    $messageBody,
                    $owner->name,
                    NotificationGreeting::civilityForRecipient($owner),
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
                ->queue(new TaskReviewedMail(
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
