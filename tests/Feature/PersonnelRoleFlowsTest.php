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
            'matricule' => 'FLOW-' . $user->id,
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

test('employee role can open reports with creation and permissions', function () {
    $user = createPersonnelFlowUser('employe');

    $this->actingAs($user)->get('/reports')
        ->assertOk()
        ->assertSee('Nouveau rapport')
        ->assertDontSee('Forbidden');

    $this->actingAs($user)->get('/permissions')
        ->assertOk()
        ->assertSee('Demandes de permission')
        ->assertDontSee('user does not have right roles');
});
