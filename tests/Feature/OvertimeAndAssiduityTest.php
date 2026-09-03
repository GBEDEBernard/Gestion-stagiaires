<?php

/**
 * Le cas signalé : stage 08:00–18:00, arrivée à 10:00, départ à 20:00.
 * Le système déclarait cette personne assidue à 100 % de volume horaire, les
 * heures du soir rachetant la matinée manquée.
 */

use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Models\WorkScheduleSetting;
use App\Services\StageReportService;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->reports = app(StageReportService::class);

    WorkScheduleSetting::query()->delete();
    // Pause explicitement nulle : ces tests mesurent le découpage dans/hors
    // plage, pas la déduction de la pause — qui a son propre test plus bas.
    WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00', 'break_minutes' => 0]);
});

afterEach(fn() => Carbon::setTestNow());

function otUser(): User
{
    $p = Personnel::create(['nom' => 'Ot', 'prenom' => 'Test', 'email' => Str::random(8) . '@example.com']);
    $p->forceFill(['date_debut_pointage' => today()->subMonths(2)->toDateString()])->save();

    $u = User::create([
        'personnel_id' => $p->id, 'name' => 'Ot Test', 'email' => $p->email,
        'password' => Hash::make('password'), 'status' => 'actif',
    ]);
    $u->assignRole('etudiant');
    $u->forceFill(['created_at' => today()->subMonths(2)])->save();

    return $u;
}

function otStage(User $user): Stage
{
    $etudiant = Etudiant::create(['personnel_id' => $user->personnel_id, 'ecole' => 'Test']);
    Personnel::where('id', $user->personnel_id)->update([
        'personnable_type' => Etudiant::class, 'personnable_id' => $etudiant->id,
    ]);

    return Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'T ' . Str::random(5)])->id,
        'domaine_id'   => Domaine::create(['nom' => 'D ' . Str::random(5)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'S', 'is_active' => true])->id,
        'theme'        => 'Sujet',
        'date_debut'   => today()->subMonth()->toDateString(),
        'date_fin'     => today()->addMonth()->toDateString(),
        'expected_check_in_time'  => '08:00:00',
        'expected_check_out_time' => '18:00:00',
    ]);
}

/** Une journée pointée, avec ses heures réelles. */
function otDay(Stage $stage, User $user, string $date, string $in, string $out, int $lateMinutes = 0): void
{
    $day = AttendanceDay::create([
        'stage_id'          => $stage->id,
        'etudiant_id'       => $stage->etudiant_id,
        'user_id'           => $user->id,
        'attendance_date'   => $date,
        'first_check_in_at' => $date . ' ' . $in . ':00',
        'last_check_out_at' => $date . ' ' . $out . ':00',
        'arrival_status'    => $lateMinutes > 0 ? 'late' : 'ontime',
        'late_minutes'      => $lateMinutes,
        'worked_minutes'    => Carbon::parse("$date $in")->diffInMinutes(Carbon::parse("$date $out")),
    ]);

    DB::table('attendance_days')->where('id', $day->id)->update(['attendance_date' => $date]);
}

test('evening hours no longer make up for a late morning', function () {
    $user  = otUser();
    $stage = otStage($user);

    // Le cas exact : arrivée 10:00, départ 20:00 sur une journée 08:00–18:00
    $lundi = today()->subWeek()->startOfWeek();
    otDay($stage, $user, $lundi->toDateString(), '10:00', '20:00', 120);

    $r = $this->reports->build($stage);

    // 10h de présence, mais seulement 8h dans la plage attendue
    expect($r['counts']['worked_hours'])->toBe(8.0)
        ->and($r['counts']['overtime_hours'])->toBe(2.0);
});

test('overtime is counted apart and never enters the volume ratio', function () {
    $user  = otUser();
    $stage = otStage($user);

    $lundi = today()->subWeek()->startOfWeek();
    otDay($stage, $user, $lundi->toDateString(), '08:00', '21:00');

    $r = $this->reports->build($stage);

    // 13h de présence : 10h dans la plage, 3h au-delà
    expect($r['counts']['worked_hours'])->toBe(10.0)
        ->and($r['counts']['overtime_hours'])->toBe(3.0);
});

test('arriving early also counts as time outside the window', function () {
    $user  = otUser();
    $stage = otStage($user);

    $lundi = today()->subWeek()->startOfWeek();
    otDay($stage, $user, $lundi->toDateString(), '06:30', '18:00');

    $r = $this->reports->build($stage);

    expect($r['counts']['worked_hours'])->toBe(10.0)
        ->and($r['counts']['overtime_hours'])->toBe(1.5);
});

test('a day worked exactly to the schedule has no overtime', function () {
    $user  = otUser();
    $stage = otStage($user);

    $lundi = today()->subWeek()->startOfWeek();
    otDay($stage, $user, $lundi->toDateString(), '08:00', '18:00');

    $r = $this->reports->build($stage);

    expect($r['counts']['worked_hours'])->toBe(10.0)
        ->and($r['counts']['overtime_hours'])->toBe(0.0);
});

test('the average lateness is reported, not just the rate', function () {
    $user  = otUser();
    $stage = otStage($user);

    $lundi = today()->subWeek()->startOfWeek();
    otDay($stage, $user, $lundi->toDateString(), '10:00', '18:00', 120);
    otDay($stage, $user, $lundi->copy()->addDay()->toDateString(), '08:30', '18:00', 30);

    $r = $this->reports->build($stage);

    // « 0 % de ponctualité » ne dit pas s'il s'agit d'une minute ou de deux heures
    expect($r['counts']['avg_late_minutes'])->toBe(75);
});

test('the break is deducted on both sides of the volume ratio', function () {
    $user  = otUser();
    $stage = otStage($user);
    $stage->update(['break_minutes' => 60]);

    $lundi = today()->subWeek()->startOfWeek();
    otDay($stage, $user, $lundi->toDateString(), '08:00', '18:00');

    $r = $this->reports->build($stage);

    // 10h de plage moins 1h de pause : 9h retenues, des deux côtés
    expect($r['counts']['worked_hours'])->toBe(9.0);
});
