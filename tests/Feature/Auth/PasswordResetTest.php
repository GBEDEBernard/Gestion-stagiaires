<?php

use App\Models\User;
use App\Notifications\SendPasswordResetPin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertOk();
});

test('password reset pin can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertRedirect(route('password.verify-pin-show', ['email' => $user->email], false));
    Notification::assertSentTo($user, SendPasswordResetPin::class);
    $this->assertDatabaseHas('password_reset_pins', [
        'email' => $user->email,
        'used' => false,
    ]);
});

test('valid password reset pin redirects to the reset form', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $pin = DB::table('password_reset_pins')
        ->where('email', $user->email)
        ->value('pin');

    $response = $this->post('/verify-pin', [
        'email' => $user->email,
        'pin' => $pin,
    ]);

    $response->assertRedirect(route('password.reset-form', [
        'email' => $user->email,
        'pin' => $pin,
    ], false));
});

test('password can be reset with valid pin', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $pin = DB::table('password_reset_pins')
        ->where('email', $user->email)
        ->value('pin');

    $response = $this->post('/reset-password-with-pin', [
        'email' => $user->email,
        'pin' => $pin,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertRedirect(route('login', absolute: false));
    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
    $this->assertDatabaseMissing('password_reset_pins', [
        'email' => $user->email,
    ]);
});
