<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\StudentStageExpiryService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie à chaque requête que le compte authentifié est toujours valide :
 *  - un stagiaire dont tous les stages sont terminés est désactivé immédiatement ;
 *  - tout compte dont le statut a basculé en inactif est déconnecté sur le champ.
 * Couvre l'intervalle entre deux exécutions du cron de désactivation.
 */
class EnsureAccountActive
{
    public function __construct(private StudentStageExpiryService $expiryService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user instanceof User) {
            return $next($request);
        }

        if ($user->status === 'inactif') {
            return $this->revoke($request, 'Votre compte a été désactivé.');
        }

        if ($user->status === 'actif' && $user->hasRole('etudiant') && $this->expiryService->hasExpiredStage($user)) {
            $user->forceFill(['status' => 'inactif'])->save();

            return $this->revoke($request, 'Votre stage est terminé, votre compte a été désactivé.');
        }

        return $next($request);
    }

    protected function revoke(Request $request, string $message): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', $message);
    }
}