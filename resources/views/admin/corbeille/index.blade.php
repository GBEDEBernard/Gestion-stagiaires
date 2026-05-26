<x-app-layout>

    {{-- ═══════════════════════════════════════════
         HERO HEADER
    ════════════════════════════════════════════ --}}
    <div class="mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">

                {{-- Titre & description --}}
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="text-xs font-medium uppercase tracking-widest text-slate-400">Corbeille</span>
                    </div>
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Éléments supprimés</h1>
                    <p class="mt-1.5 max-w-md text-sm text-slate-500 dark:text-slate-400">
                        Retrouve ici tous les étudiants, le personnel, les stages, les services et badges supprimés.
                    </p>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:flex lg:shrink-0 lg:gap-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">
                            {{ $stagesTrash->count() + $etudiantsTrash->count() + $personnelsTrash->count() + $badgesTrash->count() + $servicesTrash->count() }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-teal-100 bg-teal-50 px-4 py-3 dark:border-teal-900 dark:bg-teal-950">
                        <p class="text-xs font-medium uppercase tracking-wider text-teal-500 dark:text-teal-400">Étudiants</p>
                        <p class="mt-1 text-2xl font-semibold text-teal-700 dark:text-teal-300">{{ $etudiantsTrash->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 dark:border-amber-900 dark:bg-amber-950">
                        <p class="text-xs font-medium uppercase tracking-wider text-amber-500 dark:text-amber-400">Personnel</p>
                        <p class="mt-1 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $personnelsTrash->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-violet-100 bg-violet-50 px-4 py-3 dark:border-violet-900 dark:bg-violet-950">
                        <p class="text-xs font-medium uppercase tracking-wider text-violet-500 dark:text-violet-400">Stages</p>
                        <p class="mt-1 text-2xl font-semibold text-violet-700 dark:text-violet-300">{{ $stagesTrash->count() }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         SECTIONS ACCORDÉON
    ════════════════════════════════════════════ --}}
    <div class="space-y-3">

        {{-- ── Étudiants ── --}}
        <x-trash-table
            :items="$etudiantsTrash"
            title="Étudiants supprimés"
            :columns="['Étudiant', 'Email', 'Supprimé le', 'Actions']"
            :restoreRoute="'etudiants.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'etudiants.forceDelete'"
            :fields="['personnel.full_name', 'personnel.email']"
            color="teal"
            icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0121 13c0 5.523-4.477 10-9 10S3 18.523 3 13a12.083 12.083 0 012.84-7.578L12 14z"/></svg>'
        />

        {{-- ── Personnel (corrigé : utilise $personnelsTrash et les bonnes routes) ── --}}
        <x-trash-table
            :items="$personnelsTrash"
            title="Personnel supprimé"
            :columns="['Nom', 'Email', 'Supprimé le', 'Actions']"
            :restoreRoute="'personnels.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'personnels.force-delete'"
            :fields="['full_name', 'email']"
            color="amber"
            icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        />

        {{-- ── Stages ── --}}
        <x-trash-table
            :items="$stagesTrash"
            title="Stages supprimés"
            :columns="['Étudiant', 'Période', 'Supprimé le', 'Actions']"
            :restoreRoute="'stages.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'stages.forceDelete'"
            :fields="['etudiant.personnel.full_name', 'date_debut', 'date_fin']"
            color="violet"
            icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>'
        />

        {{-- ── Services ── --}}
        <x-trash-table
            :items="$servicesTrash"
            title="Services supprimés"
            :columns="['Service', 'Supprimé le', 'Actions']"
            :restoreRoute="'services.restore'"
            :restoreMethod="'PATCH'"
            :forceDeleteRoute="'services.force-delete'"
            :fields="['nom']"
            color="amber"
            icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'
        />

        {{-- ── Badges ── --}}
        <x-trash-table
            :items="$badgesTrash"
            title="Badges supprimés"
            :columns="['Badge', 'Supprimé le', 'Actions']"
            :restoreRoute="'badges.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'badges.force-delete'"
            :fields="['badge']"
            color="indigo"
            icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'
        />

    </div>

    {{-- ═══════════════════════════════════════════
         BANNIÈRE AVERTISSEMENT
    ════════════════════════════════════════════ --}}
    <div class="mt-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 dark:border-amber-800/50 dark:bg-amber-900/20">
        <div class="mt-0.5 shrink-0 rounded-lg bg-amber-100 p-1.5 dark:bg-amber-900/40">
            <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Suppression définitive irréversible</p>
            <p class="mt-0.5 text-sm text-amber-700 dark:text-amber-400">
                Un élément force-delete ne pourra plus jamais être récupéré. Agis avec précaution.
            </p>
        </div>
    </div>

</x-app-layout>