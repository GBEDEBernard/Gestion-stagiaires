<?php

use App\Models\DailyReport;
use App\Models\DailyReportReview;
use App\Models\Task;
use App\Models\User;
use App\Services\DailyReportService;
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

it('ouvre la discussion au premier rapport', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner);
    $report = makeReport($owner, $task, 40);

    app(DailyReportService::class)->syncTaskProgress($report, $task, $owner, false);
    $task->refresh();

    expect($task->status)->toBe('in_progress')
        ->and($task->last_progress_percent)->toBe(40)
        ->and($task->discussionState())->toBe('open');
});

it('passe « en attente de validation » à 100 % sans auto-clôturer', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner);
    $report = makeReport($owner, $task, 100);

    app(DailyReportService::class)->syncTaskProgress($report, $task, $owner, false);
    $task->refresh();

    expect($task->status)->toBe('awaiting_validation')
        ->and($task->isCompleted())->toBeFalse()
        ->and($task->completed_at)->toBeNull();

    // La discussion reste ouverte (pas clôturée) tant que l'admin ne valide pas.
    expect($task->discussionState())->toBe('open');
});

it('clôture la discussion quand la tâche est terminée par l\'admin', function () {
    $owner = User::factory()->create();
    $task = makeTask($owner, ['status' => 'awaiting_validation', 'last_progress_percent' => 100]);
    makeReport($owner, $task, 100);

    // Simulation de la clôture admin (logique métier).
    $task->update(['status' => 'completed', 'completed_at' => now()]);

    expect($task->discussionState())->toBe('closed');
});

it('rend la page workspace (état verrouillé) sans erreur pour le propriétaire', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe');
    $owner->givePermissionTo('tasks.view');
    $task = makeTask($owner);

    $this->actingAs($owner)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->assertSee($task->title);
});

it('rend la page workspace (rapport soumis) avec le rapport visible', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe');
    $owner->givePermissionTo('tasks.view');
    $task = makeTask($owner);
    $report = makeReport($owner, $task, 70);
    app(DailyReportService::class)->syncTaskProgress($report, $task, $owner, false);

    $this->actingAs($owner)
        ->get(route('tasks.show', $task->refresh()))
        ->assertOk()
        ->assertSee('Avancement du jour'); // résumé du rapport visible dans la section académique
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

it('permet de commenter un rapport via la route storeComment', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe');
    $owner->givePermissionTo('tasks.view');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $admin->givePermissionTo('tasks.view');

    $task = makeTask($owner);
    $report = makeReport($owner, $task, 50);

    $this->actingAs($admin)
        ->post(route('reports.comments.store', $report->id), [
            'comment' => 'Bon travail, continue !',
            'action'  => 'comment',
        ])
        ->assertRedirect();

    expect(DailyReportReview::where('daily_report_id', $report->id)->count())->toBe(1);

    $review = DailyReportReview::where('daily_report_id', $report->id)->first();
    expect($review->comment)->toBe('Bon travail, continue !')
        ->and($review->action)->toBe('comment')
        ->and($review->reviewer_id)->toBe($admin->id);
});

it('permet à un admin de valider un rapport via la route storeComment', function () {
    $owner = User::factory()->create();
    $owner->assignRole('employe');
    $owner->givePermissionTo('tasks.view');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $admin->givePermissionTo('tasks.view');

    $task = makeTask($owner);
    $report = makeReport($owner, $task, 80);

    $this->actingAs($admin)
        ->post(route('reports.comments.store', $report->id), [
            'comment' => 'Rapport d\'activité relu et validé.',
            'action'  => 'approved',
        ])
        ->assertRedirect();

    $report->refresh();
    expect($report->status)->toBe('reviewed')
        ->and($report->reviewed_by)->toBe($admin->id)
        ->and($report->reviewed_at)->not->toBeNull();

    $review = DailyReportReview::where('daily_report_id', $report->id)->first();
    expect($review->action)->toBe('approved');
});
