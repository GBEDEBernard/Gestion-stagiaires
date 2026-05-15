<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Personnel;
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
        // Extraire nom et prénom depuis le champ 'name' (format "Prénom Nom")
        $fullName = trim($request->input('name'));
        $parts = explode(' ', $fullName, 2);
        $prenom = $parts[0] ?? '';
        $nom = $parts[1] ?? '';

        // Créer le personnel (informations personnelles)
        $personnel = Personnel::create([
            'nom'       => $nom,
            'prenom'    => $prenom,
            'email'     => $request->input('email'),
            'telephone' => null,
            'genre'     => null,
            'date_naissance' => null,
            'adresse'   => null,
            'personnable_type' => null,  // pas de profil spécifique à l'inscription
            'personnable_id'   => null,
            'created_by' => null,
        ]);

        // Créer l'utilisateur (compte de connexion) lié au personnel
        $user = User::create([
            'personnel_id' => $personnel->id,
            'password'     => Hash::make($request->input('password')),
            'status'       => 'actif',
            'must_change_password' => false,
            'temporary_password_created_at' => null,
            'password_changed_at' => null,
        ]);

        // Générer un code PIN à 4 chiffres
        $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Supprimer les anciens PINs pour cet email
        DB::table('email_verification_pins')
            ->where('email', $personnel->email)
            ->delete();

        // Créer un nouveau PIN
        DB::table('email_verification_pins')->insert([
            'email'      => $personnel->email,
            'pin'        => $pin,
            'user_id'    => $user->id,
            'expires_at' => now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Envoyer l'email avec le PIN
        $user->notify(new SendEmailVerificationPin($pin));

        // Dispatcher l'événement Registered
        event(new Registered($user));

        // Rediriger vers la page de vérification du PIN
        return redirect()->route('verification.pin.show', ['email' => $personnel->email])
            ->with('status', 'Un code de vérification a été envoyé à votre adresse email.');
    }
}