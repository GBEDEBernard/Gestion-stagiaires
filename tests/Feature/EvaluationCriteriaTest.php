<?php

use App\Models\EvaluationCriterion;
use App\Models\Personnel;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\EvaluationGridService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->grid = app(EvaluationGridService::class);
});

function critereUser(string $role = 'admin'): User
{
    $personnel = Personnel::create([
        'nom'    => 'Critere',
        'prenom' => ucfirst($role),
        'email'  => "{$role}." . Str::random(6) . '@example.com',
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

function makeType(string $libelle): TypeStage
{
    return TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => $libelle]);
}

test('the grid is global: every active criterion applies to every stage type', function () {
    // Décision : plus de distinction académique / professionnel. Les critères
    // déjà créés réapparaissent pour tout le monde.
    $academique    = makeType('Academique');
    $professionnel = makeType('Professionnel');

    EvaluationCriterion::create(['label' => 'Assiduite', 'weight' => 2, 'sort_order' => 1]);
    EvaluationCriterion::create(['label' => 'Autonomie', 'weight' => 3, 'sort_order' => 2]);

    foreach ([$academique, $professionnel] as $type) {
        expect($this->grid->forTypeStage($type)->pluck('label')->all())
            ->toBe(['Assiduite', 'Autonomie']);
    }

    expect($this->grid->totalWeight($this->grid->forTypeStage($academique)))->toBe(5);
});

test('a retired criterion leaves the grid without being deleted', function () {
    $type = makeType('Academique');

    $c = EvaluationCriterion::create(['label' => 'Ancien critere', 'typestage_id' => $type->id]);

    expect($this->grid->forTypeStage($type))->toHaveCount(1);

    $c->update(['is_active' => false]);

    expect($this->grid->forTypeStage($type))->toHaveCount(0)
        ->and(EvaluationCriterion::find($c->id))->not->toBeNull();
});

test('the overview reports an empty grid so it can be flagged', function () {
    $vide  = makeType('Sans criteres');
    $plein = makeType('Avec criteres');

    EvaluationCriterion::create(['label' => 'Un critere', 'typestage_id' => $plein->id, 'weight' => 4]);

    $overview = $this->grid->overview()->keyBy(fn($r) => $r['typestage']->libelle);

    expect($overview['Sans criteres']['total'])->toBe(0)
        ->and($overview['Avec criteres']['total'])->toBe(1)
        ->and($overview['Avec criteres']['weight'])->toBe(4);
});

test('an admin can create a criterion for one type only', function () {
    $admin = critereUser('admin');
    $type  = makeType('Academique');

    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.store'), [
            'label'        => 'Qualite du memoire',
            'typestage_id' => $type->id,
            'weight'       => 3,
            'description'  => 'Structure, orthographe, profondeur.',
            'sort_order'   => 2,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('evaluation_criteria', [
        'label'        => 'Qualite du memoire',
        'typestage_id' => $type->id,
        'weight'       => 3,
        'is_auto'      => false,
        'created_by'   => $admin->id,
    ]);
});

test('a criterion left without a type is shared across every grid', function () {
    $admin = critereUser('admin');
    makeType('Academique');

    // Le formulaire envoie une chaîne vide, pas null
    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.store'), [
            'label'        => 'Ponctualite',
            'typestage_id' => '',
            'weight'       => 1,
        ])
        ->assertRedirect();

    $criterion = EvaluationCriterion::where('label', 'Ponctualite')->first();

    expect($criterion->typestage_id)->toBeNull()
        ->and($criterion->isShared())->toBeTrue();
});

test('the same label cannot appear twice in one grid but can across two', function () {
    $admin = critereUser('admin');
    $a     = makeType('Academique');
    $b     = makeType('Professionnel');

    $payload = ['label' => 'Autonomie', 'typestage_id' => $a->id, 'weight' => 1];

    $this->actingAs($admin)->post(route('admin.evaluations.criteres.store'), $payload)->assertRedirect();

    // Doublon dans la même grille : refusé
    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.store'), $payload)
        ->assertSessionHasErrors('label');

    // Même intitulé pour un autre type : accepté
    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.store'), ['label' => 'Autonomie', 'typestage_id' => $b->id, 'weight' => 1])
        ->assertRedirect();

    expect(EvaluationCriterion::where('label', 'Autonomie')->count())->toBe(2);
});

test('an automatic criterion must say what it computes on', function () {
    $admin = critereUser('admin');

    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.store'), [
            'label'   => 'Assiduite calculee',
            'weight'  => 2,
            'is_auto' => 1,
        ])
        ->assertSessionHasErrors('auto_source');

    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.store'), [
            'label'       => 'Assiduite calculee',
            'weight'      => 2,
            'is_auto'     => 1,
            'auto_source' => 'attendance',
        ])
        ->assertRedirect();

    expect(EvaluationCriterion::where('label', 'Assiduite calculee')->first()->auto_source)
        ->toBe('attendance');
});

test('unticking the automatic box clears the computation source', function () {
    $admin = critereUser('admin');

    $criterion = EvaluationCriterion::create([
        'label'       => 'Assiduite',
        'weight'      => 2,
        'is_auto'     => true,
        'auto_source' => 'attendance',
    ]);

    $this->actingAs($admin)
        ->put(route('admin.evaluations.criteres.update', $criterion), [
            'label'  => 'Assiduite',
            'weight' => 2,
        ])
        ->assertRedirect();

    $criterion->refresh();

    // Laisser une source sur un critère redevenu manuel piégerait la phase de notation
    expect($criterion->is_auto)->toBeFalse()
        ->and($criterion->auto_source)->toBeNull();
});

test('toggling retires the criterion instead of destroying it', function () {
    $admin     = critereUser('admin');
    $criterion = EvaluationCriterion::create(['label' => 'Esprit d equipe', 'weight' => 1]);

    $this->actingAs($admin)
        ->post(route('admin.evaluations.criteres.toggle', $criterion))
        ->assertRedirect();

    expect($criterion->fresh()->is_active)->toBeFalse();

    $this->actingAs($admin)->post(route('admin.evaluations.criteres.toggle', $criterion));

    expect($criterion->fresh()->is_active)->toBeTrue();
});

test('the referential is closed to everyone but the admin', function () {
    $etudiant = critereUser('etudiant');
    $employe  = critereUser('employe');

    foreach ([$etudiant, $employe] as $user) {
        $response = $this->actingAs($user)->get(route('admin.evaluations.criteres.index'));
        expect($response->status())->not->toBe(200);
    }

    $this->actingAs($etudiant)
        ->post(route('admin.evaluations.criteres.store'), ['label' => 'Tentative', 'weight' => 1]);

    expect(EvaluationCriterion::where('label', 'Tentative')->exists())->toBeFalse();
});

test('the admin page shows each grid composition', function () {
    $admin = critereUser('admin');
    $type  = makeType('Academique');

    EvaluationCriterion::create(['label' => 'Assiduite', 'weight' => 2]);
    EvaluationCriterion::create(['label' => 'Memoire', 'typestage_id' => $type->id, 'weight' => 3]);

    $this->actingAs($admin)
        ->get(route('admin.evaluations.criteres.index', ['typestage' => $type->id]))
        ->assertOk()
        ->assertViewIs('admin.evaluations.criteres.index')
        ->assertSee('Academique')
        ->assertSee('Assiduite')
        ->assertSee('Memoire')
        ->assertSee('1 commun(s) + 1 propre(s)');
});
