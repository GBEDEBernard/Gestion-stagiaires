<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Stage;
use App\Models\Etudiant;
use App\Models\Badge;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class NavigationComposer
{
    public function compose(View $view)
    {
        // Compter les stages actifs (en cours)
        $stagesCount = Stage::whereDate('date_debut', '<=', now())
            ->whereDate('date_fin', '>=', now())
            ->count();

        // Compter les étudiants
        $etudiantsCount = Etudiant::count();

        // Compter les badges disponibles
        $badgesCount = Badge::count();

        // Compter les utilisateurs
        $usersCount = User::count();

        // Compter les rôles
        $rolesCount = Role::count();

        // Compter les anomalies ouvertes
        $anomaliesCount = \App\Models\AttendanceAnomaly::where('status', 'open')->count();

        // Compter les éléments dans la corbeille (toutes tables confondues)
        $trashCount = Stage::onlyTrashed()->count()
            + Etudiant::onlyTrashed()->count()
            + Badge::onlyTrashed()->count()
            + User::onlyTrashed()->count();

        $view->with([
            'stagesCount' => $stagesCount,
            'etudiantsCount' => $etudiantsCount,
            'badgesCount' => $badgesCount,
            'usersCount' => $usersCount,
            'rolesCount' => $rolesCount,
            'trashCount' => $trashCount,
            'anomaliesCount' => $anomaliesCount,
        ]);
    }
}
