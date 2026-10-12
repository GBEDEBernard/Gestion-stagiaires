<x-app-layout title="Gestion des Personnels">
    @php
        $totalPersonnels = \App\Models\Personnel::count();
        $avecCompteCount = \App\Models\Personnel::has('user')->count();
        $sansCompteCount = \App\Models\Personnel::doesntHave('user')->count();
        $stagiairesCount = \App\Models\Personnel::where('personnable_type', \App\Models\Etudiant::class)->count();
        $employesCount   = \App\Models\Personnel::where('personnable_type', \App\Models\Employe::class)->count();
    @endphp

    {{-- PLEINE LARGEUR w-full SANS RESTRICTION --}}
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Personnels
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Ressources Humaines
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Liste des personnels, stagiaires et employés enregistrés dans le système.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('personnels.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Nouveau personnel</span>
                    </a>
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
        @if(session('error'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-[10px] bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-medium">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="h-2 w-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="truncate">{{ session('error') }}</span>
                </div>
                <button type="button" @click="dismissed = true" class="p-1 rounded hover:bg-rose-200/50 dark:hover:bg-rose-800/50 text-rose-700 dark:text-rose-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- ── 2. CARTES KPIS (4 Indicateurs) ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
            {{-- Total personnels --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total effectif</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $totalPersonnels }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Avec compte --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Avec compte</span>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $avecCompteCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Sans compte --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sans compte</span>
                    <p class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $sansCompteCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            {{-- Répartition types --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Stagiaires / Employés</span>
                    <p class="text-xl sm:text-2xl font-bold text-sky-600 dark:text-sky-400 mt-1">{{ $stagiairesCount }} <span class="text-sm font-normal text-slate-400">/</span> {{ $employesCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── 3. FORMULAIRE DE FILTRES (AJAX PRÉSERVÉ) ── --}}
        <form id="filters-form" method="GET" action="{{ route('personnels.index') }}"
              class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 space-y-2.5 shadow-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Recherche</label>
                    <input id="personnelsSearch" type="search" name="search" value="{{ request('search') }}"
                           placeholder="Nom, email, téléphone..."
                           class="search-input w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 transition placeholder:text-slate-400" />
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Type de rôle</label>
                    <select name="type" class="filter-select w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="all" {{ request('type') === 'all' ? ' selected' : '' }}>Tous les types</option>
                        <option value="admin" {{ request('type') === 'admin' ? ' selected' : '' }}>Administrateurs</option>
                        <option value="etudiant" {{ request('type') === 'etudiant' ? ' selected' : '' }}>Stagiaires</option>
                        <option value="employe" {{ request('type') === 'employe' ? ' selected' : '' }}>Employés</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Statut du compte</label>
                    <select name="account" class="filter-select w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="all" {{ request('account') === 'all' ? ' selected' : '' }}>Tous les statuts</option>
                        <option value="with" {{ request('account') === 'with' ? ' selected' : '' }}>Avec compte actif</option>
                        <option value="without" {{ request('account') === 'without' ? ' selected' : '' }}>Sans compte actif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">École</label>
                    <select name="school" class="filter-select w-full px-3 py-1.5 rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">Toutes les écoles</option>
                        @foreach($schools as $school)
                        <option value="{{ $school }}" {{ request('school') === $school ? ' selected' : '' }}>{{ $school }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                <p id="result-count" class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <span class="font-bold text-slate-700 dark:text-slate-200">{{ $personnels->total() }}</span> personnel(s) trouvé(s)
                </p>
                @if(request()->hasAny(['search', 'type', 'account', 'school']))
                <a href="{{ route('personnels.index') }}"
                   class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Réinitialiser les filtres</span>
                </a>
                @endif
            </div>
        </form>

        {{-- ── 4. TABLEAU RESPONSIVE (CONTENEUR AJAX) ── --}}
        <div id="personnels-table-container" class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            
            {{-- Vue Desktop (> 768px) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#f6f5f0] dark:bg-[#131519] text-slate-600 dark:text-slate-300 font-semibold border-b border-[#dcdad2] dark:border-[#222630]">
                        <tr>
                            <th class="px-3.5 py-2.5 w-10 text-center">N°</th>
                            <th class="px-3.5 py-2.5">Identité</th>
                            <th class="px-3.5 py-2.5">Type</th>
                            <th class="px-3.5 py-2.5">Contact</th>
                            <th class="px-3.5 py-2.5">Affectation / Poste</th>
                            <th class="px-3.5 py-2.5">Inscription</th>
                            <th class="px-3.5 py-2.5">Début pointage</th>
                            <th class="px-3.5 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                        @forelse($personnels as $personnel)
                            @php
                                $initials = strtoupper(substr($personnel->prenom ?? '', 0, 1) . substr($personnel->nom ?? '', 0, 1)) ?: '??';
                            @endphp
                            <tr class="hover:bg-[#f6f5f0]/60 dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-3.5 py-2.5 text-center text-slate-400 font-mono text-[11px]">
                                    {{ $personnels->firstItem() + $loop->index }}
                                </td>
                                <td class="px-3.5 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-emerald-500/10 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ $initials }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 dark:text-[#f8fafc] truncate">
                                                {{ $personnel->prenom }} {{ $personnel->nom }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[14rem]">
                                                {{ $personnel->adresse ?? 'Adresse non renseignée' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3.5 py-2.5 whitespace-nowrap">
                                    @if($personnel->type_label === 'Admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            Admin
                                        </span>
                                    @elseif($personnel->type_label === 'Employé')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Employé
                                        </span>
                                    @elseif($personnel->type_label === 'Stagiaire')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                            Stagiaire
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $personnel->type_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5">
                                    <div class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-[13rem]">
                                        {{ $personnel->email }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $personnel->telephone ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-3.5 py-2.5">
                                    @if($personnel->personnable)
                                        @if($personnel->personnable_type === \App\Models\Etudiant::class)
                                            <div class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-[12rem]" title="{{ $personnel->personnable->ecole ?? '-' }}">
                                                <span class="text-slate-400">École :</span> {{ $personnel->personnable->ecole ?? '-' }}
                                            </div>
                                        @elseif($personnel->personnable_type === \App\Models\Employe::class)
                                            <div class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-[12rem]" title="{{ $personnel->personnable->poste ?? '-' }}">
                                                <span class="text-slate-400">Poste :</span> {{ $personnel->personnable->poste ?? '-' }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Non spécifié</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    {{ $personnel->date_inscription ? \Illuminate\Support\Carbon::parse($personnel->date_inscription)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-3.5 py-2.5 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    {{ $personnel->date_debut_pointage ? \Illuminate\Support\Carbon::parse($personnel->date_debut_pointage)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-3.5 py-2.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$personnel->user)
                                            <button type="button"
                                                    title="Générer compte"
                                                    aria-label="Générer compte"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-300 hover:bg-sky-100 dark:hover:bg-sky-900/60 border border-sky-200 dark:border-sky-800 transition"
                                                    onclick="openPasswordModal({{ $personnel->id }}, '{{ encrypted_route('personnels.generate-account', $personnel) }}', 'generate')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zm8 0v4m2-2h-4" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                    title="Renvoyer email"
                                                    aria-label="Renvoyer email"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 transition"
                                                    onclick="openPasswordModal({{ $personnel->id }}, '{{ encrypted_route('personnels.generate-account', $personnel) }}', 'resend')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        @endif

                                        <a href="{{ encrypted_route('personnels.show', $personnel) }}"
                                           title="Voir"
                                           aria-label="Voir"
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-slate-600 dark:text-slate-300 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        <a href="{{ encrypted_route('personnels.edit', $personnel) }}"
                                           title="Modifier"
                                           aria-label="Modifier"
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-amber-600 dark:text-amber-400 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ encrypted_route('personnels.destroy', $personnel) }}" method="POST" data-confirm-delete class="inline-flex">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    title="Supprimer"
                                                    aria-label="Supprimer"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        </div>
                                        <p class="text-xs font-medium">Aucun personnel trouvé selon vos critères.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vue Mobile (< 768px - Écran 375px sans débordement) --}}
            <div class="block md:hidden divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                @forelse($personnels as $personnel)
                    @php
                        $initials = strtoupper(substr($personnel->prenom ?? '', 0, 1) . substr($personnel->nom ?? '', 0, 1)) ?: '??';
                    @endphp
                    <div class="p-3.5 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-emerald-500/10 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-xs text-slate-900 dark:text-[#f8fafc] truncate">
                                        {{ $personnel->prenom }} {{ $personnel->nom }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        {{ $personnel->email }}
                                    </div>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-1.5">
                                @if($personnel->type_label === 'Admin')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        Admin
                                    </span>
                                @elseif($personnel->type_label === 'Employé')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        Employé
                                    </span>
                                @elseif($personnel->type_label === 'Stagiaire')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                        Stagiaire
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $personnel->type_label }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Détails supplémentaires --}}
                        <div class="rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2]/60 dark:border-[#222630] p-2.5 space-y-1 text-[11px]">
                            @if($personnel->telephone)
                                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400">Tél :</span>
                                    <span>{{ $personnel->telephone }}</span>
                                </div>
                            @endif
                            @if($personnel->personnable)
                                @if($personnel->personnable_type === \App\Models\Etudiant::class)
                                    <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                        <span class="text-slate-400">École :</span>
                                        <span class="truncate ml-2 max-w-[170px] text-right">{{ $personnel->personnable->ecole ?? '-' }}</span>
                                    </div>
                                @elseif($personnel->personnable_type === \App\Models\Employe::class)
                                    <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                        <span class="text-slate-400">Poste :</span>
                                        <span class="truncate ml-2 max-w-[170px] text-right">{{ $personnel->personnable->poste ?? '-' }}</span>
                                    </div>
                                @endif
                            @endif
                            @if($personnel->adresse)
                                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400">Adresse :</span>
                                    <span class="truncate ml-2 max-w-[170px] text-right">{{ $personnel->adresse }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                <span class="text-slate-400">Inscription :</span>
                                <span>{{ $personnel->date_inscription ? \Illuminate\Support\Carbon::parse($personnel->date_inscription)->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>

                        {{-- Rangée d'actions mobile --}}
                        <div class="flex items-center justify-between gap-2 pt-1">
                            @if(!$personnel->user)
                                <button type="button"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-sky-600 dark:text-sky-400 hover:underline"
                                        onclick="openPasswordModal({{ $personnel->id }}, '{{ encrypted_route('personnels.generate-account', $personnel) }}', 'generate')">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zm8 0v4m2-2h-4"/></svg>
                                    <span>Créer compte</span>
                                </button>
                            @else
                                <button type="button"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline"
                                        onclick="openPasswordModal({{ $personnel->id }}, '{{ encrypted_route('personnels.generate-account', $personnel) }}', 'resend')">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>Renvoyer accès</span>
                                </button>
                            @endif

                            <div class="flex items-center gap-1">
                                <a href="{{ encrypted_route('personnels.show', $personnel) }}"
                                   class="px-2 py-1 rounded bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-[11px] font-medium text-slate-700 dark:text-slate-300">
                                    Détails
                                </a>
                                <a href="{{ encrypted_route('personnels.edit', $personnel) }}"
                                   class="px-2 py-1 rounded bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-[11px] font-medium text-amber-600 dark:text-amber-400">
                                    Modifier
                                </a>
                                <form action="{{ encrypted_route('personnels.destroy', $personnel) }}" method="POST" data-confirm-delete class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-[11px] font-medium text-rose-600 dark:text-rose-400">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                        Aucun personnel trouvé selon vos critères.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="p-3 sm:p-4 border-t border-[#dcdad2] dark:border-[#222630]">
                {{ $personnels->links() }}
            </div>
        </div>

    </div>

    {{-- ── MODALE MOT DE PASSE PERSONNALISÉ (STYLISÉE MON STAGE) ── --}}
    <div id="passwordModal" class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-xs flex items-center justify-center z-50 p-4 {{ $errors->has('custom_password') ? '' : 'hidden' }}">
        <div class="bg-[#fffefa] dark:bg-[#161a22] rounded-[14px] border border-[#dcdad2] dark:border-[#222630] shadow-xl p-5 w-full max-w-md relative">
            <!-- Bouton fermeture -->
            <button type="button" onclick="closePasswordModal()" class="absolute top-3.5 right-3.5 p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-[#f6f5f0] dark:hover:bg-[#1f232d] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h2 id="passwordModalTitle" class="text-base font-bold text-slate-900 dark:text-[#f8fafc]">Générer un compte</h2>
            </div>
            <p id="passwordModalDescription" class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                Entrez un mot de passe temporaire pour ce personnel. Si laissé vide, un mot de passe aléatoire sera généré.
            </p>

            {{-- Affichage des erreurs de validation --}}
            @if($errors->any())
            <div class="mb-3 p-2.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 rounded-lg text-rose-800 dark:text-rose-300 text-xs">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="passwordForm" method="POST" action="{{ old('_action') ?? '' }}">
                @csrf
                <div class="mb-4">
                    <label for="customPassword" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Mot de passe temporaire (optionnel)</label>
                    <input type="password" id="customPassword" name="custom_password" value="{{ old('custom_password') }}"
                           placeholder="Laisser vide pour générer aléatoirement"
                           class="w-full px-3 py-2 text-xs rounded-lg bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('custom_password')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="closePasswordModal()"
                            class="flex-1 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] rounded-lg transition">
                        Annuler
                    </button>
                    <button type="submit" id="passwordSubmitBtn"
                            class="flex-1 px-3 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition shadow-xs">
                        Générer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(personnelId, actionUrl, mode = 'generate') {
            var resend = mode === 'resend';
            document.getElementById('passwordModalTitle').textContent = resend ? "Renvoyer l'email d'activation" : 'Générer un compte';
            document.getElementById('passwordModalDescription').textContent = resend
                ? "Un compte existe déjà pour ce personnel. Vous pouvez définir un nouveau mot de passe temporaire qui sera envoyé par email."
                : "Entrez un mot de passe temporaire pour ce personnel. Si laissé vide, un mot de passe aléatoire sera généré.";
            document.getElementById('passwordSubmitBtn').textContent = resend ? 'Renvoyer' : 'Générer';

            document.getElementById('passwordForm').action = actionUrl;
            document.getElementById('customPassword').value = '';
            const errorContainer = document.getElementById('passwordModal').querySelector('.bg-rose-50');
            if (errorContainer) errorContainer.remove();

            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            const errorContainer = document.getElementById('passwordModal').querySelector('.bg-rose-50');
            if (errorContainer) errorContainer.remove();
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePasswordModal();
            }
        });

        document.getElementById('passwordModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closePasswordModal();
            }
        });

        // ---- AJAX recherche dynamique ----
        document.addEventListener('DOMContentLoaded', function () {
            const filtersForm = document.getElementById('filters-form');
            const searchInput = filtersForm?.querySelector('.search-input');
            const filterSelects = filtersForm?.querySelectorAll('.filter-select');
            const container = document.getElementById('personnels-table-container');

            if (!filtersForm || !container) return;

            let debounceTimer;

            function updatePersonnelsTable() {
                const url = new URL(filtersForm.action);
                const formData = new FormData(filtersForm);
                for (let [key, value] of formData.entries()) {
                    if (value && value !== '') url.searchParams.set(key, value);
                    else url.searchParams.delete(key);
                }

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('personnels-table-container');
                    if (newContainer) {
                        container.innerHTML = newContainer.innerHTML;
                        const newCount = doc.querySelector('#result-count');
                        const oldCount = document.getElementById('result-count');
                        if (newCount && oldCount) oldCount.innerHTML = newCount.innerHTML;
                    } else {
                        const newTable = doc.querySelector('#personnels-table-container');
                        if (newTable) container.innerHTML = newTable.innerHTML;
                    }
                })
                .catch(err => console.error('Erreur AJAX:', err));
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => updatePersonnelsTable(), 400);
                });
            }

            if (filterSelects) {
                filterSelects.forEach(select => {
                    select.addEventListener('change', () => updatePersonnelsTable());
                });
            }

            filtersForm.addEventListener('submit', function(e) {
                e.preventDefault();
                updatePersonnelsTable();
            });
        });
    </script>
</x-app-layout>