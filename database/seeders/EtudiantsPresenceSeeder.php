<?php

namespace Database\Seeders;

use App\Models\AttendanceEvent;
use App\Models\Badge;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class EtudiantsPresenceSeeder extends Seeder
{
    public function run(): void
    {
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
                'ecole' => 'Université de Paris',
                'theme' => 'Administration système et réseaux sous Linux Debian',
                'date_debut' => now()->subMonth()->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Emma',
                'email' => 'emma.dupont@etudiant.tfg',
                'telephone' => '06 12 34 56 02',
                'genre' => 'Feminin',
                'ecole' => 'Epitech Paris',
                'theme' => 'Architecture micro-services & API REST sécurisée sous Laravel',
                'date_debut' => now()->subWeeks(3)->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'nom' => 'Lefevre',
                'prenom' => 'Noah',
                'email' => 'noah.lefevre@etudiant.tfg',
                'telephone' => '06 12 34 56 03',
                'genre' => 'Masculin',
                'ecole' => 'INSA Lyon',
                'theme' => 'Optimisation et monitoring des bases de données PostgreSQL',
                'date_debut' => now()->subWeeks(2)->format('Y-m-d'),
                'date_fin' => now()->addMonth()->format('Y-m-d'),
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Jade',
                'email' => 'jade.moreau@etudiant.tfg',
                'telephone' => '06 12 34 56 04',
                'genre' => 'Feminin',
                'ecole' => 'EPITA',
                'theme' => 'Sécurité informatique et audit des vulnérabilités applicatives',
                'date_debut' => now()->subDays(10)->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Roux',
                'prenom' => 'Leo',
                'email' => 'leo.roux@etudiant.tfg',
                'telephone' => '06 12 34 56 05',
                'genre' => 'Masculin',
                'ecole' => 'UTC Compiègne',
                'theme' => 'Développement d\'une application mobile Flutter avec synchronisation hors-ligne',
                'date_debut' => now()->subMonth()->format('Y-m-d'),
                'date_fin' => now()->addMonth()->format('Y-m-d'),
            ],
            [
                'nom' => 'Bernard',
                'prenom' => 'Gabriel',
                'email' => 'gabriel.bernard@etudiant.tfg',
                'telephone' => '06 12 34 56 06',
                'genre' => 'Masculin',
                'ecole' => 'Télécom Paris',
                'theme' => 'Mise en place de conteneurisation Docker & orchestration Kubernetes',
                'date_debut' => now()->subWeeks(2)->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'nom' => 'Petit',
                'prenom' => 'Sarah',
                'email' => 'sarah.petit@etudiant.tfg',
                'telephone' => '06 12 34 56 07',
                'genre' => 'Feminin',
                'ecole' => 'Polytech Nantes',
                'theme' => 'Conception d\'interfaces utilisateur réactives avec Tailwind CSS et Alpine.js',
                'date_debut' => now()->subWeeks(4)->format('Y-m-d'),
                'date_fin' => now()->addMonth()->format('Y-m-d'),
            ],
            [
                'nom' => 'Robert',
                'prenom' => 'Hugo',
                'email' => 'hugo.robert@etudiant.tfg',
                'telephone' => '06 12 34 56 08',
                'genre' => 'Masculin',
                'ecole' => 'CentraleSupélec',
                'theme' => 'Automatisation du pipeline CI/CD avec GitHub Actions et tests automatisés',
                'date_debut' => now()->subMonth()->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Richard',
                'prenom' => 'Manon',
                'email' => 'manon.richard@etudiant.tfg',
                'telephone' => '06 12 34 56 09',
                'genre' => 'Feminin',
                'ecole' => 'ESILV Paris',
                'theme' => 'Intégration d\'un moteur de recherche temps réel avec Meilisearch',
                'date_debut' => now()->subDays(5)->format('Y-m-d'),
                'date_fin' => now()->addMonths(4)->format('Y-m-d'),
            ],
            [
                'nom' => 'Durand',
                'prenom' => 'Arthur',
                'email' => 'arthur.durand@etudiant.tfg',
                'telephone' => '06 12 34 56 10',
                'genre' => 'Masculin',
                'ecole' => 'ENSIMAG Grenoble',
                'theme' => 'Traitement et analyse de données massives avec Python et Pandas',
                'date_debut' => now()->subMonths(2)->format('Y-m-d'),
                'date_fin' => now()->addWeeks(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Chloe',
                'email' => 'chloe.dubois@etudiant.tfg',
                'telephone' => '06 12 34 56 11',
                'genre' => 'Feminin',
                'ecole' => 'Université de Rennes',
                'theme' => 'Digitalisation des feuilles de présence et pointage par QR Code dynamique',
                'date_debut' => now()->subWeeks(3)->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Simon',
                'prenom' => 'Jules',
                'email' => 'jules.simon@etudiant.tfg',
                'telephone' => '06 12 34 56 12',
                'genre' => 'Masculin',
                'ecole' => 'Efrei Paris',
                'theme' => 'Conception d\'un portail intranet pour le suivi des stagiaires en entreprise',
                'date_debut' => now()->subMonth()->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'nom' => 'Laurent',
                'prenom' => 'Lea',
                'email' => 'lea.laurent@etudiant.tfg',
                'telephone' => '06 12 34 56 13',
                'genre' => 'Feminin',
                'ecole' => 'Université de Bordeaux',
                'theme' => 'Optimisation SEO et performance technique des applications web',
                'date_debut' => now()->subMonths(3)->format('Y-m-d'),
                'date_fin' => now()->subDays(2)->format('Y-m-d'), // Terminé
            ],
            [
                'nom' => 'Michel',
                'prenom' => 'Louis',
                'email' => 'louis.michel@etudiant.tfg',
                'telephone' => '06 12 34 56 14',
                'genre' => 'Masculin',
                'ecole' => 'ISEN Brest',
                'theme' => 'Supervision réseau et mise en place de serveurs DNS et VPN sécurisés',
                'date_debut' => now()->subMonths(4)->format('Y-m-d'),
                'date_fin' => now()->subWeeks(2)->format('Y-m-d'), // Terminé
            ],
            [
                'nom' => 'Garcia',
                'prenom' => 'Camille',
                'email' => 'camille.garcia@etudiant.tfg',
                'telephone' => '06 12 34 56 15',
                'genre' => 'Feminin',
                'ecole' => 'Sup de Vente',
                'theme' => 'Gestion de la relation client et déploiement de solutions CRM',
                'date_debut' => now()->subWeeks(2)->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Thomas',
                'prenom' => 'Paul',
                'email' => 'paul.thomas@etudiant.tfg',
                'telephone' => '06 12 34 56 16',
                'genre' => 'Masculin',
                'ecole' => 'Polytech Montpellier',
                'theme' => 'Développement d\'une API GraphQL avec gestion des droits d\'accès granulaires',
                'date_debut' => now()->addWeeks(1)->format('Y-m-d'), // À venir
                'date_fin' => now()->addMonths(4)->format('Y-m-d'),
            ],
            [
                'nom' => 'David',
                'prenom' => 'Ines',
                'email' => 'ines.david@etudiant.tfg',
                'telephone' => '06 12 34 56 17',
                'genre' => 'Feminin',
                'ecole' => 'Université Côte d\'Azur',
                'theme' => 'Audit ergonomique et amélioration de l\'expérience utilisateur (UX)',
                'date_debut' => now()->subMonth()->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Bertrand',
                'prenom' => 'Maxime',
                'email' => 'maxime.bertrand@etudiant.tfg',
                'telephone' => '06 12 34 56 18',
                'genre' => 'Masculin',
                'ecole' => 'Université de Strasbourg',
                'theme' => 'Sécurisation des échanges de données et chiffrement de bout en bout',
                'date_debut' => now()->subWeeks(3)->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'nom' => 'Rouxel',
                'prenom' => 'Clara',
                'email' => 'clara.rouxel@etudiant.tfg',
                'telephone' => '06 12 34 56 19',
                'genre' => 'Feminin',
                'ecole' => 'Université de Lille',
                'theme' => 'Développement de composants web réutilisables sous Vue.js et Tailwind',
                'date_debut' => now()->subMonths(2)->format('Y-m-d'),
                'date_fin' => now()->addMonth()->format('Y-m-d'),
            ],
            [
                'nom' => 'Vincent',
                'prenom' => 'Clement',
                'email' => 'clement.vincent@etudiant.tfg',
                'telephone' => '06 12 34 56 20',
                'genre' => 'Masculin',
                'ecole' => 'Epitech Lyon',
                'theme' => 'Intégration d\'un service de paiement en ligne et facturation récurrente',
                'date_debut' => now()->subDays(8)->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'nom' => 'Fournier',
                'prenom' => 'Juliette',
                'email' => 'juliette.fournier@etudiant.tfg',
                'telephone' => '06 12 34 56 21',
                'genre' => 'Feminin',
                'ecole' => 'Institut Mines-Télécom',
                'theme' => 'Conception d\'un système de notifications push temps réel avec WebSockets',
                'date_debut' => now()->subWeeks(2)->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Morel',
                'prenom' => 'Antoine',
                'email' => 'antoine.morel@etudiant.tfg',
                'telephone' => '06 12 34 56 22',
                'genre' => 'Masculin',
                'ecole' => 'Polytech Tours',
                'theme' => 'Administration de serveurs web Nginx et équilibrage de charge (Load Balancing)',
                'date_debut' => now()->subMonth()->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Girard',
                'prenom' => 'Oceane',
                'email' => 'oceane.girard@etudiant.tfg',
                'telephone' => '06 12 34 56 23',
                'genre' => 'Feminin',
                'ecole' => 'Université de Toulouse',
                'theme' => 'Modélisation relationnelle avancée et indexation performante sous MariaDB',
                'date_debut' => now()->subMonths(3)->format('Y-m-d'),
                'date_fin' => now()->subWeeks(1)->format('Y-m-d'), // Terminé
            ],
            [
                'nom' => 'Andre',
                'prenom' => 'Theo',
                'email' => 'theo.andre@etudiant.tfg',
                'telephone' => '06 12 34 56 24',
                'genre' => 'Masculin',
                'ecole' => 'UTBM Belfort',
                'theme' => 'Développement de tests unitaires et fonctionnels avec Pest et PHPUnit',
                'date_debut' => now()->subWeeks(1)->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'nom' => 'Bonnet',
                'prenom' => 'Zoe',
                'email' => 'zoe.bonnet@etudiant.tfg',
                'telephone' => '06 12 34 56 25',
                'genre' => 'Feminin',
                'ecole' => 'Université de Nancy',
                'theme' => 'Génération automatique de rapports et bilans PDF dynamiques avec DomPDF',
                'date_debut' => now()->subWeeks(3)->format('Y-m-d'),
                'date_fin' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'nom' => 'Mercier',
                'prenom' => 'Mathilde',
                'email' => 'mathilde.mercier@etudiant.tfg',
                'telephone' => '06 12 34 56 26',
                'genre' => 'Feminin',
                'ecole' => 'Université de Montpellier',
                'theme' => 'Mise en place d\'un tableau de bord décisionnel Power BI pour les RH',
                'date_debut' => now()->subWeeks(2)->format('Y-m-d'),
                'date_fin' => now()->addMonths(3)->format('Y-m-d'),
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
                ['ecole' => $data['ecole'] ?? 'TFG Demo']
            );

            $personnel->update([
                'personnable_type' => Etudiant::class,
                'personnable_id' => $etudiant->id,
            ]);

            $userAttributes = [
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'status' => 'actif',
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userAttributes['name'] = "{$data['prenom']} {$data['nom']}";
            }

            if (Schema::hasColumn('users', 'email')) {
                $userAttributes['email'] = $data['email'];
            }

            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                $userAttributes
            );

            $user->assignRole('etudiant');
            $presetService->ensureRoleDefaults($user, ['etudiant']);

            $stage = Stage::updateOrCreate(
                [
                    'etudiant_id' => $etudiant->id,
                    'theme' => $data['theme'] ?? 'Stage demo presence',
                ],
                [
                    'typestage_id' => TypeStage::inRandomOrder()->first()?->id ?? 1,
                    'site_id' => $tfgSite->id,
                    'supervisor_id' => User::role('superviseur')
                        ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
                        ->orderBy('personnels.nom')
                        ->orderBy('personnels.prenom')
                        ->select('users.*')
                        ->first()?->id ?? 1,
                    'date_debut' => $data['date_debut'] ?? now()->subWeek()->format('Y-m-d'),
                    'date_fin' => $data['date_fin'] ?? now()->addMonth()->format('Y-m-d'),
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

            $userAttributes = [
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'status' => 'actif',
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userAttributes['name'] = $data['name'];
            }

            if (Schema::hasColumn('users', 'email')) {
                $userAttributes['email'] = $data['email'];
            }

            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                $userAttributes
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
