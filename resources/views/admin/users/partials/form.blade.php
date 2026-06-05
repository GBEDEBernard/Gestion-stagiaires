@php
$isEdit = $isEdit ?? false;
$userType = $selectedRoles[0] ?? 'admin';
@endphp

<form action="{{ $formAction }}" method="POST" class="space-y-8">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    {{-- AFFICHAGE DES ERREURS DE VALIDATION --}}
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-5 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-800/50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                    {{ $errors->count() }} erreur(s) empêchent l'enregistrement
                </p>
                <ul class="mt-2 space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-sm text-red-700 dark:text-red-400 flex items-start gap-2">
                        <span class="text-red-400">•</span>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Section Statut du compte (visible uniquement en édition) --}}
    @if($isEdit)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            Statut du compte
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                <select name="status" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('status') border-red-500 @enderror">
                    <option value="actif" {{ old('status', $user->status ?? '') == 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ old('status', $user->status ?? '') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
                @error('status')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nouveau mot de passe</label>
                    <input type="password" name="password" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('password') border-red-500 @enderror" placeholder="Laisser vide pour conserver l'actuel">
                    <p class="text-xs text-gray-400 mt-1">Remplissez pour définir un nouveau mot de passe</p>
                    @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl" placeholder="Confirmez le nouveau mot de passe">
                </div>
            </div>
            @if(in_array('admin', $selectedRoles))
            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900/50 rounded-xl cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                    <input type="checkbox" name="is_signer" value="1" {{ old('is_signer', $isSignerValue ?? false) ? 'checked' : '' }} class="rounded border-blue-300 text-blue-600">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Peut signer les attestations</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-4">Vérifiez cette option pour permettre à cet administrateur de signer les attestations de stage</p>
                @error('is_signer')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Section Informations personnelles --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            Informations personnelles
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom *</label>
                <input type="text" name="nom" value="{{ old('nom', $nomValue ?? '') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('nom') border-red-500 @enderror">
                @error('nom')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom *</label>
                <input type="text" name="prenom" value="{{ old('prenom', $prenomValue ?? '') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('prenom') border-red-500 @enderror">
                @error('prenom')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                <input type="email" name="email" value="{{ old('email', $emailValue ?? '') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('email') border-red-500 @enderror">
                @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone', $telephoneValue ?? '') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('telephone') border-red-500 @enderror">
                @error('telephone')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Genre</label>
                <select name="genre" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('genre') border-red-500 @enderror">
                    <option value="">Non spécifié</option>
                    <option value="Homme" {{ old('genre', $genreValue ?? '') == 'Homme' ? 'selected' : '' }}>Homme</option>
                    <option value="Femme" {{ old('genre', $genreValue ?? '') == 'Femme' ? 'selected' : '' }}>Femme</option>
                </select>
                @error('genre')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Section Étudiant --}}
    @if(in_array('etudiant', $selectedRoles))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            Informations étudiant
        </h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">École</label>
            <input type="text" name="etudiant_ecole" value="{{ old('etudiant_ecole', $etudiantEcole ?? '') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('etudiant_ecole') border-red-500 @enderror" placeholder="Ex: EPAC, UAC, ...">
            @error('etudiant_ecole')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
    @endif

    {{-- Section Employé --}}
    @if(in_array('employe', $selectedRoles))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            Informations professionnelles
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Domaine *</label>
                <select name="domaine_id" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('domaine_id') border-red-500 @enderror">
                    <option value="">Sélectionner</option>
                    @foreach($domaines as $domaine)
                    <option value="{{ $domaine->id }}" {{ old('domaine_id', $domaineIdValue ?? '') == $domaine->id ? 'selected' : '' }}>
                        {{ $domaine->nom }}
                    </option>
                    @endforeach
                </select>
                @error('domaine_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site *</label>
                <select name="employe_site_id" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('employe_site_id') border-red-500 @enderror">
                    <option value="">Sélectionner</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ old('employe_site_id', $employeSiteId ?? '') == $site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                    </option>
                    @endforeach
                </select>
                @error('employe_site_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Matricule</label>
                <input type="text" name="employe_matricule" value="{{ old('employe_matricule', $employeMatricule ?? '') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl bg-gray-100" readonly disabled>
                <p class="text-xs text-gray-400 mt-1">Généré automatiquement</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Poste</label>
                <input type="text" name="employe_poste" value="{{ old('employe_poste', $employePoste ?? '') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('employe_poste') border-red-500 @enderror">
                @error('employe_poste')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- Section Rôles et permissions --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            Rôles et permissions
        </h3>

        {{-- Sélection du rôle principal --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'utilisateur *</label>
            <select name="user_type" id="user_type" class="w-full md:w-1/2 px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('user_type') border-red-500 @enderror" required>
                <option value="">Sélectionner un type</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ in_array($role->name, $selectedRoles) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Le rôle principal détermine le type de compte et les permissions par défaut</p>
            @error('user_type')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Rôles additionnels --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rôles additionnels</label>
            <div class="flex flex-wrap gap-3">
                @foreach($roles as $role)
                @if(!in_array($role->name, ['super_admin']))
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-900 border rounded-xl cursor-pointer">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                        {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm">{{ ucfirst($role->name) }}</span>
                </label>
                @endif
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1">Cochez pour attribuer plusieurs rôles à cet utilisateur</p>
        </div>

        {{-- Permissions détaillées --}}
        <div class="mt-4">
            <div class="flex justify-between items-center mb-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions associées</label>
                <div class="flex gap-2">
                    <button type="button" id="togglePermissions" class="text-xs text-blue-600 hover:text-blue-800">Tout afficher/masquer</button>
                    <button type="button" id="selectAllPermissions" class="text-xs text-green-600 hover:text-green-800">Tout sélectionner</button>
                    <button type="button" id="unselectAllPermissions" class="text-xs text-red-600 hover:text-red-800">Tout désélectionner</button>
                </div>
            </div>
            <div id="permissionsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2 border border-gray-200 dark:border-gray-700 rounded-lg">
                @foreach($permissionGroups as $group => $perms)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2 capitalize">{{ $group }}</h4>
                    @foreach($perms as $perm)
                    <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 rounded px-1">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                            {{ in_array($perm->name, $selectedPermissions) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 permission-checkbox">
                        <span class="text-gray-600 dark:text-gray-400">{{ $perm->name }}</span>
                    </label>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Boutons --}}
    <div class="flex items-center justify-end gap-4 pt-4">
        <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition font-medium">
            Annuler
        </a>
        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-600/20">
            {{ $submitLabel }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('permissionsContainer');
        const toggleBtn = document.getElementById('togglePermissions');
        const selectAllBtn = document.getElementById('selectAllPermissions');
        const unselectAllBtn = document.getElementById('unselectAllPermissions');
        let permissionsVisible = true;

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                permissionsVisible = !permissionsVisible;
                container.style.display = permissionsVisible ? 'grid' : 'none';
                toggleBtn.textContent = permissionsVisible ? 'Tout masquer' : 'Tout afficher';
            });
        }

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = true;
                });
            });
        }

        if (unselectAllBtn) {
            unselectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = false;
                });
            });
        }

        const userTypeSelect = document.getElementById('user_type');
        const rolePermissionMap = @json($rolePermissionMap);

        if (userTypeSelect) {
            userTypeSelect.addEventListener('change', function() {
                const selectedRole = this.value;
                if (selectedRole && rolePermissionMap[selectedRole]) {
                    document.querySelectorAll('.permission-checkbox').forEach(cb => {
                        cb.checked = false;
                    });
                    rolePermissionMap[selectedRole].forEach(permission => {
                        const checkbox = document.querySelector(`.permission-checkbox[value="${permission}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            });
        }
    });
</script>