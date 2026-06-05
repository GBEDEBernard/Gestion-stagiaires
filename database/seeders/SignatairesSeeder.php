<?php

namespace Database\Seeders;

use App\Models\Signataire;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class SignatairesSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate([
            'name'       => 'signer_attestation',
            'guard_name' => 'web',
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Signataire::truncate();
        Schema::enableForeignKeyConstraints();

        $definitions = [
            'Directeur Général'           => ['sigle' => 'DG',  'ordre' => 1, 'peut_par_ordre' => false],
            'Directeur Technique'         => ['sigle' => 'DT',  'ordre' => 2, 'peut_par_ordre' => true],
            'Directeur Technique Adjoint' => ['sigle' => 'DTA', 'ordre' => 3, 'peut_par_ordre' => true],
        ];

        foreach ($definitions as $poste => $config) {
            // Chercher l'utilisateur via signataire_poste
            $user = User::where('signataire_poste', $poste)
                ->where('is_signer', true)
                ->first();

            if (!$user) {
                // Chercher via la relation employe
                $user = User::whereHas('personnel.personnable', function ($q) use ($poste) {
                    $q->where('poste', $poste);
                })->first();
            }

            if (!$user) {
                $this->command->warn("⚠ Aucun utilisateur trouvé pour le poste : {$poste}");
                continue;
            }

            $fullName = $user->personnel ? ($user->personnel->full_name ?? $user->email) : $user->email;
            $signerEmail = $user->getEmailForVerification();

            $signataire = Signataire::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nom'            => $fullName,
                    'email'          => $signerEmail,
                    'poste'          => $poste,
                    'sigle'          => $config['sigle'],
                    'ordre'          => $config['ordre'],
                    'peut_par_ordre' => $config['peut_par_ordre'],
                ]
            );

            $this->command->info("✓ Signataire créé : {$fullName} ({$poste})");
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('SignatairesSeeder terminé.');
    }
}