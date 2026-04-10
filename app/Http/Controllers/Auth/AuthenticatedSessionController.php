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

<<<<<<< HEAD
        // 👉 Redirige tout le monde vers le dashboard
        return redirect()->route('dashboard');
=======
        $user = $request->user();

        // jb -> Ordre volontaire du parcours apres login:
        // 1. verification email
        // 2. remplacement du mot de passe temporaire
        // 3. acces au vrai tableau de bord du role
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->requiresPasswordChange()) {
            return redirect()->route('password.first.edit');
        }

        return redirect()->route($user->homeRouteName());
>>>>>>> e9635ab
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
