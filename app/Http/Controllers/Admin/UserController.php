<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ------------------ INDEX ------------------
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // ------------------ CREATE ------------------
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    // ------------------ STORE ------------------
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        // 🔹 Vider le cache des permissions pour que tout prenne effet immédiatement
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->forgetCachedPermissions();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    // ------------------ EDIT ------------------
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    // ------------------ UPDATE ------------------
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'roles' => 'array',
            'permissions' => 'array',
        ]);

        // Mise à jour du nom et prénom
        $user->update([
            'name' => $request->name,
            'prenom' => $request->prenom,
        ]);

        // Seul l'admin peut modifier les rôles et permissions
        if (auth()->user()->hasRole('admin')) {
            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);
        }

        // Vider le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->forgetCachedPermissions();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // ------------------ DESTROY ------------------
    public function destroy(User $user)
    {
        $user->delete();

        // 🔹 Vider le cache après suppression
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé');
    }

    // ------------------ CREATE NEW PERMISSION ------------------
    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:100',
        ]);

        Permission::create(['name' => $request->name]);

        // 🔹 Vider le cache après création de permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', 'Permission créée avec succès.');
=======
use App\Models\Activity;
use App\Models\Etudiant;
use App\Models\User;
use App\Notifications\AccountProvisionedNotification;
use App\Services\RolePermissionPresetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct(
        protected RolePermissionPresetService $rolePermissionPresetService
    ) {
    }

    public function index()
    {
        $users = User::with(['roles', 'permissions', 'etudiant'])
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $selectedRoles = $this->rolePermissionPresetService->normalizeRoleNames([
            $request->query('role'),
        ]);

        return view('admin.users.create', $this->buildFormData(
            selectedRoles: $selectedRoles,
            selectedPermissions: $this->rolePermissionPresetService->permissionsForRoles($selectedRoles)
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUserPayload($request);
        $selectedRoles = $this->rolePermissionPresetService->normalizeRoleNames($validated['roles'] ?? []);
        $selectedPermissions = $this->resolveSubmittedPermissions($request, $selectedRoles);

        $accountEmailSent = false;

        $user = DB::transaction(function () use ($validated, $selectedRoles, $selectedPermissions, &$accountEmailSent) {
            $user = User::create([
                'name' => $this->resolveUserName($validated),
                'email' => $validated['email'],
                'password' => $validated['password'],
                'status' => 'actif',
                'email_verified_at' => null,
                'must_change_password' => true,
                'temporary_password_created_at' => now(),
                'password_changed_at' => null,
            ]);

            // jb -> Les roles servent ici de preset metier et de repere
            // d'interface; les permissions finales du compte sont ensuite
            // synchronisees exactement selon les cases gardees par l'admin.
            $this->rolePermissionPresetService->assignRolesAndPermissions($user, $selectedRoles, $selectedPermissions);
            $this->syncEtudiantProfile($user, $validated, $selectedRoles);

            try {
                $user->notify(new AccountProvisionedNotification($validated['password']));
                $accountEmailSent = true;
            } catch (\Throwable) {
                $accountEmailSent = false;
            }

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'Creation utilisateur',
                'description' => "Utilisateur {$user->email} cree avec les roles: " . implode(', ', $selectedRoles),
            ]);

            return $user;
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur cree avec succes.')
            ->with('generated_account', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $validated['password'],
                'account_email_sent' => $accountEmailSent,
            ]);
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'permissions', 'etudiant']);

        $selectedRoles = old('roles', $user->roles->pluck('name')->all());
        $defaultPermissions = $this->rolePermissionPresetService->permissionsForRoles($selectedRoles);
        $selectedPermissions = old(
            'permissions',
            collect($defaultPermissions)
                ->merge($user->permissions->pluck('name')->all())
                ->unique()
                ->values()
                ->all()
        );

        return view('admin.users.edit', $this->buildFormData(
            user: $user,
            selectedRoles: $selectedRoles,
            selectedPermissions: $selectedPermissions
        ));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUserPayload($request, $user);
        $selectedRoles = $this->rolePermissionPresetService->normalizeRoleNames($validated['roles'] ?? []);
        $selectedPermissions = $this->resolveSubmittedPermissions($request, $selectedRoles);
        $oldEmail = $user->email;

        $verificationEmailSent = false;
        $accountEmailSent = false;

        DB::transaction(function () use ($validated, $selectedRoles, $selectedPermissions, $user, $oldEmail, &$verificationEmailSent, &$accountEmailSent) {
            $payload = [
                'name' => $this->resolveUserName($validated, $user),
                'email' => $validated['email'],
                'status' => $validated['status'] ?? $user->status,
                'email_verified_at' => $validated['email'] !== $oldEmail ? null : $user->email_verified_at,
            ];

            if (!empty($validated['password'])) {
                // jb -> Si l'admin redefinit un mot de passe temporaire,
                // le compte repasse volontairement dans son cycle de prise
                // en main: nouveau secret temporaire puis mot de passe perso.
                $payload['password'] = $validated['password'];
                $payload['must_change_password'] = true;
                $payload['temporary_password_created_at'] = now();
                $payload['password_changed_at'] = null;
            }

            $user->update($payload);

            $this->rolePermissionPresetService->assignRolesAndPermissions($user, $selectedRoles, $selectedPermissions);
            $this->syncEtudiantProfile($user, $validated, $selectedRoles);

            if (!empty($validated['password'])) {
                try {
                    $user->notify(new AccountProvisionedNotification($validated['password']));
                    $accountEmailSent = true;
                } catch (\Throwable) {
                    $accountEmailSent = false;
                }
            } elseif ($validated['email'] !== $oldEmail) {
                try {
                    $user->sendEmailVerificationNotification();
                    $verificationEmailSent = true;
                } catch (\Throwable) {
                    $verificationEmailSent = false;
                }
            }

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'Mise a jour utilisateur',
                'description' => "Utilisateur {$user->email} mis a jour.",
            ]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur mis a jour avec succes.')
            ->with('updated_account', [
                'email' => $validated['email'],
                'temporary_password_reset' => !empty($validated['password']),
                'account_email_sent' => $accountEmailSent,
                'verification_email_sent' => $validated['email'] !== $oldEmail ? $verificationEmailSent : false,
            ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tu ne peux pas supprimer ton propre compte.');
        }

        $user->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression utilisateur',
            'description' => "Utilisateur {$user->email} envoye dans la corbeille.",
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprime avec succes.');
    }

    public function createPermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return back()->with('success', 'Permission creee avec succes.');
    }

    protected function validateUserPayload(Request $request, ?User $user = null): array
    {
        $isEtudiant = in_array('etudiant', $request->input('roles', []), true);

        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [
                $user ? 'nullable' : 'required',
                'confirmed',
                'min:8',
                'max:255',
            ],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in($this->rolePermissionPresetService->allowedRoleNames())],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'permissions_overridden' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['actif', 'inactif'])],
            'etudiant_nom' => [$isEtudiant ? 'required' : 'nullable', 'string', 'max:255'],
            'etudiant_prenom' => [$isEtudiant ? 'required' : 'nullable', 'string', 'max:255'],
            'etudiant_genre' => [$isEtudiant ? 'required' : 'nullable', 'string', 'max:50'],
            'etudiant_telephone' => ['nullable', 'string', 'max:20'],
            'etudiant_ecole' => ['nullable', 'string', 'max:255'],
        ]);
    }

    protected function resolveSubmittedPermissions(Request $request, array $selectedRoles): array
    {
        $submittedPermissions = $this->rolePermissionPresetService->sanitizePermissionNames(
            $request->input('permissions', [])
        );

        if ($request->boolean('permissions_overridden')) {
            return $submittedPermissions;
        }

        return $this->rolePermissionPresetService->permissionsForRoles($selectedRoles);
    }

    protected function resolveUserName(array $validated, ?User $user = null): string
    {
        $studentName = trim(($validated['etudiant_prenom'] ?? '') . ' ' . ($validated['etudiant_nom'] ?? ''));

        if ($studentName !== '') {
            return $studentName;
        }

        if (!empty($validated['name'])) {
            return $validated['name'];
        }

        return $user?->name ?? $validated['email'];
    }

    protected function syncEtudiantProfile(User $user, array $validated, array $selectedRoles): void
    {
        $mustManageEtudiant = in_array('etudiant', $selectedRoles, true) || $user->etudiant !== null;

        if (!$mustManageEtudiant) {
            return;
        }

        $existingEtudiant = $user->etudiant;

        if (!$existingEtudiant) {
            $existingEtudiant = Etudiant::where('email', $validated['email'])->first();
        }

        if ($existingEtudiant && $existingEtudiant->user_id && (int) $existingEtudiant->user_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'email' => "Cet email est deja rattache a une autre fiche etudiant.",
            ]);
        }

        $etudiant = $existingEtudiant ?? new Etudiant();

        $etudiant->fill([
            'nom' => $validated['etudiant_nom'] ?? $etudiant->nom,
            'prenom' => $validated['etudiant_prenom'] ?? $etudiant->prenom,
            'genre' => $validated['etudiant_genre'] ?? $etudiant->genre,
            'email' => $validated['email'],
            'telephone' => $validated['etudiant_telephone'] ?? $etudiant->telephone,
            'ecole' => $validated['etudiant_ecole'] ?? $etudiant->ecole,
        ]);

        $etudiant->user()->associate($user);
        $etudiant->save();
    }

    protected function buildFormData(?User $user = null, array $selectedRoles = [], array $selectedPermissions = []): array
    {
        $roles = $this->rolePermissionPresetService->orderedRoles();
        $permissions = Permission::query()->orderBy('name')->get();
        $permissionGroups = $permissions->groupBy(fn(Permission $permission) => explode('.', $permission->name)[0]);
        $selectedRoles = old('roles', $selectedRoles);
        $oldInput = session()->getOldInput();

        if (is_array($oldInput) && (array_key_exists('permissions', $oldInput) || array_key_exists('permissions_overridden', $oldInput))) {
            $selectedPermissions = old('permissions', []);
        }

        return [
            'user' => $user,
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'rolePermissionMap' => $this->rolePermissionPresetService->rolePermissionMap(),
            'selectedRoles' => $selectedRoles,
            'selectedPermissions' => $selectedPermissions,
        ];
>>>>>>> e9635ab
    }
}
