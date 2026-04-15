<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Etudiant;
use App\Models\User;
use App\Models\Domaine;
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
    ) {}

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
        $selectedDomaineId = $request->query('domaine_id');

        $formData = $this->buildFormData(
            selectedRoles: $selectedRoles,
            selectedPermissions: $this->rolePermissionPresetService->permissionsForRoles($selectedRoles),
            selectedDomaineId: $selectedDomaineId,
        );

        return view('admin.users.create', compact('formData'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUserPayload($request);

        // Auto-assign role from user_type
        $userType = $validated['user_type'];
        $selectedRoles = [$userType];
        $selectedPermissions = $this->rolePermissionPresetService->permissionsForRoles($selectedRoles);

        $accountEmailSent = false;

        $user = DB::transaction(function () use ($validated, $selectedRoles, $selectedPermissions, &$accountEmailSent, $userType) {
            $user = User::create([
                'name' => $this->resolveUserName($validated),
                'email' => $validated['email'],
                'password' => $validated['password'],
                'status' => 'actif',
                'email_verified_at' => null,
                'must_change_password' => true,
                'temporary_password_created_at' => now(),
                'password_changed_at' => null,
                'domaine_id' => $validated['domaine_id'] ?? null,
            ]);

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
                'description' => "Utilisateur {$user->email} cree ({$userType}): " . implode(', ', $selectedRoles),
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
                'type' => $userType,
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

        // Auto-assign from user_type if present (create mode), else keep existing
        $selectedRoles = !empty($validated['user_type'])
            ? [$validated['user_type']]
            : $this->rolePermissionPresetService->normalizeRoleNames($validated['roles'] ?? []);
        $selectedPermissions = $this->rolePermissionPresetService->permissionsForRoles($selectedRoles);
        $oldEmail = $user->email;

        $verificationEmailSent = false;
        $accountEmailSent = false;

        DB::transaction(function () use ($validated, $selectedRoles, $selectedPermissions, $user, $oldEmail, &$verificationEmailSent, &$accountEmailSent) {
            $payload = [
                'name' => $this->resolveUserName($validated, $user),
                'email' => $validated['email'],
                'status' => $validated['status'] ?? $user->status,
                'email_verified_at' => $validated['email'] !== $oldEmail ? null : $user->email_verified_at,
                'domaine_id' => $validated['domaine_id'] ?? null,
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

    public function indexByDomaine(Domaine $domaine)
    {
        $users = User::where('domaine_id', $domaine->id)
            ->with(['roles', 'permissions'])
            ->latest()
            ->paginate(10);

        return view('admin.employes.by_domaine', compact('users', 'domaine'));
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
        $selectedType = $request->input('user_type');
        $isEtudiant = $selectedType === 'etudiant';
        $isEmploye = $selectedType === 'employe';

        $rules = [
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
            'user_type' => ['required', 'string', Rule::in($this->rolePermissionPresetService->allowedRoleNames())],
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
            'domaine_id' => ['nullable', 'exists:domaines,id', $isEmploye ? 'required' : 'nullable'],
        ];

        return $request->validate($rules);
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

    protected function buildFormData(?User $user = null, array $selectedRoles = [], array $selectedPermissions = [], ?int $selectedDomaineId = null): array
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
        'permissions' => $permissions,
        'permissionGroups' => $permissionGroups,
        'rolePermissionMap' => $this->rolePermissionPresetService->rolePermissionMap(),
        'selectedRoles' => $selectedRoles,
        'selectedPermissions' => $selectedPermissions,
        'domaines' => Domaine::orderBy('nom')->get(),
        'selectedDomaineId' => $selectedDomaineId,
    ];
}
}
