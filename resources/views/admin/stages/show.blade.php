<x-app-layout>
    {{-- Header + Breadcrumbs --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('stages.index') }}"
                class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Profil du stagiaire
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    <a href="{{ route('stages.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Stages</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</span>
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2.5">
            @if($stage->badge)
            <a href="{{ encrypted_route('admin.stages.badge.show', $stage) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-medium hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Badge
            </a>
            @endif

            <button onclick="document.getElementById('modalAttestation').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 font-medium hover:bg-violet-200 dark:hover:bg-violet-900/50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Attestation
            </button>

            <a href="{{ encrypted_route('stages.edit', $stage) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-medium hover:bg-amber-200 dark:hover:bg-amber-900/50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier
            </a>
        </div>
    </div>

    {{-- Modal Attestation --}}
    <div id="modalAttestation" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Générer une attestation
                </h2>
            </div>

            <form method="POST" action="{{ encrypted_route('stages.attestation.store', $stage) }}" class="p-6 space-y-4">
                @csrf

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Sélectionnez les signataires pour cette attestation :</p>

                @forelse($signataires as $signataire)
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <input type="checkbox"
                        name="signataires[{{ $signataire->id }}][selected]"
                        value="1"
                        class="signataire-checkbox w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                        id="sign_{{ $signataire->id }}"
                        data-ordre="ordre_{{ $signataire->id }}"
                        data-parordre="parordre_{{ $signataire->id }}">

                    <label for="sign_{{ $signataire->id }}" class="flex-1 cursor-pointer">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $signataire->nom }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">({{ $signataire->poste }})</span>
                    </label>

                    @if(!$signataire->isDG() && $signataire->peut_par_ordre)
                    <div class="flex items-center gap-2">
                        <input type="number"
                            name="signataires[{{ $signataire->id }}][ordre]"
                            min="1" max="2"
                            placeholder="Ordre"
                            class="w-14 px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-violet-200 dark:focus:ring-violet-800"
                            id="ordre_{{ $signataire->id }}"
                            disabled>

                        <label for="parordre_{{ $signataire->id }}" class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                            <input type="checkbox"
                                name="signataires[{{ $signataire->id }}][par_ordre]"
                                value="1"
                                id="parordre_{{ $signataire->id }}"
                                class="rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                                disabled>
                            <span>P.O</span>
                        </label>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Aucun signataire disponible
                </div>
                @endforelse

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button"
                        onclick="document.getElementById('modalAttestation').classList.add('hidden')"
                        class="px-5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-medium hover:from-violet-700 hover:to-purple-700 transition shadow-lg shadow-violet-600/20">
                        Valider
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Left Column - Profile & Current Stage --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Student Profile Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="relative h-32 bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600">
                    <div class="absolute -bottom-12 left-6">
                        <div class="h-24 w-24 rounded-2xl overflow-hidden ring-4 ring-white dark:ring-gray-800 shadow-xl bg-white flex items-center justify-center">
                            @if($stage->etudiant->genre === 'Masculin')
                            <svg class="h-14 w-14 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                            @else
                            <svg class="h-14 w-14 text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-16 pb-6 px-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</h2>
                            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $stage->etudiant->ecole ?? 'École non renseignée' }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                    {{ $stage->etudiant->genre === 'Masculin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' }}">
                                    {{ $stage->etudiant->genre ?? 'Non spécifié' }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                    @if($statutEnCours === 'En cours') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                    @elseif($statutEnCours === 'À venir') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400 @endif">
                                    ● {{ $statutEnCours }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="mailto:{{ $stage->etudiant->email }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Email">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </a>
                            @if($stage->etudiant->telephone)
                            <a href="tel:{{ $stage->etudiant->telephone }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Téléphone">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Current Stage Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Stage en cours
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Theme --}}
                        <div class="bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-xl p-4 border border-violet-100 dark:border-violet-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-violet-100 dark:bg-violet-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide">Thème</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $stage->theme ?? 'Non défini' }}</p>
                        </div>

                        {{-- Type de stage --}}
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Type</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $stage->typestage->libelle ?? 'Non défini' }}</p>
                        </div>

                        {{-- Badge --}}
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-emerald-100 dark:border-emerald-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Badge</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $stage->badge->badge ?? 'Non attribué' }}</p>
                        </div>

                        {{-- Période --}}
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-amber-100 dark:border-amber-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-amber-100 dark:bg-amber-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Période</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                {{ $stage->date_debut?->format('d/m/Y') ?? '—' }} <span class="text-gray-400">→</span> {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>

                        {{-- Service --}}
                        <div class="bg-gradient-to-br from-cyan-50 to-sky-50 dark:from-cyan-900/20 dark:to-sky-900/20 rounded-xl p-4 border border-cyan-100 dark:border-cyan-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-cyan-100 dark:bg-cyan-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wide">Service</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $stage->service->nom ?? 'Non défini' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stage->service->responsable ?? 'Responsable non défini' }}</p>
                        </div>

                        {{-- Jours de travail --}}
                        <div class="bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-rose-100 dark:border-rose-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-rose-100 dark:bg-rose-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wide">Jours</span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @forelse($stage->jours as $jour)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400">
                                    {{ substr($jour->libelle, 0, 3) }}
                                </span>
                                @empty
                                <span class="text-gray-500 dark:text-gray-400">—</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Historical Stages --}}
            @if($stagesTermines->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Historique des stages
                    </h3>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($stagesTermines as $stageHist)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $stageHist->theme ?? 'Stage' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $stageHist->typestage->libelle ?? 'Type non défini' }} • {{ $stageHist->service->nom ?? 'Service non défini' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $stageHist->date_debut?->format('d/m/Y') ?? '—' }} - {{ $stageHist->date_fin?->format('d/m/Y') ?? '—' }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    Terminé
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column - Stats & Attestations --}}
        <div class="space-y-6">

            {{-- Statistics Cards --}}
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 rounded-2xl shadow-xl overflow-hidden text-white">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Statistiques
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-3xl font-bold">{{ $nombreStages }}</p>
                            <p class="text-sm text-white/70">Stage(s)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-3xl font-bold">{{ $dureeTotale }}</p>
                            <p class="text-sm text-white/70">Jour(s)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attestations --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Attestations
                    </h3>
                </div>

                @if($attestations->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-80 overflow-y-auto">
                    @foreach($attestations as $attestation)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $attestation->stage->typestage->libelle ?? 'Stage' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attestation->stage->theme ?? '' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $attestation->reference }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $attestation->date_delivrance?->format('d/m/Y') ?? '—' }}
                            </p>
                            <a href="{{ encrypted_route('stages.attestation.show', $attestation->stage) }}"
                                class="text-xs text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 font-medium">
                                Voir →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Aucune attestation</p>
                </div>
                @endif
            </div>

            {{-- Contact Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contact
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $stage->etudiant->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Téléphone</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $stage->etudiant->telephone ?? 'Non défini' }}</p>
                        </div>
                    </div>

                    @if($stage->etudiant->adresse)
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Adresse</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $stage->etudiant->adresse }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Script gestion des signataires --}}
    <script>
        document.querySelectorAll('.signataire-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const ordreInputId = this.dataset.ordre;
                const parOrdreId = this.dataset.parordre;

                const ordreInput = document.getElementById(ordreInputId);
                const parOrdreInput = document.getElementById(parOrdreId);

                if (ordreInput) ordreInput.disabled = !this.checked;
                if (parOrdreInput) parOrdreInput.disabled = !this.checked;
            });
        });
    </script>
</x-app-layout>