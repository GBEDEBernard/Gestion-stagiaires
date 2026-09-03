<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Permet à un admin de se connecter temporairement au compte d'un employé
 * ou d'un stagiaire (impersonation), via la session "impersonate_user_id".
 */
class ImpersonateController extends Controller
{
    /** Rôles ciblables par l'impersonation. */
    public const TARGET_ROLES = ['employe', 'fonctionnaire', 'etudiant'];

    /**
     * Liste des comptes ciblables (recherche pour la modale d'impersonation).
     */
    public function options(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = User::query()
            ->where('status', 'actif')
            ->whereDoesntHave('roles', function ($roles) {
                $roles->whereIn('name', ['admin', 'superviseur']);
            })
            ->whereHas('roles', function ($roles) {
                $roles->whereIn('name', self::TARGET_ROLES);
            })
            ->orderBy('name')
            ->limit(30);

        if ($q !== '') {
            $query->where(function ($search) use ($q) {
                $search->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        return response()->json(
            $query->get()->map(function (User $u) {
                $labels = $u->roles->pluck('name')
                    ->map(fn ($role) => match ($role) {
                        'etudiant' => 'Stagiaire',
                        'employe', 'fonctionnaire' => 'Employé',
                        default => ucfirst($role),
                    })
                    ->unique()
                    ->values();

                return [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'email' => $u->email,
                    'type'  => $u->hasRole('etudiant') ? 'stagiaire' : 'employe',
                    'roles' => $labels,
                ];
            })
        );
    }

    /**
     * Démarre l'impersonation : bascule vers le compte ciblé.
     */
    public function store(Request $request, User $user)
    {
        $admin = auth()->user();

        abort_unless($admin && $admin->hasRole('admin'), 403, 'Réservé aux administrateurs.');

        abort_if($user->id === $admin->id, 403, 'Vous ne pouvez pas vous connecter à votre propre compte.');
        abort_if($user->hasRole('admin') || $user->hasRole('superviseur'), 403, 'Impossible de se connecter à un compte de gestion.');
        abort_if(! $user->hasAnyRole(self::TARGET_ROLES), 403, 'Ce compte ne peut pas être ciblé.');
        abort_if($user->status !== 'actif', 403, 'Ce compte est inactif.');

        session()->put('impersonate_user_id', $user->id);

        return redirect()->route('dashboard')
            ->with('success', "Vous êtes maintenant connecté au compte de {$user->name}.");
    }

    /**
     * Fin de l'impersonation : retour à l'espace administrateur.
     */
    public function leave()
    {
        session()->forget('impersonate_user_id');

        return redirect()->route('dashboard')
            ->with('success', 'Vous êtes revenu à votre espace administrateur.');
    }
}