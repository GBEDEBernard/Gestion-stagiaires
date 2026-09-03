<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Personnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\Etudiant;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load([
            'personnel',
            'personnel.personnable',
            'personnel.personnable.supervisor',
            'personnel.personnable.supervisor.personnel',
            'personnel.personnable.site',
            'personnel.personnable.domaine',
        ]);

        $profil = $user->personnel?->personnable;
        if ($profil instanceof Etudiant) {
            $user->load([
                'personnel.personnable.stages.typestage',
                'personnel.personnable.stages.domaine',
                'personnel.personnable.stages.site',
                'personnel.personnable.stages.badge',
            ]);
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $personnel = $user->personnel;

        // Mise à jour des informations du personnel
        if (!$personnel) {
            $personnel = new Personnel();
        }

        $personnel->fill([
            'nom' => $request->input('nom'),
            'prenom' => $request->input('prenom'),
            'email' => $request->input('email'),
            'telephone' => $request->input('phone'),
        ]);
        $personnel->save();

        if (!$user->personnel_id) {
            $user->personnel_id = $personnel->id;
        }

        $emailChanged = $personnel->wasChanged('email');

        if ($user->getRawOriginal('email') !== $personnel->email) {
            $user->email = $personnel->email;
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        // Gestion de l'avatar (la colonne existe maintenant dans users)
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Mise à jour de la bio (colonne ajoutée)
        $user->bio = $request->input('bio');
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
