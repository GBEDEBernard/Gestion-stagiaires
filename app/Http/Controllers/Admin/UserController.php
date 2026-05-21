<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Services\RolePermissionPresetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Domaine;
use App\Models\Employe;

class UserController extends Controller
{
    public function __construct(protected RolePermissionPresetService $roleService) {}
// la methode pour la page des utilisateurs avec ces filter
public function index(Request $request)
{
    $query = User::with('personnel');

    // Recherche sur nom, prénom, email du personnel
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
        if ($verified === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($verified === 'not_verified') {
            $query->whereNull('email_verified_at');
        }
    }

    if ($passStatus = $request->get('password_status')) {
        if ($passStatus === 'temporary') {
            $query->where('must_change_password', true);
        } elseif ($passStatus === 'permanent') {
            $query->where('must_change_password', false);
        }
    }

    if ($role = $request->get('role')) {
        $query->whereHas('roles', fn($q) => $q->where('name', $role));
    }

    $users = $query->paginate(10)->withQueryString();
    $roles = \Spatie\Permission\Models\Role::pluck('name', 'name');

    return view('admin.users.index', compact('users', 'roles'));
}

    public function create(Request $request)
    {
        $selectedRoles = (array) $request->query('role');
        if (empty($selectedRoles)) {
            $selectedRoles = [];
        }

        $roles = $this->roleService->orderedRoles();
        $rolePermissionMap = $this->roleService->rolePermissionMap();
        $selectedPermissions = [];

        // Pré-charger les permissions en fonction des rôles sélectionnés
        foreach ($selectedRoles as $roleName) {
            if (isset($rolePermissionMap[$roleName])) {
                $selectedPermissions = array_merge($selectedPermissions, $rolePermissionMap[$roleName]);
            }
        }

        $etudiants = Etudiant::with(['personnel:id,nom,prenom'])
            ->select('id', 'personnel_id')
            ->get()
            ->map(function (Etudiant $etudiant) {
                return [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->personnel?->nom,
                    'prenom' => $etudiant->personnel?->prenom,
                ];
            });

        $domaines = \App\Models\Domaine::get(['id', 'nom']);
        $sites = \App\Models\Site::get(['id', 'name']);

        $formData = [
            'user' => null,
            'selectedRoles' => $selectedRoles,
            'selectedPermissions' => $selectedPermissions,
            'rolePermissionMap' => $rolePermissionMap,
            'roles' => $roles,
            'etudiants' => $etudiants,
            'domaines' => $domaines,
            'sites' => $sites,
        ];

        return view('admin.users.create', compact('formData', 'selectedRoles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_type' => 'required|string|exists:roles,name',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => trim($validated['prenom'] . ' ' . $validated['nom']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'actif',
            'must_change_password' => true,
            'temporary_password_created_at' => now(),
        ]);

        // Assigner les rôles
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        } else {
            $user->syncRoles([$validated['user_type']]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        $user->load('personnel');
        $roles = $this->roleService->orderedRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['actif', 'inactif'])],
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => true,
                'temporary_password_created_at' => now(),
                'password_changed_at' => null,
            ]);
            // Envoyer notification si souhaité
        }

        $user->update(['status' => $validated['status']]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Compte utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    public function show(User $user)
    {
        // Redirige vers le profil approprié (étudiant ou employé)
        $profil = $user->profil();
        if ($profil instanceof \App\Models\Etudiant) {
            return redirect()->route('etudiants.index', ['etudiant' => $profil->id]);
        } elseif ($profil instanceof \App\Models\Employe) {
            return redirect()->route('employes.index', ['employe' => $profil->id]);
        }
        return redirect()->route('admin.users.index');
    }
    // UserController.php
    public function indexByDomaine(Domaine $domaine)
    {
        $employes = Employe::where('domaine_id', $domaine->id)
            ->with('personnel.user')
            ->paginate(10);
        return view('admin.employes.by_domaine', compact('domaine', 'employes'));
    }

       /**
         * Activer / Désactiver un utilisateur.
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
                : "Le compte de {$user->name} a été désactivé. L'utilisateur ne pourra plus se connecter.";

            return back()->with('success', $message);
        }
}
