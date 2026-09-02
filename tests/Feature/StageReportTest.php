<?php

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\StageReportService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->service = app(StageReportService::class);
});

function reportUser(string $role = 'etudiant', ?string $createdAt = null): User
{
    $personnel = Personnel::create([
        'nom'    => 'Rapport',
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

    // getUserDetailedStats ignore tout ce qui precede la creation du compte et
    // la date du plus ancien utilisateur : un compte cree aujourd'hui ne peut
    // pas avoir de jours attendus le mois dernier.
    if ($createdAt) {
        $user->forceFill(['created_at' => $createdAt])->save();
        $personnel->forceFill(['date_debut_pointage' => $createdAt])->save();
    }

    return $user;
}

/**
 * Cree l'Etudiant ET le lien polymorphe personnels.personnable_id, sans lequel
 * la relation Etudiant->user (hasOneThrough) ne resout rien : AdminPresenceService
 * ne verrait alors aucun stage actif et tous les jours seraient exclus.
 */
function makeEtudiant(User $studentUser): Etudiant
{
    $etudiant = Etudiant::create([
        'personnel_id' => $studentUser->personnel_id,
        'ecole'        => 'Universite Test',
    ]);

    Personnel::where('id', $studentUser->personnel_id)->update([
        'personnable_type' => Etudiant::class,
        'personnable_id'   => $etudiant->id,
    ]);

    return $etudiant;
}

/** Un stage terminé, pour que la fenêtre du rapport soit stable dans le temps. */
function finishedStage(User $studentUser): Stage
{
    $etudiant = makeEtudiant($studentUser);

    return Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => 'AC' . Str::random(3), 'libelle' => 'Academique'])->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Refonte du systeme de pointage',
        'date_debut'   => now()->subMonths(2)->startOfMonth()->toDateString(),
        'date_fin'     => now()->subMonth()->endOfMonth()->toDateString(),
    ]);
}

test('a ratio stays a bare fraction below the reliability threshold', function () {
    // 4 jours au dénominateur : la fraction s'affiche, pas le pourcentage
    $low = $this->service->ratio(4, 4);
    expect($low['numerator'])->toBe(4)
        ->and($low['denominator'])->toBe(4)
        ->and($low['rate'])->toBeNull()
        ->and($low['reliable'])->toBeFalse();

    $ok = $this->service->ratio(4, 5);
    expect($ok['rate'])->toBe(0.8)
        ->and($ok['reliable'])->toBeTrue();
});

test('a zero denominator never divides by zero', function () {
    $empty = $this->service->ratio(0, 0);
    expect($empty['rate'])->toBeNull()
        ->and($empty['reliable'])->toBeFalse();
});

test('punctuality is measured against attended days, not expected days', function () {
    $user  = reportUser('etudiant', now()->subMonths(3)->toDateString());
    $stage = finishedStage($user);

    // 6 jours pointés, dont 2 en retard — sur une période qui en attendait bien plus
    $date = \Carbon\Carbon::parse($stage->date_debut);
    $made = 0;
    while ($made < 6) {
        if (!$date->isWeekend()) {
            AttendanceDay::create([
                'stage_id'          => $stage->id,
                'etudiant_id'       => $stage->etudiant_id,
                'user_id'           => $user->id,
                'attendance_date'   => $date->toDateString(),
                'first_check_in_at' => $date->copy()->setTime(8, 0),
                'last_check_out_at' => $date->copy()->setTime(17, 0),
                'arrival_status'    => $made < 2 ? 'late' : 'on_time',
                'worked_minutes'    => 540,
            ]);
            $made++;
        }
        $date->addDay();
    }

    $report = $this->service->build($stage);

    // Le dénominateur de la ponctualité est le nombre de jours pointés, pas attendus
    expect($report['ratios']['ponctualite']['denominator'])->toBe(6)
        ->and($report['ratios']['ponctualite']['numerator'])->toBe(4)
        ->and($report['counts']['checked_in_days'])->toBe(6);

    // L'assiduité, elle, se mesure bien sur les jours attendus — bien plus nombreux
    expect($report['ratios']['assiduite']['denominator'])
        ->toBeGreaterThan($report['ratios']['ponctualite']['denominator']);
});

test('incomplete days and early departures are counted separately', function () {
    $user  = reportUser('etudiant', now()->subMonths(3)->toDateString());
    $stage = finishedStage($user);

    $date = \Carbon\Carbon::parse($stage->date_debut);
    while ($date->isWeekend()) $date->addDay();

    // Jour 1 : complet et sans départ anticipé
    AttendanceDay::create([
        'stage_id'                => $stage->id,
        'etudiant_id'             => $stage->etudiant_id,
        'user_id'                 => $user->id,
        'attendance_date'         => $date->toDateString(),
        'first_check_in_at'       => $date->copy()->setTime(8, 0),
        'last_check_out_at'       => $date->copy()->setTime(17, 0),
        'arrival_status'          => 'on_time',
        'early_departure_minutes' => 0,
    ]);

    // Jour 2 : oubli de pointer la sortie, et parti en avance
    $next = $date->copy()->addDay();
    while ($next->isWeekend()) $next->addDay();

    AttendanceDay::create([
        'stage_id'                => $stage->id,
        'etudiant_id'             => $stage->etudiant_id,
        'user_id'                 => $user->id,
        'attendance_date'         => $next->toDateString(),
        'first_check_in_at'       => $next->copy()->setTime(8, 0),
        'last_check_out_at'       => null,
        'arrival_status'          => 'on_time',
        'early_departure_minutes' => 45,
    ]);

    $report = $this->service->build($stage);

    expect($report['ratios']['journees_completes']['numerator'])->toBe(1)
        ->and($report['ratios']['journees_completes']['denominator'])->toBe(2)
        ->and($report['ratios']['tenue_poste']['numerator'])->toBe(1);
});

test('the report counts anomalies over the whole stage window', function () {
    $user  = reportUser('etudiant', now()->subMonths(3)->toDateString());
    $stage = finishedStage($user);

    AttendanceAnomaly::create([
        'stage_id'     => $stage->id,
        'etudiant_id'  => $stage->etudiant_id,
        'user_id'      => $user->id,
        'anomaly_type' => 'outside_geofence',
        'severity'     => 'medium',
        'status'       => 'open',
        'detected_at'  => \Carbon\Carbon::parse($stage->date_debut)->addDays(3),
    ]);

    AttendanceAnomaly::create([
        'stage_id'     => $stage->id,
        'etudiant_id'  => $stage->etudiant_id,
        'user_id'      => $user->id,
        'anomaly_type' => 'outside_geofence',
        'severity'     => 'low',
        'status'       => 'resolved',
        'detected_at'  => \Carbon\Carbon::parse($stage->date_debut)->addDays(5),
    ]);

    $report = $this->service->build($stage);

    expect($report['anomalies']['total'])->toBe(2)
        ->and($report['anomalies']['open'])->toBe(1)
        ->and($report['anomalies']['resolved'])->toBe(1)
        ->and($report['anomalies']['by_type']['outside_geofence'])->toBe(2);
});

test('hourly volume is hidden when the stage has no declared schedule', function () {
    $user  = reportUser();
    $stage = finishedStage($user);

    expect($stage->expected_check_in_time)->toBeNull();
    expect($this->service->build($stage)['ratios']['volume_horaire'])->toBeNull();
});

test('an ongoing stage is only judged on elapsed days', function () {
    $user     = reportUser('etudiant', now()->subMonths(2)->toDateString());
    $etudiant = makeEtudiant($user);

    $stage = Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => 'AC' . Str::random(3), 'libelle' => 'Academique'])->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Stage en cours',
        'date_debut'   => now()->subWeeks(2)->toDateString(),
        'date_fin'     => now()->addMonths(3)->toDateString(),
    ]);

    $report = $this->service->build($stage);

    expect($report['window']['is_ongoing'])->toBeTrue()
        ->and($report['window']['effective_to']->toDateString())->toBe(today()->toDateString());

    // Deux semaines ecoulees : un denominateur reel, mais les trois mois a
    // venir ne doivent pas y figurer.
    expect($report['counts']['expected_days'])->toBeGreaterThan(0)
        ->and($report['counts']['expected_days'])->toBeLessThan(20);
});

test('an admin can open the report and the default anomaly level hides the details', function () {
    $admin = reportUser('admin');
    $user  = reportUser();
    $stage = finishedStage($user);

    AttendanceAnomaly::create([
        'stage_id'     => $stage->id,
        'etudiant_id'  => $stage->etudiant_id,
        'user_id'      => $user->id,
        'anomaly_type' => 'shared_device_detected',
        'severity'     => 'medium',
        'status'       => 'open',
        'detected_at'  => \Carbon\Carbon::parse($stage->date_debut)->addDays(2),
    ]);

    $response = $this->actingAs($admin)->get(route('stages.rapport', $stage));

    $response->assertOk()
        ->assertViewIs('admin.stages.rapport')
        ->assertSee('Refonte du systeme de pointage')
        ->assertSee('Assiduité', false);

    // Le niveau par défaut ne nomme pas les faits mis en cause
    $response->assertDontSee("Téléphone d'un collègue utilisé");
});

test('the detailed anomaly level names the facts only when explicitly asked', function () {
    $admin = reportUser('admin');
    $user  = reportUser();
    $stage = finishedStage($user);

    AttendanceAnomaly::create([
        'stage_id'     => $stage->id,
        'etudiant_id'  => $stage->etudiant_id,
        'user_id'      => $user->id,
        'anomaly_type' => 'shared_device_detected',
        'severity'     => 'medium',
        'status'       => 'open',
        'detected_at'  => \Carbon\Carbon::parse($stage->date_debut)->addDays(2),
    ]);

    $this->actingAs($admin)
        ->get(route('stages.rapport', $stage) . '?anomalies=detailed')
        ->assertOk()
        ->assertSee("Téléphone d'un collègue utilisé");
});

test('a student cannot open the report before finalisation exists', function () {
    $student = reportUser();
    $stage   = finishedStage($student);

    $response = $this->actingAs($student)->get(route('stages.rapport', $stage));

    // Deux couches le bloquent : le middleware de pointage quotidien, puis
    // l'autorisation du controleur. Le code exact depend de celle qui intervient
    // en premier — la propriete qui compte est qu'il n'obtienne pas le rapport.
    expect($response->status())->not->toBe(200);
    $response->assertDontSee('Refonte du systeme de pointage');
});
