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
            {{-- Télétravail autorisé --}}
            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-3 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-900/50 rounded-xl cursor-pointer hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                    <input type="checkbox" name="remote_work_enabled" value="1" {{ old('remote_work_enabled', $remoteWorkEnabledValue ?? false) ? 'checked' : '' }} class="rounded border-indigo-300 text-indigo-600">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Télétravail autorisé</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-4">
                    Permet au producteur de soumettre des rapports à distance (à domicile), y compris
                    après son pointage de départ, à condition qu'une tâche lui soit assignée.
                </p>
                @error('remote_work_enabled')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    @else
    {{-- Section création : mot de passe obligatoire --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            Sécurité
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mot de passe *</label>
                <input type="password" name="password" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('password') border-red-500 @enderror" required>
                @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirmer le mot de passe *</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl" required>
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
            {{-- Télétravail autorisé (création) --}}
            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-3 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-900/50 rounded-xl cursor-pointer hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                    <input type="checkbox" name="remote_work_enabled" value="1" {{ old('remote_work_enabled', $remoteWorkEnabledValue ?? false) ? 'checked' : '' }} class="rounded border-indigo-300 text-indigo-600">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Télétravail autorisé</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-4">
                    Permet au producteur de soumettre des rapports à distance (à domicile), y compris
                    après son pointage de départ, à condition qu'une tâche lui soit assignée.
                </p>
                @error('remote_work_enabled')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
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
        {{-- SUPERVISEUR POUR ÉTUDIANT --}}
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Superviseur attitré</label>
            <select name="etudiant_supervisor_id" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('etudiant_supervisor_id') border-red-500 @enderror">
                <option value="">Aucun</option>
                @foreach($superviseurs ?? [] as $sup)
                <option value="{{ $sup->id }}" {{ old('etudiant_supervisor_id', $etudiantSupervisorId ?? null) == $sup->id ? 'selected' : '' }}>
                    {{ $sup->personnel?->full_name ?? $sup->name }} ({{ $sup->email }})
                </option>
                @endforeach
            </select>
            @error('etudiant_supervisor_id')
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Poste</label>
                <input type="text" name="employe_poste" value="{{ old('employe_poste', $employePoste ?? '') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('employe_poste') border-red-500 @enderror">
                @error('employe_poste')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Superviseur attitré</label>
                <select name="supervisor_id" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border rounded-xl @error('supervisor_id') border-red-500 @enderror">
                    <option value="">Aucun</option>
                    @foreach($superviseurs ?? [] as $sup)
                    <option value="{{ $sup->id }}" {{ old('supervisor_id', $supervisorIdValue ?? null) == $sup->id ? 'selected' : '' }}>
                        {{ $sup->personnel?->full_name ?? $sup->name }} ({{ $sup->email }})
                    </option>
                    @endforeach
                </select>
                @error('supervisor_id')
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
            @php $labelService = app(\App\Services\PermissionLabelService::class); @endphp

            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions associées</label>
                    <span id="total-count-badge"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all">
                        0 sélectionnée(s)
                    </span>
                </div>
                <button type="button" onclick="toggleAllPermissions()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tout sélectionner / Désélectionner
                </button>
            </div>

            {{-- Search bar --}}
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                </div>
                <input type="text" id="permission-search"
                    placeholder="Rechercher une permission ou un groupe..."
                    class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400">
                <button type="button" id="clear-search"
                    class="absolute inset-y-0 right-0 pr-3.5 items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                    style="display:none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Accordion groups --}}
            <div class="space-y-2" id="permissions-accordion">

                @forelse($permissionGroups as $group => $perms)
                <div class="accordion-item border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                     data-group="{{ $group }}"
                     data-group-label="{{ strtolower($labelService->getGroupLabel($group)) }}">

                    {{-- ── Accordion Header ── --}}
                    <div class="accordion-header flex items-center gap-3 px-4 py-3.5 bg-white dark:bg-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors select-none"
                         onclick="toggleAccordion('{{ $group }}')">

                        <div class="flex-shrink-0" onclick="event.stopPropagation()">
                            <input type="checkbox"
                                id="select-all-{{ $group }}"
                                class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 select-all-checkbox cursor-pointer"
                                data-group="{{ $group }}"
                                title="Sélectionner / désélectionner tout le groupe">
                        </div>

                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                    bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/40">
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                {{ strtoupper(substr($labelService->getGroupLabel($group), 0, 2)) }}
                            </span>
                        </div>

                        <span class="flex-1 text-sm font-semibold text-gray-800 dark:text-white truncate">
                            {{ $labelService->getGroupLabel($group) }}
                        </span>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="group-count-badge inline-flex items-center justify-center
                                         min-w-[3.5rem] px-2 py-0.5 rounded-full text-xs font-medium
                                         bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400
                                         transition-all duration-200"
                                  data-group="{{ $group }}"
                                  data-total="{{ count($perms) }}">
                                0 / {{ count($perms) }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                 id="chevron-{{ $group }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- ── Accordion Body ── --}}
                    <div id="body-{{ $group }}"
                         data-open="false"
                         style="max-height:0; overflow:hidden; transition: max-height .3s cubic-bezier(.4,0,.2,1)">
                        <div class="px-4 pb-4 pt-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/20">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-0.5">
                                @foreach($perms as $perm)
                                <div class="permission-item flex items-center gap-2.5 px-2 py-2 rounded-lg hover:bg-white dark:hover:bg-gray-800/60 transition-colors"
                                     data-label="{{ strtolower($labelService->getLabel($perm->name)) }}">
                                    <input type="checkbox" name="permissions[]"
                                        id="permission-{{ $perm->id }}"
                                        value="{{ $perm->name }}"
                                        {{ in_array($perm->name, $selectedPermissions) ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 permission-checkbox cursor-pointer flex-shrink-0"
                                        data-group="{{ $group }}">
                                    <label for="permission-{{ $perm->id }}"
                                        class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer hover:text-gray-900 dark:hover:text-gray-100 transition-colors select-none leading-snug">
                                        {{ $labelService->getLabel($perm->name) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
                @empty
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Aucune permission disponible</p>
                </div>
                @endforelse

                <div id="no-results" class="hidden text-center py-8">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun résultat pour votre recherche.</p>
                </div>

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

@php
    $userTypeJson = old('user_type', $selectedRoles[0] ?? '');
@endphp

<script>
// ── Accordion ────────────────────────────────────────────────────────────────
function toggleAccordion(group) {
    const body   = document.getElementById('body-' + group);
    const chev   = document.getElementById('chevron-' + group);
    const isOpen = body.dataset.open === 'true';

    if (isOpen) {
        if (body.style.maxHeight === 'none') body.style.maxHeight = body.scrollHeight + 'px';
        requestAnimationFrame(() => requestAnimationFrame(() => { body.style.maxHeight = '0px'; }));
        body.dataset.open   = 'false';
        chev.style.transform = 'rotate(0deg)';
    } else {
        body.style.maxHeight = body.scrollHeight + 'px';
        body.dataset.open   = 'true';
        chev.style.transform = 'rotate(180deg)';
        body.addEventListener('transitionend', function once() {
            if (body.dataset.open === 'true') body.style.maxHeight = 'none';
            body.removeEventListener('transitionend', once);
        });
    }
}

function openAccordionDirect(group) {
    const body = document.getElementById('body-' + group);
    const chev = document.getElementById('chevron-' + group);
    body.style.transition = 'none';
    body.style.maxHeight  = 'none';
    body.dataset.open     = 'true';
    chev.style.transform  = 'rotate(180deg)';
    requestAnimationFrame(() => { body.style.transition = ''; });
}

function closeAccordionDirect(group) {
    const body = document.getElementById('body-' + group);
    const chev = document.getElementById('chevron-' + group);
    body.style.transition = 'none';
    body.style.maxHeight  = '0px';
    body.dataset.open     = 'false';
    chev.style.transform  = 'rotate(0deg)';
    requestAnimationFrame(() => { body.style.transition = ''; });
}

// ── Count badges ─────────────────────────────────────────────────────────────
const BADGE_NONE  = ['bg-gray-100','dark:bg-gray-700','text-gray-500','dark:text-gray-400'];
const BADGE_SOME  = ['bg-indigo-100','dark:bg-indigo-900','text-indigo-700','dark:text-indigo-300'];
const BADGE_ALL   = ['bg-indigo-600','text-white'];
const ALL_BADGE_C = [...BADGE_NONE, ...BADGE_SOME, ...BADGE_ALL];

function updateGroupCount(group) {
    const all     = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
    const checked = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:checked`);
    const badge   = document.querySelector(`.group-count-badge[data-group="${group}"]`);
    const item    = document.querySelector(`.accordion-item[data-group="${group}"]`);
    if (!badge || !item) return;

    const n = checked.length, t = all.length;
    badge.textContent = n + ' / ' + t;
    ALL_BADGE_C.forEach(c => badge.classList.remove(c));

    if (n === 0) {
        BADGE_NONE.forEach(c => badge.classList.add(c));
        item.style.borderColor = '';
    } else if (n === t) {
        BADGE_ALL.forEach(c => badge.classList.add(c));
        item.style.borderColor = 'rgb(99 102 241 / 0.55)';
    } else {
        BADGE_SOME.forEach(c => badge.classList.add(c));
        item.style.borderColor = 'rgb(99 102 241 / 0.3)';
    }

    updateSelectAllState(group);
    updateTotalCount();
}

function updateTotalCount() {
    const n     = document.querySelectorAll('.permission-checkbox:checked').length;
    const badge = document.getElementById('total-count-badge');
    if (!badge) return;
    badge.textContent = n + ' sélectionnée(s)';
    badge.className   = n > 0
        ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 transition-all'
        : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all';
}

function updateSelectAllState(group) {
    const cbs  = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
    const allC = Array.from(cbs).every(cb => cb.checked);
    const smC  = Array.from(cbs).some(cb => cb.checked);
    const sa   = document.getElementById('select-all-' + group);
    if (sa) { sa.checked = allC; sa.indeterminate = smC && !allC; }
}

// ── Event listeners ──────────────────────────────────────────────────────────
document.querySelectorAll('.select-all-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        const g = this.dataset.group;
        document.querySelectorAll(`.permission-checkbox[data-group="${g}"]`).forEach(c => { c.checked = this.checked; });
        updateGroupCount(g);
    });
});

document.querySelectorAll('.permission-checkbox').forEach(cb => {
    cb.addEventListener('change', function () { updateGroupCount(this.dataset.group); });
});

// ── Toggle All ───────────────────────────────────────────────────────────────
function toggleAllPermissions() {
    const all   = document.querySelectorAll('.permission-checkbox');
    const anyOn = Array.from(all).some(cb => cb.checked);
    all.forEach(cb => { cb.checked = !anyOn; });
    const groups = new Set(Array.from(all).map(cb => cb.dataset.group));
    groups.forEach(g => updateGroupCount(g));
}

// ── Search ───────────────────────────────────────────────────────────────────
const searchInput = document.getElementById('permission-search');
const clearBtn    = document.getElementById('clear-search');

searchInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    clearBtn.style.display = q ? 'flex' : 'none';
    filterPermissions(q);
});

clearBtn.addEventListener('click', function () {
    searchInput.value      = '';
    this.style.display     = 'none';
    filterPermissions('');
});

function filterPermissions(q) {
    let visible = 0;
    document.querySelectorAll('.accordion-item').forEach(item => {
        const gLabel = (item.dataset.groupLabel || '').toLowerCase();
        const group  = item.dataset.group;
        const pItems = item.querySelectorAll('.permission-item');

        if (!q) {
            pItems.forEach(pi => { pi.style.display = ''; });
            item.style.display = '';
            closeAccordionDirect(group);
            return;
        }

        const groupMatch = gLabel.includes(q);
        let matchCount   = 0;

        pItems.forEach(pi => {
            const show = groupMatch || (pi.dataset.label || '').toLowerCase().includes(q);
            pi.style.display = show ? '' : 'none';
            if (show) matchCount++;
        });

        if (matchCount > 0) {
            item.style.display = '';
            visible++;
            openAccordionDirect(group);
        } else {
            item.style.display = 'none';
        }
    });

    const noRes = document.getElementById('no-results');
    if (noRes) noRes.classList.toggle('hidden', !q || visible > 0);
}

// ── User type change → auto-select permissions ───────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const groups = new Set(
        Array.from(document.querySelectorAll('.permission-checkbox')).map(cb => cb.dataset.group)
    );
    groups.forEach(g => updateGroupCount(g));

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
                groups.forEach(g => updateGroupCount(g));
            }
        });
    }
});
</script>