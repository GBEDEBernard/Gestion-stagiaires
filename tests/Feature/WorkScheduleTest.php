<?php

use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\WorkScheduleResolver;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->resolver = app(WorkScheduleResolver::class);
});

function schedUser(string $role = 'admin'): User
{
    $personnel = Personnel::create([
        'nom'    => 'Sched',
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

    return $user;
}

function schedStage(array $attrs = []): Stage
{
    $personnel = Personnel::create(['nom' => 'Etu', 'prenom' => 'Test', 'email' => Str::random(8) . '@example.com']);
    $etudiant  = Etudiant::create(['personnel_id' => $personnel->id, 'ecole' => 'Test']);

    return Stage::create(array_merge([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'Academique'])->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Sujet',
        'date_debut'   => '2026-09-01',
        'date_fin'     => '2026-12-31',
        'expected_check_in_time'  => '08:30:00',
        'expected_check_out_time' => '17:30:00',
    ], $attrs));
}

test('the expected arrival follows the stage schedule and not a hardcoded hour', function () {
    $stage = schedStage();

    // 2026-09-07 est un lundi
    $at = Carbon::parse('2026-09-07 08:20');

    expect($this->resolver->expectedArrival($stage, $at)->format('H:i'))->toBe('08:30');

    // Arriver à 08:20 pour un stage à 08:30, c'est être en avance.
    // Le 08:00 en dur déclarait pourtant 20 minutes de retard.
    expect($at->greaterThan($this->resolver->expectedArrival($stage, $at)))->toBeFalse();
});

test('there is no tolerance: one minute late is late', function () {
    $stage = schedStage();

    $pile    = Carbon::parse('2026-09-07 08:30');
    $uneMin  = Carbon::parse('2026-09-07 08:31');
    $expected = $this->resolver->expectedArrival($stage, $pile);

    expect($pile->greaterThan($expected))->toBeFalse()
        ->and($uneMin->greaterThan($expected))->toBeTrue();
});

test('a day can override the stage schedule for a half day', function () {
    $stage    = schedStage();
    $mercredi = Jour::create(['jour' => 'Mercredi']);
    $lundi    = Jour::create(['jour' => 'Lundi']);

    $stage->jours()->sync([
        $lundi->id    => ['start_time' => null, 'end_time' => null, 'break_minutes' => null],
        $mercredi->id => ['start_time' => '08:30', 'end_time' => '12:30', 'break_minutes' => 0],
    ]);

    $stage->load('jours');

    // Lundi suit l'horaire du stage
    expect($this->resolver->forStage($stage, '2026-09-07')['end'])->toBe('17:30');

    // Mercredi est une demi-journée
    expect($this->resolver->forStage($stage, '2026-09-09')['end'])->toBe('12:30');
});

test('the break is deducted from presence to give worked time', function () {
    $stage = schedStage(['break_minutes' => 120]);

    $in  = Carbon::parse('2026-09-07 08:30');
    $out = Carbon::parse('2026-09-07 17:30');

    // 9 h de présence, 2 h de pause : 7 h de travail
    expect($this->resolver->workedMinutes($stage, $in, $out))->toBe(420);
});

test('a day shorter than the break keeps its presence time rather than falling to zero', function () {
    $stage = schedStage(['break_minutes' => 120]);

    $in  = Carbon::parse('2026-09-07 08:30');
    $out = Carbon::parse('2026-09-07 09:30');

    // Une heure de présence : retirer deux heures de pause n'aurait aucun sens
    expect($this->resolver->workedMinutes($stage, $in, $out))->toBe(60);
});

test('a day break overrides the stage break', function () {
    $stage    = schedStage(['break_minutes' => 120]);
    $mercredi = Jour::create(['jour' => 'Mercredi']);

    $stage->jours()->sync([$mercredi->id => ['start_time' => '08:30', 'end_time' => '12:30', 'break_minutes' => 0]]);
    $stage->load('jours');

    $in  = Carbon::parse('2026-09-09 08:30');
    $out = Carbon::parse('2026-09-09 12:30');

    // Pas de pause sur une demi-journée : les 4 h comptent en entier
    expect($this->resolver->workedMinutes($stage, $in, $out))->toBe(240);
});

test('without a stage the company schedule from the database applies', function () {
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00']);

    $schedule = app(WorkScheduleResolver::class)->forStage(null);

    expect($schedule['start'])->toBe('08:00')
        ->and($schedule['end'])->toBe('18:00');
});

test('a stage without declared hours falls back on the company schedule', function () {
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '07:45', 'end_time' => '17:00']);

    $stage = schedStage(['expected_check_in_time' => null, 'expected_check_out_time' => null]);

    expect(app(WorkScheduleResolver::class)->forStage($stage)['start'])->toBe('07:45');
});

test('an admin can change the company schedule and it takes effect', function () {
    $admin = schedUser('admin');

    $this->actingAs($admin)
        ->put(route('admin.presence.horaire.update'), [
            'start_time'    => '07:30',
            'end_time'      => '16:30',
            'break_minutes' => 60,
        ])
        ->assertRedirect();

    // L'horaire n'est plus dans le code : il vient de la base
    expect(app(WorkScheduleResolver::class)->forStage(null))
        ->toMatchArray(['start' => '07:30', 'end' => '16:30', 'break_minutes' => 60]);

    $this->assertDatabaseHas('activities', ['action' => 'Modification horaire de reference']);
});

test('the company schedule refuses a departure before the arrival', function () {
    $this->actingAs(schedUser('admin'))
        ->put(route('admin.presence.horaire.update'), ['start_time' => '17:00', 'end_time' => '08:00'])
        ->assertSessionHasErrors('end_time');
});

test('only an admin can change the company schedule', function () {
    foreach (['etudiant', 'employe', 'superviseur'] as $role) {
        $response = $this->actingAs(schedUser($role))->get(route('admin.presence.horaire.edit'));
        expect($response->status())->not->toBe(200);
    }
});

test('the stage form records the schedule and the per day overrides', function () {
    $admin    = schedUser('admin');
    $stage    = schedStage();
    $lundi    = Jour::create(['jour' => 'Lundi']);
    $mercredi = Jour::create(['jour' => 'Mercredi']);

    $this->actingAs($admin)
        ->put(route('stages.update', $stage), [
            'etudiant_id'  => $stage->etudiant_id,
            'typestage_id' => $stage->typestage_id,
            'domaine_id'   => $stage->domaine_id,
            'site_id'      => $stage->site_id,
            'theme'        => $stage->theme,
            'date_debut'   => '2026-09-01',
            'date_fin'     => '2026-12-31',
            'jours_id'     => [$lundi->id, $mercredi->id],
            'expected_check_in_time'  => '09:00',
            'expected_check_out_time' => '18:00',
            'break_minutes'           => 60,
            'jour_schedule' => [
                $lundi->id    => ['start_time' => '', 'end_time' => '', 'break_minutes' => ''],
                $mercredi->id => ['start_time' => '09:00', 'end_time' => '13:00', 'break_minutes' => 0],
            ],
        ])
        ->assertRedirect();

    $stage->refresh()->load('jours');

    expect(substr($stage->expected_check_in_time, 0, 5))->toBe('09:00')
        ->and($stage->break_minutes)->toBe(60);

    // Les champs vides restent NULL : le jour suit l'horaire du stage
    $pivotLundi = $stage->jours->firstWhere('id', $lundi->id)->pivot;
    expect($pivotLundi->start_time)->toBeNull()
        ->and($pivotLundi->break_minutes)->toBeNull();

    $pivotMercredi = $stage->jours->firstWhere('id', $mercredi->id)->pivot;
    expect(substr($pivotMercredi->end_time, 0, 5))->toBe('13:00')
        ->and($pivotMercredi->break_minutes)->toBe(0);
});

test('a departure time before the arrival time is refused', function () {
    $admin = schedUser('admin');
    $stage = schedStage();
    $lundi = Jour::create(['jour' => 'Lundi']);

    $this->actingAs($admin)
        ->put(route('stages.update', $stage), [
            'etudiant_id'  => $stage->etudiant_id,
            'typestage_id' => $stage->typestage_id,
            'domaine_id'   => $stage->domaine_id,
            'site_id'      => $stage->site_id,
            'theme'        => $stage->theme,
            'date_debut'   => '2026-09-01',
            'date_fin'     => '2026-12-31',
            'jours_id'     => [$lundi->id],
            'expected_check_in_time'  => '17:00',
            'expected_check_out_time' => '09:00',
        ])
        ->assertSessionHasErrors('expected_check_out_time');
});
