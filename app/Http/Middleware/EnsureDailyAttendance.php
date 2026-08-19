<?php

namespace App\Http\Middleware;

use App\Models\AttendanceDay;
use App\Models\Holiday;
use App\Models\HolidayEmergencyExemption;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Oblige l'utilisateur (étudiant ou employé) à faire son pointage du jour
 * avant toute autre action sur le site. Les admins et superviseurs sont libres.
 */
class EnsureDailyAttendance
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';

        // Les routes du flux de pointage et la déconnexion restent accessibles.
        if (Str::startsWith($routeName, 'presence.') || $routeName === 'logout') {
            return $next($request);
        }

        // Admin et superviseur : libres (équipe de gestion).
        if ($user->hasAnyRole(['admin', 'superviseur'])) {
            return $next($request);
        }

        // Jour férié actif : seuls les appelés en urgence (et les bypass) ont accès
        // au site. Les autres sont redirigés vers la page de pointage.
        if ($this->isActiveHolidayToday()) {
            $isExempted = HolidayEmergencyExemption::isExempted($user);
            if (!$user->can('holidays.bypass') && !$isExempted) {
                return redirect()->route('presence.pointage')
                    ->with('error', 'Jour férié : l\'accès au site est désactivé aujourd\'hui. Seules les personnes appelées en urgence peuvent travailler.');
            }
        }

        // Si le pointage est impossible aujourd'hui (jour férié, pas de stage
        // actif, pas de domaine, jour de repos), aucun blocage n'est appliqué
        // pour éviter une impossibilité permanente.
        if (!$this->canCheckInToday($user)) {
            return $next($request);
        }

        if ($this->hasCheckedInToday($user)) {
            return $next($request);
        }

        return redirect()->route('presence.pointage')
            ->with('error', 'Votre pointage du jour est requis avant de continuer.');
    }

    /**
     * Un jour férié est-il déclaré actif aujourd'hui ?
     */
    protected function isActiveHolidayToday(): bool
    {
        return Holiday::todayIsHoliday();
    }

    /**
     * L'utilisateur a déjà pointé (arrivée enregistrée) aujourd'hui.
     */
    protected function hasCheckedInToday(User $user): bool
    {
        return AttendanceDay::query()
            ->whereDate('attendance_date', today())
            ->whereNotNull('first_check_in_at')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);

                $etudiant = $user->etudiant;
                if ($etudiant) {
                    $query->orWhere('etudiant_id', $etudiant->id);
                }
            })
            ->exists();
    }

    /**
     * Le pointage est-il techniquement possible aujourd'hui pour cet utilisateur ?
     */
    protected function canCheckInToday(User $user): bool
    {
        $isExempted = HolidayEmergencyExemption::isExempted($user);
        if (Holiday::todayIsHoliday() && !$user->can('holidays.bypass') && !$isExempted) {
            return false;
        }

        if ($user->hasRole('etudiant')) {
            $etudiant = $user->etudiant;

            if (!$etudiant) {
                return false;
            }

            $activeStage = $etudiant->stages()
                ->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->orderByDesc('date_debut')
                ->first();

            return $activeStage && $activeStage->isWorkDay();
        }

        // Employé / fonctionnaire
        return (bool) $user->domaine;
    }
}