<?php

use App\Models\DailyReport;
use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\TaskMessageReaction;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    Storage::fake('public');

    // Producteur (employé) — pas besoin de stage pour les rapports.
    $this->owner = User::factory()->create();
    $this->owner->assignRole('employe');
    $this->owner->givePermissionTo('tasks.view');

    // Tâche avec un rapport => discussion OUVERTE.
    $this->task = Task::create([
        'owner_id'              => $this->owner->id,
        'assigned_by'           => $this->owner->id,
        'title'                 => 'Tâche chat',
        'priority'              => 'normal',
        'status'                => 'in_progress',
        'last_progress_percent' => 30,
    ]);

    DailyReport::create([
        'user_id'               => $this->owner->id,
        'task_id'               => $this->task->id,
        'report_date'           => today(),
        'summary'               => 'init',
        'task_progress_percent' => 30,
        'status'                => 'submitted',
        'hours_declared'        => 0,
    ]);
});

/** Petit utilitaire local : crée un message humain dans la tâche. */
function chatMessage(User $author, Task $task, string $body = 'Bonjour'): TaskMessage
{
    return TaskMessage::create([
        'task_id' => $task->id,
        'user_id' => $author->id,
        'type'    => 'message',
        'body'    => $body,
    ]);
}

/* ====================== PHASE 4 — PIÈCES JOINTES / VOCAL ====================== */

it('accepte un message vocal et stocke le fichier audio', function () {
    $voice = UploadedFile::fake()->create('note.webm', 80, 'audio/webm');

    $this->actingAs($this->owner)
        ->post(route('tasks.messages.store', $this->task), [
            'attachment_type'     => 'audio',
            'attachment_duration' => 7,
            'attachment'          => $voice,
        ], ['Accept' => 'application/json'])
        ->assertCreated();

    $msg = TaskMessage::where('task_id', $this->task->id)->where('type', 'message')->latest('id')->first();

    expect($msg)->not->toBeNull()
        ->and($msg->attachment_type)->toBe('audio')
        ->and($msg->attachment_duration)->toBe(7)
        ->and($msg->body)->toBeNull();

    Storage::disk('public')->assertExists($msg->attachment_path);
});

it('accepte une image jointe', function () {
    // PNG 1×1 réel (évite la dépendance GD de UploadedFile::fake()->image()).
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    $tmp = tempnam(sys_get_temp_dir(), 'img') . '.png';
    file_put_contents($tmp, $png);
    $img = new UploadedFile($tmp, 'photo.png', 'image/png', null, true);

    $this->actingAs($this->owner)
        ->post(route('tasks.messages.store', $this->task), [
            'attachment_type' => 'image',
            'attachment'      => $img,
        ], ['Accept' => 'application/json'])
        ->assertCreated();

    $msg = TaskMessage::where('task_id', $this->task->id)->where('attachment_type', 'image')->first();
    expect($msg)->not->toBeNull();
    Storage::disk('public')->assertExists($msg->attachment_path);
});

it('refuse un fichier au format exécutable', function () {
    $evil = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');

    $this->actingAs($this->owner)
        ->post(route('tasks.messages.store', $this->task), [
            'attachment_type' => 'file',
            'attachment'      => $evil,
        ], ['Accept' => 'application/json'])
        ->assertStatus(422);

    expect(TaskMessage::where('task_id', $this->task->id)->where('type', 'message')->count())->toBe(0);
});

it('refuse un message sans texte ni pièce jointe', function () {
    $this->actingAs($this->owner)
        ->post(route('tasks.messages.store', $this->task), ['body' => ''], ['Accept' => 'application/json'])
        ->assertStatus(422);
});

it('refuse de poster quand la discussion est verrouillée', function () {
    $locked = Task::create([
        'owner_id'              => $this->owner->id,
        'assigned_by'           => $this->owner->id,
        'title'                 => 'Sans rapport',
        'priority'              => 'normal',
        'status'                => 'pending',
        'last_progress_percent' => 0,
    ]);

    $this->actingAs($this->owner)
        ->post(route('tasks.messages.store', $locked), ['body' => 'coucou'], ['Accept' => 'application/json'])
        ->assertStatus(422);
});

/* ====================== PHASE 5 — RÉACTIONS ====================== */

it('bascule une réaction emoji (ajout puis retrait)', function () {
    $msg = chatMessage($this->owner, $this->task);

    $this->actingAs($this->owner)
        ->postJson(route('tasks.messages.react', [$this->task, $msg]), ['emoji' => '👍'])
        ->assertOk()
        ->assertJsonPath('action', 'added');

    expect(TaskMessageReaction::where('task_message_id', $msg->id)->count())->toBe(1);

    $this->actingAs($this->owner)
        ->postJson(route('tasks.messages.react', [$this->task, $msg]), ['emoji' => '👍'])
        ->assertOk()
        ->assertJsonPath('action', 'removed');

    expect(TaskMessageReaction::where('task_message_id', $msg->id)->count())->toBe(0);
});

it('refuse un emoji hors liste blanche', function () {
    $msg = chatMessage($this->owner, $this->task);

    $this->actingAs($this->owner)
        ->postJson(route('tasks.messages.react', [$this->task, $msg]), ['emoji' => '🤬'])
        ->assertStatus(422);

    expect(TaskMessageReaction::count())->toBe(0);
});

/* ====================== PHASE 5 — ÉDITION / SUPPRESSION ====================== */

it('permet à l\'auteur d\'éditer son message et marque edited_at', function () {
    $msg = chatMessage($this->owner, $this->task, 'version 1');

    $this->actingAs($this->owner)
        ->patchJson(route('tasks.messages.update', [$this->task, $msg]), ['body' => 'version 2'])
        ->assertOk();

    $fresh = $msg->fresh();
    expect($fresh->body)->toBe('version 2')
        ->and($fresh->edited_at)->not->toBeNull();
});

it('empêche un non-auteur (même admin) d\'éditer le message', function () {
    $msg = chatMessage($this->owner, $this->task, 'à moi');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $admin->givePermissionTo('tasks.view');

    $this->actingAs($admin)
        ->patchJson(route('tasks.messages.update', [$this->task, $msg]), ['body' => 'piraté'])
        ->assertStatus(403);

    expect($msg->fresh()->body)->toBe('à moi');
});

it('autorise un admin à supprimer le message d\'un autre', function () {
    $msg = chatMessage($this->owner, $this->task, 'à supprimer');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $admin->givePermissionTo('tasks.view');

    $this->actingAs($admin)
        ->deleteJson(route('tasks.messages.destroy', [$this->task, $msg]))
        ->assertOk();

    expect(TaskMessage::find($msg->id))->toBeNull();
});

/* ====================== PHASE 5 — SAISIE (TYPING) ====================== */

it('expose l\'endpoint de saisie sans erreur', function () {
    $this->actingAs($this->owner)
        ->postJson(route('tasks.typing', $this->task))
        ->assertOk()
        ->assertJsonPath('ok', true);
});

/* ====================== PHASE 4 — RAPPORT VOCAL ====================== */

it('accepte un rapport uniquement vocal (sans résumé écrit)', function () {
    $task2 = Task::create([
        'owner_id'              => $this->owner->id,
        'assigned_by'           => $this->owner->id,
        'title'                 => 'Tâche vocale',
        'priority'              => 'normal',
        'status'                => 'pending',
        'last_progress_percent' => 0,
    ]);

    $voice = UploadedFile::fake()->create('rapport.webm', 90, 'audio/webm');

    $this->actingAs($this->owner)
        ->post(route('reports.store'), [
            'status_action'         => 'submit',
            'task_id'               => $task2->id,
            'task_progress_percent' => 20,
            'voice'                 => $voice,
            'voice_duration'        => 12,
        ], ['Accept' => 'application/json'])
        ->assertRedirect();

    $report = DailyReport::where('task_id', $task2->id)->whereDate('report_date', today())->first();

    expect($report)->not->toBeNull()
        ->and($report->summary)->toBeNull()
        ->and($report->voice_path)->not->toBeNull()
        ->and($report->voice_duration)->toBe(12);

    Storage::disk('public')->assertExists($report->voice_path);
});

it('refuse un rapport sans résumé ni vocal', function () {
    $task3 = Task::create([
        'owner_id'              => $this->owner->id,
        'assigned_by'           => $this->owner->id,
        'title'                 => 'Tâche vide',
        'priority'              => 'normal',
        'status'                => 'pending',
        'last_progress_percent' => 0,
    ]);

    $this->actingAs($this->owner)
        ->post(route('reports.store'), [
            'status_action'         => 'submit',
            'task_id'               => $task3->id,
            'task_progress_percent' => 10,
        ], ['Accept' => 'application/json'])
        ->assertStatus(422);
});
