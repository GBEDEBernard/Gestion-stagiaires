<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bascule l'utilisateur authentifié vers le compte ciblé par un admin
 * (session "impersonate_user_id"). Tant que la clé est présente et que le
 * compte de base est bien un admin, chaque requête se déroule "en tant que"
 * l'utilisateur visé. La clé est purgée si le contexte n'est plus valide.
 */
class Impersonate
{
    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->session();

        if (!$session->has('impersonate_user_id')) {
            return $next($request);
        }

        $base = Auth::user();

        if (!$base || !$base->hasRole('admin')) {
            $session->forget('impersonate_user_id');

            return $next($request);
        }

        $target = User::find($session->get('impersonate_user_id'));

        if (!$target || $target->hasRole('admin') || $target->hasRole('superviseur')) {
            $session->forget('impersonate_user_id');

            return $next($request);
        }

        Auth::setUser($target);

        return $next($request);
    }
}