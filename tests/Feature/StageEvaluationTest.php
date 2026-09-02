<?php

use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\EvaluationCriterion;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\StageEvaluation;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\StageEvaluationService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->service = app(StageEvaluationService::class);
});

function evalUser(string $role = 'admin', ?string $createdAt = null): User
{
    $personnel = Personnel::create([
        'nom'    => 'Eval',
        'prenom' => ucfirst($role),
        'email'  => "{$role}." . Str::random(6) . '@example.com',
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

    if ($createdAt) {
        $user->forceFill(['created_at' => $createdAt])->save();
        $personnel->forceFill(['date_debut_pointage' => $createdAt])->save();
    }

    return $user;
}

function evalStage(User $studentUser, ?TypeStage $type = null): Stage
{
    $etudiant = Etudiant::create(['personnel_id' => $studentUser->personnel_id, 'ecole' => 'Test']);

    Personnel::where('id', $studentUser->personnel_id)->update([
        'personnable_type' => Etudiant::class,
        'personnable_id'   => $etudiant->id,
    ]);

    return Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => ($type ?? TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'Academique ' . Str::random(4)]))->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Sujet du stage',
        'date_debut'   => now()->subMonths(2)->startOfMonth()->toDateString(),
        'date_fin'     => now()->subMonth()->endOfMonth()->toDateString(),
    ]);
}

/** Un stage encore en cours : le compte du stagiaire reste actif. */
function ongoingStage(User $studentUser): Stage
{
    $etudiant = Etudiant::create(['personnel_id' => $studentUser->personnel_id, 'ecole' => 'Test']);

    Personnel::where('id', $studentUser->personnel_id)->update([
        'personnable_type' => Etudiant::class,
        'personnable_id'   => $etudiant->id,
    ]);

    return Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'Academique ' . Str::random(4)])->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Sujet du stage',
        'date_debut'   => now()->subMonth()->toDateString(),
        'date_fin'     => now()->addMonth()->toDateString(),
    ]);
}

test('the draft grid is built from every active criterion', function () {
    // Grille unique : le type de stage n'entre plus en compte.
    $admin   = evalUser('admin');
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = ongoingStage($student);

    EvaluationCriterion::create(['label' => 'Assiduite', 'weight' => 2, 'sort_order' => 1]);
    EvaluationCriterion::create(['label' => 'Autonomie', 'weight' => 1, 'sort_order' => 2]);

    $evaluation = $this->service->draftFor($stage, $admin);

    expect($evaluation->scores->pluck('label_snapshot')->all())
        ->toBe(['Assiduite', 'Autonomie']);
});

test('finalising freezes labels and weights against later edits', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    $criterion = EvaluationCriterion::create(['label' => 'Autonomie', 'weight' => 2]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();

    $this->service->saveScores($evaluation, [$row->id => ['score' => 15, 'comment' => null]], null, null);
    $this->service->finalize($evaluation->fresh('scores'), $admin);

    // L'administrateur renomme le critère et change son coefficient
    $criterion->update(['label' => 'Autonomie et initiative', 'weight' => 5]);

    $frozen = StageEvaluation::where('stage_id', $stage->id)->first();

    expect($frozen->final_score)->toEqual('15.00')
        ->and($frozen->scores->first()->label_snapshot)->toBe('Autonomie')
        ->and($frozen->scores->first()->weight_snapshot)->toBe(2);
});

test('the final score is a weighted average of the retained notes', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create(['label' => 'Leger', 'weight' => 1, 'sort_order' => 1]);
    EvaluationCriterion::create(['label' => 'Lourd', 'weight' => 3, 'sort_order' => 2]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $leger = $evaluation->scores->firstWhere('label_snapshot', 'Leger');
    $lourd = $evaluation->scores->firstWhere('label_snapshot', 'Lourd');

    $this->service->saveScores($evaluation, [
        $leger->id => ['score' => 8,  'comment' => null],
        $lourd->id => ['score' => 16, 'comment' => null],
    ], null, null);

    // (8×1 + 16×3) / 4 = 14
    expect($this->service->weightedAverage($evaluation->fresh('scores')))->toBe(14.0);
});

test('an automatic criterion arrives pre-filled from the attendance data', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    // Quelques jours pointés à l'heure et complets
    $date = \Carbon\Carbon::parse($stage->date_debut);
    $made = 0;
    while ($made < 8) {
        if (!$date->isWeekend()) {
            AttendanceDay::create([
                'stage_id'                => $stage->id,
                'etudiant_id'             => $stage->etudiant_id,
                'user_id'                 => $student->id,
                'attendance_date'         => $date->toDateString(),
                'first_check_in_at'       => $date->copy()->setTime(8, 0),
                'last_check_out_at'       => $date->copy()->setTime(17, 0),
                'arrival_status'          => 'on_time',
                'early_departure_minutes' => 0,
            ]);
            $made++;
        }
        $date->addDay();
    }

    EvaluationCriterion::create([
        'label'       => 'Assiduite',
        'weight'      => 2,
        'is_auto'     => true,
        'auto_source' => 'attendance',
    ]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();

    expect($row->is_auto)->toBeTrue()
        ->and($row->computed_score)->not->toBeNull()
        // La note proposée est reprise telle quelle tant qu'on ne la change pas
        ->and((float) $row->score)->toEqual((float) $row->computed_score)
        ->and((float) $row->computed_score)->toBeGreaterThan(0);
});

test('an automatic criterion stays unscored when there is no attendance data at all', function () {
    $student = evalUser('etudiant');
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create([
        'label' => 'Assiduite', 'weight' => 1, 'is_auto' => true, 'auto_source' => 'attendance',
    ]);

    $evaluation = $this->service->draftFor($stage, $admin);

    // Mettre 0 à quelqu'un dont on n'a aucune donnée serait une sanction, pas une mesure
    expect($evaluation->scores->first()->computed_score)->toBeNull();
});

test('replacing a computed note without justification blocks finalisation', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    $date = \Carbon\Carbon::parse($stage->date_debut);
    for ($i = 0; $i < 10; $i++) {
        if (!$date->isWeekend()) {
            AttendanceDay::create([
                'stage_id' => $stage->id, 'etudiant_id' => $stage->etudiant_id, 'user_id' => $student->id,
                'attendance_date' => $date->toDateString(),
                'first_check_in_at' => $date->copy()->setTime(8, 0),
                'last_check_out_at' => $date->copy()->setTime(17, 0),
                'arrival_status' => 'on_time', 'early_departure_minutes' => 0,
            ]);
        }
        $date->addDay();
    }

    EvaluationCriterion::create([
        'label' => 'Assiduite', 'weight' => 1, 'is_auto' => true, 'auto_source' => 'attendance',
    ]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();

    // Surcharge sans un mot d'explication
    $this->service->saveScores($evaluation, [$row->id => ['score' => 3, 'comment' => null]], null, null);

    $issues = $this->service->blockingIssues($evaluation->fresh('scores'));
    expect(implode(' ', $issues))->toContain('justification');

    expect(fn() => $this->service->finalize($evaluation->fresh('scores'), $admin))
        ->toThrow(ValidationException::class);

    // Avec la justification, la finalisation passe
    $this->service->saveScores($evaluation->fresh('scores'), [
        $row->id => ['score' => 3, 'comment' => 'Hospitalisation non declaree a temps.'],
    ], null, null);

    $final = $this->service->finalize($evaluation->fresh('scores'), $admin);

    expect($final->isFinalized())->toBeTrue()
        ->and($final->scores->first()->isOverridden())->toBeTrue();
});

test('an unscored criterion blocks finalisation', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create(['label' => 'Note manquante', 'weight' => 1]);

    $evaluation = $this->service->draftFor($stage, $admin);

    expect(implode(' ', $this->service->blockingIssues($evaluation)))->toContain("n'est pas encore noté");
});

test('an empty grid cannot be finalised', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    $evaluation = $this->service->draftFor($stage, $admin);

    expect(implode(' ', $this->service->blockingIssues($evaluation)))->toContain('grille est vide');
});

test('a finalised evaluation refuses any further write', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create(['label' => 'Critere', 'weight' => 1]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();

    $this->service->saveScores($evaluation, [$row->id => ['score' => 12, 'comment' => null]], null, null);
    $final = $this->service->finalize($evaluation->fresh('scores'), $admin);

    expect(fn() => $this->service->saveScores($final, [$row->id => ['score' => 20, 'comment' => null]], null, null))
        ->toThrow(ValidationException::class);

    expect((float) $final->scores->first()->score)->toEqual(12.0);
});

test('reopening returns the evaluation to draft and records who did it', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create(['label' => 'Critere', 'weight' => 1]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();
    $this->service->saveScores($evaluation, [$row->id => ['score' => 12, 'comment' => null]], null, null);
    $this->service->finalize($evaluation->fresh('scores'), $admin);

    $reopened = $this->service->reopen($evaluation->fresh('scores'), $admin);

    expect($reopened->isDraft())->toBeTrue()
        ->and($reopened->reopened_by)->toBe($admin->id)
        ->and($reopened->reopened_at)->not->toBeNull();
});

test('the attendance snapshot is frozen and survives a later correction', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create(['label' => 'Critere', 'weight' => 1]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();
    $this->service->saveScores($evaluation, [$row->id => ['score' => 14, 'comment' => null]], null, null);

    $final = $this->service->finalize($evaluation->fresh('scores'), $admin);

    expect($final->attendance_snapshot)->toBeArray()
        ->and($final->attendance_snapshot)->toHaveKeys(['counts', 'ratios', 'anomalies', 'frozen_at']);

    $frozenExpected = $final->attendance_snapshot['counts']['expected_days'];

    // Une absence corrigée après coup ne doit pas déplacer la note déjà remise
    \App\Models\AttendanceException::create([
        'user_id'         => $student->id,
        'attendance_date' => \Carbon\Carbon::parse($stage->date_debut)->addDays(1)->toDateString(),
        'reason'          => 'Correction tardive',
    ]);

    expect($final->fresh()->attendance_snapshot['counts']['expected_days'])->toBe($frozenExpected)
        ->and((float) $final->fresh()->final_score)->toEqual(14.0);
});

test('a student never consults the report online, even once finalised', function () {
    // Décision métier : le rapport est remis en main propre sous forme de PDF.
    // Il reste un document interne, jamais une page consultable par le stagiaire.
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = ongoingStage($student);

    EvaluationCriterion::create(['label' => 'Critere', 'weight' => 1]);

    // Stage en cours et pointage du jour fait : rien ne le bloque en amont,
    // c'est bien l'autorisation du rapport qui refuse.
    AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $stage->etudiant_id,
        'user_id'           => $student->id,
        'attendance_date'   => today()->toDateString(),
        'first_check_in_at' => now(),
    ]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();
    $this->service->saveScores($evaluation, [$row->id => ['score' => 14, 'comment' => null]], null, null);
    $this->service->finalize($evaluation->fresh('scores'), $admin);

    $this->actingAs($student)
        ->get(route('stages.rapport', $stage))
        ->assertForbidden();
});

test('a student whose stage has ended is locked out by account deactivation', function () {
    // Comportement existant, hors de cette feature : EnsureAccountActive
    // desactive et deconnecte un stagiaire des que son dernier stage est
    // termine. Une evaluation finalisee apres la fin du stage — le cas normal —
    // reste donc hors de portee du stagiaire tant que cette regle est en place.
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    EvaluationCriterion::create(['label' => 'Critere', 'weight' => 1]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();
    $this->service->saveScores($evaluation, [$row->id => ['score' => 14, 'comment' => null]], null, null);
    $this->service->finalize($evaluation->fresh('scores'), $admin);

    $response = $this->actingAs($student)->get(route('stages.rapport', $stage));

    expect($response->status())->not->toBe(200)
        ->and($student->fresh()->status)->toBe('inactif');
});

test('a student never sees another student report even once finalised', function () {
    $mine   = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $other  = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin  = evalUser('admin');
    $stage  = evalStage($other);

    EvaluationCriterion::create(['label' => 'Critere', 'weight' => 1]);

    $evaluation = $this->service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();
    $this->service->saveScores($evaluation, [$row->id => ['score' => 14, 'comment' => null]], null, null);
    $this->service->finalize($evaluation->fresh('scores'), $admin);

    $response = $this->actingAs($mine)->get(route('stages.rapport', $stage));
    expect($response->status())->not->toBe(200);
});

test('only an admin can reach the grading screen', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = evalStage($student);

    foreach ([$student, evalUser('employe'), evalUser('superviseur')] as $user) {
        $response = $this->actingAs($user)->get(route('stages.evaluation.edit', $stage));
        expect($response->status())->not->toBe(200);
    }

    $this->actingAs(evalUser('admin'))
        ->get(route('stages.evaluation.edit', $stage))
        ->assertOk()
        ->assertViewIs('admin.stages.evaluation');
});

test('a criterion removed from the referential leaves the draft grid', function () {
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $admin   = evalUser('admin');
    $stage   = evalStage($student);

    $keep = EvaluationCriterion::create(['label' => 'Garde', 'weight' => 1]);
    $drop = EvaluationCriterion::create(['label' => 'Retire', 'weight' => 1]);

    expect($this->service->draftFor($stage, $admin)->scores)->toHaveCount(2);

    $drop->update(['is_active' => false]);

    $refreshed = $this->service->draftFor($stage->fresh(), $admin);

    expect($refreshed->scores)->toHaveCount(1)
        ->and($refreshed->scores->first()->label_snapshot)->toBe('Garde');
});

test('the grid is composed inline from the evaluation table', function () {
    $admin   = evalUser('admin');
    $student = evalUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = ongoingStage($student);

    // Aucun critère au départ : le tableau est vierge
    expect(EvaluationCriterion::count())->toBe(0);

    $this->actingAs($admin)
        ->post(route('stages.evaluation.grid.save', $stage), [
            'rows' => [
                ['id' => '', 'label' => 'Assiduite',  'weight' => 2],
                ['id' => '', 'label' => 'Autonomie',  'weight' => 1],
                ['id' => '', 'label' => '',           'weight' => 1], // ligne vide ignorée
            ],
        ])
        ->assertRedirect();

    $evaluation = $stage->fresh()->evaluation;

    expect(EvaluationCriterion::count())->toBe(2)
        ->and($evaluation->grid_validated_at)->not->toBeNull()
        ->and($evaluation->scores->pluck('label_snapshot')->all())->toBe(['Assiduite', 'Autonomie']);

    // Les critères sont globaux, pas rattachés au type
    expect(EvaluationCriterion::whereNotNull('typestage_id')->count())->toBe(0);
});

test('criteria created for one student reappear for the next', function () {
    $admin = evalUser('admin');

    $first  = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));
    $second = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));

    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $first), [
        'rows' => [['id' => '', 'label' => 'Ponctualite', 'weight' => 3]],
    ]);

    // Le second stagiaire hérite de la grille déjà composée
    $evaluation = $this->service->draftFor($second, $admin);

    expect($evaluation->scores->pluck('label_snapshot')->all())->toBe(['Ponctualite'])
        ->and($evaluation->scores->first()->weight_snapshot)->toBe(3);
});

test('a row removed from the table is retired, not destroyed', function () {
    $admin = evalUser('admin');
    $stage = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));

    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $stage), [
        'rows' => [
            ['id' => '', 'label' => 'Garde', 'weight' => 1],
            ['id' => '', 'label' => 'Retire', 'weight' => 1],
        ],
    ]);

    $garde = EvaluationCriterion::where('label', 'Garde')->first();

    // On revalide sans la seconde ligne
    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $stage), [
        'rows' => [['id' => $garde->id, 'label' => 'Garde', 'weight' => 1]],
    ]);

    // La ligne retirée existe toujours : les évaluations passées la référencent
    expect(EvaluationCriterion::where('label', 'Retire')->first()->is_active)->toBeFalse()
        ->and(EvaluationCriterion::count())->toBe(2);
});

test('a row can be renamed and reweighted in place', function () {
    $admin = evalUser('admin');
    $stage = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));

    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $stage), [
        'rows' => [['id' => '', 'label' => 'Assidutee', 'weight' => 1]],
    ]);

    $c = EvaluationCriterion::first();

    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $stage), [
        'rows' => [['id' => $c->id, 'label' => 'Assiduité', 'weight' => 4]],
    ]);

    $c->refresh();
    expect($c->label)->toBe('Assiduité')
        ->and($c->weight)->toBe(4)
        ->and(EvaluationCriterion::count())->toBe(1);
});

test('the grid can be reopened to change the criteria without losing the marks', function () {
    $admin = evalUser('admin');
    $stage = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));

    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $stage), [
        'rows' => [['id' => '', 'label' => 'Autonomie', 'weight' => 1]],
    ]);

    $evaluation = $stage->fresh()->evaluation;
    $row        = $evaluation->scores->first();

    $this->service->saveScores($evaluation, [$row->id => ['score' => 15, 'comment' => null]], null, null);

    $this->actingAs($admin)
        ->post(route('stages.evaluation.grid.edit', $stage))
        ->assertRedirect();

    $evaluation->refresh();

    // On repasse en composition, mais la note déjà saisie est conservée
    expect($evaluation->grid_validated_at)->toBeNull()
        ->and((float) $evaluation->scores->first()->score)->toBe(15.0);
});

test('an empty grid cannot be validated', function () {
    $admin = evalUser('admin');
    $stage = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));

    $this->actingAs($admin)
        ->post(route('stages.evaluation.grid.save', $stage), [
            'rows' => [['id' => '', 'label' => '   ', 'weight' => 1]],
        ])
        ->assertSessionHasErrors('rows');

    expect($stage->fresh()->evaluation?->grid_validated_at)->toBeNull();
});

test('the notes screen always offers a way back to the criteria', function () {
    $admin = evalUser('admin');
    $stage = ongoingStage(evalUser('etudiant', now()->subMonths(3)->toDateString()));

    $this->actingAs($admin)->post(route('stages.evaluation.grid.save', $stage), [
        'rows' => [['id' => '', 'label' => 'Autonomie', 'weight' => 1]],
    ]);

    // Le retour doit être présent sur l'écran de saisie des notes, pas seulement
    // atteignable par l'URL : sans lui, l'admin est coincé.
    $this->actingAs($admin)
        ->get(route('stages.evaluation.edit', $stage))
        ->assertOk()
        ->assertSee('Modifier les critères')
        // Les URL admin sont chiffrées : on vérifie le chemin, pas l'identifiant
        ->assertSee('/evaluation/grille/modifier', false);
});
