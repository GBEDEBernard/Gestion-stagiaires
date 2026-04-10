<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class FirstPasswordController extends Controller
{
    public function edit(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        // jb -> L'utilisateur ne peut pas personnaliser son mot de passe
        // tant que l'adresse email n'est pas d'abord confirmee.
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (!$user->requiresPasswordChange()) {
            return redirect()->route($user->homeRouteName());
        }

        return view('auth.first-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // jb -> Meme garde-fou ici pour eviter qu'un compte non verifie
        // contourne le parcours de validation de l'adresse email.
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $validated = $request->validateWithBag('firstPassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // jb -> Le premier mot de passe doit sortir le compte de son
        // etat temporaire avant l'acces normal a l'application.
        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
            'temporary_password_created_at' => null,
            'password_changed_at' => now(),
        ]);

        return redirect()
            ->route($user->homeRouteName())
            ->with('success', 'Mot de passe personnel enregistre avec succes.');
    }
}
