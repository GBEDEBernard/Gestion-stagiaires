<?php

namespace App\Http\Controllers;

use App\Events\TaskMessageCreated;
use App\Events\TaskMessageReactionAdded;
use App\Events\TaskMessageRead;
use App\Events\UserIsTyping;
use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\TaskMessageReaction;
use App\Models\TaskRead;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TaskThreadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskMessageController extends Controller
{
    /** Emojis autorisés pour les réactions (all-list, évite le stockage arbitraire). */
    public const ALLOWED_EMOJIS = ['👍', '❤️', '😂', '🎉', '🙏', '👏', '🔥', '✅', '👀', '😮'];

    /** Extensions interdites pour les pièces jointes « fichier ». */
    private const BLOCKED_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'exe', 'sh', 'bash',
        'bat', 'cmd', 'com', 'cgi', 'pl', 'jar', 'js', 'mjs', 'htaccess', 'htm', 'html',
    ];

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
     *
     * Supporte : texte, réponse citée (parent_id) et pièce jointe (vocal / image /
     * fichier) via le champ `attachment`. Le texte devient optionnel si une pièce
     * jointe est présente.
     */
    public function store(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        if ($task->discussionState() !== 'open') {
            return $this->fail($request, "La discussion n'est pas ouverte.", 422);
        }

        $hasFile = $request->hasFile('attachment');

        $data = $request->validate([
            'body'                => [$hasFile ? 'nullable' : 'required', 'string', 'max:5000'],
            'parent_id'           => ['nullable', 'integer', 'exists:task_messages,id'],
            'attachment'          => ['nullable', 'file', 'max:10240'], // 10 Mo
            'attachment_type'     => ['nullable', 'in:audio,image,file'],
            'attachment_duration' => ['nullable', 'integer', 'min:0', 'max:36000'],
        ]);

        // Au moins un contenu : texte OU pièce jointe.
        if (!$hasFile && trim((string) ($data['body'] ?? '')) === '') {
            return $this->fail($request, 'Le message est vide.', 422);
        }

        $parentId = $this->resolveParentId($task, $data['parent_id'] ?? null);

        $attachment = $hasFile
            ? $this->storeAttachment($request, $task, $request->file('attachment'), $data['attachment_type'] ?? null)
            : null;

        $message = TaskMessage::create([
            'task_id'             => $task->id,
            'user_id'             => $user->id,
            'parent_id'           => $parentId,
            'type'                => 'message',
            'body'                => $data['body'] ?? null,
            'attachment_type'     => $attachment['type'] ?? null,
            'attachment_path'     => $attachment['path'] ?? null,
            'attachment_name'     => $attachment['name'] ?? null,
            'attachment_mime'     => $attachment['mime'] ?? null,
            'attachment_size'     => $attachment['size'] ?? null,
            'attachment_duration' => $attachment && ($attachment['type'] === 'audio')
                ? ($data['attachment_duration'] ?? null)
                : null,
        ]);

        // L'auteur a « lu » son propre message.
        $this->touchRead($task, $user->id, $message->id);

        $this->notifyOtherParty($task, $user, $this->notificationExtract($message));

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
     * Édite le texte d'un message (auteur uniquement, discussion ouverte).
     */
    public function update(Request $request, Task $task, TaskMessage $message)
    {
        $user = auth()->user();

        abort_unless(Task::whereKey($task->getKey())->visibleTo($user)->exists(), 403);
        abort_unless((int) $message->task_id === (int) $task->id, 404);
        abort_unless($message->type === 'message', 422);
        abort_unless((int) $message->user_id === (int) $user->id, 403);

        if ($task->discussionState() !== 'open') {
            return $this->fail($request, "La discussion n'est pas ouverte.", 422);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message->update([
            'body'      => $data['body'],
            'edited_at' => now(),
        ]);

        broadcast(new TaskMessageCreated($message, $task))->toOthers();

        $message->load(['user', 'parent.user', 'dailyReport', 'reactions']);

        return response()->json([
            'message' => $this->thread->serializeMessage($message, $task, $user),
        ]);
    }

    /**
     * Supprime un message (auteur ou admin). La pièce jointe est purgée du disque.
     */
    public function destroy(Request $request, Task $task, TaskMessage $message)
    {
        $user = auth()->user();

        abort_unless(Task::whereKey($task->getKey())->visibleTo($user)->exists(), 403);
        abort_unless((int) $message->task_id === (int) $task->id, 404);
        abort_unless($message->type === 'message', 422);
        abort_unless((int) $message->user_id === (int) $user->id || $user->hasRole('admin'), 403);

        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $deletedId = $message->id;
        $message->reactions()->delete();
        $message->delete();

        broadcast(new TaskMessageCreated($message, $task))->toOthers();

        return response()->json(['ok' => true, 'deleted' => $deletedId]);
    }

    /**
     * Bascule une réaction emoji (ajoute si absente, retire si déjà posée).
     */
    public function react(Request $request, Task $task, TaskMessage $message)
    {
        $user = auth()->user();

        abort_unless(Task::whereKey($task->getKey())->visibleTo($user)->exists(), 403);
        abort_unless((int) $message->task_id === (int) $task->id, 404);

        $data = $request->validate([
            'emoji' => ['required', 'string', 'max:16'],
        ]);

        abort_unless(in_array($data['emoji'], self::ALLOWED_EMOJIS, true), 422);

        $attrs = [
            'task_message_id' => $message->id,
            'user_id'         => $user->id,
            'emoji'           => $data['emoji'],
        ];

        $existing = TaskMessageReaction::where($attrs)->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
            $reaction = new TaskMessageReaction($attrs); // transient, pour le broadcast
        } else {
            $reaction = TaskMessageReaction::create($attrs);
            $action = 'added';
        }

        broadcast(new TaskMessageReactionAdded($reaction, $task))->toOthers();

        return response()->json([
            'action'     => $action,
            'message_id' => $message->id,
            'reactions'  => $this->thread->reactionsFor($message->fresh(), $user),
        ]);
    }

    /**
     * Signale que l'utilisateur est en train d'écrire (éphémère, temps réel seulement).
     */
    public function typing(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless(Task::whereKey($task->getKey())->visibleTo($user)->exists(), 403);

        broadcast(new UserIsTyping($task, $user->id, $user->name))->toOthers();

        return response()->json(['ok' => true]);
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

    /**
     * Valide et stocke une pièce jointe sur le disque public.
     *
     * @return array{type:string,path:string,name:string,mime:string,size:int}
     */
    protected function storeAttachment(Request $request, Task $task, UploadedFile $file, ?string $declaredType): array
    {
        $mime = $file->getMimeType() ?: 'application/octet-stream';
        $type = $declaredType ?: $this->guessAttachmentType($mime);

        // Cohérence type déclaré / mime réel.
        // NB : un vocal MediaRecorder (.webm/.ogg, audio seul) est souvent détecté
        // comme « video/webm » / « video/ogg » par fileinfo : on tolère ces conteneurs.
        if ($type === 'audio') {
            $audioOk = Str::startsWith($mime, 'audio/')
                || in_array($mime, ['video/webm', 'video/ogg', 'application/ogg', 'application/octet-stream'], true);
            if (!$audioOk) {
                abort(422, 'Fichier audio invalide.');
            }
        }
        if ($type === 'image' && !Str::startsWith($mime, 'image/')) {
            abort(422, 'Image invalide.');
        }

        // Anti-exécutable pour les fichiers génériques.
        $ext = strtolower($file->getClientOriginalExtension());
        if (in_array($ext, self::BLOCKED_EXTENSIONS, true)) {
            abort(422, 'Type de fichier non autorisé.');
        }

        $path = $file->store("task-attachments/{$task->id}", 'public');

        return [
            'type' => $type,
            'path' => $path,
            'name' => $file->getClientOriginalName() ?: ('piece-jointe.' . ($ext ?: 'bin')),
            'mime' => $mime,
            'size' => (int) $file->getSize(),
        ];
    }

    protected function guessAttachmentType(string $mime): string
    {
        if (Str::startsWith($mime, 'audio/')) {
            return 'audio';
        }
        if (Str::startsWith($mime, 'image/')) {
            return 'image';
        }

        return 'file';
    }

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

    /** Texte court pour les notifications (gère les pièces jointes). */
    protected function notificationExtract(TaskMessage $message): string
    {
        if ($message->isVoice()) {
            return '🎤 Message vocal';
        }
        if ($message->isImage()) {
            return '🖼️ Photo';
        }
        if ($message->isFile()) {
            return '📎 ' . ($message->attachment_name ?? 'Fichier');
        }

        return Str::limit((string) $message->body, 60);
    }

    /**
     * Notifie « l'autre partie » : si l'auteur est un relecteur → le producteur ;
     * si l'auteur est le producteur → superviseur + admins.
     */
    protected function notifyOtherParty(Task $task, User $author, string $extract): void
    {
        $url = encrypted_route('tasks.show', $task);
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
