<?php

/**
 * Rendu réel de chaque écran touché ou créé par cette branche.
 *
 * Les tests de service ne rendent aucune page : c'est par ce trou qu'est passée
 * une référence à config('presence...') restée dans un partiel après la
 * suppression du fichier de configuration. Une page qui s'affiche est le
 * minimum vérifiable, et c'est ce que ce fichier garantit.
 */

use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\EvaluationCriterion;
use App\Models\Jour;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Models\WorkScheduleSetting;
use Carbon\Carbon;
use Database\Seeders\PermissionTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(PermissionTypeSeeder::class);

    WorkScheduleSetting::query()->delete();
    WorkScheduleSetting::create(['start_time' => '08:00', 'end_time' => '18:00']);

    // Avant l'heure d'arrivée : aucune page ne bascule sur un écran de retard.
    Carbon::setTestNow(today()->setTime(7, 45));
});

afterEach(fn() => Carbon::setTestNow());

function smokeUser(string $role): User
{
    $personnel = Personnel::create([
        'nom'    => 'Smoke',
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

function smokeSite(): Site
{
    return Site::create([
        'code'      => 'S' . Str::upper(Str::random(4)),
        'name'      => 'Siege ' . Str::random(4),
        'qr_token'  => 'tok-' . Str::random(16),
        'is_active' => true,
    ]);
}

function smokeStage(User $student, Site $site): Stage
{
    $etudiant = Etudiant::create(['personnel_id' => $student->personnel_id, 'ecole' => 'Test']);

    Personnel::where('id', $student->personnel_id)->update([
        'personnable_type' => Etudiant::class,
        'personnable_id'   => $etudiant->id,
    ]);

    $stage = Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'T ' . Str::random(5)])->id,
        'domaine_id'   => Domaine::create(['nom' => 'D ' . Str::random(5)])->id,
        'site_id'      => $site->id,
        'theme'        => 'Sujet du stage',
        'date_debut'   => today()->subMonth()->toDateString(),
        'date_fin'     => today()->addMonth()->toDateString(),
        'expected_check_in_time'  => '08:00:00',
        'expected_check_out_time' => '18:00:00',
    ]);

    // Le jour de la semaine doit être celui d'aujourd'hui, sinon la page
    // affiche « pas un jour de présence » et non le bouton de pointage.
    $noms  = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];
    $jour  = Jour::firstOrCreate(['jour' => $noms[(int) today()->format('N')]]);
    $stage->jours()->sync([$jour->id => []]);

    return $stage;
}

test('the stage creation form renders', function () {
    // C'est cette page qui plantait : le partiel des horaires lisait une
    // configuration supprimée.
    $this->actingAs(smokeUser('admin'))
        ->get(route('stages.create'))
        ->assertOk()
        ->assertSee('Horaires')
        ->assertSee('Arrivée attendue');
});

test('the stage edit form renders', function () {
    $admin = smokeUser('admin');
    $stage = smokeStage(smokeUser('etudiant'), smokeSite());

    $this->actingAs($admin)
        ->get(route('stages.edit', $stage))
        ->assertOk()
        ->assertSee('Horaires');
});

test('the company schedule screen renders', function () {
    $this->actingAs(smokeUser('admin'))
        ->get(route('admin.presence.horaire.edit'))
        ->assertOk()
        ->assertSee('Horaire de référence');
});

test('the evaluation criteria referential renders', function () {
    $this->actingAs(smokeUser('admin'))
        ->get(route('admin.evaluations.criteres.index'))
        ->assertOk();
});

test('the stage report renders', function () {
    $admin = smokeUser('admin');
    $stage = smokeStage(smokeUser('etudiant'), smokeSite());

    $this->actingAs($admin)
        ->get(route('stages.rapport', $stage))
        ->assertOk()
        ->assertSee('Assiduité');
});

test('the evaluation table renders, empty then composed', function () {
    $admin = smokeUser('admin');
    $stage = smokeStage(smokeUser('etudiant'), smokeSite());

    $this->actingAs($admin)
        ->get(route('stages.evaluation.edit', $stage))
        ->assertOk()
        // false : l'apostrophe est littérale dans le HTML, assertSee
        // l'échapperait en &#039; et ne la trouverait jamais.
        ->assertSee("Critère d'évaluation", false);

    EvaluationCriterion::create(['label' => 'Assiduite', 'weight' => 2]);

    $this->actingAs($admin)
        ->get(route('stages.evaluation.edit', $stage))
        ->assertOk();
});

test('the admin user sheet renders', function () {
    $admin = smokeUser('admin');

    $this->actingAs($admin)
        ->get(route('admin.users.show', $admin))
        ->assertOk()
        ->assertSee('Appareils de pointage');
});

test('the sites list and the qr poster render', function () {
    $admin = smokeUser('admin');
    $site  = smokeSite();

    $this->actingAs($admin)->get(route('sites.index'))->assertOk();

    $this->actingAs($admin)
        ->get(route('sites.qr-poster', $site))
        ->assertOk()
        ->assertSee('POINTAGE DES PRÉSENCES', false);
});

test('the student pointage page renders', function () {
    $student = smokeUser('etudiant');
    smokeStage($student, smokeSite());

    $this->actingAs($student)
        ->get(route('presence.pointage'))
        ->assertOk()
        ->assertSee('Arrivée')
        ->assertSee('Pointer mon arrivée')
        // La page accueille par le prénom
        ->assertSee('Bonjour, Etudiant');
});

test('the employee pointage page renders', function () {
    $user    = smokeUser('employe');
    $site    = smokeSite();
    $domaine = Domaine::create(['nom' => 'D ' . Str::random(5)]);
    $domaine->sites()->attach($site->id);

    $employe = Employe::create([
        'personnel_id' => $user->personnel_id,
        'domaine_id'   => $domaine->id,
        'site_id'      => $site->id,
        'poste'        => 'Agent',
    ]);

    // Sans le lien polymorphe, $user->domaine reste nul et le contrôleur
    // redirige au lieu d'afficher la page.
    Personnel::where('id', $user->personnel_id)->update([
        'personnable_type' => Employe::class,
        'personnable_id'   => $employe->id,
    ]);

    $this->actingAs($user)
        ->get(route('presence.pointage'))
        ->assertOk()
        ->assertSee('Pointer mon arrivée')
        ->assertSee('Bonjour, Employe');
});

test('the profile page renders without the account activity card', function () {
    $user = smokeUser('employe');

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Mon appareil de pointage')
        ->assertDontSee('Activité du compte');
});

test('the attendance tracking history renders', function () {
    $admin = smokeUser('admin');

    $this->actingAs($admin)
        ->get(route('attendance.tracking.user.historique', $admin))
        ->assertOk();
});

test('the qr scan screen renders for an enrolled phone', function () {
    $user = smokeUser('employe');
    $site = smokeSite();

    $this->actingAs($user)
        ->get(route('presence.qr.scan', ['site_token' => $site->qr_token]))
        ->assertOk()
        ->assertSee('Bonjour');
});

test('a late arrival asks for the reason in a modal, not inline', function () {
    Carbon::setTestNow(today()->setTime(10, 30));

    $student = smokeUser('etudiant');
    smokeStage($student, smokeSite());

    $html = $this->actingAs($student)
        ->get(route('presence.pointage'))
        ->assertOk()
        ->assertSee('Vous arrivez après 08:00')
        ->getContent();

    // Le champ vit dans la modale, pas dans le flux de la page
    expect($html)->toContain('modalRetard')
        ->and($html)->toContain("pointageForm(true)");
});

test('an on time arrival carries no late modal at all', function () {
    $student = smokeUser('etudiant');
    smokeStage($student, smokeSite());

    $this->actingAs($student)
        ->get(route('presence.pointage'))
        ->assertOk()
        ->assertDontSee('Vous arrivez après')
        ->assertSee('pointageForm(false)', false);
});
