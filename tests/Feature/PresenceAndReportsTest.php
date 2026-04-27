<?php

use App\Mail\PermissionRequestReviewMail;
use App\Models\AppNotification;
use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\PermissionRequest;
use App\Models\Service;
use App\Models\Signataire;
use App\Models\Site;
use App\Models\SiteGeofence;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function grantRoleWithPermissions(User $user, string $roleName, array $permissions = []): void
{
    Role::findOrCreate($roleName, 'web');

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user->assignRole($roleName);

    if ($permissions !== []) {
        $user->givePermissionTo($permissions);
    }
}

test('employee pointage page renders without requiring an active stage', function () {
    $user = User::factory()->create();
    grantRoleWithPermissions($user, 'employe', ['presence.view']);

    $domaine = Domaine::create([
        'nom' => 'Informatique',
        'description' => 'Equipe interne',
        'created_by' => $user->id,
    ]);

    $user->update(['domaine_id' => $domaine->id]);

    AttendanceDay::create([
        'user_id' => $user->id,
        'attendance_date' => today(),
        'first_check_in_at' => now()->startOfDay()->addHours(8),
        'worked_minutes' => 120,
        'day_status' => 'in_progress',
        'validation_status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get('/presence/pointage');

    $response->assertOk();
    $response->assertSee('Statut employe');
    $response->assertSee('Pointage employe');
    $response->assertSee('Informatique');
    $response->assertDontSee('Aucun stage actif');
});

test('report updates resubmit the workflow and clear prior review state', function () {
    $user = User::factory()->create();
    $reviewer = User::factory()->create();
    grantRoleWithPermissions($user, 'employe');

    $report = DailyReport::create([
        'user_id' => $user->id,
        'report_date' => today(),
        'title' => 'Rapport du jour',
        'summary' => 'Ancien resume',
        'hours_declared' => 4,
        'status' => 'changes_requested',
        'reviewed_by' => $reviewer->id,
        'reviewed_at' => now(),
        'supervisor_comment' => 'Merci de completer les details.',
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/reports?period=daily&edit=' . $report->id)
        ->put('/reports/' . $report->id, [
            'status_action' => 'submit',
            'summary' => 'Nouveau resume complet',
            'blockers' => 'Aucun',
            'next_steps' => 'Continuer demain',
            'hours_declared' => 7,
        ]);

    $response->assertRedirect('/reports?period=daily&edit=' . $report->id);

    $report->refresh();

    expect($report->summary)->toBe('Nouveau resume complet');
    expect($report->status)->toBe('submitted');
    expect($report->reviewed_by)->toBeNull();
    expect($report->reviewed_at)->toBeNull();
    expect($report->supervisor_comment)->toBeNull();
});

test('student pointage page resolves the active stage through the controller', function () {
    $user = User::factory()->create();
    grantRoleWithPermissions($user, 'etudiant', ['presence.view']);

    $etudiant = Etudiant::create([
        'user_id' => $user->id,
        'nom' => 'Doe',
        'prenom' => 'Jane',
        'email' => $user->email,
    ]);

    $service = Service::create(['nom' => 'Developpement']);
    $typeStage = TypeStage::create(['code' => 'ACA', 'libelle' => 'Academique']);
    $site = Site::create([
        'code' => 'SITE-001',
        'name' => 'Siege',
    ]);

    Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'service_id' => $service->id,
        'site_id' => $site->id,
        'theme' => 'Plateforme RH',
        'date_debut' => today()->subDay(),
        'date_fin' => today()->addDay(),
    ]);

    $response = $this->actingAs($user)->get('/presence/pointage');

    $response->assertOk();
    $response->assertSee('Statut de presence');
    $response->assertSee('Plateforme RH');
    $response->assertDontSee('Aucun stage actif');
});

test('pointage page renders its injected geolocation script stack', function () {
    $user = User::factory()->create();
    grantRoleWithPermissions($user, 'employe', ['presence.view']);

    $domaine = Domaine::create([
        'nom' => 'Operations',
        'description' => 'Equipe support',
        'created_by' => $user->id,
    ]);

    $user->update(['domaine_id' => $domaine->id]);

    $response = $this->actingAs($user)->get('/presence/pointage');

    $response->assertOk();
    $response->assertSee('navigator.geolocation', false);
    $response->assertSee('Demande GPS', false);
});

test('historique page renders its chart script stack', function () {
    $user = User::factory()->create();
    grantRoleWithPermissions($user, 'employe', ['presence.view']);

    $domaine = Domaine::create([
        'nom' => 'Produit',
        'description' => 'Equipe produit',
        'created_by' => $user->id,
    ]);

    $user->update(['domaine_id' => $domaine->id]);

    AttendanceDay::create([
        'user_id' => $user->id,
        'attendance_date' => today(),
        'first_check_in_at' => now()->startOfDay()->addHours(8),
        'last_check_out_at' => now()->startOfDay()->addHours(17),
        'worked_minutes' => 480,
        'late_minutes' => 5,
        'day_status' => 'completed',
        'validation_status' => 'approved',
    ]);

    $response = $this->actingAs($user)->get('/presence/historique');

    $response->assertOk();
    $response->assertSee('personalPresenceChart', false);
    $response->assertSee('Minutes de retard', false);
});

test('student cannot access admin reports even with the report permission', function () {
    $student = User::factory()->create();
    grantRoleWithPermissions($student, 'etudiant', ['daily_reports.view']);

    $response = $this->actingAs($student)->get(route('admin.reports.index'));

    $response->assertForbidden();
});

test('permission request submission resolves the first approver for employees', function () {
    $manager = User::factory()->create();
    $employee = User::factory()->create();

    grantRoleWithPermissions($manager, 'superviseur');
    grantRoleWithPermissions($employee, 'employe');

    $domaine = Domaine::create([
        'nom' => 'Direction technique',
        'description' => 'Equipe engineering',
        'created_by' => $manager->id,
    ]);

    $employee->update(['domaine_id' => $domaine->id]);

    $response = $this
        ->actingAs($employee)
        ->post('/permissions', [
            'type' => 'retard',
            'request_date' => today()->toDateString(),
            'start_time' => '08:30',
            'end_time' => '09:00',
            'reason' => 'Retard justifie',
            'details' => 'Incident de transport',
            'intent' => 'submit',
        ]);

    $permissionRequest = PermissionRequest::query()->latest('id')->first();

    expect($permissionRequest)->not->toBeNull();
    expect($permissionRequest->first_approver_id)->toBe($manager->id);
    expect($permissionRequest->status)->toBe(PermissionRequest::STATUS_UNDER_REVIEW);

    $response->assertRedirect(route('permission-requests.show', $permissionRequest));
});

test('permission request submission notifies the supervisor and admin by mail and app notification', function () {
    Mail::fake();
    Storage::fake('local');

    $supervisor = User::factory()->create();
    $admin = User::factory()->create();
    $employee = User::factory()->create();

    grantRoleWithPermissions($supervisor, 'superviseur');
    grantRoleWithPermissions($admin, 'admin');
    grantRoleWithPermissions($employee, 'employe');

    $domaine = Domaine::create([
        'nom' => 'Support',
        'description' => 'Equipe support',
        'created_by' => $supervisor->id,
    ]);

    $employee->update(['domaine_id' => $domaine->id]);

    $this
        ->actingAs($employee)
        ->post('/permissions', [
            'type' => 'retard',
            'request_date' => today()->toDateString(),
            'start_time' => '08:45',
            'end_time' => '09:15',
            'reason' => 'Retard de circulation',
            'details' => 'Embouteillage exceptionnel',
            'intent' => 'submit',
        ])
        ->assertRedirect();

    $permissionRequest = PermissionRequest::query()->latest('id')->firstOrFail();

    expect($permissionRequest->status)->toBe(PermissionRequest::STATUS_UNDER_REVIEW);
    expect($permissionRequest->first_approver_id)->toBe($supervisor->id);

    Mail::assertSent(PermissionRequestReviewMail::class, function ($mail) use ($supervisor) {
        return $mail->hasTo($supervisor->email);
    });

    Mail::assertSent(PermissionRequestReviewMail::class, function ($mail) use ($admin) {
        return $mail->hasTo($admin->email);
    });

    expect(AppNotification::query()
        ->where('type', 'permission_request_submitted')
        ->whereIn('user_id', [$supervisor->id, $admin->id])
        ->count())->toBe(2);
});

test('admin can view but cannot approve a permission request assigned to a supervisor', function () {
    $supervisor = User::factory()->create();
    $admin = User::factory()->create();
    $employee = User::factory()->create();

    grantRoleWithPermissions($supervisor, 'superviseur');
    grantRoleWithPermissions($admin, 'admin');
    grantRoleWithPermissions($employee, 'employe');

    $domaine = Domaine::create([
        'nom' => 'Operations',
        'description' => 'Equipe terrain',
        'created_by' => $supervisor->id,
    ]);

    $employee->update(['domaine_id' => $domaine->id]);

    $this
        ->actingAs($employee)
        ->post('/permissions', [
            'type' => 'absence',
            'request_date' => today()->toDateString(),
            'reason' => 'Absence justifiee',
            'details' => 'Rendez-vous administratif',
            'intent' => 'submit',
        ]);

    $permissionRequest = PermissionRequest::query()->latest('id')->firstOrFail();

    $this
        ->actingAs($admin)
        ->post(route('permission-requests.review.approve', $permissionRequest), [
            'notes' => 'Tentative de validation admin.',
        ])
        ->assertForbidden();
});

test('permission request approval sends the generated pdf to active signataires', function () {
    Mail::fake();
    Storage::fake('local');

    $manager = User::factory()->create();
    $employee = User::factory()->create();

    grantRoleWithPermissions($manager, 'superviseur');
    grantRoleWithPermissions($employee, 'employe');

    $domaine = Domaine::create([
        'nom' => 'Ressources humaines',
        'description' => 'Equipe RH',
        'created_by' => $manager->id,
    ]);

    $employee->update(['domaine_id' => $domaine->id]);

    Signataire::create([
        'nom' => 'Direction generale',
        'poste' => 'Directeur General',
        'sigle' => 'DG',
        'email' => 'dg@example.test',
        'ordre' => 1,
        'is_active' => true,
    ]);

    $this
        ->actingAs($employee)
        ->post('/permissions', [
            'type' => 'absence',
            'request_date' => today()->toDateString(),
            'reason' => 'Absence justifiee',
            'details' => 'Motif personnel',
            'intent' => 'submit',
        ]);

    $permissionRequest = PermissionRequest::query()->latest('id')->firstOrFail();

    $this
        ->actingAs($manager)
        ->post(route('permission-requests.review.approve', $permissionRequest), [
            'notes' => 'Validation metier accordee.',
        ])
        ->assertRedirect(route('permission-requests.review.index'));

    $permissionRequest->refresh();

    expect($permissionRequest->status)->toBe(PermissionRequest::STATUS_SENT);
    expect($permissionRequest->pdf_path)->not->toBeNull();
    expect(Storage::disk('local')->exists($permissionRequest->pdf_path))->toBeTrue();
    expect($permissionRequest->sent_at)->not->toBeNull();

    Mail::assertSent(\App\Mail\PermissionRequestSignatoryMail::class, function ($mail) {
        return $mail->hasTo('dg@example.test');
    });
});

test('employee prepare checkin stores the resolved site and geofence without mixing sites', function () {
    $manager = User::factory()->create();
    $employee = User::factory()->create();

    grantRoleWithPermissions($manager, 'superviseur');
    grantRoleWithPermissions($employee, 'employe', ['presence.checkin']);

    $domaine = Domaine::create([
        'nom' => 'Logistique',
        'description' => 'Equipe logistique',
        'created_by' => $manager->id,
    ]);

    $employee->update(['domaine_id' => $domaine->id]);

    $siteA = Site::create([
        'code' => 'SITE-A',
        'name' => 'Site A',
        'is_active' => true,
    ]);

    $siteB = Site::create([
        'code' => 'SITE-B',
        'name' => 'Site B',
        'is_active' => true,
    ]);

    $domaine->sites()->attach([$siteA->id, $siteB->id]);

    SiteGeofence::create([
        'site_id' => $siteA->id,
        'name' => 'Zone A',
        'center_latitude' => 6.3600000,
        'center_longitude' => 2.4100000,
        'radius_meters' => 120,
        'allowed_accuracy_meters' => 40,
        'is_primary' => true,
        'is_active' => true,
    ]);

    $siteBGeofence = SiteGeofence::create([
        'site_id' => $siteB->id,
        'name' => 'Zone B',
        'center_latitude' => 6.3700000,
        'center_longitude' => 2.3900000,
        'radius_meters' => 120,
        'allowed_accuracy_meters' => 40,
        'is_primary' => true,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($employee)
        ->post(route('presence.prepareCheckin'), [
            'latitude' => 6.3701000,
            'longitude' => 2.3901000,
            'accuracy_meters' => 12,
            'device_fingerprint' => 'device-abc',
        ]);

    $response->assertOk();
    $response->assertSee('Site B');
    $response->assertSessionHas('pending_pointage.resolved_site_id', $siteB->id);
    $response->assertSessionHas('pending_pointage.resolved_site_geofence_id', $siteBGeofence->id);
});

test('admin presence user stats page renders the analytics charts', function () {
    $admin = User::factory()->create();
    $employee = User::factory()->create();

    grantRoleWithPermissions($admin, 'admin');
    grantRoleWithPermissions($employee, 'employe');

    $domaine = Domaine::create([
        'nom' => 'Audit',
        'description' => 'Equipe audit',
        'created_by' => $admin->id,
    ]);

    $employee->update(['domaine_id' => $domaine->id]);

    AttendanceDay::create([
        'user_id' => $employee->id,
        'attendance_date' => today(),
        'first_check_in_at' => now()->startOfDay()->addHours(8),
        'last_check_out_at' => now()->startOfDay()->addHours(16),
        'worked_minutes' => 420,
        'late_minutes' => 10,
        'arrival_status' => 'late',
        'day_status' => 'completed',
        'validation_status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.presence.user-stats', ['user' => $employee->id, 'period' => 'month']));

    $response->assertOk();
    $response->assertSee('workedHoursChart', false);
    $response->assertSee('lateChart', false);
    $response->assertSee($employee->name);
});
