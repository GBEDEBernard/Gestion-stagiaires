@php
    /** Rendu d'une ligne de critère, partagé entre la grille commune et celle du type. */
@endphp

<x-app-layout>
    @isset($retourStage)
        {{-- L'admin est venu d'une notation : le chemin du retour doit rester
             visible, y compris après un enregistrement qui recharge la page. --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 px-4 py-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50">
            <p class="text-sm text-blue-900 dark:text-blue-200">
                Vous ajustez la grille depuis la notation de
                <strong>{{ $retourStage->etudiant?->personnel?->full_name ?? 'ce stagiaire' }}</strong>.
                Les critères ajoutés apparaîtront dans sa grille.
            </p>
            <a href="{{ encrypted_route('stages.evaluation.edit', $retourStage) }}"
                class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Revenir à la notation
            </a>
        </div>
    @endisset

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Grilles d'évaluation</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
            Chaque type de stage a sa grille. Un critère rattaché à un type ne vaut que pour lui ;
            un critère commun s'applique à toutes les grilles, ce qui évite de ressaisir
            « Assiduité » quatre fois et de les voir diverger.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm text-emerald-800 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Composition de chaque grille --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        @forelse($overview as $row)
            <a href="{{ route('admin.evaluations.criteres.index', ['typestage' => $row['typestage']->id]) }}"
               class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-2 transition
                      {{ $selected?->id === $row['typestage']->id
                            ? 'border-blue-500'
                            : 'border-transparent hover:border-gray-200 dark:hover:border-gray-700' }}">
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $row['typestage']->libelle }}</h3>
                <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">
                    {{ $row['total'] }}
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">critère(s)</span>
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ $row['shared'] }} commun(s) + {{ $row['own'] }} propre(s)
                    &middot; coefficients {{ $row['weight'] }}
                </p>
                @if($row['total'] === 0)
                    <p class="mt-2 text-xs text-amber-600 dark:text-amber-400">Grille vide — aucune note possible</p>
                @elseif(!$row['has_auto'])
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">Sans critère automatique</p>
                @endif
            </a>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun type de stage n'est encore défini.</p>
        @endforelse
    </div>

    {{-- Formulaire d'ajout --}}
    <div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" class="mb-8">
        <button type="button" @click="open = !open"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Ajouter un critère
        </button>

        <div x-show="open" x-cloak class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.evaluations.criteres.store') }}" x-data="{ auto: false }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Intitulé</label>
                        <input type="text" name="label" value="{{ old('label') }}" required maxlength="120"
                            placeholder="Ex. : Respect des consignes"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">S'applique à</label>
                        <select name="typestage_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                            <option value="">Toutes les grilles (critère commun)</option>
                            @foreach($typestages as $t)
                                <option value="{{ $t->id }}" @selected(old('typestage_id', $selected?->id) == $t->id)>
                                    {{ $t->libelle }} uniquement
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Description <span class="font-normal text-gray-400">— aide à la notation, facultative</span>
                        </label>
                        <textarea name="description" rows="2" maxlength="1000"
                            placeholder="Ce que le notateur doit regarder pour attribuer sa note."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Coefficient
                        </label>
                        <input type="number" name="weight" value="{{ old('weight', 1) }}" min="1" max="20" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Tous à 1 = moyenne simple. Augmentez-le pour peser davantage dans la note finale.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Ordre d'affichage</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="999"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                    </div>

                    <div class="md:col-span-2 border-t border-gray-100 dark:border-gray-700 pt-5">
                        <label class="inline-flex items-center gap-3">
                            <input type="checkbox" name="is_auto" value="1" x-model="auto" @checked(old('is_auto'))
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                Note calculée automatiquement
                            </span>
                        </label>
                        <p class="mt-1 ml-7 text-xs text-gray-500 dark:text-gray-400">
                            La note sera pré-remplie à partir des données de présence. L'administrateur pourra
                            toujours la remplacer, avec justification.
                        </p>

                        <div x-show="auto" x-cloak class="mt-4 ml-7">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Base de calcul</label>
                            <select name="auto_source"
                                class="w-full md:w-2/3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                                @foreach(\App\Models\EvaluationCriterion::AUTO_SOURCES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('auto_source') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-sm">
                        Créer le critère
                    </button>
                    <button type="button" @click="open = false"
                        class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Critères communs --}}
    <x-evaluation-criteria-list
        title="Critères communs"
        subtitle="Présents dans toutes les grilles, quel que soit le type de stage."
        :criteria="$shared"
        :typestages="$typestages" />

    {{-- Critères propres au type sélectionné --}}
    @if($selected)
        <div class="mt-8">
            <x-evaluation-criteria-list
                :title="'Critères propres — ' . $selected->libelle"
                subtitle="Ne s'appliquent qu'à ce type de stage."
                :criteria="$criteria"
                :typestages="$typestages" />
        </div>
    @else
        <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
            Choisissez un type de stage ci-dessus pour voir et modifier ses critères propres.
        </p>
    @endif
</x-app-layout>
