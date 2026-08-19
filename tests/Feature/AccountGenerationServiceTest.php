<?php

use App\Models\Personnel;
use App\Models\User;
use App\Notifications\AccountProvisionedNotification;
use App\Services\AccountGenerationService;
use Illuminate\Support\Facades\Notification;

test('account provisioning email can be resent for an existing personnel account', function () {
    Notification::fake();

    $personnel = Personnel::create([
        'nom' => 'Doe',
        'prenom' => 'Jane',
        'email' => 'jane.account@example.com',
    ]);

    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'email' => $personnel->email,
        'status' => 'actif',
    ]);

    app(AccountGenerationService::class)->resendProvisioningEmail($personnel);

    Notification::assertSentTo($user, AccountProvisionedNotification::class);
});

test('resending provisioning email with a custom password updates the user password', function () {
    Notification::fake();

    $personnel = Personnel::create([
        'nom' => 'Roe',
        'prenom' => 'John',
        'email' => 'john.roe@example.com',
    ]);

    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'email' => $personnel->email,
        'password' => Hash::make('ancien-mot-de-passe'),
        'status' => 'actif',
    ]);

    app(AccountGenerationService::class)->resendProvisioningEmail($personnel, 'temporaire-1234');

    $user->refresh();

    expect($user->must_change_password)->toBe(true);
    expect(Hash::check('temporaire-1234', $user->password))->toBeTrue()
        ->and(Hash::check('ancien-mot-de-passe', $user->password))->toBeFalse();

    Notification::assertSentTo($user, AccountProvisionedNotification::class);
});
