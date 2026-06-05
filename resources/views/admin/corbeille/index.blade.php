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
                        <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="text-xs font-medium uppercase tracking-widest text-red-500">Corbeille système</span>
                    </div>
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Éléments supprimés</h1>
                    <p class="mt-1.5 max-w-md text-sm text-slate-500 dark:text-slate-400">
                        Retrouve ici tous les étudiants, le personnel, les stages, les services et badges supprimés.
                    </p>
                </div>

                {{-- Bouton retour --}}
                <div>
                    <a href="{{ route('stages.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         STATS CARDS
    ════════════════════════════════════════════ --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">
                {{ ($stagesTrash->count() ?? 0) + ($etudiantsTrash->count() ?? 0) + ($personnelsTrash->count() ?? 0) + ($badgesTrash->count() ?? 0) + ($servicesTrash->count() ?? 0) }}
            </p>
        </div>
        <div class="rounded-xl border border-teal-100 bg-teal-50 px-4 py-3 dark:border-teal-900 dark:bg-teal-950">
            <p class="text-xs font-medium uppercase tracking-wider text-teal-500 dark:text-teal-400">Étudiants</p>
            <p class="mt-1 text-2xl font-semibold text-teal-700 dark:text-teal-300">{{ $etudiantsTrash->count() ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 dark:border-amber-900 dark:bg-amber-950">
            <p class="text-xs font-medium uppercase tracking-wider text-amber-500 dark:text-amber-400">Personnel</p>
            <p class="mt-1 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $personnelsTrash->count() ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-violet-100 bg-violet-50 px-4 py-3 dark:border-violet-900 dark:bg-violet-950">
            <p class="text-xs font-medium uppercase tracking-wider text-violet-500 dark:text-violet-400">Stages</p>
            <p class="mt-1 text-2xl font-semibold text-violet-700 dark:text-violet-300">{{ $stagesTrash->count() ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950">
            <p class="text-xs font-medium uppercase tracking-wider text-indigo-500 dark:text-indigo-400">Badges/Services</p>
            <p class="mt-1 text-2xl font-semibold text-indigo-700 dark:text-indigo-300">{{ ($badgesTrash->count() ?? 0) + ($servicesTrash->count() ?? 0) }}</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         SECTIONS ACCORDÉON
    ════════════════════════════════════════════ --}}
    <div class="space-y-3" x-data="{
        openSections: {
            etudiants: {{ $etudiantsTrash->count() > 0 ? 'true' : 'false' }},
            personnels: {{ $personnelsTrash->count() > 0 ? 'true' : 'false' }},
            stages: {{ $stagesTrash->count() > 0 ? 'true' : 'false' }},
            services: {{ $servicesTrash->count() > 0 ? 'true' : 'false' }},
            badges: {{ $badgesTrash->count() > 0 ? 'true' : 'false' }}
        }
    }">

        {{-- ── Étudiants ── --}}
        @if(isset($etudiantsTrash) && $etudiantsTrash->count() > 0)
        <div class="rounded-xl border border-teal-200 bg-white dark:border-teal-800 dark:bg-slate-900 overflow-hidden">
            <button @click="openSections.etudiants = !openSections.etudiants" class="w-full flex items-center justify-between p-4 bg-teal-50 dark:bg-teal-950/30 hover:bg-teal-100 dark:hover:bg-teal-950/50 transition">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0121 13c0 5.523-4.477 10-9 10S3 18.523 3 13a12.083 12.083 0 012.84-7.578L12 14z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-teal-800 dark:text-teal-300">Étudiants supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-200 text-teal-800 dark:bg-teal-800 dark:text-teal-200">{{ $etudiantsTrash->count() }}</span>
                </div>
                <svg class="h-5 w-5 transform transition-transform duration-200" :class="openSections.etudiants ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.etudiants" x-collapse class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supprimé le</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($etudiantsTrash as $etudiant)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $etudiant->personnel->full_name ?? $etudiant->personnel->nom . ' ' . $etudiant->personnel->prenom }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $etudiant->personnel->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $etudiant->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <form method="POST" action="{{ route('etudiants.restore', $etudiant->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('etudiants.forceDelete', $etudiant->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">Supprimer déf.</button>
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
        <div class="rounded-xl border border-amber-200 bg-white dark:border-amber-800 dark:bg-slate-900 overflow-hidden">
            <button @click="openSections.personnels = !openSections.personnels" class="w-full flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-950/50 transition">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-amber-800 dark:text-amber-300">Personnel supprimé</h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-200 text-amber-800 dark:bg-amber-800 dark:text-amber-200">{{ $personnelsTrash->count() }}</span>
                </div>
                <svg class="h-5 w-5 transform transition-transform duration-200" :class="openSections.personnels ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.personnels" x-collapse class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supprimé le</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($personnelsTrash as $personnel)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $personnel->full_name ?? $personnel->nom . ' ' . $personnel->prenom }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $personnel->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $personnel->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <form method="POST" action="{{ route('personnels.restore', $personnel->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('personnels.force-delete', $personnel->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">Supprimer déf.</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── STAGES (Corbeille des stages intégrée) ── --}}
        @if(isset($stagesTrash) && $stagesTrash->count() > 0)
        <div class="rounded-xl border border-violet-200 bg-white dark:border-violet-800 dark:bg-slate-900 overflow-hidden">
            <button @click="openSections.stages = !openSections.stages" class="w-full flex items-center justify-between p-4 bg-violet-50 dark:bg-violet-950/30 hover:bg-violet-100 dark:hover:bg-violet-950/50 transition">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-violet-800 dark:text-violet-300">Stages supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-200 text-violet-800 dark:bg-violet-800 dark:text-violet-200">{{ $stagesTrash->count() }}</span>
                </div>
                <svg class="h-5 w-5 transform transition-transform duration-200" :class="openSections.stages ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.stages" x-collapse class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thème</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supprimé le</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($stagesTrash as $stage)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $stage->etudiant->personnel->nom ?? '' }} {{ $stage->etudiant->personnel->prenom ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $stage->theme ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $stage->date_debut?->format('d/m/Y') ?? '-' }} → {{ $stage->date_fin?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $stage->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <form method="POST" action="{{ route('stages.restore', $stage->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('stages.forceDelete', $stage->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">Supprimer déf.</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── Services ── --}}
        @if(isset($servicesTrash) && $servicesTrash->count() > 0)
        <div class="rounded-xl border border-amber-200 bg-white dark:border-amber-800 dark:bg-slate-900 overflow-hidden">
            <button @click="openSections.services = !openSections.services" class="w-full flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-950/50 transition">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-amber-800 dark:text-amber-300">Services supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-200 text-amber-800 dark:bg-amber-800 dark:text-amber-200">{{ $servicesTrash->count() }}</span>
                </div>
                <svg class="h-5 w-5 transform transition-transform duration-200" :class="openSections.services ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.services" x-collapse class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supprimé le</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($servicesTrash as $service)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $service->nom }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $service->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <form method="POST" action="{{ route('services.restore', $service->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('services.force-delete', $service->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">Supprimer déf.</button>
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
        <div class="rounded-xl border border-indigo-200 bg-white dark:border-indigo-800 dark:bg-slate-900 overflow-hidden">
            <button @click="openSections.badges = !openSections.badges" class="w-full flex items-center justify-between p-4 bg-indigo-50 dark:bg-indigo-950/30 hover:bg-indigo-100 dark:hover:bg-indigo-950/50 transition">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-indigo-800 dark:text-indigo-300">Badges supprimés</h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-200 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200">{{ $badgesTrash->count() }}</span>
                </div>
                <svg class="h-5 w-5 transform transition-transform duration-200" :class="openSections.badges ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openSections.badges" x-collapse class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badge</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supprimé le</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($badgesTrash as $badge)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $badge->badge }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $badge->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <form method="POST" action="{{ route('badges.restore', $badge->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs">Restaurer</button>
                                </form>
                                <form method="POST" action="{{ route('badges.force-delete', $badge->id) }}" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">Supprimer déf.</button>
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
        @if(($stagesTrash->count() ?? 0) === 0 && ($etudiantsTrash->count() ?? 0) === 0 && ($personnelsTrash->count() ?? 0) === 0 && ($badgesTrash->count() ?? 0) === 0 && ($servicesTrash->count() ?? 0) === 0)
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-slate-900 p-12 text-center">
            <div class="mx-auto w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Corbeille vide</h3>
            <p class="text-gray-500 dark:text-gray-400">Aucun élément supprimé pour le moment.</p>
        </div>
        @endif

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
                Un élément supprimé définitivement ne pourra plus jamais être récupéré. Agis avec précaution.
            </p>
        </div>
    </div>

</x-app-layout>