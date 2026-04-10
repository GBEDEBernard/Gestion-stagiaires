<?php

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $typesToDelete = [
            'nouveau',
            'stage_fin_semaine',
            'stage_termine',
        ];

        User::with('roles')
            ->whereHas('roles', fn($query) => $query->where('name', 'etudiant'))
            ->get()
            ->each(function (User $user) use ($typesToDelete) {
                if ($user->hasAnyRole(['admin', 'superviseur'])) {
                    return;
                }

                // jb -> Ces notifications sont des alertes de pilotage
                // back-office. Si elles ont deja ete creees sur un compte
                // etudiant avant le correctif, on les purge ici.
                AppNotification::query()
                    ->where('user_id', $user->id)
                    ->whereIn('type', $typesToDelete)
                    ->delete();
            });
    }

    public function down(): void
    {
        // jb -> Purge irreversible: on ne recree pas artificiellement
        // d'anciennes notifications d'administration.
    }
};
