<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest; // ton FormRequest personnalisé
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        // Pas besoin de $request->validate(), RegisterRequest s'en occupe déjà

        $user = User::create([
            'name' => $request->input('name'),   // <-- propre
            'email' => $request->input('email'), // <-- propre
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Compte créé avec succès. Vérifiez votre email pour activer le compte.');
    }
}
