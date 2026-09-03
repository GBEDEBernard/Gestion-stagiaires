<?php

namespace App\Services;

use App\Mail\TaskCreatedMail;
use App\Mail\TaskReviewedMail;
use App\Models\Task;
use App\Models\User;
use App\Support\NotificationGreeting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function __construct(
        protected TaskNotificationRecipients $recipients,
        protected TaskEmailUrlService $urlService
    ) {}

    public function notifyTaskCreated(Task $task): void
    {
        foreach ($this->recipients->getAllRecipientsForTask($task) as $recipient) {
            $email = $recipient->getEmailForVerification();

            if (!$this->isValidEmail($email)) {
                Log::warning("Email invalide pour l'envoi de tâche : {$email} (user #{$recipient->id})");
                continue;
            }

            try {
                $url = $this->urlService->forRecipient($task, $recipient);

                Mail::to($email)
                    ->send(new TaskCreatedMail(
                        $task,
                        $recipient->name,
                        NotificationGreeting::civilityForRecipient($recipient),
                        NotificationGreeting::greetingForNow(),
                        $url
                    ));
            } catch (\Throwable $e) {
                Log::error("Échec envoi email tâche à {$email}: " . $e->getMessage());
            }
        }
    }

    public function notifyTaskReviewed(Task $task, User $reviewer, string $action, ?string $comment): void
    {
        $owner = $task->owner;

        if ($owner && $owner->id !== $reviewer->id) {
            $email = $owner->getEmailForVerification();

            if (!$this->isValidEmail($email)) {
                Log::warning("Email invalide pour review : {$email} (user #{$owner->id})");
                return;
            }

            try {
                $url = $this->urlService->forRecipient($task, $owner);

                Mail::to($email)
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
            } catch (\Throwable $e) {
                Log::error("Échec envoi email review à {$email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Vérifie que l'email est syntaxiquement correct et que le domaine existe (MX).
     */
    protected function isValidEmail(string $email): bool
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, '@'), 1);

        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
    }
}
