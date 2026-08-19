<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Stage;
use App\Models\Etudiant;
use App\Models\Badge;
use App\Models\AttendanceAnomaly;
use App\Models\Domaine;
use App\Models\Site;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class NavigationComposer
{
    public function compose(View $view)
    {
        $stagesCount = Stage::whereDate('date_debut', '<=', now())
            ->whereDate('date_fin', '>=', now())
            ->count();

        $etudiantsCount = Etudiant::count();
        $badgesCount = Badge::count();
        $usersCount = User::count();
        $rolesCount = Role::count();

        $domaines = collect();
        $sites = collect();

        if (Auth::check() && Auth::user()->hasAnyRole(['admin', 'superviseur'])) {
            $domaines = Domaine::with('sites')->get();
            // Charger uniquement les sites, sans la relation 'domaines.users' (obsolète)
            $sites = Site::orderBy('name')->get();
        }

        $anomaliesCount = AttendanceAnomaly::where('status', 'open')->count();

        $trashCount = Stage::onlyTrashed()->count()
            + Etudiant::onlyTrashed()->count()
            + Badge::onlyTrashed()->count()
            + User::onlyTrashed()->count();

        $activeStage = null;
        if (Auth::check() && Auth::user()->hasRole('etudiant') && Auth::user()->etudiant) {
            $activeStage = Stage::where('etudiant_id', Auth::user()->etudiant->id)
                ->whereDate('date_debut', '<=', now())
                ->whereDate('date_fin', '>=', now())
                ->with(['site', 'typestage'])
                ->orderByDesc('date_debut')
                ->first();

            if ($activeStage) {
                $todayAttendance = \App\Models\AttendanceDay::where('stage_id', $activeStage->id)
                    ->whereDate('attendance_date', today())
                    ->first();
                $activeStage->todayAttendance = $todayAttendance;
            }
        }

        $view->with([
            'stagesCount' => $stagesCount,
            'etudiantsCount' => $etudiantsCount,
            'badgesCount' => $badgesCount,
            'usersCount' => $usersCount,
            'rolesCount' => $rolesCount,
            'domaines' => $domaines,
            'sites' => $sites,
            'trashCount' => $trashCount,
            'anomaliesCount' => $anomaliesCount,
            'activeStage' => $activeStage,
        ]);
    }
}