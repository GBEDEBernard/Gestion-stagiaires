<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class VerifyPinController extends Controller
{
    /**
     * Display the verify PIN view.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-pin', compact('email'));
    }

    /**
     * Verify the PIN.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'pin' => ['required', 'numeric', 'digits:6'],
        ]);

        $pinRecord = DB::table('password_reset_pins')
            ->where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->first();

        if (!$pinRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['pin' => 'Le code PIN est invalide ou expiré.']);
        }

        // Marquer le PIN comme utilisé
        DB::table('password_reset_pins')
            ->where('id', $pinRecord->id)
            ->update(['used' => true]);

        // Rediriger vers la page de réinitialisation du mot de passe
        return redirect()->route('password.reset-form', [
            'email' => $request->email,
            'pin' => $request->pin,
        ]);
    }
}
