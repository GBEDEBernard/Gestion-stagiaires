@php
$linkedEtudiant = $user?->etudiant;
$selectedRoleNames = collect($selectedRoles ?? [])->values()->all();
$selectedPermissionNames = collect($selectedPermissions ?? [])->values()->all();
$shouldShowEtudiantBlock = in_array('etudiant', $selectedRoleNames, true) || $linkedEtudiant;
$showDomaineSection = in_array('employe', $selectedRoleNames, true);
$isCreate = !$user;
$rolePermissionMapJson = json_encode($rolePermissionMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

// Pré-remplissage des champs nom/prénom
$nomValue = old('nom', $linkedEtudiant?->nom ?? ($user ? explode(' ', $user->name, 2)[1] ?? '' : ''));
$prenomValue = old('prenom', $linkedEtudiant?->prenom ?? ($user ? explode(' ', $user->name, 2)[0] ?? '' : ''));
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <form
        action="{{ $formAction }}"
        method="POST"
        class="p-6 space-y-6"
        data-user-unified-form
        data-role-permission-map="{{ $rolePermissionMapJson }}">
        @csrf
        @isset($formMethod)
        @method($formMethod)
        @endisset

        <input type="hidden" name="permissions_overridden" value="{{ old('permissions_overridden', 0) }}" data-permissions-overridden>

        <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700 space-y-1">
            <p class="font-semibold">Parcours unifié de création de compte</p>
            <p>Tu choisis ici les rôles du compte, les permissions se préchargent automatiquement, puis tu peux les ajuster librement.</p>
            <p>Si le rôle <span class="font-semibold">étudiant</span> est coché, la fiche stagiaire est créée ou mise à jour dans ce même formulaire.</p>
        </div>

        @if($user)
        <div class="grid gap-3 md:grid-cols-2">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-sm">
                <p class="font-semibold text-gray-800 dark:text-gray-100">Vérification email</p>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    {{ $user->hasVerifiedEmail() ? 'Email déjà vérifié.' : 'Email encore en attente de vérification.' }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-sm">
                <p class="font-semibold text-gray-800 dark:text-gray-100">Mot de passe</p>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    {{ $user->must_change_password ? 'Mot de passe temporaire encore actif.' : 'Mot de passe personnel déjà défini.' }}
                </p>
            </div>
        </div>
        @endif

        {{-- SECTION COMPTE UTILISATEUR --}}
        <section class="space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Compte utilisateur</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ces informations servent à la connexion, à l'envoi du mail et à l'identité globale du compte.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="nom"
                        id="nom"
                        value="{{ $nomValue }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Dupont">
                    @error('nom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="prenom"
                        id="prenom"
                        value="{{ $prenomValue }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Jean">
                    @error('prenom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email de connexion <span class="text-red-500">*</span></label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $user?->email) }}"
                        required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="exemple@email.com">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cet email sera celui utilisé par l'utilisateur pour se connecter.</p>
                    @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $user ? 'Redéfinir le mot de passe temporaire (optionnel)' : 'Mot de passe temporaire' }}
                        @unless($user)<span class="text-red-500">*</span>@endunless
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        @unless($user) required @endunless
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Définis le mot de passe initial">
                    @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirmation @unless($user)<span class="text-red-500">*</span>@endunless</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        @unless($user) required @endunless
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Confirme le mot de passe temporaire">
                </div>
            </div>
        </section>

        {{-- SECTION RÔLES ET PERMISSIONS --}}
        <section class="space-y-5 border-t border-gray-100 dark:border-gray-700 pt-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Rôles et permissions</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Les rôles servent de presets. Les permissions ci-dessous représentent exactement ce que le compte pourra faire.</p>
            </div>

            <div>
                <label for="user_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Type d'utilisateur <span class="text-red-500">*</span></label>
                <select id="user_type" name="user_type" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white" required>
                    <option value="">Choisir le type...</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('user_type', $selectedRoleNames[0] ?? '') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                    @endforeach
                </select>
                @error('user_type')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror

                <input type="hidden" name="roles[]" value="" id="hidden_role" data-role-input>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Les permissions sont chargées automatiquement selon le rôle.
                    @if($isCreate)
                    Elles seront appliquées à la création du compte et peuvent être modifiées ensuite en édition de l'utilisateur ou du rôle.
                    @else
                    Vous pouvez les ajuster librement pour ce compte.
                    @endif
                </p>

                @unless($isCreate)
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach($permissionGroups as $group => $groupPermissions)
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ ucfirst($group) }}</p>
                        <div class="space-y-2">
                            @foreach($groupPermissions as $permission)
                            <label class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-emerald-500 permission-checkbox"
                                    {{ in_array($permission->name, $selectedPermissionNames) ? 'checked' : '' }}>
                                <span class="leading-tight">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                @enderror
                @endunless
            </div>
        </section>

        {{-- SECTION DOMAINE DE TRAVAIL (pour employé) --}}
        <section class="space-y-5 border-t border-gray-100 dark:border-gray-700 pt-6 {{ $showDomaineSection ? '' : 'hidden' }}" data-domaine-section>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Domaine de travail</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sélectionnez d'abord le site, puis le domaine correspondant pour les employés.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="site_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site <span class="text-red-500">*</span></label>
                    <select
                        name="site_id"
                        id="site_id"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white"
                        required>
                        <option value="">Sélectionner un site...</option>
                        @foreach($sites ?? [] as $site)
                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le site détermine les domaines disponibles.</p>
                    @error('site_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="domaine_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Domaine <span class="text-red-500">*</span></label>
                    <select
                        name="domaine_id"
                        id="domaine_id"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white"
                        required
                        disabled>
                        <option value="">Sélectionner un site d'abord...</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Les employés doivent être assignés à un domaine pour pouvoir pointer.</p>
                    @error('domaine_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- SECTION FICHE ÉTUDIANT (sans nom/prénom) --}}
        <section
            class="space-y-5 border-t border-gray-100 dark:border-gray-700 pt-6 {{ $shouldShowEtudiantBlock ? '' : 'hidden' }}"
            data-etudiant-fields>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Fiche étudiant</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ce bloc apparaît quand le rôle <span class="font-semibold">étudiant</span> est choisi. Il crée ou met à jour la fiche stagiaire dans le même mouvement.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="etudiant_genre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Genre <span class="text-red-500">*</span></label>
                    <select
                        name="etudiant_genre"
                        id="etudiant_genre"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white">
                        <option value="">Sélectionner...</option>
                        <option value="Masculin" {{ old('etudiant_genre', $linkedEtudiant?->genre) === 'Masculin' ? 'selected' : '' }}>Masculin</option>
                        <option value="Féminin" {{ old('etudiant_genre', $linkedEtudiant?->genre) === 'Féminin' ? 'selected' : '' }}>Féminin</option>
                    </select>
                    @error('etudiant_genre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="etudiant_telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                    <input
                        type="text"
                        name="etudiant_telephone"
                        id="etudiant_telephone"
                        value="{{ old('etudiant_telephone', $linkedEtudiant?->telephone) }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: +229 01 00 00 00 00">
                    @error('etudiant_telephone')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="etudiant_ecole" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">École</label>
                    <input
                        type="text"
                        name="etudiant_ecole"
                        id="etudiant_ecole"
                        value="{{ old('etudiant_ecole', $linkedEtudiant?->ecole) }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Université d'Abomey-Calavi">
                    @error('etudiant_ecole')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a
                href="{{ route('admin.users.index') }}"
                class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                Annuler
            </a>
            <button
                type="submit"
                class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl hover:from-green-700 hover:to-emerald-800 transition font-medium shadow-lg shadow-green-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-user-unified-form]');

        if (!form) {
            return;
        }

        const roleMap = JSON.parse(form.dataset.rolePermissionMap || '{}');
        const typeSelect = form.querySelector('#user_type');
        const hiddenRoleInput = form.querySelector('#hidden_role');
        const domaineSection = form.querySelector('[data-domaine-section]');
        const permissionCheckboxes = Array.from(form.querySelectorAll('.permission-checkbox'));
        const etudiantFields = form.querySelector('[data-etudiant-fields]');
        const permissionsOverriddenInput = form.querySelector('[data-permissions-overridden]');

        const selectedRole = () => typeSelect ? [typeSelect.value] : [];
        const defaultPermissions = () => {
            const permissions = new Set();
            const roles = selectedRole();
            roles.forEach((roleName) => {
                (roleMap[roleName] || []).forEach((permissionName) => permissions.add(permissionName));
            });
            return permissions;
        };

        const syncEtudiantVisibility = () => {
            const hasEtudiantRole = selectedRole().includes('etudiant');
            etudiantFields.classList.toggle('hidden', !hasEtudiantRole);
        };

        const syncDomaineVisibility = () => {
            const isEmploye = selectedRole().includes('employe');
            domaineSection?.classList.toggle('hidden', !isEmploye);
        };

        const refreshManualFlags = () => {
            const defaults = defaultPermissions();
            permissionCheckboxes.forEach((checkbox) => {
                checkbox.dataset.manual = String(checkbox.checked !== defaults.has(checkbox.value));
            });
        };

        const applyTypeDefaults = () => {
            const selectedType = typeSelect.value;
            hiddenRoleInput.value = selectedType || '';

            const defaults = defaultPermissions();
            if (permissionsOverriddenInput.value !== '1') {
                permissionCheckboxes.forEach((checkbox) => {
                    checkbox.checked = defaults.has(checkbox.value);
                });
            }

            syncEtudiantVisibility();
            syncDomaineVisibility();
        };

        typeSelect.addEventListener('change', () => {
            permissionsOverriddenInput.value = '0';
            applyTypeDefaults();
        });

        permissionCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                permissionsOverriddenInput.value = '1';
                refreshManualFlags();
            });
        });

        applyTypeDefaults();
        refreshManualFlags();
        syncEtudiantVisibility();
        syncDomaineVisibility();

        // Gestion du chargement dynamique des domaines par site
        const siteSelect = document.getElementById('site_id');
        const domaineSelect = document.getElementById('domaine_id');

        if (siteSelect && domaineSelect) {
            // Charger les domaines quand le site change
            siteSelect.addEventListener('change', async function() {
                const siteId = this.value;

                if (!siteId) {
                    // Réinitialiser le select des domaines
                    domaineSelect.innerHTML = '<option value="">Sélectionner un site d\'abord...</option>';
                    domaineSelect.disabled = true;
                    return;
                }

                try {
                    const response = await fetch(`/api/sites/${siteId}/domaines`);
                    const domaines = await response.json();

                    if (domaines.length > 0) {
                        domaineSelect.innerHTML = '<option value="">Sélectionner un domaine...</option>';
                        domaines.forEach(domaine => {
                            const option = document.createElement('option');
                            option.value = domaine.id;
                            option.textContent = domaine.nom;
                            domaineSelect.appendChild(option);
                        });
                        domaineSelect.disabled = false;
                    } else {
                        domaineSelect.innerHTML = '<option value="">Aucun domaine pour ce site</option>';
                        domaineSelect.disabled = true;
                    }
                } catch (error) {
                    console.error('Erreur lors du chargement des domaines:', error);
                    domaineSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    domaineSelect.disabled = true;
                }
            });

            // Déclencher le chargement si un site est déjà sélectionné (mode édition)
            if (siteSelect.value) {
                siteSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>