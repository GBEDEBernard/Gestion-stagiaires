<?php

use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

function fichePosteAdmin(): User
{
    test()->seed(RolePermissionSeeder::class);

    $user = User::factory()->create([
        'status' => 'actif',
    ]);

    $user->assignRole('admin');

    return $user;
}

function fichePosteStage(): Stage
{
    $personnel = Personnel::create([
        'nom' => 'Fiche',
        'prenom' => 'Poste',
        'email' => 'fiche.poste@example.com',
    ]);

    $etudiant = Etudiant::create([
        'personnel_id' => $personnel->id,
        'ecole' => 'Université d\'Abomey-Calavi',
    ]);

    $personnel->update([
        'personnable_type' => Etudiant::class,
        'personnable_id' => $etudiant->id,
    ]);

    return Stage::create([
        'etudiant_id' => $etudiant->id,
        'typestage_id' => TypeStage::create(['libelle' => 'Académique', 'code' => '004'])->id,
        'theme' => 'Développement web',
        'date_debut' => now()->subDays(10),
        'date_fin' => now()->addDays(3),
    ]);
}

test('l admin peut remplir la fiche de poste et la fiche devient complete', function () {
    $admin = fichePosteAdmin();
    $stage = fichePosteStage();

    $this->actingAs($admin)
        ->get(route('stages.fiche-poste.edit', Crypt::encryptString((string) $stage->id)))
        ->assertOk()
        ->assertSee('Intitulé du poste')
        ->assertSee('Tuteur académique')
        ->assertSee('Livrable attendu');

    $this->actingAs($admin)
        ->put(route('stages.fiche-poste.update', Crypt::encryptString((string) $stage->id)), [
            'intitule_poste' => 'Stagiaire académique en développement web',
            'typestage_id' => $stage->typestage_id,
            'ecole' => 'Université d\'Abomey-Calavi',
            'filiere' => 'Informatique / Génie logiciel',
            'niveau_etude' => 'Licence 3',
            'domaine_id' => Domaine::create(['nom' => 'Service informatique / Développement'])->id,
            'tuteur_academique' => 'Dr KONNON Appolinaire – UAC',
            'theme' => 'Développement web',
            'indemnite' => 'Non rémunéré',
            'livrables' => ['Rapport de stage à déposer', 'Projet / réalisation technique'],
        ])
        ->assertRedirect(route('stages.fiche-poste.preview', Crypt::encryptString((string) $stage->id)));

    $stage->refresh();

    expect($stage->intitule_poste)->toBe('Stagiaire académique en développement web')
        ->and($stage->filiere)->toBe('Informatique / Génie logiciel')
        ->and($stage->niveau_etude)->toBe('Licence 3')
        ->and($stage->tuteur_academique)->toBe('Dr KONNON Appolinaire – UAC')
        ->and($stage->indemnite)->toBe('Non rémunéré')
        ->and($stage->livrables)->toBe(['Rapport de stage à déposer', 'Projet / réalisation technique']);
});

test('la fiche de poste saffiche complete apres remise des informations', function () {
    $admin = fichePosteAdmin();
    $stage = fichePosteStage();

    $stage->update([
        'intitule_poste' => 'Stagiaire académique en développement web',
        'filiere' => 'Informatique',
        'niveau_etude' => 'Master',
        'tuteur_academique' => 'Pr Tuteur – UAC',
        'indemnite' => 'Non rémunéré',
        'livrables' => ['Rapport de stage à déposer', 'Soutenance orale devant jury'],
    ]);

    $this->actingAs($admin)
        ->get(route('stages.fiche-poste.preview', Crypt::encryptString((string) $stage->id)))
        ->assertOk()
        ->assertSee('TECHNOLOGY FOREVER GROUP SARL')
        ->assertSee('Stagiaire académique en développement web')
        ->assertSee('Informatique')
        ->assertSee('Master')
        ->assertSee('Pr Tuteur – UAC')
        ->assertSee('Rapport de stage à déposer + Soutenance orale devant jury')
        ->assertSee('Non rémunéré')
        ->assertSee('Date de signature')
        ->assertSee('Lu et approuvé');
});

test('la fiche de poste se telecharge en PDF', function () {
    $admin = fichePosteAdmin();
    $stage = fichePosteStage();

    $this->actingAs($admin)
        ->get(route('stages.fiche-poste.download', Crypt::encryptString((string) $stage->id)))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});