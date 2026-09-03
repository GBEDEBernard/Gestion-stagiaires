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

test('a stage keeps the same schedule on every one of its days', function () {
    // L'horaire par jour a été retiré : 08:00–12:30 sur le stage vaut pour
    // lundi comme pour mercredi.
    $stage = schedStage(['expected_check_in_time' => '08:00:00', 'expected_check_out_time' => '12:30:00']);

    $stage->jours()->sync([
        Jour::firstOrCreate(['jour' => 'Lundi'])->id    => [],
        Jour::firstOrCreate(['jour' => 'Mercredi'])->id => [],
    ]);
    $stage->load('jours');

    foreach (['2026-09-07', '2026-09-09'] as $jour) {
        expect($this->resolver->forStage($stage, $jour)['end'])->toBe('12:30');
    }
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
            'start_time'  => '07:30',
            'end_time'    => '16:30',
            'break_hours' => 1,
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

test('the stage form records the schedule, the break in hours and the days', function () {
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
            'break_hours'             => 1,
        ])
        ->assertRedirect();

    $stage->refresh()->load('jours');

    // La pause se saisit en heures et se range en minutes
    expect(substr($stage->expected_check_in_time, 0, 5))->toBe('09:00')
        ->and($stage->break_minutes)->toBe(60)
        ->and($stage->jours)->toHaveCount(2);
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

test('the company break defaults to two hours', function () {
    \App\Models\WorkScheduleSetting::query()->delete();

    // Table vide : le repli du modèle doit lui aussi valoir 120
    expect(app(WorkScheduleResolver::class)->forStage(null)['break_minutes'])->toBe(120);
});

test('a stage without its own break follows the company one', function () {
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00', 'break_minutes' => 120]);

    // NULL, pas 0 : sans cela le stage écraserait toujours la référence
    $stage = schedStage(['break_minutes' => null]);

    expect(app(WorkScheduleResolver::class)->forStage($stage)['break_minutes'])->toBe(120);
});

test('a stage can still declare no break at all', function () {
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00', 'break_minutes' => 120]);

    // 0 explicite : la personne ne prend pas de pause, et ça doit tenir
    $stage = schedStage(['break_minutes' => 0]);

    expect(app(WorkScheduleResolver::class)->forStage($stage)['break_minutes'])->toBe(0);
});

test('the creation form proposes the company break', function () {
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00', 'break_minutes' => 120]);

    $this->actingAs(schedUser('admin'))
        ->get(route('stages.create'))
        ->assertOk()
        // Saisie en heures : deux heures, pas cent vingt minutes
        ->assertSee('name="break_hours" value="2"', false);
});

test('an empty break field means inherit, not zero', function () {
    $admin = schedUser('admin');
    $stage = schedStage(['break_minutes' => 90]);
    $lundi = Jour::firstOrCreate(['jour' => 'Lundi']);

    $this->actingAs($admin)->put(route('stages.update', $stage), [
        'etudiant_id'  => $stage->etudiant_id,
        'typestage_id' => $stage->typestage_id,
        'domaine_id'   => $stage->domaine_id,
        'site_id'      => $stage->site_id,
        'theme'        => $stage->theme,
        'date_debut'   => '2026-09-01',
        'date_fin'     => '2026-12-31',
        'jours_id'     => [$lundi->id],
        'expected_check_in_time'  => '08:00',
        'expected_check_out_time' => '18:00',
        'break_minutes'           => '',
    ])->assertRedirect();

    expect($stage->fresh()->break_minutes)->toBeNull();
});

test('the recalculation clears a lateness that never was one', function () {
    // Stage à 08:30, arrivée à 08:20 : l'ancienne règle du 08:00 en dur avait
    // enregistré vingt minutes de retard sur une arrivée en avance.
    $stage = schedStage(['expected_check_in_time' => '08:30:00', 'expected_check_out_time' => '17:30:00']);

    $jour = \App\Models\AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $stage->etudiant_id,
        'attendance_date'   => today(),
        'first_check_in_at' => today()->setTime(8, 20),
        'arrival_status'    => 'late',
        'late_minutes'      => 20,
    ]);

    $this->artisan('presence:recalculer-retards', ['--stage' => $stage->id])
        ->assertSuccessful();

    $jour->refresh();

    expect($jour->arrival_status)->toBe('ontime')
        ->and($jour->late_minutes)->toBe(0);
});

test('the dry run changes nothing', function () {
    $stage = schedStage(['expected_check_in_time' => '08:30:00', 'expected_check_out_time' => '17:30:00']);

    $jour = \App\Models\AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $stage->etudiant_id,
        'attendance_date'   => today(),
        'first_check_in_at' => today()->setTime(8, 20),
        'arrival_status'    => 'late',
        'late_minutes'      => 20,
    ]);

    $this->artisan('presence:recalculer-retards', ['--stage' => $stage->id, '--dry-run' => true])
        ->assertSuccessful();

    expect($jour->fresh()->arrival_status)->toBe('late');
});

test('a genuine lateness keeps its exact minutes', function () {
    $stage = schedStage(['expected_check_in_time' => '08:00:00', 'expected_check_out_time' => '18:00:00']);

    $jour = \App\Models\AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $stage->etudiant_id,
        'attendance_date'   => today(),
        'first_check_in_at' => today()->setTime(9, 15),
        'arrival_status'    => 'late',
        'late_minutes'      => 75,
    ]);

    $this->artisan('presence:recalculer-retards', ['--stage' => $stage->id])->assertSuccessful();

    // Rien ne doit bouger : le retard était déjà juste
    expect($jour->fresh()->arrival_status)->toBe('late')
        ->and($jour->fresh()->late_minutes)->toBe(75);
});
