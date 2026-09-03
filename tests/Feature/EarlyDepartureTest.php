<?php

use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\PermissionRequest;
use App\Models\PermissionType;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Models\WorkScheduleSetting;
use App\Services\PresenceService;
use Carbon\Carbon;
use Database\Seeders\PermissionTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(PermissionTypeSeeder::class);
    $this->service = app(PresenceService::class);

    WorkScheduleSetting::query()->delete();
    WorkScheduleSetting::create(['start_time' => '07:00', 'end_time' => '18:00']);
});

afterEach(function () {
    Carbon::setTestNow();
});

function depUser(string $role = 'etudiant'): User
{
    $personnel = Personnel::create([
        'nom'    => 'Dep',
        'prenom' => ucfirst($role),
        'email'  => "{$role}." . Str::random(6) . '@example.com',
    ]);

    $user = User::create([
        'personnel_id'      => $personnel->id,
        'name'              => $personnel->full_name,
        'email'             => $personnel->email,
        'password'          => Hash::make('password'),
        'status'            => 'actif',
    ]);

    $user->assignRole($role);

    return $user;
}

/** Stage journée complète 07:00 – 18:00, déjà pointé à l'arrivée. */
function fullDayStage(User $user, string $end = '18:00'): Stage
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
        'date_debut'   => today()->subMonth()->toDateString(),
        'date_fin'     => today()->addMonth()->toDateString(),
        'expected_check_in_time'  => '07:00:00',
        'expected_check_out_time' => $end . ':00',
    ]);

    $day = AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $etudiant->id,
        'user_id'           => $user->id,
        'attendance_date'   => today(),
        'first_check_in_at' => today()->setTime(7, 0),
        'arrival_status'    => 'ontime',
    ]);

    // Le service retrouve la journée avec une date au format 'Y-m-d' brut,
    // là où le cast du modèle écrirait 'Y-m-d 00:00:00'. On aligne l'écriture
    // sur la lecture, sinon le test bute sur cet écart et non sur le garde-fou.
    \Illuminate\Support\Facades\DB::table('attendance_days')
        ->where('id', $day->id)
        ->update(['attendance_date' => today()->toDateString()]);

    return $stage;
}

/** Une permission de départ anticipé, pour la date donnée. */
function earlyPermission(User $user, string $date, ?string $time = null, string $status = 'approved'): PermissionRequest
{
    return PermissionRequest::create([
        'user_id'            => $user->id,
        'permission_type_id' => PermissionType::where('slug', 'depart-anticipe')->first()->id,
        'fields_data'        => array_filter(['date' => $date, 'departure_time' => $time, 'motif' => 'Motif']),
        'status'             => $status,
        'decided_at'         => now(),
    ]);
}

test('a full day departure is refused before the end of the day', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    Carbon::setTestNow(today()->setTime(15, 30));

    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    // Rien n'a été écrit : la journée reste ouverte
    expect(AttendanceDay::first()->last_check_out_at)->toBeNull();
});

test('the refusal names the expected departure time', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    Carbon::setTestNow(today()->setTime(15, 30));

    try {
        $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]);
        $this->fail('Le départ aurait dû être refusé.');
    } catch (ValidationException $e) {
        expect(collect($e->errors())->flatten()->first())->toContain('18:00');
    }
});

test('the departure is allowed once the time has come', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    Carbon::setTestNow(today()->setTime(18, 0));

    $event = $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]);

    expect($event)->not->toBeNull()
        ->and(AttendanceDay::first()->fresh()->last_check_out_at)->not->toBeNull();
});

test('a half day stage is judged on its own end time', function () {
    $user  = depUser();
    // Demi-journée : fin à 12:30
    $stage = fullDayStage($user, '12:30');

    Carbon::setTestNow(today()->setTime(11, 0));
    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    Carbon::setTestNow(today()->setTime(12, 30));
    expect($this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->not->toBeNull();
});

test('an approved permission for today lifts the block', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    earlyPermission($user, today()->toDateString(), '15:00');

    Carbon::setTestNow(today()->setTime(15, 30));

    expect($this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->not->toBeNull();
});

test('a permission granted another day is not a permanent pass', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    // Permission obtenue la semaine dernière : elle ne vaut plus rien aujourd'hui
    earlyPermission($user, today()->subDays(7)->toDateString(), '15:00');

    Carbon::setTestNow(today()->setTime(15, 30));

    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    expect(AttendanceDay::first()->last_check_out_at)->toBeNull();
});

test('a permission still pending does not lift the block', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    earlyPermission($user, today()->toDateString(), '15:00', 'pending');

    Carbon::setTestNow(today()->setTime(15, 30));

    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);
});

test('a permission is honoured only from the hour it authorises', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    earlyPermission($user, today()->toDateString(), '15:00');

    // 14:00 : la permission existe mais n'autorise pas encore le départ
    Carbon::setTestNow(today()->setTime(14, 0));
    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    Carbon::setTestNow(today()->setTime(15, 0));
    expect($this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->not->toBeNull();
});

test('a permission belonging to someone else does not help', function () {
    $user   = depUser();
    $autre  = depUser();
    $stage  = fullDayStage($user);

    earlyPermission($autre, today()->toDateString(), '15:00');

    Carbon::setTestNow(today()->setTime(15, 30));

    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);
});

test('the qr scan applies the same block as the classic flow', function () {
    $user  = depUser();
    $stage = fullDayStage($user);
    $site  = Site::find($stage->site_id);
    $site->update(['qr_token' => 'tok-' . Str::random(12)]);

    Carbon::setTestNow(today()->setTime(15, 30));

    // Le scan ne doit pas être un contournement du blocage
    expect(fn() => $this->service->registerFromQrScan($user, $site, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    expect(AttendanceDay::first()->last_check_out_at)->toBeNull();
});

test('an arrival cannot be recorded once the day is over', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    // La journée s'arrête à 18:00 ; personne n'a pointé
    App\Models\AttendanceDay::query()->update(['first_check_in_at' => null, 'arrival_status' => null]);

    Carbon::setTestNow(today()->setTime(19, 30));

    expect(fn() => $this->service->registerCheckIn($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    expect(App\Models\AttendanceDay::first()->first_check_in_at)->toBeNull();
});

test('the refusal to arrive late names the end of the day', function () {
    $user  = depUser();
    $stage = fullDayStage($user);
    App\Models\AttendanceDay::query()->update(['first_check_in_at' => null, 'arrival_status' => null]);

    Carbon::setTestNow(today()->setTime(19, 30));

    try {
        $this->service->registerCheckIn($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]);
        $this->fail("L'arrivée aurait dû être refusée.");
    } catch (ValidationException $e) {
        expect(collect($e->errors())->flatten()->first())
            ->toContain('terminée')
            ->toContain('18:00');
    }
});

test('an arrival is still possible one minute before the end', function () {
    $user  = depUser();
    $stage = fullDayStage($user);
    App\Models\AttendanceDay::query()->update(['first_check_in_at' => null, 'arrival_status' => null]);

    Carbon::setTestNow(today()->setTime(17, 59));

    expect($this->service->registerCheckIn($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->not->toBeNull();
});

test('a half day closes the arrival window at its own end time', function () {
    $user  = depUser();
    $stage = fullDayStage($user, '12:30');
    App\Models\AttendanceDay::query()->update(['first_check_in_at' => null, 'arrival_status' => null]);

    // 14:00 est après la fin de la demi-journée : trop tard pour arriver
    Carbon::setTestNow(today()->setTime(14, 0));

    expect(fn() => $this->service->registerCheckIn($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);
});

test('the qr scan refuses a late arrival just like the classic flow', function () {
    $user  = depUser();
    $stage = fullDayStage($user);
    $site  = App\Models\Site::find($stage->site_id);
    $site->update(['qr_token' => 'tok-' . Str::random(12)]);

    App\Models\AttendanceDay::query()->update(['first_check_in_at' => null, 'arrival_status' => null]);

    Carbon::setTestNow(today()->setTime(19, 30));

    expect(fn() => $this->service->registerFromQrScan($user, $site, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);
});

test('a student can point through the qr flow without a database error', function () {
    // Le chemin stagiaire du scan filtrait sur stages.status, colonne qui
    // n'existe pas : toute tentative levait une erreur SQL en production alors
    // que les tests QR, écrits sur des employés, ne l'empruntaient jamais.
    $user  = depUser();
    $stage = fullDayStage($user);
    $site  = App\Models\Site::find($stage->site_id);
    $site->update(['qr_token' => 'tok-' . Str::random(12)]);

    App\Models\AttendanceDay::query()->delete();

    Carbon::setTestNow(today()->setTime(9, 0));

    $resultat = $this->service->registerFromQrScan(
        $user,
        $site,
        ['latitude' => 6.36, 'longitude' => 2.41],
        null,
        "Embouteillage sur la voie de Godomey."
    );

    expect($resultat['event_type'])->toBe('check_in')
        ->and(App\Models\AttendanceDay::first()->first_check_in_at)->not->toBeNull();
});

test('a permission requested today for a future day works on that day', function () {
    $user  = depUser();
    $stage = fullDayStage($user);

    // Demandée et approuvée aujourd'hui, mais portant sur vendredi 11:00
    $vendredi = today()->next(\Carbon\CarbonInterface::FRIDAY);
    earlyPermission($user, $vendredi->toDateString(), '11:00');

    // Aujourd'hui, elle ne sert à rien : ce n'est pas le jour visé
    Carbon::setTestNow(today()->setTime(15, 0));
    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    // Le vendredi venu, avant 11:00 : encore trop tôt
    Carbon::setTestNow($vendredi->copy()->setTime(10, 30));
    App\Models\AttendanceDay::query()->update(['attendance_date' => $vendredi->toDateString()]);
    \Illuminate\Support\Facades\DB::table('attendance_days')->update([
        'attendance_date'   => $vendredi->toDateString(),
        'first_check_in_at' => $vendredi->copy()->setTime(7, 0),
        'last_check_out_at' => null,
    ]);

    expect(fn() => $this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->toThrow(ValidationException::class);

    // À partir de 11:00 ce vendredi-là, le départ passe
    Carbon::setTestNow($vendredi->copy()->setTime(11, 0));

    expect($this->service->registerCheckOut($stage, $user, ['latitude' => 6.36, 'longitude' => 2.41]))
        ->not->toBeNull();
});
