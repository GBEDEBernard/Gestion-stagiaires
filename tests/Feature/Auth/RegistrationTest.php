<?php

use App\Models\User;
use App\Notifications\SendEmailVerificationPin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk();
});

test('new users can register and are redirected to pin verification', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'terms' => 'on',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('verification.pin.show', ['email' => 'test@example.com'], false));

    $user = User::where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, SendEmailVerificationPin::class);
    $this->assertDatabaseHas('email_verification_pins', [
        'email' => 'test@example.com',
        'user_id' => $user->id,
        'used' => false,
    ]);
});

test('valid registration pin verifies the email and assigns the student role', function () {
    Notification::fake();
    Role::findOrCreate('etudiant', 'web');
    collect([
        'presence.view',
        'presence.checkin',
        'presence.checkout',
        'daily_reports.view',
        'daily_reports.create',
        'daily_reports.submit',
        'tasks.view',
    ])->each(fn ($permission) => Permission::findOrCreate($permission, 'web'));

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'terms' => 'on',
    ]);

    $user = User::where('email', 'test@example.com')->firstOrFail();
    $pin = DB::table('email_verification_pins')
        ->where('email', $user->email)
        ->value('pin');

    $response = $this->post('/verify-register-pin', [
        'email' => $user->email,
        'pin' => $pin,
    ]);

    $response->assertRedirect(route('login', absolute: false));
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    expect($user->fresh()->hasRole('etudiant'))->toBeTrue();
    $this->assertDatabaseHas('email_verification_pins', [
        'email' => $user->email,
        'pin' => $pin,
        'used' => true,
    ]);
});
