<?php

/**
 * Dépôt d'un rapport : le découpage préalable et le coche des tâches faites.
 *
 * La progression d'une tâche se calcule sur ses sous-tâches puis sur leurs
 * items. Un rapport déposé sur une tâche sans sous-tâche ni item ne pouvait
 * donc rien faire bouger : le pourcentage restait à 0 quoi qu'y ait d'écrit.
 * Le dépôt est désormais refusé tant que la tâche n'est pas découpée ET que
 * rien n'a été coché — un rapport validé doit dire ce qui a été accompli.
 *
 * La règle vaut pour tout le monde, y compris pour un admin qui porte aussi
 * le rôle employé : elle porte sur le travail rapporté, pas sur la fonction
 * de qui le rapporte. Le brouillon, lui, reste toujours possible.
 */

use App\Models\DailyReport;
use App\Models\Personnel;
use App\Models\Subtask;
use App\Models\SubtaskItem;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function depositUser(string $role = 'employe', array $extraRoles = []): User
{
    $personnel = Personnel::create([
        'nom'    => 'Rapport',
        'prenom' => Str::random(6),
        'email'  => Str::random(10) . '@example.com',
    ]);

    $user = User::create([
        'personnel_id'      => $personnel->id,
        'name'              => $personnel->full_name,
        'email'             => $personnel->email,
        'email_verified_at' => now(),
        'password'          => Hash::make('password'),
        'status'            => 'actif',
    ]);

    $user->assignRole($role);

    foreach ($extraRoles as $extra) {
        $user->assignRole($extra);
    }

    return $user;
}

function depositTask(User $owner): Task
{
    return Task::create([
        'owner_id'    => $owner->id,
        'title'       => 'Remonter le dossier',
        'description' => 'Description',
        'status'      => 'pending',
        'priority'    => 'normal',
    ]);
}

/** Découpe la tâche pour CE participant : une sous-tâche, un item. */
function cutTask(Task $task, User $user): SubtaskItem
{
    $subtask = Subtask::create([
        'task_id'             => $task->id,
        'assigned_to_user_id' => $user->id,
        'title'               => 'Relire le plan',
        'display_order'       => 1,
    ]);

    return SubtaskItem::create([
        'subtask_id'    => $subtask->id,
        'title'         => 'Page 1',
        'display_order' => 1,
    ]);
}

it('refuse de soumettre un rapport sur une tâche non découpée', function () {
    $user = depositUser();
    $task = depositTask($user);

    expect($task->reportReadiness()['can_submit'])->toBeFalse();

    $this->actingAs($user)
        ->post(route('reports.store'), [
            'status_action' => 'submit',
            'summary'       => 'J ai avance sur le dossier',
            'task_id'       => $task->id,
        ])
        ->assertSessionHasErrors('status_action');

    expect(DailyReport::where('task_id', $task->id)->count())->toBe(0);
});

it('refuse encore quand il y a une sous-tache mais aucun item', function () {
    $user = depositUser();
    $task = depositTask($user);

    Subtask::create(['task_id' => $task->id, 'title' => 'Relire le plan', 'display_order' => 1]);

    $readiness = $task->reportReadiness();

    expect($readiness['subtasks'])->toBe(1)
        ->and($readiness['items'])->toBe(0)
        ->and($readiness['can_submit'])->toBeFalse();

    $this->actingAs($user)
        ->post(route('reports.store'), [
            'status_action' => 'submit',
            'summary'       => 'J ai avance sur le dossier',
            'task_id'       => $task->id,
        ])
        ->assertSessionHasErrors('status_action');
});

it('refuse un rapport ou rien n a ete coche', function () {
    $user = depositUser();
    $task = depositTask($user);

    $item = cutTask($task, $user);

    expect($task->reportReadiness()['can_submit'])->toBeTrue()
        ->and($task->hasChecklistFor($user->id))->toBeTrue();

    $this->actingAs($user)
        ->post(route('reports.store'), [
            'status_action'         => 'submit',
            'summary'               => 'J ai avance sur le dossier',
            'task_id'               => $task->id,
            'completed_subtask_ids' => [],
        ])
        ->assertSessionHasErrors('completed_subtask_ids');

    expect(DailyReport::where('task_id', $task->id)->where('status', 'submitted')->exists())->toBeFalse();
});

it('accepte la soumission une fois la tâche découpée et une ligne cochee', function () {
    $user = depositUser();
    $task = depositTask($user);

    $item = cutTask($task, $user);

    $this->actingAs($user)
        ->post(route('reports.store'), [
            'status_action'         => 'submit',
            'summary'               => 'J ai avance sur le dossier',
            'task_id'               => $task->id,
            'completed_subtask_ids' => [$item->id],
        ])
        ->assertSessionHasNoErrors();

    expect(DailyReport::where('task_id', $task->id)->where('status', 'submitted')->exists())->toBeTrue()
        ->and($item->fresh()->is_completed)->toBeTrue();
});

it('laisse enregistrer un brouillon sans découpage, puis le soumettre apres', function () {
    $user = depositUser();
    $task = depositTask($user);

    $this->actingAs($user)
        ->post(route('reports.store'), [
            'status_action' => 'draft',
            'summary'       => 'Je note ce que j ai fait',
            'task_id'       => $task->id,
        ])
        ->assertSessionHasNoErrors();

    $draft = DailyReport::where('task_id', $task->id)->where('status', 'draft')->first();
    expect($draft)->not->toBeNull();

    // Le brouillon reste visible tel quel dans le workspace.
    $this->actingAs($user)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->assertSee('pas encore valid')
        ->assertSee('Soumettre le brouillon');

    // Une fois la tâche découpée et une ligne cochée, il se soumet sans souci.
    $item = cutTask($task, $user);

    $this->actingAs($user)
        ->put(route('reports.update', $draft->id), [
            'status_action'         => 'submit',
            'summary'               => 'Je note ce que j ai fait',
            'task_id'               => $task->id,
            'completed_subtask_ids' => [$item->id],
        ])
        ->assertSessionHasNoErrors();

    expect($draft->fresh()->status)->toBe('submitted');
});

it('applique la meme règle aux admins et aux superviseurs', function () {
    // Y compris un admin qui porte aussi le rôle employé : c'est le travail
    // rapporté qui doit être découpé, pas la fonction de qui le rapporte.
    $cases = [
        ['admin', []],
        ['superviseur', []],
        ['employe', ['admin']],
        ['etudiant', ['superviseur']],
    ];

    foreach ($cases as [$role, $extra]) {
        $user = depositUser($role, $extra);
        $task = depositTask($user);

        $this->actingAs($user)
            ->post(route('reports.store'), [
                'status_action' => 'submit',
                'summary'       => 'Compte rendu',
                'task_id'       => $task->id,
            ])
            ->assertSessionHasErrors('status_action');

        // Une fois découpée et cochée, ça passe comme pour tout le monde.
        $item = cutTask($task, $user);

        $this->actingAs($user)
            ->post(route('reports.store'), [
                'status_action'         => 'submit',
                'summary'               => 'Compte rendu',
                'task_id'               => $task->id,
                'completed_subtask_ids' => [$item->id],
            ])
            ->assertSessionHasNoErrors();
    }
});

it('garde le bouton soumettre actif et explique le refus dans une fenetre', function () {
    $user = depositUser();
    $task = depositTask($user);

    $html = $this->actingAs($user)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->getContent();

    // Le bouton n'est pas grisé : c'est au clic que la fenêtre s'ouvre.
    expect($html)->toContain('Soumettre le premier rapport')
        ->and($html)->not->toContain('cursor:not-allowed')
        // La fenêtre annonce les deux étapes manquantes, avec leur action.
        ->and($html)->toContain("Ta tâche n'est pas encore découpée")
        ->and($html)->toContain('Un item dans cette sous-tâche')
        ->and($html)->toContain('Créer les sous-tâches')
        ->and($html)->toContain('Coche ce que tu as terminé')
        ->and($html)->toContain('Cocher mes tâches')
        // Aucune trace du bandeau retiré : l'explication vit dans la fenêtre.
        ->and($html)->not->toContain('Découpe ta tâche avant de déposer ton rapport');
});