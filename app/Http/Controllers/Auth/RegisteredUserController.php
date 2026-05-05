<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\SendEmailVerificationPin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }


    public function store(RegisterRequest $request): RedirectResponse
    {
        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Générer un code PIN à 4 chiffres
        $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Supprimer les anciens PINs pour cet email
        DB::table('email_verification_pins')
            ->where('email', $user->email)
            ->delete();

        // Créer un nouveau PIN
        DB::table('email_verification_pins')->insert([
            'email' => $user->email,
            'pin' => $pin,
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Envoyer l'email avec le PIN
        $user->notify(new SendEmailVerificationPin($pin));

        // Dispatcher l'événement Registered pour déclencher la redirection
        event(new Registered($user));

        // Rediriger vers la page de vérification du PIN
        return redirect()->route('verification.pin.show', ['email' => $user->email])
            ->with('status', 'Un code de vérification a été envoyé à votre adresse email.');
    }
}
