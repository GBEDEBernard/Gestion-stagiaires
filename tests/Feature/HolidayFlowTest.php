<?php

use App\Mail\EmergencyCallMail;
use App\Mail\HolidayPublishedMail;
use App\Models\Holiday;
use App\Models\HolidayEmergencyExemption;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Mail;

function createHolidayAdminUser(): User
{
    test()->seed(RolePermissionSeeder::class);

    $admin = User::create([
        'name' => 'Admin Holidays',
        'email' => 'admin.holidays@example.com',
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'status' => 'actif',
    ]);

    $admin->assignRole('admin');

    return $admin;
}

function createActiveHolidayUser(string $role, string $suffix): User
{
    $user = User::create([
        'name' => ucfirst($role) . ' ' . $suffix,
        'email' => "{$role}.{$suffix}@example.com",
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'status' => 'actif',
    ]);

    $user->assignRole($role);

    return $user;
}

test('la publication notifie uniquement les utilisateurs actifs (in-app + email)', function () {
    $admin = createHolidayAdminUser();

    $activeE = createActiveHolidayUser('employe', 'actif');
    $activeEt = createActiveHolidayUser('etudiant', 'actif');
    $inactive = createActiveHolidayUser('employe', 'inactif');
    $inactive->update(['status' => 'inactif']);

    $holiday = Holiday::create([
        'date' => today()->addDay()->toDateString(),
        'label' => 'Fête de test',
        'is_active' => true,
    ]);

    Mail::fake();

    $this->actingAs($admin)->post(route('admin.holidays.notify.post', $holiday))
        ->assertRedirect(route('admin.holidays.index'));

    $holiday->refresh();
    expect($holiday->notified)->toBeTrue();

    Mail::assertSent(HolidayPublishedMail::class, 2);
    Mail::assertNotSent(HolidayPublishedMail::class, fn($mail) => $mail->hasTo($inactive->email));

    expect(\App\Models\AppNotification::where('user_id', $activeE->id)->where('type', 'jour_ferie')->exists())->toBeTrue();
    expect(\App\Models\AppNotification::where('user_id', $activeEt->id)->where('type', 'jour_ferie')->exists())->toBeTrue();
    expect(\App\Models\AppNotification::where('user_id', $inactive->id)->where('type', 'jour_ferie')->exists())->toBeFalse();
});

test('l\'appel d\'urgence crée l\'exemption et envoie le mail', function () {
    $admin = createHolidayAdminUser();
    $employe = createActiveHolidayUser('employe', 'guard');

    $holiday = Holiday::create([
        'date' => today()->addDay()->toDateString(),
        'label' => 'Fête urgente',
        'is_active' => true,
    ]);

    Mail::fake();

    $this->actingAs($admin)->post(route('admin.holidays.emergency-call', $holiday), [
        'user_ids' => [$employe->id],
        'message' => 'Venir au site principal',
    ])->assertRedirect(route('admin.holidays.index'));

    expect(HolidayEmergencyExemption::where('holiday_id', $holiday->id)->where('user_id', $employe->id)->exists())->toBeTrue();
    expect(HolidayEmergencyExemption::isExempted($employe->fresh(), $holiday->date))->toBeTrue();
    Mail::assertSent(EmergencyCallMail::class, 1);
});

test('la révocation d\'urgence supprime l\'exemption', function () {
    $admin = createHolidayAdminUser();
    $employe = createActiveHolidayUser('employe', 'revoke');

    $holiday = Holiday::create([
        'date' => today()->addDay()->toDateString(),
        'label' => 'Fête révocable',
        'is_active' => true,
    ]);

    $exemption = HolidayEmergencyExemption::create([
        'holiday_id' => $holiday->id,
        'user_id' => $employe->id,
        'message' => 'Urgence',
        'called_by' => $admin->id,
    ]);

    $this->actingAs($admin)->delete(route('admin.holidays.exemptions.destroy', $exemption))
        ->assertRedirect(route('admin.holidays.index'));

    expect(HolidayEmergencyExemption::find($exemption->id))->toBeNull();
    expect(HolidayEmergencyExemption::isExempted($employe->fresh(), $holiday->date))->toBeFalse();
});

test('la page index des jours fériés se rend sans erreur pour l\'admin', function () {
    $admin = createHolidayAdminUser();

    Holiday::create([
        'date' => today()->addDay()->toDateString(),
        'label' => 'Fête index',
        'is_active' => true,
        'notified' => true,
    ]);

    createActiveHolidayUser('employe', 'appele');

    $this->actingAs($admin)->get(route('admin.holidays.index'))
        ->assertOk()
        ->assertSee('Fête index');
});

test('la publication échoue si le jour férié est inactif', function () {
    $admin = createHolidayAdminUser();

    $holiday = Holiday::create([
        'date' => today()->addDay()->toDateString(),
        'label' => 'Fête inactive',
        'is_active' => false,
    ]);

    Mail::fake();

    $this->actingAs($admin)->post(route('admin.holidays.notify.post', $holiday))
        ->assertRedirect(route('admin.holidays.index'));

    Mail::assertNothingSent();
    expect($holiday->refresh()->notified)->toBeFalse();
});