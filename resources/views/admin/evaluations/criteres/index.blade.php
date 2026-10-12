<x-app-layout title="Grilles d'évaluation">
    {{-- PLEINE LARGEUR w-full SANS RESTRICTION --}}
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        @isset($retourStage)
            <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-[10px] bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 text-xs">
                <p class="text-blue-900 dark:text-blue-200">
                    Vous ajustez la grille depuis la notation de
                    <strong>{{ $retourStage->etudiant?->personnel?->full_name ?? 'ce stagiaire' }}</strong>.
                    Les critères ajoutés apparaîtront dans sa grille.
                </p>
                <a href="{{ encrypted_route('stages.evaluation.edit', $retourStage) }}"
                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Revenir à la notation
                </a>
            </div>
        @endisset

        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Grilles d'évaluation
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                Notation
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Chaque type de stage a sa grille. Les critères communs s'appliquent à tous les types.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Alertes Flash ── --}}
        @if(session('success'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-[10px] bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-medium">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="truncate">{{ session('success') }}</span>
                </div>
                <button type="button" @click="dismissed = true" class="p-1 rounded hover:bg-emerald-200/50 dark:hover:bg-emerald-800/50 text-emerald-700 dark:text-emerald-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if($errors->any())
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="p-3 rounded-[10px] bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-medium">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- ── 2. COMPOSITION DE CHAQUE GRILLE (CARTES SÉLECTIONNABLES) ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            @forelse($overview as $row)
                <a href="{{ route('admin.evaluations.criteres.index', ['typestage' => $row['typestage']->id]) }}"
                   class="rounded-[10px] p-3.5 sm:p-4 border transition block shadow-xs
                          {{ $selected?->id === $row['typestage']->id
                                ? 'bg-blue-50/50 dark:bg-blue-950/20 border-blue-500 dark:border-blue-500'
                                : 'bg-[#fffefa] dark:bg-[#161a22] border-[#dcdad2] dark:border-[#222630] hover:border-slate-400 dark:hover:border-slate-600' }}">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-[#f8fafc]">{{ $row['typestage']->libelle }}</h3>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-[#f6f5f0] dark:bg-[#181c24] text-slate-500 dark:text-slate-400">
                            {{ $row['typestage']->code }}
                        </span>
                    </div>
                    <p class="mt-2 text-xl font-bold text-slate-900 dark:text-[#f8fafc] tabular-nums">
                        {{ $row['total'] }}
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">critère(s)</span>
                    </p>
                    <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $row['shared'] }} commun(s) + {{ $row['own'] }} propre(s)
                        &middot; coef. {{ $row['weight'] }}
                    </p>
                    @if($row['total'] === 0)
                        <p class="mt-1.5 text-[11px] text-amber-600 dark:text-amber-400 font-medium">Grille vide — aucune note possible</p>
                    @elseif(!$row['has_auto'])
                        <p class="mt-1.5 text-[11px] text-slate-400">Sans calcul automatique</p>
                    @endif
                </a>
            @empty
                <p class="text-xs text-slate-500 dark:text-slate-400">Aucun type de stage n'est encore défini.</p>
            @endforelse
        </div>

        {{-- ── 3. FORMULAIRE D'AJOUT REPLIABLE ── --}}
        <div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" class="space-y-2.5">
            <button type="button" @click="open = !open"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Ajouter un critère</span>
            </button>

            <div x-show="open" x-cloak class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 shadow-xs">
                <form method="POST" action="{{ route('admin.evaluations.criteres.store') }}" x-data="{ auto: false }" class="space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Intitulé</label>
                            <input type="text" name="label" value="{{ old('label') }}" required maxlength="120"
                                placeholder="Ex. : Respect des consignes"
                                class="w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">S'applique à</label>
                            <select name="typestage_id"
                                class="w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Toutes les grilles (critère commun)</option>
                                @foreach($typestages as $t)
                                    <option value="{{ $t->id }}" @selected(old('typestage_id', $selected?->id) == $t->id)>
                                        {{ $t->libelle }} uniquement
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                Description <span class="font-normal text-slate-400">(aide à la notation, facultative)</span>
                            </label>
                            <textarea name="description" rows="2" maxlength="1000"
                                placeholder="Ce que le notateur doit observer pour attribuer sa note."
                                class="w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Coefficient</label>
                            <input type="number" name="weight" value="{{ old('weight', 1) }}" min="1" max="20" required
                                class="w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Ordre d'affichage</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="999"
                                class="w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2 border-t border-[#dcdad2]/60 dark:border-[#222630] pt-3">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="is_auto" value="1" x-model="auto" @checked(old('is_auto'))
                                    class="rounded border-[#dcdad2] dark:border-[#222630] text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    Note calculée automatiquement
                                </span>
                            </label>
                            <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                La note sera pré-remplie à partir des données de présence du stagiaire.
                            </p>

                            <div x-show="auto" x-cloak class="mt-2.5">
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Base de calcul</label>
                                <select name="auto_source"
                                    class="w-full md:w-2/3 px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    @foreach(\App\Models\EvaluationCriterion::AUTO_SOURCES as $key => $label)
                                        <option value="{{ $key }}" @selected(old('auto_source') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="submit"
                            class="px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-xs">
                            Créer le critère
                        </button>
                        <button type="button" @click="open = false"
                            class="px-3.5 py-2 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-slate-700 dark:text-slate-300 text-xs font-semibold transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── 4. LISTE DES CRITÈRES COMMUNS ── --}}
        <x-evaluation-criteria-list
            title="Critères communs"
            subtitle="Présents dans toutes les grilles, quel que soit le type de stage."
            :criteria="$shared"
            :typestages="$typestages" />

        {{-- ── 5. CRITÈRES PROPRES AU TYPE SÉLECTIONNÉ ── --}}
        @if($selected)
            <div class="mt-4">
                <x-evaluation-criteria-list
                    :title="'Critères propres — ' . $selected->libelle"
                    subtitle="Ne s'appliquent qu'à ce type de stage."
                    :criteria="$criteria"
                    :typestages="$typestages" />
            </div>
        @else
            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 italic">
                Sélectionnez un type de stage ci-dessus pour consulter et ajuster ses critères propres.
            </p>
        @endif

    </div>
</x-app-layout>
