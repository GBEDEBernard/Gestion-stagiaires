<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class TaskEmailUrlService
{
    /**
     * Génère une URL signée qui connecte automatiquement $recipient
     * et le redirige vers la tâche.
     */
    public function forRecipient(Task $task, User $recipient): string
    {
        $taskUrl = encrypted_route('tasks.show', $task);

        // URL signée valable 7 jours, avec uid du destinataire
        return URL::temporarySignedRoute(
            'email.access',
            now()->addDays(7),
            [
                'uid'      => $recipient->id,
                'redirect' => $taskUrl,
                'token'    => \Illuminate\Support\Str::random(8), // unicité
            ]
        );
    }
}