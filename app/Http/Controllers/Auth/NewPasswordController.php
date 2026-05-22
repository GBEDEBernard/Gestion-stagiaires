<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Personnel;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NewPasswordController extends Controller
{
    /**
     * Affiche le formulaire de réinitialisation du mot de passe.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Gère la réinitialisation du mot de passe.
     * Cherche l'utilisateur via la table personnels (l'email "canonique" est là).
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            // 1. Trouver le personnel par email
            $personnel = Personnel::where('email', $request->email)->first();

            if (! $personnel) {
                Log::error('Personnel non trouvé lors du reset', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Aucun utilisateur trouvé avec cet email.',
                ]);
            }

            // 2. Trouver l'utilisateur lié au personnel
            $user = User::where('personnel_id', $personnel->id)->first();

            if (! $user) {
                Log::error('Utilisateur non trouvé lors du reset', ['personnel_id' => $personnel->id]);
                return back()->withErrors([
                    'email' => 'Aucun compte utilisateur associé à cet email.',
                ]);
            }

            Log::info('Reset password — utilisateur trouvé via personnel', [
                'user_id'      => $user->id,
                'personnel_id' => $personnel->id,
                'email'        => $request->email,
            ]);

            // 3. Vérifier que le token existe bien en base
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (! $tokenRecord) {
                Log::error('Token de reset introuvable', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Le lien de réinitialisation a expiré ou est invalide.',
                ]);
            }

            // 4. Vérifier que le token correspond bien à cet utilisateur
            if (! Password::tokenExists($user, $request->token)) {
                Log::error('Token de reset invalide', [
                    'email'   => $request->email,
                    'user_id' => $user->id,
                ]);
                return back()->withErrors([
                    'email' => 'Le token de réinitialisation est invalide.',
                ]);
            }

            // 5. Réinitialiser le mot de passe et marquer l'email comme vérifié
            DB::transaction(function () use ($user, $request) {
                $user->forceFill([
                    'password'                      => Hash::make($request->password),
                    'remember_token'                => Str::random(60),
                    'must_change_password'          => false,
                    'temporary_password_created_at' => null,
                    'password_changed_at'           => now(),
                    // ← CORRECTION PRINCIPALE : on marque l'email comme vérifié
                    // pour ne pas bloquer l'utilisateur sur la page verify-email
                    // après sa première connexion.
                    'email_verified_at'             => now(),
                ])->save();

                // Supprimer le token utilisé
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                event(new PasswordReset($user));

                Log::info('Mot de passe réinitialisé avec succès', [
                    'user_id' => $user->id,
                    'email'   => $request->email,
                ]);
            });

            return redirect()->route('login')->with(
                'success',
                'Votre mot de passe a été défini avec succès. Vous pouvez maintenant vous connecter.'
            );

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe', [
                'email' => $request->email ?? 'non fourni',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'email' => 'Une erreur est survenue : ' . $e->getMessage(),
            ]);
        }
    }
}