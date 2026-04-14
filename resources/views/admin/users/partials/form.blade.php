@php
$linkedEtudiant = $user?->etudiant;
$selectedRoleNames = collect($selectedRoles ?? [])->values()->all();
$selectedPermissionNames = collect($selectedPermissions ?? [])->values()->all();
$shouldShowEtudiantBlock = in_array('etudiant', $selectedRoleNames, true) || $linkedEtudiant;
$rolePermissionMapJson = json_encode($rolePermissionMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
$etudiantNomValue = old('etudiant_nom', $linkedEtudiant?->nom);
$etudiantPrenomValue = old('etudiant_prenom', $linkedEtudiant?->prenom);
$accountNameValue = old('name', $user?->name);
$generatedEtudiantName = trim(($etudiantPrenomValue ?? '') . ' ' . ($etudiantNomValue ?? ''));
$isAccountNameManual = filled($accountNameValue) && $accountNameValue !== $generatedEtudiantName;
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
            <p class="font-semibold">Parcours unifie de creation de compte</p>
            <p>Tu choisis ici les roles du compte, les permissions se prechargent automatiquement, puis tu peux les ajuster librement.</p>
            <p>Si le role <span class="font-semibold">etudiant</span> est coche, la fiche stagiaire est creee ou mise a jour dans ce meme formulaire.</p>
        </div>

        @if($user)
        <div class="grid gap-3 md:grid-cols-2">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-sm">
                <p class="font-semibold text-gray-800 dark:text-gray-100">Verification email</p>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    {{ $user->hasVerifiedEmail() ? 'Email deja verifie.' : 'Email encore en attente de verification.' }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-sm">
                <p class="font-semibold text-gray-800 dark:text-gray-100">Mot de passe</p>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    {{ $user->must_change_password ? 'Mot de passe temporaire encore actif.' : 'Mot de passe personnel deja defini.' }}
                </p>
            </div>
        </div>
        @endif

        <section class="space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Compte utilisateur</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ces informations servent a la connexion, a l'envoi du mail et a l'identite globale du compte.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom d'affichage</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ $accountNameValue }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Jean Dupont"
                        data-account-name
                        data-manual="{{ $isAccountNameManual ? 'true' : 'false' }}">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pour un etudiant, ce champ est rempli automatiquement a partir du prenom et du nom.</p>
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

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
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cet email sera celui utilise par l'utilisateur pour se connecter.</p>
                    @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $user ? 'Redefinir le mot de passe temporaire (optionnel)' : 'Mot de passe temporaire' }}
                        @unless($user)<span class="text-red-500">*</span>@endunless
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        @unless($user) required @endunless
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Definis le mot de passe initial">
                    @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
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

        <section class="space-y-5 border-t border-gray-100 dark:border-gray-700 pt-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Roles et permissions</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Les roles servent de presets. Les permissions ci-dessous representent exactement ce que le compte pourra faire.</p>
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

                {{-- Hidden inputs for auto-assignment --}}
                <input type="hidden" name="roles[]" value="" id="hidden_role" data-role-input>
                @foreach($roles as $role)
                <input type="hidden" name="roles[]" value="{{ $role->name }}" class="hidden-role" style="display: none;" data-role="{{ $role->name }}">
                @endforeach
            </div>

            {{-- Permissions hidden & auto-assigned --}}
            <input type="hidden" name="permissions_overridden" value="0">
            @foreach($permissions as $permission)
            <input type="hidden" name="permissions[]" value="{{ $permission->name }}" class="hidden-permission" style="display: none;" data-permission="{{ $permission->name }}">
            @endforeach
        </section>

        <section class="space-y-5 border-t border-gray-100 dark:border-gray-700 pt-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Domaine de travail</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Assignez un domaine de travail pour les employés (non applicable aux étudiants).</p>
            </div>

            <div>
                <label for="domaine_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Domaine</label>
                <select
                    name="domaine_id"
                    id="domaine_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white">
                    <option value="">Aucun domaine (Étudiant ou non assigné)</option>
                    @foreach($domaines ?? [] as $domaine)
                    <option value="{{ $domaine->id }}" {{ old('domaine_id', $user?->domaine_id) == $domaine->id ? 'selected' : '' }}>
                        {{ $domaine->nom }}
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Les employés doivent être assignés à un domaine pour pouvoir pointer.</p>
                @error('domaine_id')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section
            class="space-y-5 border-t border-gray-100 dark:border-gray-700 pt-6 {{ $shouldShowEtudiantBlock ? '' : 'hidden' }}"
            data-etudiant-fields>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Fiche etudiant</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ce bloc apparait quand le role <span class="font-semibold">etudiant</span> est choisi. Il cree ou met a jour la fiche stagiaire dans le meme mouvement.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="etudiant_nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="etudiant_nom"
                        id="etudiant_nom"
                        value="{{ $etudiantNomValue }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Dupont"
                        data-etudiant-nom>
                    @error('etudiant_nom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="etudiant_prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prenom <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="etudiant_prenom"
                        id="etudiant_prenom"
                        value="{{ $etudiantPrenomValue }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Jean"
                        data-etudiant-prenom>
                    @error('etudiant_prenom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="etudiant_genre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Genre <span class="text-red-500">*</span></label>
                    <select
                        name="etudiant_genre"
                        id="etudiant_genre"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white">
                        <option value="">Selectionner...</option>
                        <option value="Masculin" {{ old('etudiant_genre', $linkedEtudiant?->genre) === 'Masculin' ? 'selected' : '' }}>Masculin</option>
                        <option value="Feminin" {{ old('etudiant_genre', $linkedEtudiant?->genre) === 'Feminin' ? 'selected' : '' }}>Feminin</option>
                    </select>
                    @error('etudiant_genre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="etudiant_telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telephone</label>
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
                    <label for="etudiant_ecole" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ecole</label>
                    <input
                        type="text"
                        name="etudiant_ecole"
                        id="etudiant_ecole"
                        value="{{ old('etudiant_ecole', $linkedEtudiant?->ecole) }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Universite d'Abomey-Calavi">
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
        const hiddenRoleInputs = form.querySelectorAll('.hidden-role');
        const hiddenPermissionInputs = form.querySelectorAll('.hidden-permission');
        const etudiantFields = form.querySelector('[data-etudiant-fields]');
        const permissionsOverriddenInput = form.querySelector('[data-permissions-overridden]');
        const accountNameInput = form.querySelector('[data-account-name]');
        const etudiantNomInput = form.querySelector('[data-etudiant-nom]');
        const etudiantPrenomInput = form.querySelector('[data-etudiant-prenom]');

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

        const syncNameFromEtudiant = () => {
            if (!selectedRole().includes('etudiant')) {
                return;
            }

            if (accountNameInput.dataset.manual === 'true') {
                return;
            }

            const generatedName = `${etudiantPrenomInput.value || ''} ${etudiantNomInput.value || ''}`.trim();

            accountNameInput.value = generatedName;
        };

        const refreshManualFlags = () => {
            const defaults = defaultPermissions();

            permissionCheckboxes.forEach((checkbox) => {
                checkbox.dataset.manual = String(checkbox.checked !== defaults.has(checkbox.value));
            });
        };

        const applyTypeDefaults = () => {
            // Show selected role input
            hiddenRoleInputs.forEach(input => input.style.display = 'none');
            const selectedType = typeSelect.value;
            const matchingRole = Array.from(hiddenRoleInputs).find(input => input.dataset.role === selectedType);
            if (matchingRole) {
                matchingRole.style.display = 'block';
            }
            hiddenRoleInput.value = selectedType || '';

            // Auto-show all preset permissions (hidden)
            const defaults = defaultPermissions();
            hiddenPermissionInputs.forEach(input => {
                input.style.display = defaults.has(input.dataset.permission) ? 'block' : 'none';
            });

            syncEtudiantVisibility();
            syncNameFromEtudiant();
        };

        accountNameInput.addEventListener('input', () => {
            accountNameInput.dataset.manual = accountNameInput.value.trim() !== '' ? 'true' : 'false';
        });

        [etudiantNomInput, etudiantPrenomInput].forEach((input) => {
            input?.addEventListener('input', () => {
                syncNameFromEtudiant();
            });
        });

        typeSelect.addEventListener('change', applyTypeDefaults);

        permissionCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                permissionsOverriddenInput.value = '1';
                refreshManualFlags();
            });
        });

        refreshManualFlags();
        syncEtudiantVisibility();
        syncNameFromEtudiant();
    });
</script>