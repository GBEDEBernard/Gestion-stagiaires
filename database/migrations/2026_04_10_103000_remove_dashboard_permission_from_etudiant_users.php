<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        User::with('roles')
            ->whereHas('roles', fn($query) => $query->where('name', 'etudiant'))
            ->get()
            ->each(function (User $user) {
                if ($user->hasAnyRole(['admin', 'superviseur'])) {
                    return;
                }

                // jb -> On retire ici l'ancien reliquat de migration:
                // certains comptes etudiants avaient encore dashboard.view
                // en permission directe a cause de l'ancien preset.
                $user->revokePermissionTo('dashboard.view');
            });
    }

    public function down(): void
    {
        User::with('roles')
            ->whereHas('roles', fn($query) => $query->where('name', 'etudiant'))
            ->get()
            ->each(function (User $user) {
                if ($user->hasAnyRole(['admin', 'superviseur'])) {
                    return;
                }

                $user->givePermissionTo('dashboard.view');
            });
    }
};
