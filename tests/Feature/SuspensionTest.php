<?php

use App\Models\AppNotification;
use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Models\WorkScheduleSetting;
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

function suspUser(string $role = 'etudiant'): User
{
    $personnel = Personnel::create([
        'nom'    => 'Susp',
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

/** Une journée déjà clôturée d'office, et jamais réclamée. */
function journeeCloturee(User $user, Carbon $date): AttendanceDay
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
        'expected_check_out_time' => '18:00:00',
    ]);

    $day = AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $etudiant->id,
        'user_id'           => $user->id,
        'attendance_date'   => $date,
        'first_check_in_at' => $date->copy()->setTime(8, 0),
        'last_check_out_at' => $date->copy()->setTime(18, 0),
        'arrival_status'    => 'ontime',
        'departure_status'  => 'auto_closed',
    ]);

    DB::table('attendance_days')->where('id', $day->id)->update([
        'attendance_date' => $date->toDateString(),
    ]);

    return $day->refresh();
}

test('the warning comes first, and the account stays open', function () {
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    $day->refresh();
    $user->refresh();

    expect($day->suspension_warned_at)->not->toBeNull()
        ->and($user->status)->toBe('actif');

    $notification = AppNotification::where('user_id', $user->id)
        ->where('type', 'suspension_avertissement')
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->title)->toBe('Votre compte sera suspendu demain');
});

test('nobody is suspended the same day they are warned', function () {
    // Avertir et couper dans la même exécution reviendrait à ne pas prévenir.
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();
    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    expect($user->refresh()->status)->toBe('actif');
});

test('the account is suspended the day after the warning', function () {
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    // Le lendemain, toujours rien de déclaré.
    Carbon::setTestNow(now()->addDay());
    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    expect($user->refresh()->status)->toBe('inactif');
});

test('declaring an hour protects the account', function () {
    // Ce qui est sanctionné, c'est le silence, pas l'oubli.
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    $day->forceFill([
        'departure_status'     => 'claimed',
        'claimed_check_out_at' => $day->attendance_date->copy()->setTime(18, 30),
        'claimed_at'           => now(),
    ])->save();

    Carbon::setTestNow(now()->addDay());
    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    expect($user->refresh()->status)->toBe('actif');
});

test('a day still fresh is not worth a warning', function () {
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDay());

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    expect($day->refresh()->suspension_warned_at)->toBeNull();
});

test('an admin is never locked out of their own application', function () {
    // Suspendre un administrateur fermerait la porte à celui qui doit la rouvrir.
    $admin = suspUser('admin');
    journeeCloturee($admin, today()->subDays(5));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();
    Carbon::setTestNow(now()->addDay());
    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    expect($admin->refresh()->status)->toBe('actif');
});

test('the dry run writes nothing', function () {
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved', ['--dry-run' => true])->assertSuccessful();

    expect($day->refresh()->suspension_warned_at)->toBeNull()
        ->and($user->refresh()->status)->toBe('actif');
});

test('a suspended account is logged out on its next request', function () {
    $user = suspUser();
    journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();
    Carbon::setTestNow(now()->addDay());
    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    $this->actingAs($user->refresh())
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse();
});

test('the admins are told why the door closed', function () {
    $admin = suspUser('admin');
    $user  = suspUser();
    journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();
    Carbon::setTestNow(now()->addDay());
    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    expect(AppNotification::where('user_id', $admin->id)
        ->where('type', 'attendance_suspension')
        ->exists())->toBeTrue();
});

test('the warned modal announces the suspension and cannot be dismissed', function () {
    $user = suspUser();
    $day  = journeeCloturee($user, today()->subDays(2));

    $this->artisan('attendance:suspend-unresolved')->assertSuccessful();

    // Le pointage du jour doit exister : la question d'hier ne s'affiche
    // qu'une fois l'arrivée d'aujourd'hui enregistrée.
    $stage = $day->stage;

    AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $day->etudiant_id,
        'user_id'           => $user->id,
        'attendance_date'   => today(),
        'first_check_in_at' => now()->setTime(8, 0),
        'arrival_status'    => 'ontime',
    ]);

    $this->actingAs($user)
        ->get(route('presence.pointage'))
        ->assertOk()
        ->assertSee('Votre compte sera suspendu demain')
        ->assertDontSee('Plus tard');
});
