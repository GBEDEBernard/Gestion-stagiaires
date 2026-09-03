<?php

use App\Models\AttendanceDay;
use App\Models\AttendanceException;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

function createAttendanceExceptionUser(string $role): User
{
    test()->seed(RolePermissionSeeder::class);

    $personnel = Personnel::create([
        'nom' => 'Exception',
        'prenom' => ucfirst($role),
        'email' => "exception.{$role}@example.com",
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
    $user->assignRole($role);

    return $user;
}

test('un admin peut corriger et annuler une absence', function () {
    $admin = createAttendanceExceptionUser('admin');

    $target = User::create([
        'name' => 'Cible Exception',
        'email' => 'cible.exception@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'status' => 'actif',
    ]);

    $this->actingAs($admin);

    $response = $this->post(route('attendance.tracking.user.exception.store', $target), [
        'attendance_date' => '2026-08-05',
        'reason' => 'Site pas encore en ligne',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('attendance_exceptions', [
        'user_id' => $target->id,
        'attendance_date' => '2026-08-05',
        'reason' => 'Site pas encore en ligne',
        'created_by' => $admin->id,
    ]);

    $exception = AttendanceException::where('user_id', $target->id)->first();

    $this->delete(route('attendance.tracking.user.exception.destroy', [$target, $exception]))
        ->assertRedirect();

    $this->assertDatabaseMissing('attendance_exceptions', ['id' => $exception->id]);
});

test('un jour avec pointage existant ne peut pas être corrigé', function () {
    $admin = createAttendanceExceptionUser('admin');

    $target = User::create([
        'name' => 'Cible Présent',
        'email' => 'cible.present@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'status' => 'actif',
    ]);

    AttendanceDay::create([
        'user_id' => $target->id,
        'attendance_date' => '2026-08-05',
        'first_check_in_at' => '2026-08-05 08:00:00',
    ]);

    $this->actingAs($admin);

    $this->post(route('attendance.tracking.user.exception.store', $target), [
        'attendance_date' => '2026-08-05',
    ])->assertSessionHasErrors('attendance_date');

    $this->assertDatabaseCount('attendance_exceptions', 0);
});

test('un non-admin ne peut pas corriger une absence', function () {
    $employe = createAttendanceExceptionUser('employe');

    $target = User::create([
        'name' => 'Cible2',
        'email' => 'cible2.exception@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'status' => 'actif',
    ]);

    $this->actingAs($employe);

    $this->post(route('attendance.tracking.user.exception.store', $target), [
        'attendance_date' => '2026-08-05',
    ])->assertForbidden();

    $this->assertDatabaseCount('attendance_exceptions', 0);
});

test('le détail des stats utilisateur ignore les jours corrigés', function () {
    $user = createAttendanceExceptionUser('employe');

    AttendanceException::create([
        'user_id' => $user->id,
        'attendance_date' => '2026-08-05',
        'reason' => 'test',
        'created_by' => $user->id,
    ]);

    $svc = app(\App\Services\AdminPresenceService::class);
    $stats = $svc->getUserDetailedStats($user->id, 'month', '2026-08-01', '2026-08-31');

    $dates = $stats['chart_data']['dates'];
    $absences = $stats['chart_data']['absences'];

    $index = array_search('2026-08-05', $dates);
    expect($index)->not->toBeFalse()
        ->and($absences[$index])->toBe(0);
});

test('la page historique affiche l interface de correction et son script', function () {
    $admin = createAttendanceExceptionUser('admin');

    $target = User::create([
        'name' => 'Cible Page',
        'email' => 'cible.page@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'status' => 'actif',
    ]);

    $this->actingAs($admin);

    $this->get(route('attendance.tracking.user.historique', $target))
        ->assertOk()
        ->assertSee('Corriger les absences')
        ->assertSee('openCorrection')
        ->assertSee('/exceptions');
});
