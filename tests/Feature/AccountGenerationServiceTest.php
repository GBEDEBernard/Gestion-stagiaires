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
