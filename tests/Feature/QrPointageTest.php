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

function createQrTestUser(string|array $role = 'employe'): User
{
    $roles = (array) $role;
    $label = implode('-', $roles);

    $personnel = Personnel::create([
        'nom'    => ucfirst($label),
        'prenom' => 'Test',
        'email'  => "{$label}." . Str::random(6) . "@example.com",
    ]);

    $user = User::create([
        'personnel_id'      => $personnel->id,
        'name'              => $personnel->full_name,
        'email'             => $personnel->email,
        'email_verified_at' => now(),
        'password'          => Hash::make('password'),
        'status'            => 'actif',
    ]);

    $user->assignRole($roles);

    return $user;
}

/** Un compte de terrain rattaché à un Employe, comme en production. */
function attachQrTestEmploye(User $user, Site $site): Domaine
{
    $domaine = Domaine::create(['nom' => 'Développement ' . Str::random(4)]);
    $domaine->sites()->attach($site->id);

    $employe = \App\Models\Employe::create([
        'personnel_id' => $user->personnel_id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'poste'        => 'Développeur',
    ]);

    // Le pointeur polymorphique est ce qui relie la fiche Employe au compte.
    // Sans lui, $user->domaine reste vide et la page de pointage se dérobe.
    $user->personnel->update([
        'personnable_type' => \App\Models\Employe::class,
        'personnable_id'   => $employe->id,
    ]);

    $user->unsetRelation('personnel');

    return $domaine;
}

/** Le badge de la porte porté par le téléphone de $user. */
function enrollQrTestBadge(User $user, string $fingerprint = 'fp_test'): string
{
    $raw = Str::random(64);

    TrustedDevice::create([
        'user_id'             => $user->id,
        'device_fingerprint'  => $fingerprint,
        'pointage_token_hash' => hash('sha256', $raw),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
    ]);

    return $raw;
}

/** Horaire d'entreprise à 08:00, comme dans le reste de la suite. */
function setQrTestWorkHours(string $start = '08:00', string $end = '18:00'): void
{
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => $start, 'end_time' => $end]);
}

function freezeQrTestClock(string $time): void
{
    // Les tests voisins écrivent '07:45:00', d'autres '07:45' : on accepte les
    // deux plutôt que de leur faire choisir une écriture.
    $parts = explode(':', $time);
    $parts[] = '00';

    \Carbon\Carbon::setTestNow(
        \Carbon\Carbon::parse(today()->toDateString() . ' ' . implode(':', array_slice($parts, 0, 3)))
    );
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

test('a user can enroll one phone as a badge, a second is rejected', function () {
    $user = createQrTestUser('employe');

    $this->actingAs($user)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_phone_1',
        'device_name'        => 'iPhone 14 Pro',
    ])->assertStatus(200)->assertJson(['success' => true]);

    expect(TrustedDevice::where('user_id', $user->id)->where('is_qr_badge', true)->count())->toBe(1);

    // Un utilisateur, un téléphone : le second est refusé
    $this->actingAs($user)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_phone_2',
        'device_name'        => 'Android Galaxy',
    ])->assertStatus(422)->assertJson(['success' => false]);

    expect(TrustedDevice::where('user_id', $user->id)->where('is_qr_badge', true)->count())->toBe(1);
});

test('a phone already used as a badge cannot be claimed by a second account', function () {
    $premier = createQrTestUser('employe');
    $second  = createQrTestUser('employe');

    $this->actingAs($premier)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_telephone_partage',
        'device_name'        => 'Le telephone',
    ])->assertStatus(200);

    // Un téléphone, un badge : le second compte est refusé sur le même appareil
    $this->actingAs($second)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_telephone_partage',
        'device_name'        => 'Le meme telephone',
    ])->assertStatus(422);

    expect(TrustedDevice::where('user_id', $second->id)->where('is_qr_badge', true)->count())->toBe(0);
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
    $response->assertViewIs('presence.qr.pointage');
    $response->assertViewHas('user');
});

test('a phone carrying an old second profile still lands on a single scan screen', function () {
    // Le choix entre profils n'existe plus. Des données anciennes peuvent
    // encore porter deux badges sur une même empreinte : l'écran de scan doit
    // rester simple plutôt que de proposer une liste.
    $site  = createQrTestSite();
    $user1 = createQrTestUser('employe');
    $user2 = createQrTestUser('employe');

    $token1 = Str::random(64);
    $token2 = Str::random(64);

    foreach ([[$user1, $token1], [$user2, $token2]] as [$u, $t]) {
        TrustedDevice::create([
            'user_id'             => $u->id,
            'device_fingerprint'  => 'fp_ancien_partage',
            'pointage_token_hash' => hash('sha256', $t),
            'is_qr_badge'         => true,
            'is_trusted'          => true,
        ]);
    }

    $this->withCookie('pointage_device_tokens', json_encode([$token1, $token2]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertViewIs('presence.qr.pointage');
});

test('the scan screen is the pointage card itself', function () {
    // L'unification tient à ceci : le badge tombe sur la carte de pointage et
    // poste sur la route du scan. Le libellé du bouton a changé — le scan suffit
    // désormais — mais c'est bien la même carte, au même endroit.
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse(today()->toDateString() . ' 07:45:00'));

    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    $raw  = Str::random(64);

    TrustedDevice::create([
        'user_id'             => $user->id,
        'device_fingerprint'  => 'fp_carte_unifiee',
        'pointage_token_hash' => hash('sha256', $raw),
        'is_qr_badge'         => true,
        'is_trusted'          => true,
    ]);

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertSee('data-auto-pointage="1"', false)
        ->assertSee(route('presence.qr.process', ['site_token' => $site->qr_token]), false)
        ->assertSee('Mon historique');

    \Carbon\Carbon::setTestNow();
});

test('processing attendance returns standalone result view without redirection', function () {
    // Ces tests portent sur le résultat du pointage, pas sur le retard : on se
    // place avant l'heure d'arrivée pour ne pas déclencher l'écran d'observation.
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse(today()->toDateString() . ' 07:45:00'));

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

    \Carbon\Carbon::setTestNow();
});

test('shared device generates shared_device_detected anomaly and notifies admins', function () {
    // Ces tests portent sur le résultat du pointage, pas sur le retard : on se
    // place avant l'heure d'arrivée pour ne pas déclencher l'écran d'observation.
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse(today()->toDateString() . ' 07:45:00'));

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

    \Carbon\Carbon::setTestNow();
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
        // Le formulaire de stage porte désormais l'horaire, qui détermine le
        // seuil de retard : il est requis à l'enregistrement.
        'expected_check_in_time'  => '08:30',
        'expected_check_out_time' => '17:30',
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

test('clearing cookies and re-enrolling the same phone keeps working', function () {
    $user = createQrTestUser('employe');

    $this->actingAs($user)->postJson(route('presence.devices.enroll'), [
        'device_fingerprint' => 'fp_phone_a',
        'device_name'        => 'Mon telephone',
    ])->assertStatus(200);

    // L'utilisateur vide ses cookies et rescanne : son empreinte est inchangée,
    // il doit récupérer son badge sans se heurter à son propre plafond.
    $this->actingAs($user)
        ->postJson(route('presence.devices.enroll'), [
            'device_fingerprint' => 'fp_phone_a',
            'device_name'        => 'Mon telephone',
        ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(TrustedDevice::where('user_id', $user->id)->where('is_qr_badge', true)->count())->toBe(1);
});

test('a late qr check-in asks for an observation before recording anything', function () {
    $site    = createQrTestSite();
    $user    = createQrTestUser('employe');
    $domaine = Domaine::create(['nom' => 'Support']);
    $domaine->sites()->attach($site->id);

    \App\Models\Employe::create([
        'personnel_id' => $user->personnel_id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'poste'        => 'Agent',
    ]);

    // Horaire d'entreprise à 08:00 ; on scanne à 09:30
    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00']);
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse(today()->toDateString() . ' 09:30:00'));

    $payload = [
        'latitude'           => 6.3654,
        'longitude'          => 2.4183,
        'accuracy_meters'    => 10,
        'device_fingerprint' => 'fp_retard',
    ];

    $response = $this->actingAs($user)->post(route('presence.qr.process', ['site_token' => $site->qr_token]), $payload);

    // On demande le motif, et surtout rien n'est encore écrit
    $response->assertOk()->assertViewIs('presence.qr.observation');
    expect(\App\Models\AttendanceDay::count())->toBe(0);

    // Un motif trop court ne passe pas non plus
    $this->actingAs($user)
        ->post(route('presence.qr.process', ['site_token' => $site->qr_token]), $payload + ['observation_message' => 'trop'])
        ->assertViewIs('presence.qr.observation');

    expect(\App\Models\AttendanceDay::count())->toBe(0);

    // Avec un motif suffisant, le pointage est enregistré et le motif conservé
    $this->actingAs($user)
        ->post(route('presence.qr.process', ['site_token' => $site->qr_token]),
            $payload + ['observation_message' => 'Embouteillage sur la voie de Godomey.'])
        ->assertViewIs('presence.qr.result');

    $day = \App\Models\AttendanceDay::first();
    expect($day)->not->toBeNull()
        ->and($day->arrival_status)->toBe('late')
        ->and($day->late_observation)->toContain('Godomey');

    \Carbon\Carbon::setTestNow();
});

test('an on time qr check-in never asks for an observation', function () {
    $site    = createQrTestSite();
    $user    = createQrTestUser('employe');
    $domaine = Domaine::create(['nom' => 'Support']);
    $domaine->sites()->attach($site->id);

    \App\Models\Employe::create([
        'personnel_id' => $user->personnel_id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'poste'        => 'Agent',
    ]);

    \App\Models\WorkScheduleSetting::query()->delete();
    \App\Models\WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00']);
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse(today()->toDateString() . ' 07:50:00'));

    $this->actingAs($user)
        ->post(route('presence.qr.process', ['site_token' => $site->qr_token]), [
            'latitude'           => 6.3654,
            'longitude'          => 2.4183,
            'accuracy_meters'    => 10,
            'device_fingerprint' => 'fp_a_lheure',
        ])
        ->assertViewIs('presence.qr.result');

    expect(\App\Models\AttendanceDay::first()->arrival_status)->toBe('ontime');

    \Carbon\Carbon::setTestNow();
});

// ==========================================================================
//  POINTAGE AUTOMATIQUE : LE SCAN SUFFIT
// ==========================================================================

test('scanning the qr code arms the pointage without waiting for a click', function () {
    // Le scan valide les conditions et enregistre : l'écran montre la carte mais
    // n'attend rien de l'utilisateur. C'est l'attribut du formulaire qui le dit,
    // le texte du bouton restant dans le HTML pour le cas du réessai.
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    attachQrTestEmploye($user, $site);
    $raw  = enrollQrTestBadge($user, 'fp_auto');

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertViewIs('presence.qr.pointage')
        ->assertSee('data-auto-pointage="1"', false)
        ->assertSee("pointageForm(false, false, true)", false)
        ->assertSee('Vérification de votre présence', false);

    \Carbon\Carbon::setTestNow();
});

test('the retry button only appears once the location capture failed', function () {
    // Tant qu'aucune erreur n'est remontée, le bouton reste masqué : un clic de
    // plus n'apporterait rien. En cas d'échec GPS, il revient, en « Réessayer »,
    // pour ne pas obliger à rescanner le code.
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    attachQrTestEmploye($user, $site);
    $raw  = enrollQrTestBadge($user, 'fp_retry');

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertSee("x-show=\"erreur !== ''\"", false)
        ->assertSee('Réessayer', false)
        ->assertDontSee('Pointer mon arrivée', false);

    \Carbon\Carbon::setTestNow();
});

test('a late qr scan still asks the person to explain themselves', function () {
    // Le pointage automatique ne remplace pas la motivation du retard : le
    // motif ne s'écrit pas à la place de l'intéressé. Le bouton reste donc en
    // place, et l'automatisme est désarmé.
    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    attachQrTestEmploye($user, $site);
    $raw  = enrollQrTestBadge($user, 'fp_late_auto');

    setQrTestWorkHours('08:00');
    freezeQrTestClock('09:30:00');

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertSee('data-auto-pointage="0"', false)
        ->assertSee('Pointer mon arrivée', false)
        ->assertSee('Vous arrivez après', false);

    \Carbon\Carbon::setTestNow();
});

test('an early departure scan keeps the button, it waits for a permission', function () {
    // Un départ avant l'heure n'est pas un relevé de position : il faut une
    // permission approuvée. L'automatisme n'a rien à décider, il s'efface.
    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    attachQrTestEmploye($user, $site);
    $raw  = enrollQrTestBadge($user, 'fp_depart_early');

    setQrTestWorkHours('08:00');
    freezeQrTestClock('07:30:00');

    // Arrivée pointée, rapport soumis : la seule chose qui reste est l'heure.
    \App\Models\DailyReport::create([
        'user_id'    => $user->id,
        'report_date'=> today(),
        'status'     => 'submitted',
    ]);

    $journee = \App\Models\AttendanceDay::create([
        'user_id'            => $user->id,
        'attendance_date'    => today(),
        'first_check_in_at'  => now()->subHours(3),
        'arrival_status'     => 'ontime',
    ]);

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertSee('data-auto-pointage="0"', false)
        ->assertSee('Pointer mon départ', false);

    \Carbon\Carbon::setTestNow();
});

test('the in app pointage page is untouched and keeps its button', function () {
    // Le pointage automatique est une règle de la porte, pas de l'application :
    // depuis le compte, on garde la confirmation, parce qu'on peut y pointer
    // autrement qu'en sortant de son véhicule.
    $user = createQrTestUser('employe');
    $site = createQrTestSite();
    attachQrTestEmploye($user, $site);

    setQrTestWorkHours('08:00');
    freezeQrTestClock('07:45:00');

    $this->actingAs($user)
        ->get(route('presence.pointage'))
        ->assertOk()
        ->assertSee('data-auto-pointage="0"', false)
        ->assertSee("pointageForm(false, false, false)", false)
        ->assertSee('Pointer mon arrivée', false);

    \Carbon\Carbon::setTestNow();
});

// ==========================================================================
//  QUI A LE DROIT DE POINTER
// ==========================================================================

test('a pure admin scanning the qr code is told they cannot point', function () {
    // L'administrateur du groupe scanne en passant : pas de carte de pointage,
    // pas d'écriture, et une phrase qui dit quoi faire s'il exerce un poste.
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser('admin');
    $raw  = enrollQrTestBadge($user, 'fp_admin');

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertViewIs('presence.qr.result')
        ->assertViewHas('status', 'rejected')
        ->assertViewHas('reasonCode', 'not_pointable')
        ->assertSee('Compte non habilité au pointage')
        ->assertDontSee('data-auto-pointage');

    expect(\App\Models\AttendanceEvent::count())->toBe(0)
        ->and(\App\Models\AttendanceDay::count())->toBe(0);

    \Carbon\Carbon::setTestNow();
});

test('a superviseur without a pointage role is refused at the door', function () {
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser('superviseur');
    $raw  = enrollQrTestBadge($user, 'fp_superviseur');

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertViewIs('presence.qr.result')
        ->assertViewHas('reasonCode', 'not_pointable');

    expect(\App\Models\AttendanceEvent::count())->toBe(0);

    \Carbon\Carbon::setTestNow();
});

test('an admin who also carries the employe role points normally', function () {
    // C'est le rôle qui décide, pas la fiche Employe. Un administrateur qui
    // exerce un poste sur le terrain est un employé comme les autres.
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser(['admin', 'employe']);
    attachQrTestEmploye($user, $site);
    $raw  = enrollQrTestBadge($user, 'fp_admin_employe');

    expect($user->peutPointer())->toBeTrue();

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertViewIs('presence.qr.pointage')
        ->assertSee('data-auto-pointage="1"', false);

    $this->withCookie('pointage_device_tokens', json_encode([$raw]))
        ->post(route('presence.qr.process', ['site_token' => $site->qr_token]), [
            'latitude'           => 6.3654,
            'longitude'          => 2.4183,
            'accuracy_meters'    => 10,
            'device_token'       => $raw,
            'device_fingerprint' => 'fp_admin_employe',
        ])
        ->assertOk()
        ->assertViewIs('presence.qr.result')
        ->assertViewHas('status', 'approved')
        ->assertViewHas('eventType', 'check_in');

    expect(\App\Models\AttendanceDay::count())->toBe(1);

    \Carbon\Carbon::setTestNow();
});

test('posting the process route directly as a pure admin is refused', function () {
    // La route est publique : sans ce garde-fou, un POST direct enregistrerait
    // la journée d'un compte de gestion, que son scan lui-même refuse.
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser('admin');
    $domaine = Domaine::create(['nom' => 'Direction']);
    $domaine->sites()->attach($site->id);
    $raw = enrollQrTestBadge($user, 'fp_admin_direct');

    $this->actingAs($user)
        ->post(route('presence.qr.process', ['site_token' => $site->qr_token]), [
            'latitude'           => 6.3654,
            'longitude'          => 2.4183,
            'accuracy_meters'    => 10,
            'device_token'       => $raw,
            'device_fingerprint' => 'fp_admin_direct',
        ])
        ->assertOk()
        ->assertViewIs('presence.qr.result')
        ->assertViewHas('status', 'rejected')
        ->assertViewHas('reasonCode', 'not_pointable');

    expect(\App\Models\AttendanceEvent::count())->toBe(0)
        ->and(\App\Models\AttendanceDay::count())->toBe(0);

    \Carbon\Carbon::setTestNow();
});

test('a disabled account is told it is disabled, not merely refused', function () {
    // Le badge ne mène jamais ici : isBadgeActive() écarte déjà un compte inactif
    // et renvoie vers la connexion. Le cas qui reste est la session ouverte dont
    // le compte a été désactivé depuis — et là, l'écran doit nommer la cause,
    // sinon le message générique laisse l'intéressé sans réponse.
    freezeQrTestClock('07:45:00');

    $site = createQrTestSite();
    $user = createQrTestUser('employe');
    $user->update(['status' => 'inactif']);

    $this->actingAs($user)
        ->post(route('presence.qr.process', ['site_token' => $site->qr_token]), [
            'latitude'           => 6.3654,
            'longitude'          => 2.4183,
            'accuracy_meters'    => 10,
            'device_fingerprint' => 'fp_desactive',
        ])
        ->assertOk()
        ->assertViewIs('presence.qr.result')
        ->assertViewHas('status', 'rejected')
        ->assertViewHas('reasonCode', 'account_inactive')
        ->assertSee('Compte désactivé');

    expect(\App\Models\AttendanceDay::count())->toBe(0);

    \Carbon\Carbon::setTestNow();
});

// ==========================================================================
//  BADGE
// ==========================================================================

test('a non pointable account cannot turn its phone into a badge', function () {
    // Un badge de porte qui n'a pas le droit de pointer est un jeton valide
    // exposed sans raison. Le refus tombe à l'enrôlement, pas au scan.
    $admin = createQrTestUser('admin');

    $this->actingAs($admin)
        ->postJson(route('presence.devices.enroll'), [
            'device_fingerprint' => 'fp_admin_enroll',
            'device_uuid'        => 'dev_admin',
            'device_name'        => 'Mon téléphone',
        ])
        ->assertStatus(422)
        ->assertJsonPath('success', false);

    expect($admin->trustedDevices()->activeQrBadges()->exists())->toBeFalse();
});

test('a pointable account can still enroll its phone', function () {
    $user = createQrTestUser('employe');

    $this->actingAs($user)
        ->postJson(route('presence.devices.enroll'), [
            'device_fingerprint' => 'fp_employe_enroll',
            'device_uuid'        => 'dev_employe',
            'device_name'        => 'Mon téléphone',
        ])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect($user->trustedDevices()->activeQrBadges()->exists())->toBeTrue();
});
