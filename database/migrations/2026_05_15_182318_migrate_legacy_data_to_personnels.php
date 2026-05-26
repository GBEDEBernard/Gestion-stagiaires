<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Traiter les étudiants existants
        $etudiants = DB::table('etudiants')->get();
        foreach ($etudiants as $oldEtudiant) {
            // Créer le Personnel
            $personnelId = DB::table('personnels')->insertGetId([
                'nom' => $oldEtudiant->nom,
                'prenom' => $oldEtudiant->prenom,
                'email' => $oldEtudiant->email,
                'telephone' => $oldEtudiant->telephone,
                'genre' => $oldEtudiant->genre,
                'date_naissance' => null,
                'adresse' => null,
                'personnable_type' => 'App\\Models\\Etudiant',
                'personnable_id' => $oldEtudiant->id,
                'created_by' => null,
                'created_at' => $oldEtudiant->created_at,
                'updated_at' => $oldEtudiant->updated_at,
                'deleted_at' => $oldEtudiant->deleted_at,
            ]);

            // Mettre à jour l'étudiant avec personnel_id
            DB::table('etudiants')->where('id', $oldEtudiant->id)->update([
                'personnel_id' => $personnelId,
            ]);

            // Si l'étudiant avait un user_id, lier ce user au personnel
            if ($oldEtudiant->user_id) {
                DB::table('users')->where('id', $oldEtudiant->user_id)->update([
                    'personnel_id' => $personnelId,
                ]);
            }
        }

        // 2. Traiter les utilisateurs qui ne sont pas étudiants (admins, superviseurs, employés)
        $usersWithoutEtudiant = DB::table('users')
            ->leftJoin('etudiants', 'users.id', '=', 'etudiants.user_id')
            ->whereNull('etudiants.id')
            ->select('users.*')
            ->get();

        foreach ($usersWithoutEtudiant as $user) {
            // Vérifier si c'est un employé (rôle 'employe' ou domaine_id non null)
            $isEmploye = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->where('role_id', DB::table('roles')->where('name', 'employe')->value('id'))
                ->exists()
                || !is_null($user->domaine_id);

            // Créer le Personnel à partir des infos User
            $personnelId = DB::table('personnels')->insertGetId([
                'nom' => explode(' ', trim($user->name))[1] ?? $user->name,
                'prenom' => explode(' ', trim($user->name))[0] ?? '',
                'email' => $user->email,
                'telephone' => $user->phone,
                'genre' => null,
                'date_naissance' => null,
                'adresse' => null,
                'personnable_type' => $isEmploye ? 'App\\Models\\Employe' : null,
                'personnable_id' => null,
                'created_by' => null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at,
            ]);

            // Lier le user à ce personnel
            DB::table('users')->where('id', $user->id)->update([
                'personnel_id' => $personnelId,
            ]);

            // Si c'est un employé, créer une entrée dans employes
            if ($isEmploye) {
                DB::table('employes')->insert([
                    'domaine_id' => $user->domaine_id ?? 1,
                    'site_id' => 1,
                    'poste' => null,
                    'matricule' => 'EMP-' . $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $employeId = DB::getPdo()->lastInsertId();
                DB::table('personnels')->where('id', $personnelId)->update([
                    'personnable_type' => 'App\\Models\\Employe',
                    'personnable_id' => $employeId,
                ]);
            }
        }

        // 3. Supprimer les contraintes de clé étrangère avant de supprimer les colonnes
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            // Supprimer la contrainte sur domaine_id
            $table->dropForeign(['domaine_id']);
        });

        Schema::table('etudiants', function (Blueprint $table) {
            // Supprimer la contrainte sur user_id si elle existe
            $table->dropForeign(['user_id']);
        });

        // 4. Supprimer les colonnes obsolètes
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone', 'domaine_id', 'bio', 'avatar']);
        });

        Schema::table('etudiants', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'nom', 'prenom', 'email', 'telephone', 'genre']);
        });
    }

    public function down()
    {
        // Optionnel : rollback complexe
    }
};
