<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Notifications\SendPasswordResetPin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Chercher le personnel par son email
        $personnel = Personnel::where('email', $request->email)->first();

        if (!$personnel || !$personnel->user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Cet email n\'existe pas dans notre base de données.']);
        }

        $user = $personnel->user;

        // Générer un code PIN de 6 chiffres
        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Supprimer les anciens PIN pour cet email
        DB::table('password_reset_pins')
            ->where('email', $request->email)
            ->delete();

        // Créer un nouveau PIN
        DB::table('password_reset_pins')->insert([
            'email'      => $request->email,
            'pin'        => $pin,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Envoyer l'email avec le PIN
        $user->notify(new SendPasswordResetPin($pin));

        // Rediriger vers la page de saisie du PIN
        return redirect()->route('password.verify-pin-show', ['email' => $request->email])
            ->with('status', 'Un code PIN a été envoyé à votre adresse email.');
    }
}