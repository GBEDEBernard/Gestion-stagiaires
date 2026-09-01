<?php

use App\Models\Domaine;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\SiteGeofence;
use App\Models\TrustedDevice;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function createQrTestUser(string $role = 'employe'): User
{
    $personnel = Personnel::create([
        'nom'    => ucfirst($role),
        'prenom' => 'Test',
        'email'  => "{$role}." . Str::random(6) . "@example.com",
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

function createQrTestSite(): Site
{
    $site = Site::create([
        'code'      => 'SITE-' . Str::upper(Str::random(4)),
        'name'      => 'Siège Test',
        'qr_token'  => 'site-test-' . Str::random(16),
        'is_active' => true,
    ]);

    SiteGeofence::create([
        'site_id'                  => $site->id,
        'name'                     => 'Zone Principale',
        'center_latitude'          => 6.3654,
        'center_longitude'         => 2.4183,
        'radius_meters'            => 100,
        'allowed_accuracy_meters'  => 50,
        'is_primary'               => true,
        'is_active'                => true,
    ]);

    return $site;
}

test('guest without device token is redirected to login when scanning qr', function () {
    $site = createQrTestSite();

    $response = $this->get(route('presence.qr.scan', ['site_token' => $site->qr_token]));

    $response->assertRedirect(route('login'));
    expect(session('pending_qr_site'))->toBe($site->qr_token);
});

test('user can enroll up to 2 devices as qr badges and 3rd is rejected', function () {
    $user = createQrTestUser('employe');

    // 1er appareil
    $response1 = $this->actingAs($user)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_phone_1',
        'device_name'        => 'iPhone 14 Pro',
        'platform'           => 'iOS',
        'browser'            => 'Safari',
    ]);

    $response1->assertStatus(200)->assertJson(['success' => true]);
    expect(TrustedDevice::where('user_id', $user->id)->where('is_qr_badge', true)->count())->toBe(1);

    // 2ème appareil
    $response2 = $this->actingAs($user)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_phone_2',
        'device_name'        => 'iPad Air',
        'platform'           => 'iOS',
        'browser'            => 'Safari',
    ]);

    $response2->assertStatus(200)->assertJson(['success' => true]);
    expect(TrustedDevice::where('user_id', $user->id)->where('is_qr_badge', true)->count())->toBe(2);

    // 3ème appareil (rejeté avec 422)
    $response3 = $this->actingAs($user)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_phone_3',
        'device_name'        => 'Android Galaxy',
    ]);

    $response3->assertStatus(422)->assertJson(['success' => false]);
    expect(TrustedDevice::where('user_id', $user->id)->where('is_qr_badge', true)->count())->toBe(2);
});

test('user can revoke an enrolled device from profile', function () {
    $user = createQrTestUser('employe');

    $device = TrustedDevice::create([
        'user_id'            => $user->id,
        'device_fingerprint' => 'fp_phone_test',
        'device_name'        => 'Mon Téléphone',
        'is_qr_badge'        => true,
        'is_trusted'         => true,
    ]);

    $response = $this->actingAs($user)->delete(route('presence.devices.revoke', $device));

    $response->assertRedirect();
    $device->refresh();
    expect($device->is_qr_badge)->toBeFalse();
    expect($device->revoked_at)->not->toBeNull();
});

test('scanning qr with enrolled device cookie displays scan page', function () {
    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    $rawToken = Str::random(64);

    TrustedDevice::create([
        'user_id'             => $user->id,
        'device_fingerprint'  => 'fp_device_scan',
        'pointage_token_hash' => hash('sha256', $rawToken),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
    ]);

    $response = $this->withCookie('pointage_device_tokens', json_encode([$rawToken]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]));

    $response->assertStatus(200);
    $response->assertViewIs('presence.qr.scan');
    $response->assertViewHas('user');
});

test('multi-user device cookie displays user choice view', function () {
    $site = createQrTestSite();
    $user1 = createQrTestUser('employe');
    $user2 = createQrTestUser('employe');

    $rawToken1 = Str::random(64);
    $rawToken2 = Str::random(64);

    TrustedDevice::create([
        'user_id'             => $user1->id,
        'device_fingerprint'  => 'fp_shared_device',
        'pointage_token_hash' => hash('sha256', $rawToken1),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
    ]);

    TrustedDevice::create([
        'user_id'             => $user2->id,
        'device_fingerprint'  => 'fp_shared_device',
        'pointage_token_hash' => hash('sha256', $rawToken2),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
    ]);

    $response = $this->withCookie('pointage_device_tokens', json_encode([$rawToken1, $rawToken2]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]));

    $response->assertStatus(200);
    $response->assertViewIs('presence.qr.multi-user');
    $response->assertViewHas('usersList');
});

test('processing attendance returns standalone result view without redirection', function () {
    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    $domaine = Domaine::create(['nom' => 'Développement']);
    $domaine->sites()->attach($site->id);
    
    // Associer l'employé au domaine
    $employe = \App\Models\Employe::create([
        'personnel_id' => $user->personnel_id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'poste'        => 'Développeur',
    ]);

    $rawToken = Str::random(64);

    TrustedDevice::create([
        'user_id'             => $user->id,
        'device_fingerprint'  => 'fp_result_device',
        'pointage_token_hash' => hash('sha256', $rawToken),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
    ]);

    $response = $this->post(route('presence.qr.process', ['site_token' => $site->qr_token]), [
        'latitude'           => 6.3654,
        'longitude'          => 2.4183,
        'accuracy_meters'    => 10,
        'device_token'       => $rawToken,
        'device_fingerprint' => 'fp_result_device',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('presence.qr.result');
    $response->assertViewHas('status', 'approved');
    $response->assertViewHas('eventType', 'check_in');
});

test('shared device generates shared_device_detected anomaly and notifies admins', function () {
    $site = createQrTestSite();
    $admin = createQrTestUser('admin');
    $user1 = createQrTestUser('employe'); // Sarah
    $user2 = createQrTestUser('employe'); // Jean
    $domaine = Domaine::create(['nom' => 'Support']);
    $domaine->sites()->attach($site->id);

    $sharedRawToken = Str::random(64);
    $sharedFingerprint = 'fp_hardware_phone_unique';

    // Appareil badge principal de User 1 (Sarah)
    TrustedDevice::create([
        'user_id'             => $user1->id,
        'device_fingerprint'  => $sharedFingerprint,
        'pointage_token_hash' => hash('sha256', $sharedRawToken),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
        'is_primary'          => true,
    ]);

    // User 2 (Jean) pointe avec le téléphone de User 1 (Sarah)
    $response = $this->actingAs($user2)->post(route('presence.qr.process', ['site_token' => $site->qr_token]), [
        'latitude'           => 6.3654,
        'longitude'          => 2.4183,
        'accuracy_meters'    => 10,
        'device_fingerprint' => $sharedFingerprint,
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('presence.qr.result');

    // Vérifier l'anomalie enregistrée
    $this->assertDatabaseHas('attendance_anomalies', [
        'anomaly_type' => 'shared_device_detected',
    ]);

    // Vérifier la notification envoyée à l'admin
    $this->assertDatabaseHas('app_notifications', [
        'user_id' => $admin->id,
        'type'    => 'device_anomaly',
    ]);
});

test('stage date_fin update synchronizes device token expiration', function () {
    $admin = createQrTestUser('admin');
    $studentUser = createQrTestUser('etudiant');

    $etudiant = \App\Models\Etudiant::create([
        'personnel_id' => $studentUser->personnel_id,
        'ecole'        => 'Université',
    ]);

    $site = createQrTestSite();
    $typeStage = \App\Models\TypeStage::create(['code' => 'ACAD', 'libelle' => 'Académique']);
    $domaine = Domaine::create(['nom' => 'Informatique']);

    $stage = \App\Models\Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'theme'        => 'Développement Web',
        'date_debut'   => now()->subMonth()->toDateString(),
        'date_fin'     => now()->addMonth()->toDateString(),
    ]);

    $device = TrustedDevice::create([
        'user_id'             => $studentUser->id,
        'device_fingerprint'  => 'fp_intern_phone',
        'pointage_token_hash' => hash('sha256', Str::random(64)),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
        'token_expires_at'    => now()->addMonth()->endOfDay(),
    ]);

    $newDateFin = now()->addMonths(3)->toDateString();
    $jour = \App\Models\Jour::create(['jour' => 'Lundi']);

    // L'admin prolonge le stage de 3 mois
    $response = $this->actingAs($admin)->put(route('stages.update', $stage), [
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'theme'        => $stage->theme,
        'date_debut'   => $stage->date_debut->toDateString(),
        'date_fin'     => $newDateFin,
        'jours_id'     => [$jour->id],
    ]);

    $response->assertRedirect();
    $device->refresh();
    expect($device->token_expires_at->toDateString())->toBe($newDateFin);
});

test('expired badges no longer count towards the 2 device cap', function () {
    $user = createQrTestUser('employe');

    // Deux anciens téléphones dont le badge a expiré avec le stage précédent
    foreach (['fp_old_phone_1', 'fp_old_phone_2'] as $fingerprint) {
        TrustedDevice::create([
            'user_id'             => $user->id,
            'device_fingerprint'  => $fingerprint,
            'pointage_token_hash' => hash('sha256', Str::random(64)),
            'is_qr_badge'         => true,
            'is_trusted'          => true,
            'token_expires_at'    => now()->subMonth(),
        ]);
    }

    // Le scope ne doit plus les compter comme actifs
    expect($user->trustedDevices()->activeQrBadges()->count())->toBe(0);

    // ... et l'enrôlement d'un nouveau téléphone doit donc être accepté
    $this->actingAs($user)
        ->postJson(route('presence.devices.enroll'), [
            'device_fingerprint' => 'fp_new_phone',
            'device_name'        => 'Nouveau Téléphone',
        ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('user self revocation notifies the admins', function () {
    $admin = createQrTestUser('admin');
    $user  = createQrTestUser('employe');

    $device = TrustedDevice::create([
        'user_id'            => $user->id,
        'device_fingerprint' => 'fp_lost_phone',
        'device_name'        => 'Téléphone perdu',
        'is_qr_badge'        => true,
        'is_trusted'         => true,
    ]);

    $this->actingAs($user)
        ->delete(route('presence.devices.revoke', $device))
        ->assertRedirect();

    $device->refresh();
    expect($device->is_qr_badge)->toBeFalse()
        ->and($device->revoked_at)->not->toBeNull();

    $this->assertDatabaseHas('app_notifications', [
        'user_id' => $admin->id,
        'type'    => 'device_revoked',
    ]);
});

test('an admin can revoke a badge from the user sheet and the owner is notified', function () {
    $admin = createQrTestUser('admin');
    $user  = createQrTestUser('employe');

    $device = TrustedDevice::create([
        'user_id'            => $user->id,
        'device_fingerprint' => 'fp_stolen_phone',
        'device_name'        => 'Téléphone volé',
        'is_qr_badge'        => true,
        'is_trusted'         => true,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.users.devices.revoke', [$user, $device]))
        ->assertRedirect();

    $device->refresh();
    expect($device->is_qr_badge)->toBeFalse()
        ->and($device->revoked_at)->not->toBeNull();

    // L'utilisateur doit comprendre pourquoi son téléphone ne pointe plus
    $this->assertDatabaseHas('app_notifications', [
        'user_id' => $user->id,
        'type'    => 'device_revoked',
    ]);

    $this->assertDatabaseHas('activities', [
        'user_id' => $admin->id,
        'action'  => 'Revocation badge de pointage',
    ]);
});

test('an admin cannot revoke a device that belongs to another user', function () {
    $admin  = createQrTestUser('admin');
    $userA  = createQrTestUser('employe');
    $userB  = createQrTestUser('employe');

    $deviceOfB = TrustedDevice::create([
        'user_id'            => $userB->id,
        'device_fingerprint' => 'fp_phone_of_b',
        'device_name'        => 'Téléphone de B',
        'is_qr_badge'        => true,
        'is_trusted'         => true,
    ]);

    // URL forgée : l'appareil de B sous l'identifiant de A
    $this->actingAs($admin)
        ->delete(route('admin.users.devices.revoke', [$userA, $deviceOfB]))
        ->assertNotFound();

    $deviceOfB->refresh();
    expect($deviceOfB->is_qr_badge)->toBeTrue()
        ->and($deviceOfB->revoked_at)->toBeNull();
});

test('the admin user sheet lists the enrolled badges with a revoke control', function () {
    $admin = createQrTestUser('admin');
    $user  = createQrTestUser('employe');

    TrustedDevice::create([
        'user_id'            => $user->id,
        'device_fingerprint' => 'fp_visible_phone',
        'device_name'        => 'Galaxy A54',
        'is_qr_badge'        => true,
        'is_trusted'         => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users.show', $user))
        ->assertOk()
        ->assertSee('Appareils de pointage')
        ->assertSee('Galaxy A54')
        ->assertSee(route('admin.users.devices.revoke', [$user, TrustedDevice::first()]), false);
});
