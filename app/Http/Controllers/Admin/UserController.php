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
// Cette méthode affiche la liste paginée des utilisateurs. Voici la logique détaillée de cette méthode :
// 1. Récupération des utilisateurs : La méthode commence par récupérer les utilisateurs de la base de données en utilisant le modèle User. Elle utilise la méthode with pour charger les relations 'roles', 'permissions' et 'etudiant' afin d'optimiser les requêtes et éviter le problème de N+1 lors de l'affichage des rôles, permissions et profils étudiants associés à chaque utilisateur.
// 2. Tri et pagination : Les utilisateurs récupérés sont triés par ordre décroissant de création (latest) pour afficher les utilisateurs les plus récents en premier. Ensuite, la méthode paginate est utilisée pour limiter le nombre d'utilisateurs affichés par page à 10, ce qui facilite la navigation dans la liste des utilisateurs.
// 3. Affichage de la vue : Enfin, la méthode retourne une vue appelée 'admin.users.index', en passant les utilisateurs récupérés à la vue via la fonction compact. Cette vue est responsable de l'affichage de la liste des utilisateurs, ainsi que des informations pertinentes sur leurs rôles, permissions et profils étudiants associés.   
    public function index()
    {
        $users = User::with(['roles', 'permissions', 'etudiant'])
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }
// Cette méthode affiche le formulaire de création d'un nouvel utilisateur. Voici la logique détaillée de cette méthode :
// 1. Récupération des rôles et domaines sélectionnés : La méthode commence par récupérer les rôles et le domaine sélectionnés à partir des paramètres de la requête. Cela permet de pré-sélectionner certains rôles ou domaines dans le formulaire de création en fonction des paramètres passés dans l'URL, ce qui peut faciliter la création d'utilisateurs avec des rôles ou domaines spécifiques.
// 2. Construction des données du formulaire : Ensuite, la méthode appelle la méthode buildFormData pour construire les données nécessaires à l'affichage du formulaire de création. Cette méthode prépare les listes de rôles, permissions, groupes de permissions, ainsi que les rôles et permissions sélectionnés, et les domaines disponibles. Cela permet d'avoir toutes les informations nécessaires pour afficher le formulaire de manière dynamique et adaptée aux rôles et domaines disponibles dans l'application.
// 3. Affichage de la vue : Enfin, la méthode retourne la vue 'admin.users.create', en passant les données du formulaire à la vue via la fonction compact. Cette vue est responsable de l'affichage du formulaire de création d'un nouvel utilisateur, en utilisant les données préparées pour afficher les options de rôles, permissions et domaines de manière appropriée.    
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
// Cette méthode gère la création d'un nouvel utilisateur à partir d'une requête HTTP. Voici la logique détaillée de cette méthode :
// 1. Validation du payload : La méthode commence par valider les données de la requête en utilisant la méthode validateUserPayload, qui applique des règles de validation spécifiques en fonction du type d'utilisateur (étudiant, employé, etc.). Cela garantit que les données soumises sont conformes aux exigences de l'application et évite les erreurs lors de la création de l'utilisateur.
// 2. Attribution automatique des rôles : En fonction du type d'utilisateur sélectionné, la méthode attribue automatiquement le rôle correspondant (par exemple, "etudiant" pour un utilisateur de type étudiant). Les permissions associées à ce rôle sont également déterminées en utilisant le service RolePermissionPresetService.
// 3. Création de l'utilisateur : La méthode utilise une transaction de base de données pour garantir que toutes les opérations de création sont atomiques. Elle crée l'utilisateur avec les données validées, attribue les rôles et permissions, et synchronise le profil étudiant si nécessaire.
// 4. Envoi de notifications : Après la création de l'utilisateur, la méthode tente d'envoyer une notification à l'utilisateur avec les informations de son compte. Si l'envoi de la notification échoue, cela est capturé pour informer l'administrateur du succès ou de l'échec de l'envoi de l'email de compte.
// 5. Enregistrement de l'activité : Une entrée d'activité est créée pour enregistrer l'action de création de l'utilisateur, ce qui permet de garder une trace des modifications apportées aux utilisateurs dans l'application.
// 6. Redirection : Enfin, la méthode redirige l'administrateur vers la liste des utilisateurs avec un message de succès indiquant que l'utilisateur a été créé avec succès, ainsi que des informations sur le compte généré, telles que le nom, l'email, le mot de passe temporaire, le type d'utilisateur, et si l'email de compte a été envoyé avec succès ou non.   
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
// Cette méthode affiche le formulaire d'édition pour un utilisateur spécifique. Voici la logique détaillée de cette méthode :
// 1. Chargement des relations : La méthode commence par charger les relations 'roles', 'permissions' et 'etudiant' de l'utilisateur passé en paramètre. Cela permet d'avoir toutes les informations nécessaires sur les rôles, permissions et le profil étudiant de l'utilisateur pour pré-remplir le formulaire d'édition.
// 2. Détermination des rôles et permissions sélectionnés : La méthode récupère les rôles sélectionnés à partir des données précédemment soumises (old input) ou, si aucune donnée n'est soumise, à partir des rôles actuels de l'utilisateur. Ensuite, elle calcule les permissions par défaut associées à ces rôles en utilisant le service RolePermissionPresetService. Les permissions sélectionnées sont ensuite déterminées en combinant les permissions par défaut des rôles avec les permissions spécifiques de l'utilisateur, en s'assurant d'é
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
// Cette méthode gère la mise à jour d'un utilisateur spécifique. Voici la logique détaillée de cette méthode :
// 1. Validation du payload : La méthode commence par valider les données de la requête en utilisant la méthode validateUserPayload, qui applique des règles de validation spécifiques en fonction du type d'utilisateur (étudiant, employé, etc.). Cela garantit que les données soumises sont conformes aux exigences de l'application et évite les erreurs lors de la mise à jour de l'utilisateur.
// 2. Détermination des rôles et permissions : En fonction du type d'utilisateur sélectionné, la méthode détermine les rôles et permissions à assigner à l'utilisateur. Si un type d'utilisateur est spécifié, il est automatiquement assigné comme rôle. Sinon, les rôles sont déterminés à partir des données soumises dans le formulaire. Les permissions sont ensuite calculées en fonction des rôles sélectionnés, à moins que l'option "permissions_overridden" ne soit cochée, auquel cas les permissions soumises sont utilisées directement.
// 3. Mise à jour de l'utilisateur : La méthode utilise une transaction de base de données pour garantir que toutes les opérations de mise à jour sont atomiques. Elle met à jour les informations de l'utilisateur, y compris le nom, l'email, le statut, la vérification de l'email et les domaines associés. Si un nouveau mot de passe est fourni, il est également mis à jour, et le compte est marqué comme nécessitant un changement de mot de passe à la prochaine connexion.
// 4. Envoi de notifications : Si un nouveau mot de passe est défini, la méthode tente d'envoyer une notification à l'utilisateur avec les informations de son compte. Si l'email est modifié, une notification de vérification d'email est envoyée. Les résultats de l'envoi des notifications sont capturés pour informer l'administrateur du succès ou de l'échec de l'envoi des emails.
// 5. Enregistrement de l'activité : Après la mise à jour de l'utilisateur, une entrée d'activité est créée pour enregistrer l'action de mise à jour, ce qui permet de garder une trace des modifications apportées aux utilisateurs dans l'application.
// 6. Redirection : Enfin, la méthode redirige l'administrateur vers la liste des utilisateurs avec un message de succès indiquant que l'utilisateur a été mis à jour avec succès, ainsi que des informations sur le compte mis à jour, telles que l'email, si un mot de passe temporaire a été défini, et si les emails de compte ou de vérification ont été envoyés
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
// Cette méthode gère la suppression d'un utilisateur spécifique. Voici la logique détaillée de cette méthode :
// 1. Vérification de l'utilisateur actuel : La méthode commence par vérifier si l'utilisateur à supprimer est le même que l'utilisateur actuellement authentifié. Si c'est le cas, la suppression est empêchée et l'administrateur est redirigé vers la liste des utilisateurs avec un message d'erreur indiquant qu'il n'est pas possible de supprimer son propre compte. Cela empêche les administrateurs de se supprimer accidentellement et de perdre l'accès à la gestion des utilisateurs.
// 2. Suppression de l'utilisateur : Si l'utilisateur à supprimer est différent de l'utilisateur actuel, la méthode procède à la suppression de l'utilisateur en appelant la méthode delete sur
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
// Cette méthode affiche la liste des utilisateurs associés à un domaine spécifique. Voici la logique détaillée de cette méthode :
// 1. Récupération des utilisateurs : La méthode commence par récupérer les utilisateurs qui ont un domaine_id correspondant à l'id du domaine passé en paramètre. Elle utilise la méthode where pour filtrer les utilisateurs en fonction du domaine, puis la méthode with pour charger les relations 'roles' et 'permissions' afin d'optimiser les requêtes et éviter le problème de N+   1. 2. Tri et pagination : Les utilisateurs récupérés sont triés par ordre décroissant de création (latest) pour afficher les utilisateurs les plus récents en premier. Ensuite, la méthode paginate est utilisée pour limiter le nombre d'utilisateurs affichés par page à 10, ce qui facilite la navigation dans la liste des utilisateurs. 3. Affichage de la vue : Enfin, la méthode retourne une 
//vue appelée 'admin.employes.by_domaine', en passant les utilisateurs récupérés et le domaine en question à la vue via la fonction compact. Cette vue est responsable de l'affichage de la liste des utilisateurs associés au domaine sélectionné, ainsi que des informations pertinentes sur le domaine lui-même.
    public function indexByDomaine(Domaine $domaine)
    {
        $users = User::where('domaine_id', $domaine->id)
            ->with(['roles', 'permissions'])
            ->latest()
            ->paginate(10);

        return view('admin.employes.by_domaine', compact('users', 'domaine'));
    }
// Cette méthode gère la création d'une nouvelle permission à partir d'une requête HTTP. Voici la logique détaillée de cette méthode :
// 1. Validation : La méthode commence par valider les données de la requête pour s'assurer que le champ 'name' est présent, est une chaîne de caractères, ne dépasse pas 255 caractères et est unique dans la table 'permissions'. Cela garantit que chaque permission créée a un nom valide et distinct.
// 2. Création de la permission : Si la validation réussit, la méthode crée une nouvelle permission en utilisant le modèle Permission de Spatie. Elle attribue le nom validé à la permission et définit le guard_name sur 'web', ce qui signifie que cette permission sera utilisée pour les utilisateurs authentifiés via le guard 'web' de Laravel.
// 3. Redirection : Après la création de la permission, la méthode redirige l'utilisateur vers la page précédente (généralement le formulaire de création ou d'édition d'utilisateur) avec un message de succès indiquant que la permission a été créée avec succès. Cela permet à l'administrateur de continuer à gérer les utilisateurs ou les permissions sans interruption.
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
// La validation du payload utilisateur est structurée de manière à être flexible et à s'adapter aux différents types d'utilisateurs (étudiant, employé, etc.) tout en assurant l'intégrité des données. Voici la logique détaillée de cette validation :
// 1. On commence par déterminer le type d'utilisateur sélectionné à partir du champ 'user_type' du formulaire. Cela nous permet de savoir si l'utilisateur est un étudiant, un employé ou un autre type, ce qui influence les règles de validation spécifiques à appliquer.
// 2. Ensuite, on définit un tableau de règles de validation pour les différents champs du formulaire. Ces règles incluent des contraintes de type, de format, de longueur, d'unicité, etc. Par exemple, le champ 'email' doit être unique dans la table 'users', sauf si on est en mode édition et que l'email n'a pas été modifié, auquel cas on ignore l'utilisateur actuel dans la vérification d'unicité.
// 3. Certaines règles sont conditionnelles en fonction du type d'utilisateur. Par exemple, les champs spécifiques aux étudiants (comme 'etudiant_genre', 'etudiant_telephone', 'etudiant_ecole') sont requis uniquement si le type d'utilisateur est 'etudiant'. De même, le champ 'domaine_id' est requis uniquement si le type d'utilisateur est 'employe'.
// 4. Enfin, on utilise la méthode 'validate' du Request pour appliquer ces règles et obtenir les données validées. Si la validation échoue, une exception de validation est levée et les erreurs sont automatiquement renvoyées à la vue avec les messages d'erreur correspondants. Si la validation réussit, on obtient un tableau de données validées qui peut être utilisé pour créer ou mettre à jour l'utilisateur.
    protected function validateUserPayload(Request $request, ?User $user = null): array
{
    $selectedType = $request->input('user_type');
    $isEtudiant = $selectedType === 'etudiant';
    $isEmploye = $selectedType === 'employe';

    $rules = [
        'nom' => ['required', 'string', 'max:255'],
        'prenom' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user?->id),
        ],
        'password' => [
            $user ? 'nullable' : 'required',
            'min:8',
            'max:255',
            Rule::when($request->filled('password'), 'confirmed'),
        ],
        'password_confirmation' => 'nullable',
        'user_type' => ['required', 'string', Rule::in($this->rolePermissionPresetService->allowedRoleNames())],
        'roles' => ['required', 'array', 'min:1'],
        'roles.*' => ['string', Rule::in($this->rolePermissionPresetService->allowedRoleNames())],
        'permissions' => ['nullable', 'array'],
        'permissions.*' => ['string', 'exists:permissions,name'],
        'permissions_overridden' => ['nullable', 'boolean'],
        'status' => ['nullable', Rule::in(['actif', 'inactif'])],
        'etudiant_genre' => [$isEtudiant ? 'required' : 'nullable', 'string', 'max:50'],
        'etudiant_telephone' => ['nullable', 'string', 'max:20'],
        'etudiant_ecole' => ['nullable', 'string', 'max:255'],
        'domaine_id' => ['nullable', 'exists:domaines,id', $isEmploye ? 'required' : 'nullable'],
    ];

    return $request->validate($rules);
}
// La logique de résolution des permissions est la suivante :
// 1. On récupère les permissions soumises dans le formulaire et on les nettoie pour s'assurer qu'elles sont valides.
// 2. Si l'option "permissions_overridden" est cochée, cela signifie que l'administrateur souhaite spécifier manuellement les permissions pour cet utilisateur, et dans ce cas, on retourne directement les permissions soumises.
// 3. Si l'option "permissions_overridden" n'est pas cochée, cela signifie que les permissions de l'utilisateur doivent être dérivées de ses rôles. Dans ce cas, on utilise le service RolePermissionPresetService pour calculer les permissions effectives en fonction des rôles sélectionnés
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
// La logique de résolution du nom est la suivante :
// 1. Si le prénom ou le nom est fourni dans les données validées, on les combine pour former le nom complet.
// 2. Si ni le prénom ni le nom n'est fourni, on vérifie si un utilisateur existant est passé en paramètre et on utilise son nom.
// 3. Si aucune de ces conditions n'est remplie, on utilise l'email comme nom de secours.
   protected function resolveUserName(array $validated, ?User $user = null): string
{
    $nom = $validated['nom'] ?? '';
    $prenom = $validated['prenom'] ?? '';
    $fullName = trim($prenom . ' ' . $nom);
    return $fullName ?: ($user?->name ?? $validated['email']);
}
// La logique de synchronisation du profil étudiant est la suivante :
// 1. On vérifie si le rôle "etudiant" est présent dans les rôles           
  protected function syncEtudiantProfile(User $user, array $validated, array $selectedRoles): void
{
    if (!in_array('etudiant', $selectedRoles, true)) {
        return;
    }

    $etudiant = $user->etudiant ?? new Etudiant();
    $etudiant->fill([
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'genre' => $validated['etudiant_genre'] ?? null,
        'email' => $validated['email'],
        'telephone' => $validated['etudiant_telephone'] ?? null,
        'ecole' => $validated['etudiant_ecole'] ?? null,
    ]);
    $etudiant->user()->associate($user);
    $etudiant->save();
}
// La logique de construction des données du formulaire est la suivante :
// 1. On récupère la liste des rôles disponibles en utilisant le service RolePermissionPresetService, qui les ordonne de manière logique pour l'affichage.
// 2. On récup 
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
// Cette méthode affiche les détails d'un utilisateur spécifique. Voici la logique détaillée de cette méthode :
// 1. Chargement des relations : La méthode commence par charger les relations 'etudiant',
// 'domaine' et 'roles' de l'utilisateur passé en paramètre. Cela permet d'avoir toutes les
// informations nécessaires sur le profil étudiant, le domaine et les rôles de l'utilisateur pour
// l'affichage des détails.
// 2. Vérification du type d'utilisateur : La méthode vérifie si l'utilisateur a un profil étudiant associé. Si c'est le cas, cela signifie que l'utilisateur est un étudiant, et la méthode tente de récupérer le stage actif ou le premier stage de l'étudiant pour l'affichage. Si un stage est trouvé, l'utilisateur est redirigé vers la page de détails du stage. Si aucun stage n'est trouvé, l'utilisateur est redirigé vers la liste des étudiants avec un message d'information indiquant que cet étudiant n'a pas encore de stage attribué.
// 3. Affichage du profil employé : Si l'utilisateur n'est pas un étudiant, cela signifie qu'il s'agit d'un employé. La méthode calcule alors le nombre de présences et le total d'heures travaillées pour cet employé en utilisant le modèle AttendanceDay. Enfin, la méthode retourne une vue appelée 'admin.users.show_employe', en passant l'utilisateur, le nombre de présences et le total d'heures travaillées à la vue via la fonction compact. Cette vue est responsable de l'affichage des détails de l'employé, y compris ses informations personnelles, ses rôles, et ses statistiques de présence et de travail.       
    public function show(User $user)
{
    $user->load(['etudiant', 'domaine', 'roles']);
    $isEtudiant = $user->etudiant !== null;

    if ($isEtudiant) {
        // Récupérer le stage actif ou le premier stage pour affichage
        $stage = $user->etudiant->stages()
                    ->where('date_fin', '>=', now())
                    ->orderBy('date_debut')
                    ->first();
        if ($stage) {
            return redirect()->route('stages.show', $stage);
        }
        // fallback : vue étudiant simplifiée (optionnelle)
        // return view('admin.users.show_etudiant', compact('user'));
        return redirect()->route('etudiants.index')->with('info', 'Cet étudiant n’a pas encore de stage attribué.');
    }

    // Profil employé
    $nbPresences = \App\Models\AttendanceDay::where('user_id', $user->id)->count();
    $totalHeures = \App\Models\AttendanceDay::where('user_id', $user->id)->sum('worked_minutes') / 60;
    return view('admin.users.show_employe', compact('user', 'nbPresences', 'totalHeures'));
}
}
