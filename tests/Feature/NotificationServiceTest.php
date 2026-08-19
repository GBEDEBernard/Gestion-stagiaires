<?php

use App\Models\AppNotification;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Stage;
use App\Models\User;
use App\Services\NotificationService;
use Database\Seeders\RolePermissionSeeder;

function notificationAdminUser(): User
{
    test()->seed(RolePermissionSeeder::class);

    $user = User::factory()->create([
        'status' => 'actif',
    ]);

    $user->assignRole('admin');

    return $user;
}

test('notification generation ignores stages whose student was soft deleted', function () {
    $admin = notificationAdminUser();

    $personnel = Personnel::create([
        'nom' => 'Doe',
        'prenom' => 'Jane',
        'email' => 'jane.doe@example.com',
    ]);

    $etudiant = Etudiant::create([
        'personnel_id' => $personnel->id,
        'ecole' => 'Ecole test',
    ]);

    $personnel->update([
        'personnable_type' => Etudiant::class,
        'personnable_id' => $etudiant->id,
    ]);

    Stage::create([
        'etudiant_id' => $etudiant->id,
        'date_debut' => now()->subDays(10),
        'date_fin' => now()->addDays(3),
    ]);

    $etudiant->delete();

    $this->actingAs($admin);

    app(NotificationService::class)->generateNotifications();

    expect(AppNotification::where('type', 'stage_fin_semaine')->count())->toBe(0);
});
