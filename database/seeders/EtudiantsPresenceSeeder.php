<?php

namespace Database\Seeders;

use App\Models\AttendanceEvent;
use App\Models\Badge;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EtudiantsPresenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureServices();
        $this->ensureSites();
        $this->ensureTypeStages();
        $this->ensureJours();

        $tfgSite = $this->getTfgSite();
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
                'genre' => 'Feminin',
            ],
            [
                'nom' => 'Lefevre',
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
                'genre' => 'Feminin',
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
            $personnel = Personnel::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'telephone' => $data['telephone'],
                    'genre' => $data['genre'],
                    'date_naissance' => null,
                    'adresse' => null,
                    'created_by' => null,
                ]
            );

            $etudiant = Etudiant::updateOrCreate(
                ['personnel_id' => $personnel->id],
                ['ecole' => 'TFG Demo']
            );

            $personnel->update([
                'personnable_type' => Etudiant::class,
                'personnable_id' => $etudiant->id,
            ]);

            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                [
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'status' => 'actif',
                ]
            );

            $user->assignRole('etudiant');
            $presetService->ensureRoleDefaults($user, ['etudiant']);

            $stage = Stage::updateOrCreate(
                [
                    'etudiant_id' => $etudiant->id,
                    'theme' => 'Stage demo presence',
                ],
                [
                    'typestage_id' => TypeStage::inRandomOrder()->first()->id,
                    'service_id' => Service::inRandomOrder()->first()->id,
                    'site_id' => $tfgSite->id,
                    'supervisor_id' => User::role('superviseur')
                        ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
                        ->orderBy('personnels.nom')
                        ->orderBy('personnels.prenom')
                        ->select('users.*')
                        ->first()?->id ?? 1,
                    'date_debut' => now()->subWeek()->format('Y-m-d'),
                    'date_fin' => now()->addMonth()->format('Y-m-d'),
                    'expected_check_in_time' => '08:30:00',
                    'expected_check_out_time' => '17:30:00',
                    'allowed_late_minutes' => 15,
                    'allowed_early_departure_minutes' => 15,
                    'presence_mode' => 'geolocation_only',
                ]
            );

            $badge = Badge::firstOrCreate([
                'badge' => 'TFG' . str_pad((string) $etudiant->id, 4, '0', STR_PAD_LEFT),
            ]);

            $stage->update(['badge_id' => $badge->id]);
            $this->createDemoAttendance($stage, $user);
        }

        $this->createPersonnelUsers();
    }

    protected function ensureServices(): void
    {
        if (Service::count() === 0) {
            Service::create(['nom' => 'Service Technique']);
            Service::create(['nom' => 'Service Operationnel']);
            Service::create(['nom' => 'Service Commercial']);
            Service::create(['nom' => 'Service RH']);
            Service::create(['nom' => 'Service Finance']);
        }
    }

    protected function ensureSites(): void
    {
        Site::updateOrCreate(
            ['code' => 'TFG-HQ'],
            [
                'name' => 'TFG SARL',
                'address' => 'Siege TFG SARL',
                'city' => 'Cotonou',
                'country' => 'Benin',
                'latitude' => '6.4086988853745686',
                'longitude' => '2.3304884846605294',
                'is_active' => true,
            ]
        );

        if (Site::count() === 1) {
            Site::create(['code' => 'TFG02', 'name' => 'Site Technique', 'address' => '456 Avenue Maintenance', 'city' => 'Lyon', 'latitude' => '45.7640', 'longitude' => '4.8357']);
            Site::create(['code' => 'TFG03', 'name' => 'Site Operationnel', 'address' => '789 Boulevard Production', 'city' => 'Marseille', 'latitude' => '43.2965', 'longitude' => '5.3698']);
        }
    }

    protected function getTfgSite(): Site
    {
        return Site::where('code', 'TFG-HQ')
            ->orWhere(function ($query) {
                $query->where('code', 'like', 'TFG%')
                    ->orWhere('name', 'like', '%TFG%');
            })
            ->orderBy('code')
            ->first();
    }

    protected function ensureTypeStages(): void
    {
        if (TypeStage::count() === 0) {
            TypeStage::create(['code' => 'TEC', 'libelle' => 'Stage Technique']);
            TypeStage::create(['code' => 'COM', 'libelle' => 'Stage Commercial']);
            TypeStage::create(['code' => 'OPE', 'libelle' => 'Stage Operationnel']);
        }
    }

    protected function ensureJours(): void
    {
        if (Jour::count() === 0) {
            Jour::create(['jour' => 'Lundi']);
            Jour::create(['jour' => 'Mardi']);
            Jour::create(['jour' => 'Mercredi']);
            Jour::create(['jour' => 'Jeudi']);
            Jour::create(['jour' => 'Vendredi']);
        }
    }

    protected function createDemoAttendance(Stage $stage, User $user): void
    {
        AttendanceEvent::where('stage_id', $stage->id)->delete();

        foreach ([now()->subDay(), now()] as $date) {
            AttendanceEvent::create([
                'stage_id' => $stage->id,
                'etudiant_id' => $stage->etudiant_id,
                'site_id' => $stage->site_id,
                'user_id' => $user->id,
                'event_type' => 'check_in',
                'status' => 'approved',
                'occurred_at' => $date->copy()->setTime(8, rand(0, 30)),
                'latitude' => 48.8566 + (rand(-50, 50) / 10000),
                'longitude' => 2.3522 + (rand(-50, 50) / 10000),
            ]);

            AttendanceEvent::create([
                'stage_id' => $stage->id,
                'etudiant_id' => $stage->etudiant_id,
                'site_id' => $stage->site_id,
                'user_id' => $user->id,
                'event_type' => 'check_out',
                'status' => 'approved',
                'occurred_at' => $date->copy()->setTime(17, rand(0, 30)),
                'latitude' => 48.8566 + (rand(-50, 50) / 10000),
                'longitude' => 2.3522 + (rand(-50, 50) / 10000),
            ]);
        }
    }

    protected function createPersonnelUsers(): void
    {
        $presetService = app(RolePermissionPresetService::class);

        $personnelData = [
            ['name' => 'Dupuis Marc', 'email' => 'marc.dupuis@tfg.fr'],
            ['name' => 'Leroy Sophie', 'email' => 'sophie.leroy@tfg.fr'],
            ['name' => 'Gauthier Paul', 'email' => 'paul.gauthier@tfg.fr'],
        ];

        foreach ($personnelData as $data) {
            [$prenom, $nom] = $this->splitName($data['name']);

            $personnel = Personnel::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'telephone' => null,
                    'genre' => null,
                    'date_naissance' => null,
                    'adresse' => null,
                    'personnable_type' => null,
                    'personnable_id' => null,
                    'created_by' => null,
                ]
            );

            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                [
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'status' => 'actif',
                ]
            );

            $user->assignRole('superviseur');
            $presetService->ensureRoleDefaults($user, ['superviseur']);
        }
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? $parts[0] ?? '',
        ];
    }
}
