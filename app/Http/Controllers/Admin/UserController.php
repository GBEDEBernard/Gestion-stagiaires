<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\Employe;
use App\Models\Domaine;
use App\Services\RolePermissionPresetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Notifications\AccountProvisionedNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\Personnel;

class UserController extends Controller
{
    public function __construct(protected RolePermissionPresetService $roleService) {}

    /**
     * Liste des utilisateurs avec filtres.
     */
    public function index(Request $request)
    {
        $query = User::with('personnel');

        if ($search = $request->get('search')) {
            $query->whereHas('personnel', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($verified = $request->get('verified')) {
            $verified === 'verified' ? $query->whereNotNull('email_verified_at') : $query->whereNull('email_verified_at');
        }
        if ($passStatus = $request->get('password_status')) {
            $passStatus === 'temporary' ? $query->where('must_change_password', true) : $query->where('must_change_password', false);
        }
        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::pluck('name', 'name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Formulaire de création.
     */
    public function create(Request $request)
    {
        $selectedRoles = (array) $request->query('role');
        $roles = $this->roleService->orderedRoles();
        $rolePermissionMap = $this->roleService->rolePermissionMap();

        $selectedPermissions = [];
        foreach ($selectedRoles as $roleName) {
            if (isset($rolePermissionMap[$roleName])) {
                $selectedPermissions = array_merge($selectedPermissions, $rolePermissionMap[$roleName]);
            }
        }
        $selectedPermissions = array_unique($selectedPermissions);

        // Groupes de permissions pour l'affichage
        $allPermissions = Permission::orderBy('name')->get();
        $permissionGroups = $allPermissions->groupBy(fn($p) => explode('.', $p->name)[0]);

        $etudiants = Etudiant::with('personnel:id,nom,prenom')->get()->map(fn($e) => [
            'id' => $e->id,
            'nom' => $e->personnel?->nom,
            'prenom' => $e->personnel?->prenom,
        ]);

        $domaines = Domaine::select('id', 'nom')->get();
        $sites = \App\Models\Site::select('id', 'name')->get();

        $formData = [
            'user' => null,
            'selectedRoles' => $selectedRoles,
            'selectedPermissions' => $selectedPermissions,
            'rolePermissionMap' => $rolePermissionMap,
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'etudiants' => $etudiants,
            'domaines' => $domaines,
            'sites' => $sites,
        ];

        return view('admin.users.create', compact('formData'));
    }

      /**
     * Enregistrer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:personnels,email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_type' => 'required|string|exists:roles,name',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'etudiant_genre' => 'nullable|string|max:50',
            'etudiant_telephone' => 'nullable|string|max:20',
            'etudiant_ecole' => 'nullable|string|max:255',
            'domaine_id' => 'nullable|exists:domaines,id',
        ]);

        $user = DB::transaction(function () use ($validated) {
            // Créer le personnel
            $personnel = Personnel::create([
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['etudiant_telephone'] ?? null,
                'genre' => $validated['etudiant_genre'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Créer l'utilisateur lié au personnel
            $user = User::create([
                'name' => trim($validated['prenom'] . ' ' . $validated['nom']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'actif',
                'must_change_password' => true,
                'temporary_password_created_at' => now(),
                'personnel_id' => $personnel->id,
                'domaine_id' => $validated['domaine_id'] ?? null,
            ]);

            // Assigner les rôles (soit depuis les rôles sélectionnés, soit depuis le type d'utilisateur)
            $user->syncRoles($validated['roles'] ?? [$validated['user_type']]);

            // Si étudiant, créer la fiche étudiant et lier au personnel
            if (in_array('etudiant', $validated['roles'] ?? [$validated['user_type']])) {
                Etudiant::updateOrCreate(
                    ['personnel_id' => $personnel->id],
                    [
                        'ecole' => $validated['etudiant_ecole'] ?? null,
                        'email' => $validated['email'],
                    ]
                );
                // Mettre à jour le personnel pour lier l'étudiant (compatibilité avec code legacy)
                $personnel->update([
                    'personnable_type' => Etudiant::class,
                    'personnable_id' => $personnel->etudiant->id,
                ]);
            }

            // Générer le token de réinitialisation et envoyer la notification d'activation de compte
            $token = Password::broker()->createToken($user);
            $user->notify(new \App\Notifications\AccountProvisionedNotification($token));

            return $user;
        });
//       Générer un token de réinitialisation et construire l'URL de réinitialisation --- IGNORE ---
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.')
            ->with('generated_account', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $validated['password'],
                'type' => $validated['user_type'],
                'account_email_sent' => true,
            ]);
    }

    /**
     * Formulaire d'édition.
     */
     public function edit(User $user)
    {
        $user->load('personnel', 'roles', 'permissions');
        $roles = $this->roleService->orderedRoles();
        $rolePermissionMap = $this->roleService->rolePermissionMap();

        $selectedRoles = $user->roles->pluck('name')->toArray();
        $selectedPermissions = $user->permissions->pluck('name')->toArray();

        $allPermissions = Permission::orderBy('name')->get();
        $permissionGroups = $allPermissions->groupBy(fn($p) => explode('.', $p->name)[0]);

        $domaines = Domaine::select('id', 'nom')->get();
        $sites = \App\Models\Site::select('id', 'name')->get();

        $formData = [
            'user' => $user,
            'selectedRoles' => $selectedRoles,
            'selectedPermissions' => $selectedPermissions,
            'rolePermissionMap' => $rolePermissionMap,
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'domaines' => $domaines,
            'sites' => $sites,
            'nomValue' => $user->personnel?->nom ?? '',
            'prenomValue' => $user->personnel?->prenom ?? '',
            'etudiantEcole' => $user->personnel?->etudiant?->ecole ?? '',
            'etudiantGenre' => $user->personnel?->genre ?? '',
            'etudiantTelephone' => $user->personnel?->telephone ?? '',
        ];

        return view('admin.users.edit', compact('formData'));
    }
   /**
     * Mettre à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['actif', 'inactif'])],
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'etudiant_ecole' => 'nullable|string|max:255',
            'domaine_id' => 'nullable|exists:domaines,id',
        ]);

        $user = DB::transaction(function () use ($validated, $user) {
            // Mettre à jour le personnel si nécessaire
            if ($user->personnel && (isset($validated['nom']) || isset($validated['prenom']))) {
                $user->personnel->update([
                    'nom' => $validated['nom'] ?? $user->personnel->nom,
                    'prenom' => $validated['prenom'] ?? $user->personnel->prenom,
                ]);
                // Mettre à jour le nom affiché
                $user->name = trim(($validated['prenom'] ?? $user->personnel->prenom) . ' ' . ($validated['nom'] ?? $user->personnel->nom));
            }

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                    'must_change_password' => true,
                    'temporary_password_created_at' => now(),
                    'password_changed_at' => null,
                ]);
            }

            $user->update([
                'status' => $validated['status'],
                'domaine_id' => $validated['domaine_id'] ?? $user->domaine_id,
                'name' => $user->name,
            ]);

            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            // Mise à jour de la fiche étudiant si existante
            if ($user->personnel && $user->personnel->etudiant && isset($validated['etudiant_ecole'])) {
                $user->personnel->etudiant->update(['ecole' => $validated['etudiant_ecole']]);
            }

            return $user;
        });

        return redirect()->route('admin.users.index')->with('success', 'Compte utilisateur mis à jour.');
    }
    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    /**
     * Afficher le profil (redirection).
     */
    public function show(User $user)
    {
        $profil = $user->profil();
        if ($profil instanceof Etudiant) {
            return redirect()->route('etudiants.show', $profil);
        } elseif ($profil instanceof Employe) {
            return redirect()->route('employes.show', $profil);
        }
        return redirect()->route('admin.users.index');
    }

    /**
     * Liste des employés par domaine.
     */
    public function indexByDomaine(Domaine $domaine)
    {
        $employes = Employe::where('domaine_id', $domaine->id)
            ->with('personnel.user')
            ->paginate(10);
        return view('admin.employes.by_domaine', compact('domaine', 'employes'));
    }

    /**
     * Activer / désactiver un utilisateur.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }
        $newStatus = $user->status === 'actif' ? 'inactif' : 'actif';
        $user->update(['status' => $newStatus]);
        $message = $newStatus === 'actif'
            ? "Le compte de {$user->name} a été réactivé."
            : "Le compte de {$user->name} a été désactivé.";
        return back()->with('success', $message);
    }
}