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
