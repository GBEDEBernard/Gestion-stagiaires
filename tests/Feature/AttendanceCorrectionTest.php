<?php

use App\Models\AttendanceCorrection;
use App\Models\AttendanceDay;
use App\Models\Personnel;
use App\Models\User;
use App\Services\AttendanceCorrectionService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->service = app(AttendanceCorrectionService::class);
});

function corrUser(string $role = 'employe'): User
{
    $personnel = Personnel::create([
        'nom'    => 'Corr',
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

    return $user;
}

/** Une journée pointée en retard. */
function lateDay(User $user, string $date = '2026-09-07', string $checkIn = '09:12'): AttendanceDay
{
    return AttendanceDay::create([
        'user_id'           => $user->id,
        'attendance_date'   => $date,
        'first_check_in_at' => $date . ' ' . $checkIn . ':00',
        'last_check_out_at' => $date . ' 17:30:00',
        'arrival_status'    => 'late',
        'late_minutes'      => 72,
        'worked_minutes'    => 498,
    ]);
}

test('correcting the time clears the lateness but keeps the original readable', function () {
    $user  = corrUser();
    $admin = corrUser('admin');
    $day   = lateDay($user);

    $correction = $this->service->apply($user, $day, '08:00', "Lecteur QR hors service à l'entrée.", $admin);

    $day->refresh();

    expect($day->arrival_status)->toBe('ontime')
        ->and($day->late_minutes)->toBe(0)
        ->and($day->first_check_in_at->format('H:i'))->toBe('08:00');

    // Le fait constaté n'est pas effacé, seulement son effet sur la ponctualité
    expect($correction->original_check_in_at->format('H:i'))->toBe('09:12')
        ->and($correction->original_arrival_status)->toBe('late')
        ->and($correction->original_late_minutes)->toBe(72)
        ->and($correction->reason)->toContain('Lecteur QR')
        ->and($correction->created_by)->toBe($admin->id);
});

test('reverting a correction restores exactly what was recorded', function () {
    $user  = corrUser();
    $admin = corrUser('admin');
    $day   = lateDay($user);

    $correction = $this->service->apply($user, $day, '08:00', 'Panne du lecteur.', $admin);
    $this->service->revert($correction);

    $day->refresh();

    expect($day->arrival_status)->toBe('late')
        ->and($day->late_minutes)->toBe(72)
        ->and($day->first_check_in_at->format('H:i'))->toBe('09:12')
        ->and(AttendanceCorrection::count())->toBe(0);
});

test('a day without any check-in cannot be time-corrected', function () {
    $user  = corrUser();
    $admin = corrUser('admin');

    $day = AttendanceDay::create([
        'user_id'         => $user->id,
        'attendance_date' => '2026-09-07',
    ]);

    // C'est le rôle de la correction d'absence, pas de celle-ci
    expect(fn() => $this->service->apply($user, $day, '08:00', 'Motif valable', $admin))
        ->toThrow(ValidationException::class);
});

test('a correction cannot push the arrival later than what was recorded', function () {
    $user  = corrUser();
    $admin = corrUser('admin');
    $day   = lateDay($user, '2026-09-07', '09:12');

    // 10:00 est postérieur : ce serait aggraver, pas corriger
    expect(fn() => $this->service->apply($user, $day, '10:00', 'Motif valable', $admin))
        ->toThrow(ValidationException::class);

    expect($day->fresh()->arrival_status)->toBe('late');
});

test('a day already corrected refuses a second correction', function () {
    $user  = corrUser();
    $admin = corrUser('admin');
    $day   = lateDay($user);

    $this->service->apply($user, $day, '08:30', 'Premiere correction.', $admin);

    // Sans ce garde-fou, la deuxième correction écraserait l'heure d'origine
    // avec 08:30 et le pointage réel de 09:12 serait perdu.
    expect(fn() => $this->service->apply($user, $day->fresh(), '08:00', 'Deuxieme.', $admin))
        ->toThrow(ValidationException::class);

    expect(AttendanceCorrection::first()->original_check_in_at->format('H:i'))->toBe('09:12');
});

test('a malformed time is rejected', function () {
    $user  = corrUser();
    $admin = corrUser('admin');
    $day   = lateDay($user);

    foreach (['25:00', '8h00', 'midi', ''] as $bad) {
        expect(fn() => $this->service->apply($user, $day->fresh(), $bad, 'Motif valable', $admin))
            ->toThrow(ValidationException::class);
    }

    expect(AttendanceCorrection::count())->toBe(0);
});

test('an admin can correct a time through the screen and a reason is required', function () {
    $user  = corrUser();
    $admin = corrUser('admin');
    $day   = lateDay($user);

    // Sans motif : refusé
    $this->actingAs($admin)
        ->post(route('attendance.tracking.user.correction.store', [$user, $day]), ['time' => '08:00'])
        ->assertSessionHasErrors('reason');

    expect($day->fresh()->arrival_status)->toBe('late');

    $this->actingAs($admin)
        ->post(route('attendance.tracking.user.correction.store', [$user, $day]), [
            'time'   => '08:00',
            'reason' => "Lecteur QR hors service, arrivée constatée par le responsable.",
        ])
        ->assertRedirect();

    expect($day->fresh()->arrival_status)->toBe('ontime');

    $this->assertDatabaseHas('activities', ['action' => 'Correction heure arrivee']);
});

test('a correction cannot be applied to a day belonging to someone else', function () {
    $admin = corrUser('admin');
    $mine  = corrUser();
    $other = corrUser();

    $dayOfOther = lateDay($other);

    // URL forgée : la journée d'un tiers sous l'identifiant d'un autre
    $this->actingAs($admin)
        ->post(route('attendance.tracking.user.correction.store', [$mine, $dayOfOther]), [
            'time'   => '08:00',
            'reason' => 'Tentative de correction croisée.',
        ])
        ->assertNotFound();

    expect($dayOfOther->fresh()->arrival_status)->toBe('late');
});

test('a corrected day counts as on time in the punctuality ratio', function () {
    $user  = corrUser();
    $admin = corrUser('admin');

    lateDay($user, '2026-09-07');
    $day = lateDay($user, '2026-09-08');

    // Deux retards, dont un dû à une panne
    expect(AttendanceDay::where('arrival_status', 'late')->count())->toBe(2);

    $this->service->apply($user, $day, '08:00', 'Panne du lecteur.', $admin);

    // Le calcul de ponctualité s'appuie sur arrival_status : un seul retard reste
    expect(AttendanceDay::where('arrival_status', 'late')->count())->toBe(1)
        ->and(AttendanceDay::where('arrival_status', 'ontime')->count())->toBe(1);
});
