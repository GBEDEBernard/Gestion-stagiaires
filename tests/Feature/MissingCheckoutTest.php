<?php

use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Models\WorkScheduleSetting;
use App\Services\AttendanceCorrectionService;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    WorkScheduleSetting::query()->delete();
    WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00', 'break_minutes' => 120]);
});

afterEach(fn () => Carbon::setTestNow());

function oubliUser(string $role = 'etudiant'): User
{
    $personnel = Personnel::create([
        'nom'    => 'Oubli',
        'prenom' => ucfirst($role),
        'email'  => "{$role}." . Str::random(6) . '@example.com',
    ]);

    $user = User::create([
        'personnel_id' => $personnel->id,
        'name'         => $personnel->full_name,
        'email'        => $personnel->email,
        'password'     => Hash::make('password'),
        'status'       => 'actif',
    ]);

    $user->assignRole($role);

    return $user;
}

/** Une journée ouverte : arrivée pointée, départ jamais pointé. */
function journeeOuverte(User $user, Carbon $date, string $arrivee = '08:00', string $fin = '18:00'): AttendanceDay
{
    $etudiant = Etudiant::create(['personnel_id' => $user->personnel_id, 'ecole' => 'Test']);

    Personnel::where('id', $user->personnel_id)->update([
        'personnable_type' => Etudiant::class,
        'personnable_id'   => $etudiant->id,
    ]);

    $stage = Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'T ' . Str::random(4)])->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Sujet',
        'date_debut'   => $date->copy()->subMonth()->toDateString(),
        'date_fin'     => $date->copy()->addMonth()->toDateString(),
        'expected_check_in_time'  => '08:00:00',
        'expected_check_out_time' => $fin . ':00',
        'break_minutes'           => 120,
    ]);

    $day = AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $etudiant->id,
        'user_id'           => $user->id,
        'attendance_date'   => $date,
        'first_check_in_at' => $date->copy()->setTimeFromTimeString($arrivee),
        'arrival_status'    => 'ontime',
        'day_status'        => 'present',
    ]);

    DB::table('attendance_days')->where('id', $day->id)->update([
        'attendance_date' => $date->toDateString(),
    ]);

    return $day->refresh();
}

test('the closure credits the expected end time, never a fixed hour', function () {
    $user = oubliUser();
    $day  = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout')->assertSuccessful();

    $day->refresh();

    // 18:00 et non 19:30 : oublier de pointer ne doit pas rapporter davantage
    // que pointer à l'heure.
    expect($day->last_check_out_at->format('H:i'))->toBe('18:00')
        ->and($day->departure_status)->toBe('auto_closed')
        // Dix heures de présence moins deux heures de pause.
        ->and($day->worked_minutes)->toBe(480);
});

test('a half day closes at its own end time', function () {
    $user = oubliUser();
    $day  = journeeOuverte($user, today()->subDay(), '08:00', '12:00');

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout')->assertSuccessful();

    expect($day->refresh()->last_check_out_at->format('H:i'))->toBe('12:00');
});

test('the day in progress is left alone: the person may still be working', function () {
    $user = oubliUser();
    $day  = journeeOuverte($user, today());

    // 20h : la journée dépasse l'horaire, mais rien ne dit que la personne
    // est partie. Elle pointera peut-être à 22h.
    Carbon::setTestNow(today()->setTime(20, 0));
    $this->artisan('attendance:auto-checkout')->assertSuccessful();

    expect($day->refresh()->last_check_out_at)->toBeNull()
        ->and($day->departure_status)->toBeNull();
});

test('a closure never lands before the arrival', function () {
    $user = oubliUser();
    // Arrivée à 19h la veille — journée décalée : fermer à 18:00 donnerait
    // un départ antérieur à l'arrivée.
    $day = journeeOuverte($user, today()->subDay(), '19:00');

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout')->assertSuccessful();

    expect($day->refresh()->last_check_out_at->format('H:i'))->toBe('19:00');
});

test('the declaration is recorded but changes nothing', function () {
    $user = oubliUser();
    $day  = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    Carbon::setTestNow(today()->setTime(8, 5));

    $this->actingAs($user)->post(route('presence.depart-oublie'), [
        'day_id'         => $day->id,
        'claimed_time'   => '19:30',
        'claimed_reason' => 'Téléphone déchargé, je suis parti à 19h30.',
    ])->assertRedirect();

    $day->refresh();

    expect($day->claimed_check_out_at->format('H:i'))->toBe('19:30')
        ->and($day->departure_status)->toBe('claimed')
        // La journée elle-même n'a pas bougé : sans cela, il suffirait de
        // déclarer 20h pour se créditer deux heures.
        ->and($day->last_check_out_at->format('H:i'))->toBe('18:00')
        ->and($day->worked_minutes)->toBe(480);
});

test('nobody can declare on someone else day', function () {
    $auteur  = oubliUser();
    $victime = oubliUser();
    $day     = journeeOuverte($victime, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    Carbon::setTestNow(today()->setTime(8, 5));

    $this->actingAs($auteur)->post(route('presence.depart-oublie'), [
        'day_id'         => $day->id,
        'claimed_time'   => '19:30',
        'claimed_reason' => 'Une raison quelconque pour cette journée.',
    ]);

    expect($day->refresh()->claimed_at)->toBeNull();
});

test('a declared hour before the arrival is refused', function () {
    $user = oubliUser();
    $day  = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    Carbon::setTestNow(today()->setTime(8, 5));

    $this->actingAs($user)
        ->post(route('presence.depart-oublie'), [
            'day_id'         => $day->id,
            'claimed_time'   => '07:00',
            'claimed_reason' => 'Une raison quelconque pour cette journée.',
        ])
        ->assertSessionHasErrors('claimed_time');

    expect($day->refresh()->claimed_at)->toBeNull();
});

test('only the admin turns a declaration into hours', function () {
    $user  = oubliUser();
    $admin = oubliUser('admin');
    $day   = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    app(AttendanceCorrectionService::class)
        ->applyCheckOut($user, $day->refresh(), '19:30', 'Départ confirmé par le responsable', $admin);

    $day->refresh();

    expect($day->last_check_out_at->format('H:i'))->toBe('19:30')
        ->and($day->departure_status)->toBe('corrected')
        // 08:00 → 19:30 moins deux heures de pause.
        ->and($day->worked_minutes)->toBe(570)
        ->and($day->correctionDepart->original_check_out_at->format('H:i'))->toBe('18:00');
});

test('reverting a departure correction restores the automatic closure', function () {
    $user  = oubliUser();
    $admin = oubliUser('admin');
    $day   = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    $service = app(AttendanceCorrectionService::class);
    $correction = $service->applyCheckOut($user, $day->refresh(), '19:30', 'Départ confirmé', $admin);

    $service->revert($correction);

    $day->refresh();

    expect($day->last_check_out_at->format('H:i'))->toBe('18:00')
        ->and($day->worked_minutes)->toBe(480)
        ->and($day->departure_status)->toBe('auto_closed');
});

test('the admin sheet surfaces the declaration and the way to settle it', function () {
    $user  = oubliUser();
    $admin = oubliUser('admin');
    $day   = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    app(AttendanceCorrectionService::class)
        ->claimCheckOut($day->refresh(), '19:30', 'Téléphone déchargé, parti à 19h30.');

    $this->actingAs($admin)
        ->get(route('attendance.tracking.user.historique', $user))
        ->assertOk()
        ->assertSee('déclaré · 19:30', false)
        ->assertSee("Rétablir l'heure", false);
});

test('the admin endpoint settles the day and logs the original hour', function () {
    $user  = oubliUser();
    $admin = oubliUser('admin');
    $day   = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    $this->actingAs($admin)
        ->post(route('attendance.tracking.user.correction-depart.store', [$user, $day]), [
            'time'   => '19:30',
            'reason' => 'Départ confirmé par le responsable de site',
        ])
        ->assertRedirect();

    expect($day->refresh()->last_check_out_at->format('H:i'))->toBe('19:30')
        ->and($day->departure_status)->toBe('corrected');
});

test('an admin cannot settle a day that belongs to someone else', function () {
    $user    = oubliUser();
    $etranger = oubliUser();
    $admin   = oubliUser('admin');
    $day     = journeeOuverte($user, today()->subDay());

    Carbon::setTestNow(today()->setTime(5, 0));
    $this->artisan('attendance:auto-checkout');

    $this->actingAs($admin)
        ->post(route('attendance.tracking.user.correction-depart.store', [$etranger, $day]), [
            'time'   => '19:30',
            'reason' => 'Une raison quelconque',
        ])
        ->assertNotFound();

    expect($day->refresh()->departure_status)->toBe('auto_closed');
});
