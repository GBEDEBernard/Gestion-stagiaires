<?php

use App\Models\AttendanceDay;
use App\Models\AttendanceException;
use App\Models\Holiday;
use App\Models\Personnel;
use App\Models\PermissionRequest;
use App\Models\PermissionType;
use App\Models\User;
use App\Services\PermissionAttendanceSync;
use Database\Seeders\PermissionTypeSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(PermissionTypeSeeder::class);
    $this->sync = app(PermissionAttendanceSync::class);
});

function syncTestUser(): User
{
    $personnel = Personnel::create([
        'nom'    => 'Sync',
        'prenom' => 'Test',
        'email'  => 'sync.' . Str::random(6) . '@example.com',
    ]);

    return User::create([
        'personnel_id'      => $personnel->id,
        'name'              => $personnel->full_name,
        'email'             => $personnel->email,
        'email_verified_at' => now(),
        'password'          => Hash::make('password'),
        'status'            => 'actif',
    ]);
}

function approvedPermission(User $user, string $slug, array $fields, ?User $decider = null): PermissionRequest
{
    $type = PermissionType::where('slug', $slug)->firstOrFail();

    return PermissionRequest::create([
        'user_id'            => $user->id,
        'permission_type_id' => $type->id,
        'fields_data'        => $fields,
        'status'             => 'approved',
        'decided_by'         => $decider?->id ?? $user->id,
        'decided_at'         => now(),
    ]);
}

test('an approved absence excuses every working day it covers', function () {
    $user = syncTestUser();

    // Lundi 7 au mercredi 9 septembre 2026
    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-07',
        'end_date'   => '2026-09-09',
        'motif'      => 'Hospitalisation',
    ]);

    $report = $this->sync->sync($permission->load('type'));

    expect($report['created'])->toBe(3);

    foreach (['2026-09-07', '2026-09-08', '2026-09-09'] as $date) {
        expect(
            AttendanceException::where('user_id', $user->id)
                ->where('permission_request_id', $permission->id)
                ->whereDate('attendance_date', $date)
                ->exists()
        )->toBeTrue("Le {$date} aurait du etre excuse");
    }

    // Le motif saisi doit rester lisible par l'administrateur
    expect(AttendanceException::first()->reason)->toContain('Hospitalisation');
});

test('a late arrival permission never excuses the day', function () {
    $user = syncTestUser();

    $permission = approvedPermission($user, 'retard', [
        'date'       => '2026-09-07',
        'start_time' => '08:00',
        'end_time'   => '09:30',
        'motif'      => 'Embouteillage',
    ]);

    $report = $this->sync->sync($permission->load('type'));

    expect($report['created'])->toBe(0);
    expect(AttendanceException::count())->toBe(0);
});

test('an early departure permission never excuses the day', function () {
    $user = syncTestUser();

    $permission = approvedPermission($user, 'depart-anticipe', [
        'date'           => '2026-09-07',
        'departure_time' => '15:00',
        'motif'          => 'Rendez-vous medical',
    ]);

    expect($this->sync->sync($permission->load('type'))['created'])->toBe(0);
    expect(AttendanceException::count())->toBe(0);
});

test('weekends and active holidays inside the range are skipped', function () {
    $user = syncTestUser();

    // 11 septembre 2026 = vendredi ; 12-13 = week-end ; 14 = lundi férié
    Holiday::create([
        'date'      => '2026-09-14',
        'label'     => 'Fete nationale test',
        'is_active' => true,
    ]);

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-11',
        'end_date'   => '2026-09-15',
        'motif'      => 'Deces dans la famille',
    ]);

    $report = $this->sync->sync($permission->load('type'));

    expect($report['skipped_weekend'])->toBe(2)
        ->and($report['skipped_holiday'])->toBe(1)
        ->and($report['created'])->toBe(2); // vendredi 11 et mardi 15

    expect(AttendanceException::whereDate('attendance_date', '2026-09-14')->exists())->toBeFalse();
});

test('a day the person actually attended is never excused', function () {
    $user = syncTestUser();

    // Il a demandé deux jours mais est finalement venu le premier
    AttendanceDay::create([
        'user_id'           => $user->id,
        'attendance_date'   => '2026-09-07',
        'first_check_in_at' => '2026-09-07 08:05:00',
    ]);

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-07',
        'end_date'   => '2026-09-08',
        'motif'      => 'Demarches administratives',
    ]);

    $report = $this->sync->sync($permission->load('type'));

    expect($report['skipped_present'])->toBe(1)
        ->and($report['created'])->toBe(1);

    // Excuser un jour de présence le retirerait du dénominateur ET du numérateur
    expect(AttendanceException::whereDate('attendance_date', '2026-09-07')->exists())->toBeFalse()
        ->and(AttendanceException::whereDate('attendance_date', '2026-09-08')->exists())->toBeTrue();
});

test('a manual exception is never overwritten', function () {
    $user  = syncTestUser();
    $admin = syncTestUser();

    AttendanceException::create([
        'user_id'         => $user->id,
        'attendance_date' => '2026-09-07',
        'reason'          => 'Correction manuelle du responsable',
        'created_by'      => $admin->id,
    ]);

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-07',
        'end_date'   => '2026-09-07',
        'motif'      => 'Autre motif',
    ]);

    $report = $this->sync->sync($permission->load('type'));

    expect($report['skipped_existing'])->toBe(1)
        ->and($report['created'])->toBe(0);

    $existing = AttendanceException::whereDate('attendance_date', '2026-09-07')->first();
    expect($existing->reason)->toBe('Correction manuelle du responsable')
        ->and($existing->permission_request_id)->toBeNull();
});

test('syncing twice does not duplicate excused days', function () {
    $user = syncTestUser();

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-07',
        'end_date'   => '2026-09-08',
        'motif'      => 'Motif',
    ])->load('type');

    $this->sync->sync($permission);
    $second = $this->sync->sync($permission);

    expect($second['created'])->toBe(0)
        ->and($second['skipped_existing'])->toBe(2)
        ->and(AttendanceException::count())->toBe(2);
});

test('reversed dates are reordered instead of producing an empty range', function () {
    $user = syncTestUser();

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-09',
        'end_date'   => '2026-09-07',
        'motif'      => 'Saisie inversee',
    ]);

    expect($this->sync->sync($permission->load('type'))['created'])->toBe(3);
});

test('a pending permission excuses nothing', function () {
    $user = syncTestUser();
    $type = PermissionType::where('slug', 'absence')->first();

    $permission = PermissionRequest::create([
        'user_id'            => $user->id,
        'permission_type_id' => $type->id,
        'fields_data'        => ['start_date' => '2026-09-07', 'end_date' => '2026-09-08'],
        'status'             => 'pending',
    ]);

    expect($this->sync->sync($permission->load('type'))['created'])->toBe(0);
    expect(AttendanceException::count())->toBe(0);
});

test('removing a permission frees its days but leaves manual ones alone', function () {
    $user  = syncTestUser();
    $admin = syncTestUser();

    AttendanceException::create([
        'user_id'         => $user->id,
        'attendance_date' => '2026-09-15',
        'reason'          => 'Correction manuelle',
        'created_by'      => $admin->id,
    ]);

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '2026-09-07',
        'end_date'   => '2026-09-08',
        'motif'      => 'Motif',
    ])->load('type');

    $this->sync->sync($permission);
    expect(AttendanceException::count())->toBe(3);

    $removed = $this->sync->remove($permission);

    expect($removed)->toBe(2)
        ->and(AttendanceException::count())->toBe(1);

    expect(
        AttendanceException::whereDate('attendance_date', '2026-09-15')
            ->where('reason', 'Correction manuelle')
            ->exists()
    )->toBeTrue();
});

test('malformed dates are ignored without crashing', function () {
    $user = syncTestUser();

    $permission = approvedPermission($user, 'absence', [
        'start_date' => '',
        'end_date'   => '2026-09-08',
        'motif'      => 'Motif',
    ]);

    expect($this->sync->sync($permission->load('type'))['created'])->toBe(0);
    expect(AttendanceException::count())->toBe(0);
});
