<x-app-layout title="Gestion des Employés">
    @php
        $totalCount = \App\Models\Employe::count();
        $withAccountCount = \App\Models\Employe::whereHas('personnel.user')->count();
        $withoutAccountCount = \App\Models\Employe::whereDoesntHave('personnel.user')->count();
        $domainesCount = $domaines->count();
    @endphp

    {{-- OCCUPE TOUT L'ESPACE DISPONIBLE SANS MAX-W RESTRICTIF --}}
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Employés & Collaborateurs
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Administration RH
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Gestion du personnel, attribution des comptes d'accès et affectation par pôle et site.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <a href="{{ route('employes.trash') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-xs font-semibold text-slate-700 dark:text-slate-200 transition shadow-xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Corbeille</span>
                    </a>
                    <a href="{{ route('employes.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Nouvel employé</span>
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
            {{-- Total employés --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total employés</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $totalCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Comptes actifs --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Comptes actifs</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            {{ $totalCount > 0 ? round(($withAccountCount / $totalCount) * 100) : 0 }}%
                        </span>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $withAccountCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>

            {{-- Sans compte --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sans compte</span>
                    <p class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $withoutAccountCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            {{-- Domaines couverts --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Domaines</span>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $domainesCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── 3. BARRE DE FILTRES ── --}}
        <form method="GET" action="{{ route('employes.index') }}"
              class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 shadow-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Recherche</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, prénom, email..."
                           class="w-full h-8 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#1c3e96]" />
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Domaine</label>
                    <select name="domaine_id" class="w-full h-8 px-2 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-[#1c3e96]">
                        <option value="">Tous les domaines</option>
                        @foreach($domaines as $domaine)
                            <option value="{{ $domaine->id }}" {{ request('domaine_id') == $domaine->id ? 'selected' : '' }}>{{ $domaine->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Site</label>
                    <select name="site_id" class="w-full h-8 px-2 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-[#1c3e96]">
                        <option value="">Tous les sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Statut du compte</label>
                    <select name="account_status" class="w-full h-8 px-2 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-[#1c3e96]">
                        <option value="all" {{ request('account_status') === 'all' ? 'selected' : '' }}>Tous les comptes</option>
                        <option value="with" {{ request('account_status') === 'with' ? 'selected' : '' }}>Avec compte actif</option>
                        <option value="without" {{ request('account_status') === 'without' ? 'selected' : '' }}>Sans compte</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 pt-2.5 border-t border-[#dcdad2] dark:border-[#222630] flex items-center justify-between gap-2">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    <strong class="text-slate-800 dark:text-slate-200">{{ $employes->total() }}</strong> employé(s) trouvé(s)
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('employes.index') }}"
                       class="px-3 py-1.5 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-xs font-semibold text-slate-700 dark:text-slate-200 transition">
                        Réinitialiser
                    </a>
                    <button type="submit"
                            class="px-3.5 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold transition shadow-xs">
                        Filtrer
                    </button>
                </div>
            </div>
        </form>

        {{-- ── 4. TABLEAU DES EMPLOYÉS (PLEINE LARGEUR) ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            
            {{-- Vue Desktop md+ --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-[#f6f5f0] dark:bg-[#131519] border-b border-[#dcdad2] dark:border-[#222630] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="px-3.5 py-3 w-10 text-center">N°</th>
                            <th class="px-4 py-3">Collaborateur</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Domaine & Site</th>
                            <th class="px-4 py-3">Poste</th>
                            <th class="px-4 py-3">Compte</th>
                            <th class="px-4 py-3 text-right w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2] dark:divide-[#222630]">
                        @forelse($employes as $emp)
                            @php
                                $p = $emp->personnel;
                                $nomComplet = $p ? ($p->prenom . ' ' . $p->nom) : ('Employé #' . $emp->id);
                                $initiales = $p ? strtoupper(mb_substr($p->prenom, 0, 1) . mb_substr($p->nom, 0, 1)) : 'EM';
                            @endphp

                            {{-- Ligne orpheline --}}
                            @if(!$p)
                                <tr class="bg-rose-50/50 dark:bg-rose-950/20">
                                    <td colspan="7" class="px-4 py-2.5 text-xs text-rose-700 dark:text-rose-400">
                                        ⚠️ Employé #{{ $emp->id }} — fiche personnel manquante.
                                        <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline ml-2">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="underline text-[11px] font-semibold hover:text-rose-900">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                @continue
                            @endif

                            <tr class="hover:bg-[#faf9f5] dark:hover:bg-[#1a1e27] transition">
                                <td class="px-3.5 py-3 text-center font-mono text-[11px] text-slate-400">
                                    {{ $loop->iteration + ($employes->currentPage() - 1) * $employes->perPage() }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $initiales }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('employes.show', $emp) }}"
                                               class="font-bold text-slate-900 dark:text-[#f8fafc] hover:text-[#1c3e96] dark:hover:text-[#60a5fa] truncate block">
                                                {{ $p->prenom }} {{ $p->nom }}
                                            </a>
                                            <span class="text-[11px] text-slate-400">
                                                {{ $p->telephone ?: 'Sans tél' }} · {{ $p->genre ?: 'Non spécifié' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-mono text-[11px]">
                                    {{ $p->email }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-slate-900 dark:text-[#f8fafc] font-semibold text-xs">
                                        {{ $emp->domaine->nom ?? '—' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $emp->site->name ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                    {{ $emp->poste ?? 'Collaborateur' }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($p->user)
                                        <div class="space-y-0.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Actif
                                            </span>
                                            <button type="button"
                                                    onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}', 'resend')"
                                                    class="block text-[10px] text-[#1c3e96] dark:text-[#60a5fa] hover:underline">
                                                Renvoyer accès
                                            </button>
                                        </div>
                                    @else
                                        <button type="button"
                                                onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}', 'generate')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold border border-[#dcdad2] dark:border-[#222630] bg-[#f6f5f0] dark:bg-[#1f232d] hover:bg-[#eeece2] dark:hover:bg-[#282d3a] text-[#1c3e96] dark:text-[#60a5fa] transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            Créer compte
                                        </button>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('employes.show', $emp) }}"
                                           class="p-1.5 rounded-md text-slate-500 hover:text-[#1c3e96] dark:text-slate-400 dark:hover:text-[#60a5fa] hover:bg-[#f6f5f0] dark:hover:bg-[#1f232d] transition"
                                           title="Consulter la fiche">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
                                        <a href="{{ route('employes.edit', $emp) }}"
                                           class="p-1.5 rounded-md text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 hover:bg-[#f6f5f0] dark:hover:bg-[#1f232d] transition"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" onsubmit="return confirm('Mettre cet employé à la corbeille ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 rounded-md text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-[#f6f5f0] dark:hover:bg-[#1f232d] transition"
                                                    title="Mettre à la corbeille">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Aucun employé correspondant trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vue Mobile (< md / 375px) --}}
            <div class="block md:hidden divide-y divide-[#dcdad2] dark:divide-[#222630]">
                @forelse($employes as $emp)
                    @php
                        $p = $emp->personnel;
                        $nomComplet = $p ? ($p->prenom . ' ' . $p->nom) : ('Employé #' . $emp->id);
                        $initiales = $p ? strtoupper(mb_substr($p->prenom, 0, 1) . mb_substr($p->nom, 0, 1)) : 'EM';
                    @endphp

                    @if(!$p)
                        <div class="p-3 text-xs text-rose-700 dark:text-rose-400 bg-rose-50/50 dark:bg-rose-950/20">
                            ⚠️ Employé #{{ $emp->id }} — fiche personnel manquante.
                        </div>
                        @continue
                    @endif

                    <div class="p-3.5 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $initiales }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('employes.show', $emp) }}" class="font-bold text-xs text-slate-900 dark:text-[#f8fafc] truncate block">
                                        {{ $p->prenom }} {{ $p->nom }}
                                    </a>
                                    <p class="text-[11px] text-slate-400 font-mono truncate">{{ $p->email }}</p>
                                </div>
                            </div>
                            @if($p->user)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800 shrink-0">
                                    Sans compte
                                </span>
                            @endif
                        </div>

                        <div class="text-[11px] text-slate-600 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-2.5 space-y-0.5">
                            <div><span class="text-slate-400 font-medium">Poste :</span> {{ $emp->poste ?? 'Collaborateur' }}</div>
                            <div><span class="text-slate-400 font-medium">Domaine :</span> {{ $emp->domaine->nom ?? '—' }} ({{ $emp->site->name ?? '—' }})</div>
                            @if($p->telephone)
                                <div><span class="text-slate-400 font-medium">Tél :</span> {{ $p->telephone }}</div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1">
                            @if(!$p->user)
                                <button type="button"
                                        onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}', 'generate')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-[#1c3e96] text-white shadow-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Créer compte
                                </button>
                            @else
                                <button type="button"
                                        onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}', 'resend')"
                                        class="text-xs text-[#1c3e96] dark:text-[#60a5fa] hover:underline font-semibold">
                                    Renvoyer accès
                                </button>
                            @endif

                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('employes.show', $emp) }}"
                                   class="p-1.5 rounded-md bg-[#f6f5f0] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-slate-700 dark:text-slate-200 text-xs font-medium">
                                    Détails
                                </a>
                                <a href="{{ route('employes.edit', $emp) }}"
                                   class="p-1.5 rounded-md bg-[#f6f5f0] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-amber-600 dark:text-amber-400 text-xs font-medium">
                                    Modifier
                                </a>
                                <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" onsubmit="return confirm('Mettre à la corbeille ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 rounded-md bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-xs font-medium">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                        Aucun employé correspondant trouvé.
                    </div>
                @endforelse
            </div>

            @if($employes->hasPages())
                <div class="px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#faf9f5] dark:bg-[#181c24]">
                    {{ $employes->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ── Modale Génération Mot de passe (Style Mon-Stage) ── --}}
    <div id="passwordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 bg-slate-950/70 backdrop-blur-xs">
        <div class="bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] rounded-[12px] shadow-2xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f5] dark:bg-[#181c24] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    </div>
                    <h2 id="passwordModalTitle" class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">Générer un compte</h2>
                </div>
                <button type="button" onclick="closePasswordModal()" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-4">
                <p id="passwordModalDescription" class="text-xs text-slate-600 dark:text-slate-400 mb-3 leading-relaxed">
                    Définissez un mot de passe temporaire pour ce collaborateur. Si laissé vide, un code sécurisé sera généré et envoyé automatiquement par email.
                </p>

                <form id="passwordForm" method="POST" action="" class="space-y-3">
                    @csrf
                    <div>
                        <label for="customPassword" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                            Mot de passe personnalisé <span class="text-[10px] lowercase font-normal text-slate-400">(optionnel)</span>
                        </label>
                        <input type="password" id="customPassword" name="custom_password"
                               placeholder="Laisser vide pour génération automatique"
                               class="w-full h-8 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#1c3e96]">
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-[#dcdad2] dark:border-[#222630]">
                        <button type="button" onclick="closePasswordModal()"
                                class="flex-1 py-1.5 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-xs font-semibold text-slate-700 dark:text-slate-200 transition">
                            Annuler
                        </button>
                        <button type="submit" id="passwordSubmitBtn"
                                class="flex-1 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold transition shadow-xs">
                            Générer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openPasswordModal(empId, actionUrl, mode = 'generate') {
        const modal = document.getElementById('passwordModal');
        modal.classList.remove('hidden');
        document.getElementById('passwordForm').action = actionUrl;
        document.getElementById('customPassword').value = '';

        const isResend = (mode === 'resend');
        document.getElementById('passwordModalTitle').textContent = isResend ? "Renvoyer l'accès" : 'Générer un compte';
        document.getElementById('passwordModalDescription').textContent = isResend
            ? "Un compte existe déjà pour cet employé. Vous pouvez définir un nouveau mot de passe temporaire qui lui sera notifié par email."
            : "Entrez un mot de passe temporaire pour cet employé. Si laissé vide, un mot de passe aléatoire sera généré et envoyé par email.";
        document.getElementById('passwordSubmitBtn').textContent = isResend ? 'Renvoyer' : 'Générer';
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePasswordModal(); });
    document.getElementById('passwordModal')?.addEventListener('click', function(e) { if (e.target === this) closePasswordModal(); });
    </script>
</x-app-layout>