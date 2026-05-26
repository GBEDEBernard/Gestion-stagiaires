<?php

use App\Models\User;
use App\Models\Personnel;
use Illuminate\Support\Facades\Hash;

test('profile page is displayed', function () {
    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $personnel = Personnel::create([
        'nom' => 'OldNom',
        'prenom' => 'OldPrenom',
        'email' => 'old@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->put('/profile', [
            'nom' => 'NewNom',
            'prenom' => 'NewPrenom',
            'email' => 'new@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $personnel->refresh();

    $this->assertSame('NewPrenom NewNom', $user->name);
    $this->assertSame('new@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'email_verified_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->put('/profile', [
            'nom' => 'User',
            'prenom' => 'Test',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertSoftDeleted($user);
});

test('correct password must be provided to delete account', function () {
    $personnel = Personnel::create([
        'nom' => 'User',
        'prenom' => 'Test',
        'email' => 'test@example.com',
    ]);
    $user = User::factory()->create([
        'personnel_id' => $personnel->id,
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
