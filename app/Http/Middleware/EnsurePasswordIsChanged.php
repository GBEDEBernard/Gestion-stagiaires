<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // jb -> Ce middleware protege toute l'application "normale".
        // Tant que le compte est encore sur son mot de passe temporaire,
        // l'utilisateur est force de terminer son appropriation du compte.
        if (!$user || !$user->requiresPasswordChange()) {
            return $next($request);
        }

        return redirect()->route('password.first.edit');
    }
}
