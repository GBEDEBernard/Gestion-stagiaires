<x-app-layout>
<div class="max-w-2xl mx-auto px-4 sm:px-0">

    {{-- ── En-tête ── --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('employes.index') }}"
               class="p-2 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm" title="Retour">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Gestion du personnel</p>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Nouvel employé</h1>
            </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 ml-11">Renseignez les informations. Le compte de connexion pourra être généré ultérieurement.</p>
    </div>

    {{-- ── Erreurs ── --}}
    @if($errors->any())
    <div class="mb-5 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-300">
        <ul class="space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Formulaire ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('employes.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Identité --}}
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Identité</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        @error('prenom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Genre</label>
                        <select name="genre" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="">Sélectionner</option>
                            <option value="Masculin" {{ old('genre') == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                            <option value="Féminin"  {{ old('genre') == 'Féminin'  ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-700">

            {{-- Informations professionnelles --}}
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Informations professionnelles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                   
                <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Site <span class="text-red-500">*</span></label>
                        <select name="site_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="">Sélectionner un site</option>
                            @foreach($sites as $s)
                                <option value="{{ $s->id }}" {{ old('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('site_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                   <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Domaine <span class="text-red-500">*</span></label>
                        <select name="domaine_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="">Sélectionner un domaine</option>
                            @foreach($domaines as $d)
                                <option value="{{ $d->id }}" {{ old('domaine_id') == $d->id ? 'selected' : '' }}>{{ $d->nom }}</option>
                            @endforeach
                        </select>
                        @error('domaine_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Poste</label>
                        <input type="text" name="poste" value="{{ old('poste') }}"
                               placeholder="Ex : Développeur, Comptable…"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                            Matricule <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs font-normal text-gray-400">(généré automatiquement)</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="matricule"
                                   id="matricule"
                                   value="{{ old('matricule', $nextMatricule) }}"
                                   required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent pr-24">
                            {{-- Badge "Auto" --}}
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-medium pointer-events-none">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Auto
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Vous pouvez modifier ce matricule si besoin.</p>
                        @error('matricule') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('employes.index') }}"
                   class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>