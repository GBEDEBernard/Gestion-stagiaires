<x-app-layout>
    <div class="max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ encrypted_route('stages.show', $stage) }}"
                    class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Fiche de poste</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $stage->etudiant->personnel->nom ?? '' }} {{ $stage->etudiant->personnel->prenom ?? '' }}
                        — Renseignez une fois les informations, la fiche sera complète.
                    </p>
                </div>
            </div>

            <a href="{{ encrypted_route('stages.fiche-poste.preview', $stage) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Voir la fiche
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ encrypted_route('stages.fiche-poste.update', $stage) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Informations du poste --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                    <h2 class="font-bold text-gray-900 dark:text-white">Informations du poste</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ces informations apparaissent dans le tableau de la fiche de poste.</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="intitule_poste" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Intitulé du poste</label>
                            <input type="text" name="intitule_poste" id="intitule_poste"
                                value="{{ old('intitule_poste', $stage->intitule_poste ?? \App\Models\Stage::DEFAULT_INTITULE_POSTE) }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white">
                            @error('intitule_poste')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="typestage_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de stage</label>
                            <select name="typestage_id" id="typestage_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white">
                                <option value="">Sélectionner</option>
                                @foreach($typestages as $type)
                                <option value="{{ $type->id }}" {{ old('typestage_id', $stage->typestage_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->libelle }}
                                </option>
                                @endforeach
                            </select>
                            @error('typestage_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="ecole" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Établissement d'origine</label>
                            <input type="text" name="ecole" id="ecole"
                                value="{{ old('ecole', $stage->etudiant->ecole ?? '') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white"
                                placeholder="Nom de l'université / institut">
                            @error('ecole')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="filiere" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filière</label>
                            <input type="text" name="filiere" id="filiere"
                                value="{{ old('filiere', $stage->filiere ?? '') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white"
                                placeholder="Ex : Informatique / Génie Logiciel">
                            @error('filiere')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="niveau_etude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Niveau d'étude</label>
                            <input type="text" name="niveau_etude" id="niveau_etude"
                                value="{{ old('niveau_etude', $stage->niveau_etude ?? '') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white"
                                placeholder="Ex : Licence 3 / Master">
                            @error('niveau_etude')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="domaine_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service / Département d'accueil</label>
                            <select name="domaine_id" id="domaine_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white">
                                <option value="">Sélectionner une Direction/Service</option>
                                @foreach($domaines as $domaine)
                                <option value="{{ $domaine->id }}" {{ old('domaine_id', $stage->domaine_id) == $domaine->id ? 'selected' : '' }}>
                                    {{ $domaine->nom }}
                                </option>
                                @endforeach
                            </select>
                            @error('domaine_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="tuteur_academique" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tuteur académique</label>
                            <input type="text" name="tuteur_academique" id="tuteur_academique"
                                value="{{ old('tuteur_academique', $stage->tuteur_academique ?? '') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white"
                                placeholder="Nom Prénom – Établissement">
                            @error('tuteur_academique')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thème du stage</label>
                            <input type="text" name="theme" id="theme"
                                value="{{ old('theme', $stage->theme ?? '') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white"
                                placeholder="Thème du stage">
                            @error('theme')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="indemnite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Indemnité de stage</label>
                            <select name="indemnite" id="indemnite"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white">
                                <option value="Non rémunéré" {{ old('indemnite', $stage->indemnite ?? \App\Models\Stage::DEFAULT_INDEMNITE) === 'Non rémunéré' ? 'selected' : '' }}>Non rémunéré</option>
                                <option value="Rémunéré" {{ old('indemnite', $stage->indemnite ?? '') === 'Rémunéré' ? 'selected' : '' }}>Rémunéré</option>
                            </select>
                            @error('indemnite')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Informations automatiques (issues du stage) --}}
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700 p-4 space-y-3">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Récupérés automatiquement du stage</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Lieu de travail</p>
                                @if($stage->site)
                                <p class="font-medium text-gray-900 dark:text-white">{{ $stage->site->name }} – {{ $stage->site->city ?? '' }}</p>
                                @else
                                <p class="text-gray-400">Non défini</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Durée du stage</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    Du {{ $stage->date_debut?->format('d/m/Y') ?? '—' }} au {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Maître de stage</p>
                                @if($stage->supervisor)
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $stage->supervisor->personnel->nom ?? '' }} {{ $stage->supervisor->personnel->prenom ?? '' }}
                                    @if($stage->supervisor->fonction) – {{ $stage->supervisor->fonction }}@endif
                                </p>
                                @else
                                <p class="text-gray-400">Non défini</p>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Pour modifier ces éléments, modifiez le stage lui-même.</p>
                    </div>
                </div>
            </div>

            {{-- Livrables --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                    <h2 class="font-bold text-gray-900 dark:text-white">Livrable attendu</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Cochez ce que le stagiaire doit livrer durant son stage ; les éléments choisis seront affichés dans la fiche.</p>
                </div>
                <div class="p-6 space-y-3">
                    @php $selectedLivrables = old('livrables', $stage->livrables ?? $defaultLivrables); @endphp
                    @foreach(\App\Models\Stage::LIVERABLES as $livrable)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition">
                        <input type="checkbox" name="livrables[]" value="{{ $livrable }}"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ in_array($livrable, $selectedLivrables ?? []) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $livrable }}</span>
                    </label>
                    @endforeach
                    @error('livrables')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ encrypted_route('stages.show', $stage) }}"
                    class="px-5 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Enregistrer et voir la fiche
                </button>
            </div>
        </form>
    </div>
</x-app-layout>