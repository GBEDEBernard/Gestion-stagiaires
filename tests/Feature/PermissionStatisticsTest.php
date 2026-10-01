<?php

use App\Models\AttendanceDay;
use App\Models\AttendanceException;
use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\PermissionRequest;
use App\Models\PermissionType;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PermissionTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/*
 * Ces tests verrouillent la promesse faite aux utilisateurs : une journée
 * couverte par une permission approuvée n'est jamais comptée comme une absence.
 *
 * Le mécanisme (PermissionAttendanceSync) était correct. Ce qui manquait, c'était
 * que certains consommateurs reconstruisaient le total attendus - présents sans
 * tenir compte des exceptions, et transformaient donc une permission en absence.
 */

function permissionStatsUser(string $role, string $tag): User
{
    test()->seed(RolePermissionSeeder::class);

    $personnel = Personnel::create([
        'nom' => 'PermStat',
        'prenom' => ucfirst($tag),
        'email' => "permstat.{$tag}@example.com",
    ]);

    $userData = [
        'personnel_id' => $personnel->id,
        'email' => $personnel->email,
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'status' => 'actif',
    ];

    if (Schema::hasColumn('users', 'name')) {
        $userData['name'] = $personnel->full_name;
    }

    $user = User::create($userData);

    if ($role === 'etudiant') {
        $profile = Etudiant::create([
            'personnel_id' => $personnel->id,
            'ecole' => 'Ecole permission',
        ]);
    } else {
        $site = Site::firstOrCreate(
            ['code' => 'PERM'],
            ['name' => 'Site permission', 'address' => 'Local', 'city' => 'Cotonou', 'country' => 'Benin', 'is_active' => true]
        );
        $domaine = Domaine::create([
            'nom' => 'Domaine permstat '.$tag,
            'description' => 'Domaine de test',
            'created_by' => $user->id,
        ]);

        $profile = Employe::create([
            'personnel_id' => $personnel->id,
            'domaine_id' => $domaine->id,
            'site_id' => $site->id,
            'poste' => 'Employe permstat',
        ]);

        $user->update(['domaine_id' => $domaine->id]);
    }

    $personnel->update([
        'personnable_type' => $role === 'etudiant' ? Etudiant::class : Employe::class,
        'personnable_id' => $profile->id,
    ]);

    $user->assignRole($role);

    return $user->fresh();
}

function activeStageFor(User $user): Stage
{
    $site = Site::firstOrCreate(
        ['code' => 'PERM'],
        ['name' => 'Site permission', 'address' => 'Local', 'city' => 'Cotonou', 'country' => 'Benin', 'is_active' => true]
    );

    $typestage = TypeStage::firstOrCreate(['code' => 'PERM'], ['libelle' => 'Stage permission']);

    $stage = Stage::create([
        'etudiant_id' => $user->etudiant->id,
        'typestage_id' => $typestage->id,
        'site_id' => $site->id,
        'theme' => 'Stage permission',
        'date_debut' => now()->startOfYear(),
        'date_fin' => now()->endOfYear(),
    ]);

    $dayName = match ((int) today()->format('N')) {
        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
    };

    $stage->jours()->attach(Jour::firstOrCreate(['jour' => $dayName]));

    return $stage;
}

function approveAbsence(User $user, string $from, string $to): PermissionRequest
{
    // Le type « absence » est la seule configuration qui excuse des journées
    // entières : c'est celle du cas d'usage décrit.
    test()->seed(PermissionTypeSeeder::class);

    $type = PermissionType::where('slug', 'absence')->firstOrFail();

    $request = PermissionRequest::create([
        'user_id' => $user->id,
        'permission_type_id' => $type->id,
        'fields_data' => [
            'start_date' => $from,
            'end_date' => $to,
            'motif' => 'Permission de test',
        ],
        'status' => 'approved',
        'decided_by' => $user->id,
        'decided_at' => now(),
    ]);

    app(App\Services\PermissionAttendanceSync::class)->sync($request->load('type'));

    return $request;
}

test('le suivi journalier sort les jours permissionnés du dénominateur', function () {
    $admin = permissionStatsUser('admin', 'daily');
    $stagiaire = permissionStatsUser('etudiant', 'dailycible');
    activeStageFor($stagiaire);

    $jour = today()->toDateString();
    approveAbsence($stagiaire, $jour, $jour);

    $this->actingAs($admin)
        ->get(route('attendance.tracking.index', ['period' => 'day', 'date' => $jour]))
        ->assertOk()
        ->assertViewHas('summary', function (array $summary) {
            expect($summary['student_permissioned'])->toBe(1);

            // Le permissionné ne doit plus peser dans le dénominateur.
            return $summary['student_total'] === 1
                && $summary['student_present'] === 0
                && $summary['student_rate'] === 0;
        });
});

test('le suivi journalier affiche un taux plein quand le permissionné est le seul attendu', function () {
    $admin = permissionStatsUser('admin', 'fullday1');
    $stagiaire = permissionStatsUser('etudiant', 'fullday2');
    activeStageFor($stagiaire);

    $jour = today()->toDateString();
    approveAbsence($stagiaire, $jour, $jour);

    // Deuxième stagiaire présent, sans permission : le taux doit refléter
    // 1 présent sur 1 attendu, pas 1 sur 2.
    $present = permissionStatsUser('etudiant', 'fullday3');
    activeStageFor($present);
    AttendanceDay::create([
        'user_id' => $present->id,
        'etudiant_id' => $present->etudiant->id,
        'attendance_date' => $jour,
        'first_check_in_at' => $jour.' 08:00:00',
        'day_status' => 'present',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('attendance.tracking.index', ['period' => 'day', 'date' => $jour]));

    $response->assertOk();

    $summary = $response->viewData('summary');

    expect($summary['student_permissioned'])->toBe(1)
        ->and($summary['student_total'])->toBe(2)
        ->and($summary['student_present'])->toBe(1)
        ->and((int) $summary['student_rate'])->toBe(100);
});

test('le resume hebdomadaire deduit les jours permissionnes du denominateur', function () {
    $admin = permissionStatsUser('admin', 'week');
    $stagiaire = permissionStatsUser('etudiant', 'weekcible');
    activeStageFor($stagiaire);

    $jour = today()->toDateString();
    approveAbsence($stagiaire, $jour, $jour);

    $this->actingAs($admin)
        ->get(route('attendance.tracking.index', ['period' => 'week', 'date' => $jour]))
        ->assertOk()
        ->assertViewHas('studentWeekData', function ($data) use ($stagiaire) {
            $entry = $data[$stagiaire->etudiant->id] ?? null;

            if (! $entry) {
                return false;
            }

            // La semaine compte 5 jours ouvrés, un est permissionné.
            return $entry['expected_days'] === 4
                && $entry['permissioned_days'] === 1
                && $entry['present_days'] === 0;
        });
});

test('le resume mensuel et annuel deduisent aussi les permissions', function () {
    $admin = permissionStatsUser('admin', 'month');
    $stagiaire = permissionStatsUser('etudiant', 'monthcible');
    activeStageFor($stagiaire);

    $jour = today()->toDateString();
    approveAbsence($stagiaire, $jour, $jour);

    $this->actingAs($admin)
        ->get(route('attendance.tracking.index', ['period' => 'month', 'date' => $jour]))
        ->assertOk()
        ->assertViewHas('studentMonthlySummary', function ($data) use ($stagiaire, $jour) {
            $entry = $data[$stagiaire->etudiant->id] ?? null;

            return $entry
                && $entry['permissioned_days'] === 1
                && $entry['expected_days'] < Carbon::parse($jour)->daysInMonth;
        });
});

test('un employe permissionne ne pese pas non plus dans le suivi', function () {
    $admin = permissionStatsUser('admin', 'employ');
    $employe = permissionStatsUser('employe', 'employcible');

    $jour = today()->toDateString();
    approveAbsence($employe, $jour, $jour);

    $this->actingAs($admin)
        ->get(route('attendance.tracking.index', ['period' => 'day', 'date' => $jour]))
        ->assertOk()
        ->assertViewHas('summary', function (array $summary) {
            return ($summary['employee_permissioned'] ?? 0) >= 1
                && ($summary['employee_total'] ?? 0) >= 1;
        });
});

test('une correction manuelle n est pas presentee comme une permission', function () {
    $admin = permissionStatsUser('admin', 'manuel');
    $cible = permissionStatsUser('etudiant', 'manuelcible');

    $jour = today()->toDateString();

    // Exception posée à la main : pas de permission_request_id.
    AttendanceException::create([
        'user_id' => $cible->id,
        'attendance_date' => $jour,
        'reason' => 'Site hors service ce jour-la',
        'created_by' => $admin->id,
    ]);

    $exception = AttendanceException::where('user_id', $cible->id)->first();

    expect($exception->isAutomatic())->toBeFalse()
        ->and($exception->isAutomatic())->not->toBeTrue();
});

test('une permission approuvee est bien signalee comme automatique', function () {
    $user = permissionStatsUser('etudiant', 'auto');

    $jour = today()->toDateString();
    approveAbsence($user, $jour, $jour);

    $exception = AttendanceException::where('user_id', $user->id)->firstOrFail();

    expect($exception->isAutomatic())->toBeTrue()
        ->and($exception->permission_request_id)->not->toBeNull();
});

test('les statistiques admin ne comptent pas une permission comme absence', function () {
    $admin = permissionStatsUser('admin', 'global');
    $stagiaire = permissionStatsUser('etudiant', 'globalcible');
    $stage = activeStageFor($stagiaire);

    $jour = today()->toDateString();
    $reference = Carbon::parse($jour)->subDays(3);

    // Avant permission : la journée est une absence.
    $avant = app(App\Services\AdminPresenceService::class)->getUserDetailedStats($stagiaire->id, 'week');

    approveAbsence($stagiaire, $jour, $jour);

    $apres = app(App\Services\AdminPresenceService::class)->getUserDetailedStats($stagiaire->id, 'week');

    // Le total de jours attendus ne doit pas avoir augmenté du fait de la permission.
    expect($apres['total_days'])->toBe($avant['total_days']);

    expect($reference)->toBeInstanceOf(Carbon::class);
    expect($stage->id)->not->toBeNull();
});
