<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Service;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\EtudiantAccountService;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EtudiantsPresenceSeeder extends Seeder
{
    /**
     * Créer 5 étudiants complets avec toutes dépendances + comptes users
     * Stages actifs + badges + pointages récents pour démo.
     */
    public function run(EtudiantAccountService $accountService): void
    {
        // Services fictifs si vides
        $this->ensureServices();
        $this->ensureSites();
        $this->ensureTypeStages();
        $this->ensureJours();

        $presetService = app(RolePermissionPresetService::class);

        $etudiantsData = [
            [
                'nom' => 'Martin',
                'prenom' => 'Lucas',
                'email' => 'lucas.martin@etudiant.tfg',
                'telephone' => '06 12 34 56 01',
                'genre' => 'Masculin',
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Emma',
                'email' => 'emma.dupont@etudiant.tfg',
                'telephone' => '06 12 34 56 02',
                'genre' => 'Féminin',
            ],
            [
                'nom' => 'Lefèvre',
                'prenom' => 'Noah',
                'email' => 'noah.lefevre@etudiant.tfg',
                'telephone' => '06 12 34 56 03',
                'genre' => 'Masculin',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Jade',
                'email' => 'jade.moreau@etudiant.tfg',
                'telephone' => '06 12 34 56 04',
                'genre' => 'Féminin',
            ],
            [
                'nom' => 'Roux',
                'prenom' => 'Leo',
                'email' => 'leo.roux@etudiant.tfg',
                'telephone' => '06 12 34 56 05',
                'genre' => 'Masculin',
            ],
        ];

        foreach ($etudiantsData as $data) {
            $etudiant = Etudiant::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Créer compte user lié
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['prenom'] . ' ' . $data['nom'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'phone' => $data['telephone'],
                ]
            );
            $user->assignRole('etudiant');
            $presetService->ensureRoleDefaults($user, ['etudiant']);
            $etudiant->user_id = $user->id;
            $etudiant->save();

            // Stage actif
            $stage = Stage::create([
                'etudiant_id' => $etudiant->id,
                'typestage_id' => TypeStage::inRandomOrder()->first()->id,
                'service_id' => Service::inRandomOrder()->first()->id,
                'site_id' => Site::inRandomOrder()->first()->id,
                'supervisor_id' => User::role('superviseur')->first()?->id ?? 1,
                'date_debut' => now()->subWeek()->format('Y-m-d'),
                'date_fin' => now()->addMonth()->format('Y-m-d'),
                'expected_check_in_time' => '08:30:00',
                'expected_check_out_time' => '17:30:00',
                'allowed_late_minutes' => 15,
                'allowed_early_departure_minutes' => 15,
                'presence_mode' => 'geolocation_only',
            ]);

            // Badge
            $badge = Badge::create([
                'badge' => 'TFG' . str_pad(Badge::count() + 1, 4, '0', STR_PAD_LEFT),
            ]);
            $stage->badge_id = $badge->id;
            $stage->save();

            // Pointages récents pour démo
            $this->createDemoAttendance($stage);
        }

        // Personnel entreprise (sans étudiant/stage)
        $this->createPersonnelUsers();
    }

    protected function ensureServices(): void
    {
        if (Service::count() === 0) {
            Service::create(['name' => 'Service Technique', 'description' => 'Maintenance et réparation']);
            Service::create(['name' => 'Service Opérationnel', 'description' => 'Production quotidienne']);
            Service::create(['name' => 'Service Commercial', 'description' => 'Ventes et clients']);
            Service::create(['name' => 'Service RH', 'description' => 'Ressources humaines']);
            Service::create(['name' => 'Service Finance', 'description' => 'Comptabilité et gestion']);
        }
    }

    protected function ensureSites(): void
    {
        if (Site::count() === 0) {
            Site::create(['code' => 'TFG01', 'name' => 'Site Principal TFG', 'address' => '123 Rue Industrielle', 'city' => 'Paris', 'latitude' => '48.8566', 'longitude' => '2.3522']);
            Site::create(['code' => 'TFG02', 'name' => 'Site Technique', 'address' => '456 Avenue Maintenance', 'city' => 'Lyon', 'latitude' => '45.7640', 'longitude' => '4.8357']);
            Site::create(['code' => 'TFG03', 'name' => 'Site Opérationnel', 'address' => '789 Boulevard Production', 'city' => 'Marseille', 'latitude' => '43.2965', 'longitude' => '5.3698']);
        }
    }

    protected function ensureTypeStages(): void
    {
        if (TypeStage::count() === 0) {
            TypeStage::create(['nom' => 'Stage Technique', 'description' => 'Maintenance']);
            TypeStage::create(['nom' => 'Stage Commercial', 'description' => 'Ventes']);
            TypeStage::create(['nom' => 'Stage Opérationnel', 'description' => 'Production']);
        }
    }

    protected function ensureJours(): void
    {
        if (Jour::count() === 0) {
            Jour::create(['jour_semaine' => 'Lundi', 'nombre_heures' => 8]);
            Jour::create(['jour_semaine' => 'Mardi', 'nombre_heures' => 8]);
            Jour::create(['jour_semaine' => 'Mercredi', 'nombre_heures' => 8]);
            Jour::create(['jour_semaine' => 'Jeudi', 'nombre_heures' => 8]);
            Jour::create(['jour_semaine' => 'Vendredi', 'nombre_heures' => 8]);
        }
    }

    protected function createDemoAttendance(Stage $stage): void
    {
        // Pointage hier + aujourd'hui
        $dates = [now()->subDay(), now()];

        foreach ($dates as $date) {
            // Check-in
            \App\Models\AttendanceEvent::create([
                'stage_id' => $stage->id,
                'etudiant_id' => $stage->etudiant_id,
                'site_id' => $stage->site_id,
                'user_id' => $stage->etudiant->user_id,
                'event_type' => 'check_in',
                'status' => 'approved',
                'occurred_at' => $date->setTime(8, rand(0, 30)),
                'latitude' => 48.8566 + (rand(-50, 50) / 10000),
                'longitude' => 2.3522 + (rand(-50, 50) / 10000),
            ]);

            // Check-out
            \App\Models\AttendanceEvent::create([
                'stage_id' => $stage->id,
                'etudiant_id' => $stage->etudiant_id,
                'site_id' => $stage->site_id,
                'user_id' => $stage->etudiant->user_id,
                'event_type' => 'check_out',
                'status' => 'approved',
                'occurred_at' => $date->setTime(17, rand(0, 30)),
                'latitude' => 48.8566 + (rand(-50, 50) / 10000),
                'longitude' => 2.3522 + (rand(-50, 50) / 10000),
            ]);
        }
    }

    protected function createPersonnelUsers(): void
    {
        $presetService = app(RolePermissionPresetService::class);

        $personnelData = [
            ['name' => 'Dupuis Marc', 'email' => 'marc.dupuis@tfg.fr', 'service' => 'Technique'],
            ['name' => 'Leroy Sophie', 'email' => 'sophie.leroy@tfg.fr', 'service' => 'Opérationnel'],
            ['name' => 'Gauthier Paul', 'email' => 'paul.gauthier@tfg.fr', 'service' => 'Commercial'],
        ];

        foreach ($personnelData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                ]
            );
            $user->assignRole('superviseur');
            $presetService->ensureRoleDefaults($user, ['superviseur']);
        }
    }
}
