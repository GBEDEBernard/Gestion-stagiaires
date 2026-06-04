<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\Employe;
use App\Models\Domaine;
use App\Models\Personnel;
use App\Models\Site;
use App\Services\RolePermissionPresetService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Notifications\AccountProvisionedNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(protected RolePermissionPresetService $roleService) {}

    public function index(Request $request)
    {
        $query = User::with('personnel', 'roles');

        if ($search = $request->get('search')) {
            $query->whereHas('personnel', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('email', 'like', "%{$search}%");
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

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::pluck('name', 'name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(Request $request)
    {
        $defaultRole = $request->query('default_role', 'admin');

        $selectedRoles = (array) $request->query('role', [$defaultRole]);
        $roles = $this->roleService->orderedRoles();
        $rolePermissionMap = $this->roleService->rolePermissionMap();

        $selectedPermissions = [];
        foreach ($selectedRoles as $roleName) {
            if (isset($rolePermissionMap[$roleName])) {
                $selectedPermissions = array_merge($selectedPermissions, $rolePermissionMap[$roleName]);
            }
        }
        $selectedPermissions = array_unique($selectedPermissions);

        $superviseurs = User::role('superviseur')->get(['id', 'name', 'email']);

        $allPermissions = Permission::orderBy('name')->get();
        $permissionGroups = $allPermissions->groupBy(fn($p) => explode('.', $p->name)[0]);

        $etudiants = Etudiant::with('personnel:id,nom,prenom')->get()->map(fn($e) => [
            'id' => $e->id,
            'nom' => $e->personnel?->nom,
            'prenom' => $e->personnel?->prenom,
        ]);

        $domaines = Domaine::select('id', 'nom')->get();
        $sites = Site::select('id', 'name')->get();

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

    public function store(Request $request)
    {
        $rules = [
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'email'     => 'required|email|unique:personnels,email|unique:users,email',
            'password'  => 'required|min:8|confirmed',
            'user_type' => 'required|string|exists:roles,name',
            'roles'     => 'array',
            'roles.*'   => 'exists:roles,name',
            'etudiant_genre'     => 'nullable|string|max:50',
            'etudiant_telephone' => 'nullable|string|max:20',
            'etudiant_ecole'     => 'nullable|string|max:255',
            'domaine_id' => 'nullable|exists:domaines,id',
        ];

        $selectedRoles = $request->input('roles', [$request->input('user_type')]);

        if (in_array('employe', $selectedRoles)) {
            $rules['site_id']   = 'required|exists:sites,id';
            $rules['domaine_id'] = 'required|exists:domaines,id';
            $rules['matricule'] = 'nullable|string|max:255|unique:employes,matricule';
            $rules['poste']     = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        $user = DB::transaction(function () use ($validated, $selectedRoles) {
            $personnel = Personnel::create([
                'nom'        => $validated['nom'],
                'prenom'     => $validated['prenom'],
                'email'      => $validated['email'],
                'telephone'  => $validated['etudiant_telephone'] ?? $validated['telephone'] ?? null,
                'genre'      => $validated['etudiant_genre'] ?? $validated['genre'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $userData = [
                'email'                         => $validated['email'],
                'password'                      => Hash::make($validated['password']),
                'status'                        => 'actif',
                'must_change_password'          => true,
                'temporary_password_created_at' => now(),
                'personnel_id'                  => $personnel->id,
                'domaine_id'                    => $validated['domaine_id'] ?? null,
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userData['name'] = trim($validated['prenom'] . ' ' . $validated['nom']);
            }

            $user = User::create($userData);
            $user->syncRoles($selectedRoles);

            if (in_array('etudiant', $selectedRoles)) {
                $etudiant = Etudiant::create([
                    'personnel_id' => $personnel->id,
                    'ecole'        => $validated['etudiant_ecole'] ?? null,
                ]);
                $personnel->update([
                    'personnable_type' => Etudiant::class,
                    'personnable_id'   => $etudiant->id,
                ]);
            }

            if (in_array('employe', $selectedRoles)) {
                $matricule = $validated['matricule'] ?? 'EMP-' . strtoupper(Str::random(8));
                $employe = Employe::create([
                    'personnel_id' => $personnel->id,
                    'domaine_id'   => $validated['domaine_id'],
                    'site_id'      => $validated['site_id'],
                    'matricule'    => $matricule,
                    'poste'        => $validated['poste'] ?? 'Employé',
                ]);
                $personnel->update([
                    'personnable_type' => Employe::class,
                    'personnable_id'   => $employe->id,
                ]);
            }

            $token = Password::broker()->createToken($user);
            $user->notify(new AccountProvisionedNotification($token));

            return $user;
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.')
            ->with('generated_account', [
                'name'               => $user->name,
                'email'              => $user->email,
                'password'           => $validated['password'],
                'type'               => $validated['user_type'],
                'account_email_sent' => true,
            ]);
    }

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
        $sites = Site::select('id', 'name')->get();

        $etudiantEcole = '';
        $employeSiteId = null;
        $employePoste = '';
        $employeMatricule = '';
        $etudiantSupervisorId = null;

        $profil = $user->profil();
        if ($profil instanceof Etudiant) {
            $etudiantEcole = $profil->ecole ?? '';
            $etudiantSupervisorId = $profil->supervisor_id;
        } elseif ($profil instanceof Employe) {
            $employeSiteId = $profil->site_id;
            $employePoste = $profil->poste;
            $employeMatricule = $profil->matricule;
        }

        // Superviseurs possibles : administrateurs et superviseurs
        $superviseurs = User::role(['admin', 'superviseur'])
            ->with('personnel')
            ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->select('users.*')
            ->orderBy('personnels.nom')
            ->orderBy('personnels.prenom')
            ->get();

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
            'emailValue' => $user->personnel?->email ?? $user->email,
            'telephoneValue' => $user->personnel?->telephone ?? '',
            'genreValue' => $user->personnel?->genre ?? '',
            'etudiantEcole' => $etudiantEcole,
            'employeSiteId' => $employeSiteId,
            'employePoste' => $employePoste,
            'employeMatricule' => $employeMatricule,
            'domaineIdValue' => $user->domaine_id ?? ($profil instanceof Employe ? $profil->domaine_id : null),
            'isSignerValue' => (bool)$user->is_signer,
            'superviseurs' => $superviseurs,
            'supervisorIdValue' => ($profil instanceof Employe) ? $profil->supervisor_id : null,
            'etudiantSupervisorId' => $etudiantSupervisorId,
        ];

        return view('admin.users.edit', compact('formData'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['actif', 'inactif'])],
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'user_type' => 'nullable|string|exists:roles,name',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('personnels', 'email')->ignore($user->personnel_id ?? 0),
            ],
            'telephone' => 'nullable|string|max:20',
            'genre' => 'nullable|string|max:50',
            'etudiant_ecole' => 'nullable|string|max:255',
            'etudiant_supervisor_id' => 'nullable|exists:users,id',
            'domaine_id' => 'nullable|exists:domaines,id',
            'employe_site_id' => 'nullable|exists:sites,id',
            'employe_poste' => 'nullable|string|max:255',
            'supervisor_id' => 'nullable|exists:users,id',
            'is_signer' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated, $user, $request) {
            // Mise à jour du personnel
            if ($user->personnel) {
                $emailChanged = $user->personnel->email !== $validated['email'];
                $user->personnel->update([
                    'nom' => $validated['nom'],
                    'prenom' => $validated['prenom'],
                    'email' => $validated['email'],
                    'telephone' => $validated['telephone'] ?? null,
                    'genre' => $validated['genre'] ?? null,
                ]);
                if ($emailChanged) {
                    $user->update([
                        'email' => $validated['email'],
                        'email_verified_at' => null,
                    ]);
                }
            }

            // Mise à jour de l'utilisateur
            $userData = [
                'email' => $validated['email'],
                'status' => $validated['status'],
                'domaine_id' => $validated['domaine_id'] ?? $user->domaine_id,
                'is_signer' => $validated['is_signer'] ?? false,
            ];
            if (Schema::hasColumn('users', 'name')) {
                $userData['name'] = trim($validated['prenom'] . ' ' . $validated['nom']);
            }
            $user->update($userData);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                    'must_change_password' => true,
                    'temporary_password_created_at' => now(),
                    'password_changed_at' => null,
                ]);
            }

            // Rôles
            $allRoles = [];
            if ($request->filled('user_type')) {
                $allRoles[] = $request->user_type;
            }
            if (isset($validated['roles']) && is_array($validated['roles'])) {
                $allRoles = array_merge($allRoles, $validated['roles']);
            }
            $allRoles = array_unique($allRoles);
            if (!empty($allRoles)) {
                $user->syncRoles($allRoles);
            }

            // Mise à jour de la fiche métier
            $profil = $user->profil();
            if ($profil instanceof Etudiant) {
                $profil->update([
                    'ecole' => $validated['etudiant_ecole'] ?? null,
                    'supervisor_id' => $validated['etudiant_supervisor_id'] ?? null,
                ]);

                // 🔥 Synchronisation avec le stage actif de l'étudiant
                $stageActif = $profil->stages()
                    ->where('date_fin', '>=', now())
                    ->orWhereNull('date_fin')
                    ->orderBy('date_debut', 'desc')
                    ->first();

                if ($stageActif && !empty($validated['etudiant_supervisor_id'])) {
                    $stageActif->update(['supervisor_id' => $validated['etudiant_supervisor_id']]);
                }
            } elseif ($profil instanceof Employe) {
                $profil->update([
                    'site_id' => $validated['employe_site_id'] ?? $profil->site_id,
                    'poste' => $validated['employe_poste'] ?? $profil->poste,
                    'domaine_id' => $validated['domaine_id'] ?? $profil->domaine_id,
                    'supervisor_id' => $validated['supervisor_id'] ?? null,
                ]);
            }
        });

        $message = 'Compte utilisateur mis à jour.';
        if ($request->filled('password')) {
            $message .= ' Un nouveau mot de passe temporaire a été défini.';
        }
        
        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
        }

        $newStatus = $user->status === 'actif' ? 'inactif' : 'actif';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'actif'
            ? "Le compte de {$user->name} a été réactivé."
            : "Le compte de {$user->name} a été désactivé.";

        return back()->with('success', $message);
    }

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
}