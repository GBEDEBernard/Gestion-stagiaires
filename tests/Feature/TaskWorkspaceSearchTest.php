<?php

/**
 * Recherche du workspace des tâches.
 *
 * La barre de recherche est le seul moyen, pour un admin qui voit les tâches
 * de tout le monde, de retrouver « la tâche de Bernard » ou « la tâche qui
 * parle de rapport ». Elle était cassée sans qu'aucune page ne signale l'erreur :
 * la requête collait la chaîne entière sur une seule colonne, donc un nom
 * complet — « Bernard GBEDE », exactement ce qui est affiché à l'écran —
 * ne pouvait jamais être trouvé.
 */

use App\Models\Personnel;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function searchUser(string $prenom, string $nom): User
{
    $personnel = Personnel::create([
        'nom'    => $nom,
        'prenom' => $prenom,
        'email'  => Str::slug($prenom . '.' . $nom, '.') . '@example.com',
    ]);

    $user = User::create([
        'personnel_id'      => $personnel->id,
        'name'              => $personnel->full_name,
        'email'             => $personnel->email,
        'email_verified_at' => now(),
        'password'          => Hash::make('password'),
        'status'            => 'actif',
    ]);

    $user->assignRole('admin');

    return $user;
}

/** Titre simple : le HTML echappe les accents, on compare sur du texte neutre. */
function searchTask(User $owner, string $title, string $description = 'Description'): Task
{
    return Task::create([
        'owner_id'    => $owner->id,
        'title'       => $title,
        'description' => $description,
        'status'      => 'pending',
        'priority'    => 'normal',
    ]);
}

it('trouve une tâche par le nom complet du propriétaire affiché', function () {
    $bernard = searchUser('Bernard', 'GBEDE');
    $autre   = searchUser('Employe', 'TFG');

    $task = searchTask($bernard, 'Sunt qui assumenda e');
    searchTask($autre, 'Nulla adipisci quis');

    // Le nom affiché dans la liste est « Bernard GBEDE » : c'est ce mot-là
    // que l'utilisateur recopie, dans l'ordre, avec la majuscule.
    $this->actingAs($bernard)
        ->get(route('tasks.index', ['q' => 'Bernard GBEDE']))
        ->assertOk()
        ->assertSee('Sunt qui assumenda e')
        ->assertDontSee('Nulla adipisci quis');
});

it('trouve une tâche par une partie du nom, quelle que soit la casse', function () {
    $bernard = searchUser('Bernard', 'GBEDE');
    $autre   = searchUser('Employe', 'TFG');

    searchTask($bernard, 'Sunt qui assumenda e');
    searchTask($autre, 'Nulla adipisci quis');

    foreach (['gbede', 'GBEDE', 'bernard', 'BeRnArD gBeDe'] as $terme) {
        $this->actingAs($bernard)
            ->get(route('tasks.index', ['q' => $terme]))
            ->assertOk()
            ->assertSee('Sunt qui assumenda e')
            ->assertDontSee('Nulla adipisci quis');
    }
});

it('trouve une tâche par son titre complet ou par quelques mots', function () {
    $admin = searchUser('Bernard', 'GBEDE');

    searchTask($admin, 'Sunt qui assumenda e');

    foreach (['Sunt qui assumenda e', 'sunt qui', 'assumenda'] as $terme) {
        $this->actingAs($admin)
            ->get(route('tasks.index', ['q' => $terme]))
            ->assertOk()
            ->assertSee('Sunt qui assumenda e');
    }
});

it('trouve une tâche par le nom de la personne assignée, même sans la posséder', function () {
    $admin   = searchUser('Bernard', 'GBEDE');
    $brigade = searchUser('Appolinaire', 'KONNON');

    $task = searchTask($admin, 'Remonter le dossier');
    $task->assignees()->attach($brigade->id);

    $this->actingAs($admin)
        ->get(route('tasks.index', ['q' => 'KONNON']))
        ->assertOk()
        ->assertSee('Remonter le dossier');
});

it('affiche un message explicite quand rien ne correspond', function () {
    $admin = searchUser('Bernard', 'GBEDE');

    searchTask($admin, 'Sunt qui assumenda e');

    $this->actingAs($admin)
        ->get(route('tasks.index', ['q' => 'pneumatique']))
        ->assertOk()
        ->assertSee('Aucun résultat')
        ->assertDontSee('Sunt qui assumenda e');
});

it('conserve la tâche ouverte quand on lance une recherche', function () {
    $admin = searchUser('Bernard', 'GBEDE');
    $task  = searchTask($admin, 'Remonter le dossier');

    // La recherche se fait depuis le workspace : le panneau de droite ne doit
    // pas se vider sous les doigts de l'utilisateur.
    $this->actingAs($admin)
        ->get(route('tasks.index', [
            'q'    => 'dossier',
            'task' => Crypt::encryptString((string) $task->id),
        ]))
        ->assertOk()
        ->assertSee('Remonter le dossier');
});

it('ne laisse pas un tiers ouvrir une tâche hors de sa portée', function () {
    $admin = searchUser('Bernard', 'GBEDE');

    $task = Task::create([
        'title'       => 'Tache confidentielle',
        'description' => 'Description',
        'status'      => 'pending',
        'priority'    => 'normal',
    ]);

    $this->actingAs($admin)
        ->get(route('tasks.index', ['task' => Crypt::encryptString((string) $task->id)]))
        ->assertOk()
        ->assertDontSee('Tache confidentielle');
});
