<?php

use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\StudentStageExpiryService;

function createExpiredStageFor(User $user, array $dates = []): Stage
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
        'date_debut' => $dates['date_debut'] ?? now()->subMonths(2),
        'date_fin' => $dates['date_fin'] ?? now()->subWeek(),
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

test('le service désactive le compte d\'un étudiant dont tous les stages sont terminés', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(1);
    expect($user->fresh()->status)->toBe('inactif');
});

test('un étudiant avec un stage en cours n\'est pas désactivé', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user, [
        'date_debut' => now()->subWeek(),
        'date_fin' => now()->addWeek(),
    ]);

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(0);
    expect($user->fresh()->status)->toBe('actif');
});

test('un étudiant avec un stage à venir n\'est pas désactivé', function () {
    $user = createAttendanceGateUser('etudiant');
    createActiveStageFor($user);
    $user->etudiant->stages()
        ->latest('date_fin')
        ->first()
        ->update(['date_debut' => now()->addWeek(), 'date_fin' => now()->addMonth()]);

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(0);
    expect($user->fresh()->status)->toBe('actif');
});

test('un étudiant sans aucun stage n\'est pas désactivé', function () {
    $user = createAttendanceGateUser('etudiant');

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(0);
    expect($user->fresh()->status)->toBe('actif');
});

test('un étudiant avec un stage actif et un stage ancien n\'est pas désactivé', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);
    createExpiredStageFor($user, [
        'date_debut' => now()->subWeek(),
        'date_fin' => now()->addWeek(),
    ]);

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(0);
    expect($user->fresh()->status)->toBe('actif');
});

test('les comptes non-étudiants ne sont jamais touchés par la désactivation', function () {
    $adminUser = createAttendanceGateUser('admin');
    $employeUser = createAttendanceGateUser('employe');

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(0);
    expect($adminUser->fresh()->status)->toBe('actif');
    expect($employeUser->fresh()->status)->toBe('actif');
});

test('un compte déjà désactivé n\'est pas re-compté', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);
    $user->update(['status' => 'inactif']);

    $count = app(StudentStageExpiryService::class)->deactivateExpiredAccounts();

    expect($count)->toBe(0);
});

test('la commande students:deactivate-expired désactive les comptes à stages terminés', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);

    $this->artisan('students:deactivate-expired')
        ->expectsOutputToContain('1 compte(s) désactivé(s).')
        ->assertExitCode(0);

    expect($user->fresh()->status)->toBe('inactif');
});

test('la commande en dry-run ne désactive rien', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);

    $this->artisan('students:deactivate-expired --dry-run')
        ->expectsOutputToContain('1 compte(s) seraient désactivés.')
        ->assertExitCode(0);

    expect($user->fresh()->status)->toBe('actif');
});

test('un stagiaire à stage terminé est déconnecté et désactivé à la volée', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', fn($value) => str_contains($value, 'stage est terminé'));

    expect($user->fresh()->status)->toBe('inactif');
});

test('un compte inactif est déconnecté à la volée', function () {
    $user = createAttendanceGateUser('etudiant');
    createActiveStageFor($user);
    $user->update(['status' => 'inactif']);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Votre compte a été désactivé.');
});

test('un stagiaire à stage actif garde l\'accès', function () {
    $user = createAttendanceGateUser('etudiant');
    createActiveStageFor($user);
    markCheckedInToday($user);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('student.stage'));
});

test('un étudiant désactivé ne peut plus se connecter', function () {
    $user = createAttendanceGateUser('etudiant');
    createExpiredStageFor($user);
    $user->update(['status' => 'inactif']);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');
});