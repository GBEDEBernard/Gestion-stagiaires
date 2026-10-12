<x-app-layout>
<div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Corbeille système</p>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">Éléments supprimés</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-[#94a3b8] mt-0.5">Retrouvez ici tous les stagiaires, le personnel, les stages et badges supprimés.</p>
        </div>
        <div>
            <a href="{{ route('stages.index') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] text-xs font-semibold text-slate-700 dark:text-[#f8fafc] hover:bg-slate-50 dark:hover:bg-[#1f232d] transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour aux stages
            </a>
        </div>
    </div>

    {{-- ── Cartes Stats ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-3.5 shadow-xs">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-[#94a3b8]">Total corbeille</p>
            <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-[#f8fafc]">
                {{ ($stagesTrash->count() ?? 0) + ($etudiantsTrash->count() ?? 0) + ($personnelsTrash->count() ?? 0) + ($badgesTrash->count() ?? 0) }}
            </p>
        </div>
        <div class="rounded-xl border border-teal-200/60 dark:border-teal-900/40 bg-teal-50/50 dark:bg-teal-950/20 p-3.5 shadow-xs">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-teal-600 dark:text-teal-400">Stagiaires</p>
            <p class="mt-1 text-2xl font-bold text-teal-700 dark:text-teal-300">{{ $etudiantsTrash->count() ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-amber-200/60 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/20 p-3.5 shadow-xs">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Personnel</p>
            <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $personnelsTrash->count() ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-violet-200/60 dark:border-violet-900/40 bg-violet-50/50 dark:bg-violet-950/20 p-3.5 shadow-xs">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-violet-600 dark:text-violet-400">Stages</p>
            <p class="mt-1 text-2xl font-bold text-violet-700 dark:text-violet-300">{{ $stagesTrash->count() ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-indigo-200/60 dark:border-indigo-900/40 bg-indigo-50/50 dark:bg-indigo-950/20 p-3.5 shadow-xs">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Badges</p>
            <p class="mt-1 text-2xl font-bold text-indigo-700 dark:text-indigo-300">{{ $badgesTrash->count() ?? 0 }}</p>
        </div>
    </div>

    {{-- ── Sections Accordéons ── --}}
    <div class="space-y-3" x-data="{
        openSections: {
            etudiants: {{ $etudiantsTrash->count() > 0 ? 'true' : 'false' }},
            personnels: {{ $personnelsTrash->count() > 0 ? 'true' : 'false' }},
            stages: {{ $stagesTrash->count() > 0 ? 'true' : 'false' }},
            badges: {{ $badgesTrash->count() > 0 ? 'true' : 'false' }}
        }
    }">

        {{-- ── Stagiaires ── --}}
        @if(isset($etudiantsTrash) && $etudiantsTrash->count() > 0)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
            <button @click="openSections.etudiants = !openSections.etudiants" class="w-full flex items-center justify-between p-3.5 bg-slate-50/70 dark:bg-[#131519] hover:bg-slate-100/70 dark:hover:bg-[#1a1d26] transition">
                <div class="flex items-center gap-2.5">
                    <svg class="h-4.5 w-4.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0121 13c0 5.523-4.477 10-9 10S3 18.523 3 13a12.083 12.083 0 012.84-7.578L12 14z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-[#f8fafc]">Stagiaires supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300">{{ $etudiantsTrash->count() }}</span>
                </div>
                <svg class="h-4 w-4 transform transition-transform duration-200 text-slate-400" :class="openSections.etudiants ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.etudiants" x-collapse class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 dark:bg-[#131519] border-y border-slate-200 dark:border-[#222630] text-slate-500 dark:text-[#94a3b8] uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2.5">Stagiaire</th>
                            <th class="px-4 py-2.5">Email</th>
                            <th class="px-4 py-2.5">Supprimé le</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#222630]">
                        @foreach($etudiantsTrash as $etudiant)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/50 transition">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-[#f8fafc]">{{ $etudiant->personnel->full_name ?? $etudiant->personnel->nom . ' ' . $etudiant->personnel->prenom }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-[#94a3b8]">{{ $etudiant->personnel->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-[#94a3b8]">{{ $etudiant->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                <form method="POST" action="{{ route('etudiants.restore', $etudiant->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('etudiants.forceDelete', $etudiant->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Supprimer déf.</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── Personnel ── --}}
        @if(isset($personnelsTrash) && $personnelsTrash->count() > 0)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
            <button @click="openSections.personnels = !openSections.personnels" class="w-full flex items-center justify-between p-3.5 bg-slate-50/70 dark:bg-[#131519] hover:bg-slate-100/70 dark:hover:bg-[#1a1d26] transition">
                <div class="flex items-center gap-2.5">
                    <svg class="h-4.5 w-4.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-[#f8fafc]">Personnel supprimé</h2>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">{{ $personnelsTrash->count() }}</span>
                </div>
                <svg class="h-4 w-4 transform transition-transform duration-200 text-slate-400" :class="openSections.personnels ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.personnels" x-collapse class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 dark:bg-[#131519] border-y border-slate-200 dark:border-[#222630] text-slate-500 dark:text-[#94a3b8] uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2.5">Nom</th>
                            <th class="px-4 py-2.5">Email</th>
                            <th class="px-4 py-2.5">Supprimé le</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#222630]">
                        @foreach($personnelsTrash as $personnel)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/50 transition">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-[#f8fafc]">{{ $personnel->full_name ?? $personnel->nom . ' ' . $personnel->prenom }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-[#94a3b8]">{{ $personnel->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-[#94a3b8]">{{ $personnel->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                <form method="POST" action="{{ route('personnels.restore', $personnel->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('personnels.force-delete', $personnel->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Supprimer déf.</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── Stages ── --}}
        @if(isset($stagesTrash) && $stagesTrash->count() > 0)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
            <button @click="openSections.stages = !openSections.stages" class="w-full flex items-center justify-between p-3.5 bg-slate-50/70 dark:bg-[#131519] hover:bg-slate-100/70 dark:hover:bg-[#1a1d26] transition">
                <div class="flex items-center gap-2.5">
                    <svg class="h-4.5 w-4.5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-[#f8fafc]">Stages supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300">{{ $stagesTrash->count() }}</span>
                </div>
                <svg class="h-4 w-4 transform transition-transform duration-200 text-slate-400" :class="openSections.stages ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.stages" x-collapse class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 dark:bg-[#131519] border-y border-slate-200 dark:border-[#222630] text-slate-500 dark:text-[#94a3b8] uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2.5">Stagiaire</th>
                            <th class="px-4 py-2.5">Thème</th>
                            <th class="px-4 py-2.5">Période</th>
                            <th class="px-4 py-2.5">Supprimé le</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#222630]">
                        @foreach($stagesTrash as $stage)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/50 transition">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-[#f8fafc]">
                                {{ $stage->etudiant->personnel->nom ?? '' }} {{ $stage->etudiant->personnel->prenom ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-[#94a3b8]">{{ $stage->theme ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-[#94a3b8]">
                                {{ $stage->date_debut?->format('d/m/Y') ?? '-' }} → {{ $stage->date_fin?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-[#94a3b8]">{{ $stage->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                <form method="POST" action="{{ route('stages.restore', $stage->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('stages.forceDelete', $stage->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Supprimer déf.</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── Badges ── --}}
        @if(isset($badgesTrash) && $badgesTrash->count() > 0)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
            <button @click="openSections.badges = !openSections.badges" class="w-full flex items-center justify-between p-3.5 bg-slate-50/70 dark:bg-[#131519] hover:bg-slate-100/70 dark:hover:bg-[#1a1d26] transition">
                <div class="flex items-center gap-2.5">
                    <svg class="h-4.5 w-4.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-[#f8fafc]">Badges supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $badgesTrash->count() }}</span>
                </div>
                <svg class="h-4 w-4 transform transition-transform duration-200 text-slate-400" :class="openSections.badges ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.badges" x-collapse class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 dark:bg-[#131519] border-y border-slate-200 dark:border-[#222630] text-slate-500 dark:text-[#94a3b8] uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2.5">Badge</th>
                            <th class="px-4 py-2.5">Supprimé le</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#222630]">
                        @foreach($badgesTrash as $badge)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/50 transition">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-[#f8fafc]">{{ $badge->badge }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-[#94a3b8]">{{ $badge->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                <form method="POST" action="{{ route('badges.restore', $badge->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('badges.force-delete', $badge->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-xs font-semibold shadow-xs">Supprimer déf.</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Message si corbeille vide --}}
        @if(($stagesTrash->count() ?? 0) === 0 && ($etudiantsTrash->count() ?? 0) === 0 && ($personnelsTrash->count() ?? 0) === 0 && ($badgesTrash->count() ?? 0) === 0)
        <div class="rounded-xl border border-dashed border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-10 text-center">
            <div class="mx-auto w-14 h-14 bg-slate-100 dark:bg-[#131519] rounded-full flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-900 dark:text-[#f8fafc] mb-1">Corbeille vide</h3>
            <p class="text-xs text-slate-500 dark:text-[#94a3b8]">Aucun élément supprimé pour le moment.</p>
        </div>
        @endif

    </div>

    {{-- ── Bannière Avertissement ── --}}
    <div class="flex items-start gap-2.5 rounded-xl border border-amber-200/70 dark:border-amber-900/40 bg-amber-50/60 dark:bg-amber-950/20 px-3.5 py-3">
        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="text-xs font-semibold text-amber-900 dark:text-amber-200">Suppression définitive irréversible</p>
            <p class="mt-0.5 text-[11px] text-amber-800 dark:text-amber-300">
                Un élément supprimé définitivement ne pourra plus jamais être récupéré. Agissez avec précaution.
            </p>
        </div>
    </div>

</div>
</x-app-layout>