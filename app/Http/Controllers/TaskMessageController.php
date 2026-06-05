<?php

namespace App\Http\Controllers;

use App\Events\TaskMessageCreated;
use App\Events\TaskMessageRead;
use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\TaskRead;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TaskThreadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskMessageController extends Controller
{
    public function __construct(
        protected NotificationService $notifications,
        protected TaskThreadService $thread
    ) {}

    /**
     * Fil complet de la tâche (JSON). Alimente le chat et le polling de secours.
     */
    public function index(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        return response()->json($this->thread->payload($task, $user));
    }

    /**
     * Poste un message dans le fil de discussion d'une tâche.
     * Autorisé : propriétaire + superviseur du stage + admin (via scopeVisibleTo),
     * uniquement lorsque la discussion est OUVERTE.
     */
    public function store(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        // La discussion n'existe qu'à partir du 1er rapport et tant que la tâche
        // n'est pas clôturée par l'admin.
        if ($task->discussionState() !== 'open') {
            return $this->fail($request, "La discussion n'est pas ouverte.", 422);
        }

        $data = $request->validate([
            'body'      => 'required|string|max:5000',
            'parent_id' => 'nullable|integer|exists:task_messages,id',
        ]);

        $parentId = $this->resolveParentId($task, $data['parent_id'] ?? null);

        $message = TaskMessage::create([
            'task_id'   => $task->id,
            'user_id'   => $user->id,
            'parent_id' => $parentId,
            'type'      => 'message',
            'body'      => $data['body'],
        ]);

        // L'auteur a « lu » son propre message.
        $this->touchRead($task, $user->id, $message->id);

        $this->notifyOtherParty($task, $user, $data['body']);

        broadcast(new TaskMessageCreated($message, $task))->toOthers();

        if ($this->wantsJson($request)) {
            $message->load(['user', 'parent.user', 'dailyReport', 'reactions']);

            return response()->json([
                'message'         => $this->thread->serializeMessage($message, $task, $user),
                'last_message_id' => $message->id,
            ], 201);
        }

        return back()->with('success', 'Message envoyé.');
    }

    /**
     * Met à jour le curseur de lecture (✓✓) du lecteur jusqu'au dernier message.
     */
    public function markRead(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        $upTo = $request->integer('up_to')
            ?: $task->messages()->max('id');

        if ($upTo) {
            $this->touchRead($task, $user->id, (int) $upTo);
            broadcast(new TaskMessageRead($task, $user->id, (int) $upTo, now()->toIso8601String()))->toOthers();
        }

        return response()->json(['ok' => true, 'last_read_message_id' => $upTo]);
    }

    /* =========================================================================
       HELPERS
    ========================================================================= */

    /** Le parent cité doit appartenir à la même tâche. */
    protected function resolveParentId(Task $task, $parentId): ?int
    {
        if (!$parentId) {
            return null;
        }

        $belongs = TaskMessage::whereKey($parentId)->where('task_id', $task->id)->exists();

        return $belongs ? (int) $parentId : null;
    }

    protected function touchRead(Task $task, int $userId, int $messageId): void
    {
        $read = TaskRead::firstOrNew([
            'task_id' => $task->id,
            'user_id' => $userId,
        ]);

        // Ne jamais reculer le curseur.
        if (!$read->last_read_message_id || $messageId > $read->last_read_message_id) {
            $read->last_read_message_id = $messageId;
        }
        $read->last_read_at = now();
        $read->save();
    }

    protected function wantsJson(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->boolean('ajax');
    }

    protected function fail(Request $request, string $message, int $status)
    {
        if ($this->wantsJson($request)) {
            return response()->json(['message' => $message], $status);
        }

        return back()->with('error', $message);
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
