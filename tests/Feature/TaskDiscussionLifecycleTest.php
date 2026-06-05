<?php

use App\Models\DailyReport;
use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\User;
use App\Services\DailyReportService;
use App\Services\TaskThreadService;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function makeTask(User $owner, array $attrs = []): Task
{
    return Task::create(array_merge([
        'owner_id'              => $owner->id,
        'assigned_by'           => $owner->id,
        'title'                 => 'Tâche de test',
        'priority'              => 'normal',
        'status'                => 'pending',
        'last_progress_percent' => 0,
    ], $attrs));
}

function makeReport(User $owner, Task $task, int $progress): DailyReport
{
    return DailyReport::create([
        'user_id'               => $owner->id,
        'task_id'               => $task->id,
        'report_date'           => today(),
        'summary'               => 'Avancement du jour',
        'task_progress_percent' => $progress,
        'status'                => 'submitted',
        'hours_declared'        => 0,
    ]);
}

it('garde la discussion verrouillée tant qu\'aucun rapport n\'existe', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner);

    expect($task->discussionState())->toBe('locked');
});

it('ouvre la discussion au premier rapport et épingle ce rapport', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner);
    $report = makeReport($owner, $task, 40);

    app(DailyReportService::class)->syncTaskProgress($report, $task, $owner, false);
    $task->refresh();

    expect($task->status)->toBe('in_progress')
        ->and($task->last_progress_percent)->toBe(40)
        ->and($task->discussionState())->toBe('open')
        ->and(TaskMessage::where('task_id', $task->id)->where('type', 'report_jalon')->exists())->toBeTrue();

    $payload = app(TaskThreadService::class)->payload($task, $owner);
    expect($payload['pinned_report'])->not->toBeNull()
        ->and($payload['pinned_report']['progress'])->toBe(40)
        ->and($payload['pinned_report']['author']['initials'])->not->toBe('');
});

it('passe « en attente de validation » à 100 % sans auto-clôturer', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner);
    $report = makeReport($owner, $task, 100);

    app(DailyReportService::class)->syncTaskProgress($report, $task, $owner, false);
    $task->refresh();

    expect($task->status)->toBe('awaiting_validation')
        ->and($task->isCompleted())->toBeFalse()
        ->and($task->completed_at)->toBeNull()
        ->and(TaskMessage::where('task_id', $task->id)->where('type', 'status_change')->exists())->toBeTrue();

    // La discussion reste ouverte (pas clôturée) tant que l'admin ne valide pas.
    expect($task->discussionState())->toBe('open');
});

it('épingle TOUJOURS le dernier rapport (l\'ancien se dé-épingle)', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner);

    $r1 = makeReport($owner, $task, 30);
    app(DailyReportService::class)->syncTaskProgress($r1, $task, $owner, false);

    // Rapport du lendemain à 60 %.
    $r2 = DailyReport::create([
        'user_id'               => $owner->id,
        'task_id'               => $task->id,
        'report_date'           => today()->addDay(),
        'summary'               => 'Suite',
        'task_progress_percent' => 60,
        'status'                => 'submitted',
        'hours_declared'        => 0,
    ]);
    app(DailyReportService::class)->syncTaskProgress($r2, $task->refresh(), $owner, false);

    $payload = app(TaskThreadService::class)->payload($task->refresh(), $owner);
    expect($payload['pinned_report']['id'])->toBe($r2->id)
        ->and($payload['pinned_report']['progress'])->toBe(60);
});

it('clôture la discussion quand la tâche est terminée par l\'admin', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner, ['status' => 'awaiting_validation', 'last_progress_percent' => 100]);
    makeReport($owner, $task, 100);

    // Simulation de la clôture admin (logique métier).
    $task->update(['status' => 'completed', 'completed_at' => now()]);

    expect($task->discussionState())->toBe('closed');
});

it('rend la page tâche (état verrouillé) sans erreur pour le propriétaire', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe');
    $owner->givePermissionTo('tasks.view');
    $task = makeTask($owner);

    $this->actingAs($owner)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->assertSee('discussion')
        ->assertSee('premier rapport')
        ->assertSee($task->title);
});

it('rend la page tâche (discussion ouverte) avec le rapport épinglé dans le payload', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe');
    $owner->givePermissionTo('tasks.view');
    $task = makeTask($owner);
    $report = makeReport($owner, $task, 70);
    app(DailyReportService::class)->syncTaskProgress($report, $task, $owner, false);

    $this->actingAs($owner)
        ->get(route('tasks.show', $task->refresh()))
        ->assertOk()
        ->assertSee('Discussion')
        ->assertSee('Avancement du jour'); // résumé du rapport épinglé, embarqué dans le payload JSON
});

it('dédoublonne les rapports par tâche/jour (un rapport par tâche par jour)', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe'); // storeForToday tolère l'absence de stage pour un employé
    $task = makeTask($owner);

    $first = app(DailyReportService::class)->storeForToday($owner, [
        'status_action'         => 'submit',
        'task_id'               => $task->id,
        'summary'               => 'Premier jet',
        'task_progress_percent' => 20,
    ]);

    $second = app(DailyReportService::class)->storeForToday($owner, [
        'status_action'         => 'submit',
        'task_id'               => $task->id,
        'summary'               => 'Mise à jour du même jour',
        'task_progress_percent' => 50,
    ]);

    // Même ligne réutilisée (pas de doublon), progression mise à jour.
    expect($second->id)->toBe($first->id)
        ->and(DailyReport::where('task_id', $task->id)->whereDate('report_date', today())->count())->toBe(1)
        ->and($task->refresh()->last_progress_percent)->toBe(50);
});
