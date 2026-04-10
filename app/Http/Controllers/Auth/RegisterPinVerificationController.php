<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisterPinVerificationController extends Controller
{
    public function __construct(
        protected RolePermissionPresetService $rolePermissionPresetService
    ) {
    }

    /**
     * Display the verify PIN view after registration.
     */
    public function show(Request $request): View
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('register');
        }

        return view('auth.verify-register-pin', compact('email'));
    }

    /**
     * Verify the PIN and mark email as verified.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'pin' => ['required', 'numeric', 'digits:4'],
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['pin' => 'Utilisateur non trouvé.']);
        }

        // Check if email is already verified
        if ($user->email_verified_at !== null) {
            return redirect()->route('login')
                ->with('status', 'Votre email est déjà vérifié. Vous pouvez vous connecter.');
        }

        // Find the PIN record
        $pinRecord = DB::table('email_verification_pins')
            ->where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->first();

        if (!$pinRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['pin' => 'Le code PIN est invalide ou expiré.']);
        }

        // Mark the PIN as used
        DB::table('email_verification_pins')
            ->where('id', $pinRecord->id)
            ->update(['used' => true]);

        // Mark the user's email as verified
        $user->markEmailAsVerified();

        // jb -> L'inscription publique rattache par defaut le nouveau compte
        // au role metier etudiant si aucun role n'existe encore.
        if (!$user->hasAnyRole()) {
            $this->rolePermissionPresetService->ensureRoleDefaults($user, ['etudiant']);
        }

        return redirect()->route('login')
            ->with('status', 'Votre email a été vérifié avec succès ! Vous pouvez maintenant vous connecter.');
    }

    /**
     * Resend the PIN.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur non trouvé.']);
        }

        if ($user->email_verified_at !== null) {
            return redirect()->route('login')
                ->with('status', 'Votre email est déjà vérifié.');
        }

        // Generate a new 4-digit PIN
        $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Delete old PINs for this email
        DB::table('email_verification_pins')
            ->where('email', $request->email)
            ->delete();

        // Create new PIN
        DB::table('email_verification_pins')->insert([
            'email' => $request->email,
            'pin' => $pin,
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send the email
        $user->notify(new \App\Notifications\SendEmailVerificationPin($pin));

        return back()->with('status', 'Un nouveau code PIN a été envoyé à votre adresse email.');
    }
}
