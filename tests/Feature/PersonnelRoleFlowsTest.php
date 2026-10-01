<?php

use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

function createPersonnelFlowUser(string $role): User
{
    test()->seed(RolePermissionSeeder::class);

    $personnel = Personnel::create([
        'nom' => ucfirst($role),
        'prenom' => 'Test',
        'email' => "{$role}.flow@example.com",
    ]);

    $userData = [
        'personnel_id' => $personnel->id,
        'email' => $personnel->email,
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'status' => 'actif',
    ];

    if (Schema::hasColumn('users', 'name')) {
        $userData['name'] = $personnel->full_name;
    }

    $user = User::create($userData);

    if ($role === 'etudiant') {
        $profile = Etudiant::create([
            'personnel_id' => $personnel->id,
            'ecole' => 'Ecole test',
        ]);
    } else {
        $site = Site::firstOrCreate(
            ['code' => 'FLOW'],
            ['name' => 'Site flow', 'address' => 'Local', 'city' => 'Cotonou', 'country' => 'Benin', 'is_active' => true]
        );
        $domaine = Domaine::create([
            'nom' => 'Domaine flow',
            'description' => 'Domaine de test',
            'created_by' => $user->id,
        ]);

        $profile = Employe::create([
            'personnel_id' => $personnel->id,
            'domaine_id' => $domaine->id,
            'site_id' => $site->id,
            'poste' => 'Employe test',
        ]);

        $user->update(['domaine_id' => $domaine->id]);
    }

    $personnel->update([
        'personnable_type' => $role === 'etudiant' ? Etudiant::class : Employe::class,
        'personnable_id' => $profile->id,
    ]);

    $user->assignRole($role);

    return $user->fresh();
}

test('student role sees the student history and report spaces', function () {
    $user = createPersonnelFlowUser('etudiant');

    $this->actingAs($user)->get('/mon-stage')
        ->assertOk()
        ->assertDontSee("Votre compte n'est pas encore");

    $this->actingAs($user)->get('/presence/historique')
        ->assertOk()
        ->assertSee('Mon historique de présence')
        ->assertDontSee('Espace employ');

    $this->actingAs($user)->get('/reports')
        ->assertOk()
        ->assertSee('Nouveau rapport')
        ->assertDontSee('Forbidden');
});

test('admin role is redirected from student stage page to dashboard without error', function () {
    $user = createPersonnelFlowUser('admin');

    $this->actingAs($user)
        ->get('/mon-stage')
        ->assertRedirect(route('dashboard'));
});

test('employee role can open reports with creation and permissions', function () {
    $user = createPersonnelFlowUser('employe');

    // Le pointage du jour est requis avant d'accéder au site.
    \App\Models\AttendanceDay::create([
        'user_id' => $user->id,
        'attendance_date' => today()->toDateString(),
        'first_check_in_at' => now(),
        'day_status' => 'present',
        'validation_status' => 'auto_approuve',
    ]);

    $this->actingAs($user)->get('/reports')
        ->assertOk()
        ->assertSee('Nouveau rapport')
        ->assertDontSee('Forbidden');

    $this->actingAs($user)->get('/permissions')
        ->assertOk()
        ->assertSee('Demandes de permission')
        ->assertDontSee('user does not have right roles');
});

test('admin can prolong a stage and keep the historical sequence visible', function () {
    $admin = createPersonnelFlowUser('admin');

    // Le helper crée déjà la fiche Etudiant pour le rôle étudiant : en créer
    // une seconde sur le même personnel viole la clé unique.
    $etudiant = createPersonnelFlowUser('etudiant')->etudiant;

    expect($etudiant)->not->toBeNull();

    $typeStage = \App\Models\TypeStage::create(['code' => 'PRO', 'libelle' => 'Professionnel']);
    $domaine = Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test']);
    $site = Site::firstOrCreate(
        ['code' => 'TFG-PRO'],
        ['name' => 'Site prolongation', 'address' => 'Cotonou', 'city' => 'Cotonou', 'country' => 'Benin', 'is_active' => true]
    );

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id' => $domaine->id,
        'site_id' => $site->id,
        'theme' => 'Développement web',
        'date_debut' => '2025-01-10',
        'date_fin' => '2025-03-10',
        'expected_check_in_time' => '08:00',
        'expected_check_out_time' => '17:00',
    ]);

    $this->actingAs($admin)
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => '2025-03-11',
            'date_fin' => '2025-05-11',
            'theme' => 'Développement web - prolongation',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('stages', [
        'etudiant_id' => $etudiant->id,
        'domaine_id' => $domaine->id,
        'theme' => 'Développement web - prolongation',
    ]);

    $this->assertCount(2, $etudiant->fresh()->stages()->orderBy('date_debut')->get());
});

test('a stage in progress cannot be prolonged and the button is disabled', function () {
    $admin = createPersonnelFlowUser('admin');
    $etudiant = createPersonnelFlowUser('etudiant')->etudiant;

    $typeStage = \App\Models\TypeStage::create(['code' => 'PRO3', 'libelle' => 'Professionnel']);
    $domaine = Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test']);

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id' => $domaine->id,
        'theme' => 'Stage en cours',
        'date_debut' => now()->subDays(20)->toDateString(),
        'date_fin' => now()->addDays(20)->toDateString(),
    ]);

    expect($stage->estProlongable())->toBeFalse();
    expect($stage->motifProlongationRefusee())->toContain('encore en cours');

    // Le bouton doit être visible mais inactif, avec le motif en infobulle :
    // le retirer priverait l'admin de l'explication.
    $this->actingAs($admin)
        ->get(route('stages.show', $stage))
        ->assertOk()
        ->assertSee('Prolonger le stage')
        ->assertSee('cursor-not-allowed', false)
        ->assertSee('Prolonger une période en cours', false);

    // Le contrôle serveur doit refuser, même en forçant le POST : un bouton
    // désactivé n'est pas une protection.
    $this->actingAs($admin)
        ->from(route('stages.show', $stage))
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => now()->subDays(10)->toDateString(),
            'date_fin' => now()->addDays(40)->toDateString(),
        ])
        ->assertRedirect(route('stages.show', $stage))
        ->assertSessionHasErrors('date_debut');

    expect($etudiant->fresh()->stages()->count())->toBe(1);

    // Une fois la période terminée, la prolongation redevient possible et le
    // message d'explication disparaît.
    $stage->update(['date_fin' => now()->subDay()->toDateString()]);

    expect($stage->fresh()->estProlongable())->toBeTrue()
        ->and($stage->fresh()->motifProlongationRefusee())->toBeNull();
});

test('prolonging a stage reactivates the deactivated student account', function () {
    $admin = createPersonnelFlowUser('admin');
    $etudiantUser = createPersonnelFlowUser('etudiant');
    $etudiant = $etudiantUser->etudiant;

    $typeStage = \App\Models\TypeStage::create(['code' => 'PRO5', 'libelle' => 'Professionnel']);
    $domaine = Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test']);

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id' => $domaine->id,
        'theme' => 'Stage terminé',
        'date_debut' => now()->subDays(60)->toDateString(),
        'date_fin' => now()->subDays(30)->toDateString(),
    ]);

    // Tous les stages sont terminés : le compte a été désactivé par le
    // service d'expiration, comme cela se produit en production.
    expect(app(\App\Services\StudentStageExpiryService::class)->hasExpiredStage($etudiantUser))->toBeTrue();

    $etudiantUser->forceFill(['status' => 'inactif'])->save();
    expect($etudiantUser->fresh()->status)->toBe('inactif');

    $this->actingAs($admin)
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => now()->subDays(29)->toDateString(),
            'date_fin' => now()->addDays(30)->toDateString(),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Le compte redevient utilisable, sans quoi le stagiaire ne pourrait plus
    // pointer pendant une période où son stage est bien en cours.
    expect($etudiantUser->fresh()->status)->toBe('actif');

    // Et il n'est plus considéré comme désactivé par le service d'expiration.
    expect(app(\App\Services\StudentStageExpiryService::class)->hasExpiredStage($etudiantUser->fresh()))->toBeFalse();

    // Le stagiaire peut de nouveau se connecter : c'est le contrôle de
    // LoginRequest qui bloquait les comptes inactifs.
    $this->post(route('login'), [
        'email' => $etudiantUser->email,
        'password' => 'password',
    ])->assertRedirect();
});

test('a prolongation shares one attestation, a separate stage keeps its own', function () {
    $admin = createPersonnelFlowUser('admin');
    $etudiant = createPersonnelFlowUser('etudiant')->etudiant;

    $typeStage = \App\Models\TypeStage::create(['code' => 'PRO4', 'libelle' => 'Professionnel']);
    $domaine = Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test']);

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id' => $domaine->id,
        'theme' => 'Stage initial',
        'date_debut' => '2025-01-10',
        'date_fin' => '2025-03-10',
    ]);

    $this->actingAs($admin)
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => '2025-03-11',
            'date_fin' => '2025-05-11',
        ])->assertRedirect();

    $prolongation = $etudiant->fresh()->stages()->where('theme', 'Stage initial')->first();

    // Ouvrir l'attestation depuis le segment prolongé doit reprendre celle du
    // stage d'origine : deux attestations proclameraient deux stages.
    $this->actingAs($admin)
        ->get(route('stages.attestation.show', $prolongation))
        ->assertOk();

    $this->assertCount(1, \App\Models\Attestation::whereIn(
        'stage_id',
        $etudiant->fresh()->stages()->pluck('id')
    )->get());

    // Un stage sans lien reste une chaîne à part entière, avec sa propre
    // attestation.
    $autre = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id' => $domaine->id,
        'theme' => 'Nouveau stage plus tard',
        'date_debut' => '2026-09-01',
        'date_fin' => '2026-11-30',
    ]);

    $this->actingAs($admin)
        ->get(route('stages.attestation.show', $autre))
        ->assertOk();

    // Même domaine, même type, mais aucun lien : la durée du nouveau stage ne
    // doit pas s'ajouter à celle de la chaîne précédente.
    expect($autre->getRelatedSequenceStages()->pluck('id')->all())->toBe([$autre->id])
        ->and($autre->totalDurationInDays())->toBe(91);
});

test('the student sees one card per stage, prolongations included in its duration', function () {
    $etudiantUser = createPersonnelFlowUser('etudiant');
    $etudiant = $etudiantUser->etudiant;
    $admin = createPersonnelFlowUser('admin');

    $typeStage = \App\Models\TypeStage::create(['code' => 'PRO5', 'libelle' => 'Professionnel']);
    $domaine = Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test']);

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => $typeStage->id,
        'domaine_id' => $domaine->id,
        'theme' => 'Stage prolongé',
        'date_debut' => '2025-01-10',
        'date_fin' => '2025-03-10',
    ]);

    $this->actingAs($admin)
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => '2025-03-11',
            'date_fin' => '2025-05-11',
        ])->assertRedirect();

    $this->actingAs($etudiantUser)
        ->get(route('student.stage'))
        ->assertOk()
        ->assertSee('Mon parcours')
        // La carte porte la durée cumulée, pas celle du seul segment.
        // Les libellés accentués sont comparés en clair : Blade n'échappe pas
        // le texte saisi en dur dans le gabarit.
        ->assertSee('prolongé 1', false)
        ->assertSee('Périodes effectuées', false);
});

test('the badge belongs to the student, a prolongation does not duplicate it', function () {
    $admin = createPersonnelFlowUser('admin');
    $etudiant = createPersonnelFlowUser('etudiant')->etudiant;

    $badge = \App\Models\Badge::create(['badge' => 'TFG-TEST-01']);

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => \App\Models\TypeStage::create(['code' => 'PRO2', 'libelle' => 'Professionnel'])->id,
        'domaine_id' => Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test'])->id,
        'badge_id' => $badge->id,
        'theme' => 'Stage initial',
        'date_debut' => '2025-01-10',
        'date_fin' => '2025-03-10',
    ]);

    $this->actingAs($admin)
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => '2025-03-11',
            'date_fin' => '2025-05-11',
        ])
        ->assertRedirect();

    // Le numéro reste sur le stage d'origine : une seule carte par étudiant,
    // pas une carte par segment de stage.
    $prolongation = $etudiant->fresh()->stages()
        ->where('theme', 'Stage initial')->first();

    expect($stage->fresh()->badge_id)->toBe($badge->id)
        ->and($etudiant->fresh()->stages()->whereNotNull('badge_id')->count())->toBe(1);
});

test('a stage starting before the current one ends is refused', function () {
    $admin = createPersonnelFlowUser('admin');
    $etudiant = createPersonnelFlowUser('etudiant')->etudiant;

    $stage = \App\Models\Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => \App\Models\TypeStage::create(['code' => 'PRO3', 'libelle' => 'Professionnel'])->id,
        'domaine_id' => Domaine::create(['nom' => 'Informatique', 'description' => 'Domaine test'])->id,
        'theme' => 'Stage initial',
        'date_debut' => '2025-01-10',
        'date_fin' => '2025-03-10',
    ]);

    // Une prolongation qui chevauche le stage existant n'est pas une
    // prolongation : le stage d'origine est alors modifié sur une fausse
    // prémisse, et l'assiduité compterait deux fois la même période.
    $this->actingAs($admin)
        ->post(route('stages.prolonger', $stage), [
            'date_debut' => '2025-03-01',
            'date_fin' => '2025-05-11',
        ])
        ->assertSessionHasErrors('date_debut');

    expect($etudiant->fresh()->stages()->count())->toBe(1);
});
