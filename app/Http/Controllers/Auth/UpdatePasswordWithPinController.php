<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UpdatePasswordWithPinController extends Controller
{
    /**
     * Display the password reset form with PIN.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $email = $request->query('email');
        $pin = $request->query('pin');

        if (!$email || !$pin) {
            return redirect()->route('password.request');
        }

        // Vérifier que le PIN existe et n'a pas expiré
        $pinRecord = DB::table('password_reset_pins')
            ->where('email', $email)
            ->where('pin', $pin)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pinRecord) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Accès invalide. Veuillez recommencer.']);
        }

        return view('auth.reset-password-with-pin', compact('email', 'pin'));
    }

    /**
     * Update the password.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'pin' => ['required', 'numeric', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Vérifier que le PIN existe et n'a pas expiré
        $pinRecord = DB::table('password_reset_pins')
            ->where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pinRecord) {
            return back()->withErrors(['email' => 'Accès invalide. Veuillez recommencer.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur non trouvé.']);
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Supprimer les PIN utilisés
        DB::table('password_reset_pins')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
    }
}
