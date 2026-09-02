<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Si l'utilisateur est arrivé via un scan QR code
        // Arrivé par un scan de QR code : on l'emmène à sa page de pointage.
        // La proposition d'enregistrer le téléphone comme badge s'y affiche en
        // petit encart, plutôt que sur une page intermédiaire dédiée.
        if ($request->session()->has('pending_qr_site')) {
            $request->session()->pull('pending_qr_site');

            return redirect()->route('presence.pointage')->with('proposer_badge', true);
        }

        // Redirection directe vers le tableau de bord du rôle (admin,
        // superviseur, étudiant, employé) sans passer par une vérification d'email.

        return redirect()->route($user->homeRouteName());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
