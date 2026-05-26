<?php

use App\Models\Employe;
use App\Models\Etudiant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncDirectProfileKeys();
        $this->createMissingStudentProfiles();
    }

    public function down(): void
    {
        // Data repair only.
    }

    private function syncDirectProfileKeys(): void
    {
        if (Schema::hasColumn('etudiants', 'personnel_id')) {
            DB::table('personnels')
                ->where('personnable_type', Etudiant::class)
                ->whereNotNull('personnable_id')
                ->orderBy('id')
                ->get(['id', 'personnable_id'])
                ->each(function ($personnel) {
                    DB::table('etudiants')
                        ->where('id', $personnel->personnable_id)
                        ->where(function ($query) use ($personnel) {
                            $query->whereNull('personnel_id')
                                ->orWhere('personnel_id', '<>', $personnel->id);
                        })
                        ->update(['personnel_id' => $personnel->id]);
                });
        }

        if (Schema::hasColumn('employes', 'personnel_id')) {
            DB::table('personnels')
                ->where('personnable_type', Employe::class)
                ->whereNotNull('personnable_id')
                ->orderBy('id')
                ->get(['id', 'personnable_id'])
                ->each(function ($personnel) {
                    DB::table('employes')
                        ->where('id', $personnel->personnable_id)
                        ->where(function ($query) use ($personnel) {
                            $query->whereNull('personnel_id')
                                ->orWhere('personnel_id', '<>', $personnel->id);
                        })
                        ->update(['personnel_id' => $personnel->id]);
                });
        }
    }

    private function createMissingStudentProfiles(): void
    {
        $roleId = DB::table('roles')->where('name', 'etudiant')->value('id');

        if (!$roleId || !Schema::hasTable('model_has_roles')) {
            return;
        }

        DB::table('users')
            ->join('model_has_roles', function ($join) use ($roleId) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', App\Models\User::class)
                    ->where('model_has_roles.role_id', '=', $roleId);
            })
            ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->whereNotNull('users.personnel_id')
            ->whereNull('personnels.personnable_type')
            ->orderBy('users.id')
            ->select('users.id', 'users.personnel_id')
            ->get()
            ->each(function ($user) {
                $etudiantId = DB::table('etudiants')->insertGetId([
                    'personnel_id' => $user->personnel_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('personnels')
                    ->where('id', $user->personnel_id)
                    ->update([
                        'personnable_type' => Etudiant::class,
                        'personnable_id' => $etudiantId,
                        'updated_at' => now(),
                    ]);
            });
    }
};
