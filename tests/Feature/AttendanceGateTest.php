<?php

use App\Models\AttendanceDay;
use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\Holiday;
use App\Models\HolidayEmergencyExemption;
use App\Models\Jour;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

function createAttendanceGateUser(string $role): User
{
    test()->seed(RolePermissionSeeder::class);

    $personnel = Personnel::create([
        'nom' => ucfirst($role),
        'prenom' => 'Gate',
        'email' => "{$role}.gate@example.com",
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
            'ecole' => 'Ecole gate',
        ]);
    } else {
        $site = Site::firstOrCreate(
            ['code' => 'GATE'],
            ['name' => 'Site gate', 'address' => 'Local', 'city' => 'Cotonou', 'country' => 'Benin', 'is_active' => true]
        );
        $domaine = Domaine::create([
            'nom' => 'Domaine gate',
            'description' => 'Domaine de test',
            'created_by' => $user->id,
        ]);

        $profile = Employe::create([
            'personnel_id' => $personnel->id,
            'domaine_id' => $domaine->id,
            'site_id' => $site->id,
            'poste' => 'Employe gate',
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

function createActiveStageFor(User $user): Stage
{
    $site = Site::firstOrCreate(
        ['code' => 'GATE'],
        ['name' => 'Site gate', 'address' => 'Local', 'city' => 'Cotonou', 'country' => 'Benin', 'is_active' => true]
    );

    $typestage = TypeStage::firstOrCreate(['code' => 'GATE'], ['libelle' => 'Stage gate']);

    $stage = Stage::create([
        'etudiant_id' => $user->etudiant->id,
        'typestage_id' => $typestage->id,
        'site_id' => $site->id,
        'theme' => 'Stage gate',
        'date_debut' => now()->subWeek(),
        'date_fin' => now()->addWeek(),
    ]);

    $dayName = match ((int) today()->format('N')) {
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    };

    $jour = Jour::firstOrCreate(['jour' => $dayName]);
    $stage->jours()->attach($jour);

    return $stage;
}

function markCheckedInToday(User $user): void
{
    AttendanceDay::create([
        'user_id' => $user->id,
        'etudiant_id' => $user->etudiant?->id,
        'stage_id' => $user->etudiant?->stages()->whereDate('date_debut', '<=', now())->whereDate('date_fin', '>=', now())->first()?->id,
        'attendance_date' => today()->toDateString(),
        'first_check_in_at' => now(),
        'day_status' => 'present',
        'validation_status' => 'auto_approuve',
    ]);
}

test('étudiant sans pointage du jour est redirigé vers le pointage', function () {
    $user = createAttendanceGateUser('etudiant');
    createActiveStageFor($user);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('presence.pointage'));
});

test('étudiant avec pointage du jour accède librement au site', function () {
    $user = createAttendanceGateUser('etudiant');
    createActiveStageFor($user);
    markCheckedInToday($user);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('student.stage'));
});

test('employé sans pointage du jour est redirigé vers le pointage', function () {
    $user = createAttendanceGateUser('employe');

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('presence.pointage'));
});

test('employé avec pointage du jour accède librement au site', function () {
    $user = createAttendanceGateUser('employe');
    markCheckedInToday($user);

    $this->actingAs($user)->get('/dashboard')
        ->assertOk();
});

test('admin accède librement sans pointage', function () {
    $user = createAttendanceGateUser('admin');

    $this->actingAs($user)->get('/dashboard')
        ->assertOk();
});

test('superviseur accède librement sans pointage', function () {
    $user = createAttendanceGateUser('superviseur');

    $this->actingAs($user)->get('/dashboard')
        ->assertOk();
});

test('étudiant sans stage actif n\'est pas bloqué', function () {
    $user = createAttendanceGateUser('etudiant');

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('student.stage'));
});

test('jour férié actif : employé non appelé est bloqué (redirigé vers pointage)', function () {
    Holiday::create([
        'date' => today()->toDateString(),
        'label' => 'Férié gate',
        'is_active' => true,
    ]);

    $user = createAttendanceGateUser('employe');

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('presence.pointage'));
});

test('jour férié actif : employé appelé en urgence peut accéder après pointage', function () {
    $holiday = Holiday::create([
        'date' => today()->toDateString(),
        'label' => 'Férié gate',
        'is_active' => true,
    ]);

    $user = createAttendanceGateUser('employe');

    HolidayEmergencyExemption::create([
        'holiday_id' => $holiday->id,
        'user_id' => $user->id,
        'message' => 'Urgence',
    ]);

    markCheckedInToday($user);

    $this->actingAs($user)->get('/dashboard')
        ->assertOk();
});

test('jour férié actif : admin garde l\'accès libre', function () {
    Holiday::create([
        'date' => today()->toDateString(),
        'label' => 'Férié gate',
        'is_active' => true,
    ]);

    $user = createAttendanceGateUser('admin');

    $this->actingAs($user)->get('/dashboard')
        ->assertOk();
});

test('les routes du flux de pointage restent accessibles sans pointage', function () {
    $user = createAttendanceGateUser('etudiant');

    $this->actingAs($user)->get(route('presence.pointage'))
        ->assertOk();
});
