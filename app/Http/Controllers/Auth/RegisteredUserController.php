<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\User;
use App\Notifications\SendEmailVerificationPin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        [$user, $personnel, $pin] = DB::transaction(function () use ($request) {
            $fullName = trim($request->input('name'));
            $parts = explode(' ', $fullName, 2);
            $prenom = $parts[0] ?? '';
            $nom = $parts[1] ?? '';

            $personnel = Personnel::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $request->input('email'),
                'telephone' => null,
                'genre' => null,
                'date_naissance' => null,
                'adresse' => null,
                'created_by' => null,
            ]);

            $etudiant = Etudiant::create([
                'personnel_id' => $personnel->id,
            ]);

            $personnel->update([
                'personnable_type' => Etudiant::class,
                'personnable_id' => $etudiant->id,
            ]);

            $userData = [
                'personnel_id' => $personnel->id,
                'email' => $personnel->email,
                'password' => Hash::make($request->input('password')),
                'status' => 'actif',
                'must_change_password' => false,
                'temporary_password_created_at' => null,
                'password_changed_at' => null,
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userData['name'] = $personnel->full_name;
            }

            $user = User::create($userData);

            $pin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            DB::table('email_verification_pins')
                ->where('email', $personnel->email)
                ->delete();

            DB::table('email_verification_pins')->insert([
                'email' => $personnel->email,
                'pin' => $pin,
                'user_id' => $user->id,
                'expires_at' => now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [$user, $personnel, $pin];
        });

        $user->notify(new SendEmailVerificationPin($pin));

        event(new Registered($user));

        return redirect()->route('verification.pin.show', ['email' => $personnel->email])
            ->with('status', 'Un code de verification a ete envoye a votre adresse email.');
    }
}
