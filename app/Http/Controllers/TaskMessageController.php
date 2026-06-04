<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskMessageController extends Controller
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    /**
     * Poste un message dans le fil de discussion d'une tâche.
     * Autorisé : propriétaire + superviseur du stage + admin (via scopeVisibleTo).
     */
    public function store(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        TaskMessage::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'type'    => 'message',
            'body'    => $data['body'],
        ]);

        $this->notifyOtherParty($task, $user, $data['body']);

        return back()->with('success', 'Message envoyé.');
    }

    /**
     * Notifie « l'autre partie » : si l'auteur est un relecteur → le producteur ;
     * si l'auteur est le producteur → superviseur + admins.
     */
    protected function notifyOtherParty(Task $task, User $author, string $body): void
    {
        $url = encrypted_route('tasks.show', $task);
        $extract = Str::limit($body, 60);
        $isReviewer = $author->hasAnyRole(['admin', 'superviseur']) && $task->owner_id !== $author->id;

        if ($isReviewer) {
            if ($task->owner_id && (int) $task->owner_id !== (int) $author->id) {
                $this->notifications->push(
                    (int) $task->owner_id,
                    'task_message',
                    '💬 Réponse sur votre tâche',
                    $author->name . ' : ' . $extract,
                    $url,
                    'chat',
                    'indigo'
                );
            }
            return;
        }

        // L'auteur est le producteur → notifier superviseur + admins.
        $recipients = collect();

        if ($task->stage && $task->stage->supervisor_id) {
            $recipients->push($task->stage->supervisor_id);
        }

        User::role('admin')->pluck('id')->each(fn($id) => $recipients->push($id));

        $recipients->unique()
            ->reject(fn($id) => (int) $id === (int) $author->id)
            ->each(fn($id) => $this->notifications->push(
                (int) $id,
                'task_message',
                '💬 Nouveau message',
                $author->name . ' : ' . $extract,
                $url,
                'chat',
                'indigo'
            ));
    }
}
