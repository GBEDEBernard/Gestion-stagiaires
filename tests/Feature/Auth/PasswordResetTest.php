<?php

use App\Models\User;
use App\Models\Personnel;
use App\Notifications\SendPasswordResetPin;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password PIN can be requested', function () {
    Notification::fake();

    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/forgot-password', ['email' => 'test@example.com']);

    $response->assertRedirect(route('password.verify-pin-show', ['email' => 'test@example.com']));
    Notification::assertSentTo($user, SendPasswordResetPin::class);
});

test('verify PIN screen can be rendered', function () {
    $response = $this->get('/verify-pin?email=test@example.com');

    $response->assertStatus(200);
});

test('PIN can be verified', function () {
    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'email' => 'test@example.com',
    ]);

    DB::table('password_reset_pins')->insert([
        'email' => 'test@example.com',
        'pin' => '123456',
        'expires_at' => now()->addMinutes(15),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->post('/verify-pin', [
        'email' => 'test@example.com',
        'pin' => '123456',
    ]);

    $response->assertRedirect(route('password.reset-form', [
        'email' => 'test@example.com',
        'pin' => '123456',
    ]));
});

test('password can be reset with valid PIN', function () {
    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'email' => 'test@example.com',
        'password' => Hash::make('old-password'),
    ]);

    DB::table('password_reset_pins')->insert([
        'email' => 'test@example.com',
        'pin' => '123456',
        'expires_at' => now()->addMinutes(15),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->post('/reset-password-with-pin', [
        'email' => 'test@example.com',
        'pin' => '123456',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertTrue(Hash::check('NewPassword123!', $user->refresh()->password));
});
