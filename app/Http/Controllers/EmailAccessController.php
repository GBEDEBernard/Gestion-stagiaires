<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailAccessController extends Controller
{
    public function handle(Request $request)
    {
        // Vérification de la signature (middleware 'signed' le fait déjà,
        // mais on redemande explicitement pour sécurité)
        if (!$request->hasValidSignature()) {
            abort(403, 'Lien invalide ou expiré.');
        }

        $userId   = $request->query('uid');
        $redirect = $request->query('redirect');

        $user = User::find($userId);

        if (!$user || $user->status !== 'actif') {
            return redirect()->route('login')
                ->with('error', 'Compte introuvable ou inactif.');
        }

        // Déconnecter la session courante si c'est un autre utilisateur
        if (Auth::check() && Auth::id() !== (int) $userId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Connecter le bon utilisateur
        if (!Auth::check()) {
            Auth::loginUsingId($userId);
            $request->session()->regenerate();
        }

        // Rediriger vers la page cible
        if ($redirect) {
            return redirect($redirect);
        }

        return redirect()->route('tasks.index');
    }
}