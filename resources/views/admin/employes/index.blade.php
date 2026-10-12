<x-app-layout>
    @php
        $totalCount = \App\Models\Employe::count();
        $withAccountCount = \App\Models\Employe::whereHas('personnel.user')->count();
        $withoutAccountCount = \App\Models\Employe::whereDoesntHave('personnel.user')->count();
        $domainesCount = $domaines->count();
    @endphp

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }
        header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
            --ks-green: #15803d;
            --ks-green-bg: #dcfce7;
            --ks-red: #b91c1c;
            --ks-red-bg: #fee2e2;
            --ks-yellow: #b45309;
            --ks-yellow-bg: #fef3c7;
        }

        .ks-stat-card {
            border: 1px solid #dcdad2;
            border-radius: 10px;
            background-color: #fffefa;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .dark .ks-stat-card {
            background: #131519 !important;
            border-color: #222630 !important;
        }
        .ks-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
        }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.05), 0 8px 24px -16px rgba(36,31,27,.12);
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            padding: .5rem .9rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover {
            background: #162f73;
            border-color: #162f73;
        }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #dcdad2;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            padding: .5rem .9rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary {
            background: #131519;
            color: #f8fafc;
            border-color: #222630;
        }
        .ks-btn-secondary:hover {
            border-color: #1c3e96;
            color: #1c3e96;
        }
    </style>

    <div class="w-full max-w-6xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-8 space-y-4">

        {{-- ── En-tête Compact ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-[#dcdad2] dark:border-[#222630]">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-[#1c3e96] dark:bg-[#60a5fa]"></span>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                        Administration RH · Gestion du Personnel
                    </p>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc] mt-0.5">
                    Employés & Collaborateurs
                </h1>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <a href="{{ route('employes.trash') }}" class="ks-btn-secondary">
                    <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-[#a89f93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Corbeille</span>
                </a>
                <a href="{{ route('employes.create') }}" class="ks-btn-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Nouvel employé</span>
                </a>
            </div>
        </div>

        {{-- ── Mini-Bannières Flash ── --}}
        @if(session('success'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px] bg-[#dcfce7] dark:bg-[rgba(22,128,74,0.22)] border border-[#15803d]/30 text-[#15803d] dark:text-[#4ade80] text-xs font-medium">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="h-2 w-2 rounded-full bg-[#15803d] shrink-0"></span>
                    <span class="truncate">{{ session('success') }}</span>
                </div>
                <button type="button" @click="dismissed = true" class="p-1 rounded hover:bg-[#15803d]/15 text-[#15803d] dark:text-[#4ade80]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px] bg-[#fee2e2] dark:bg-[rgba(185,28,28,0.22)] border border-[#b91c1c]/30 text-[#b91c1c] dark:text-[#f87171] text-xs font-medium">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="h-2 w-2 rounded-full bg-[#b91c1c] shrink-0"></span>
                    <span class="truncate">{{ session('error') }}</span>
                </div>
                <button type="button" @click="dismissed = true" class="p-1 rounded hover:bg-[#b91c1c]/15 text-[#b91c1c] dark:text-[#f87171]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- ── 4 Cartes KPI Sparklines ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
            {{-- Total employés --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[96px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Total employés</span>
                    <div class="w-6 h-6 rounded-md bg-[#eef1fb] dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $totalCount }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] ml-1">inscrits</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Effectif actif</span>
                    <svg class="w-12 h-3.5 text-[#1c3e96] dark:text-[#60a5fa]" viewBox="0 0 48 14" fill="none"><path d="M1 10 C 12 12, 24 4, 36 6 C 42 7, 46 2, 47 2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
                </div>
            </div>

            {{-- Comptes actifs --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[96px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Comptes actifs</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/20 dark:text-[#4ade80]">
                        {{ $totalCount > 0 ? round(($withAccountCount / $totalCount) * 100) : 0 }}%
                    </span>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $withAccountCount }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] ml-1">avec accès</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Connectables</span>
                    <svg class="w-12 h-3.5 text-[#15803d] dark:text-[#4ade80]" viewBox="0 0 48 14" fill="none"><path d="M1 12 C 10 9, 20 13, 30 5 C 38 1, 44 4, 47 3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
                </div>
            </div>

            {{-- Sans compte --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[96px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Sans compte</span>
                    <div class="w-6 h-6 rounded-md bg-[#fef3c7] dark:bg-[#b45309]/20 text-[#b45309] dark:text-[#fbbf24] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $withoutAccountCount }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] ml-1">en attente</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Génération requise</span>
                    <svg class="w-12 h-3.5 text-[#b45309] dark:text-[#fbbf24]" viewBox="0 0 48 14" fill="none"><path d="M1 7 C 14 3, 26 11, 38 4 C 42 2, 45 4, 47 3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
                </div>
            </div>

            {{-- Domaines couverts --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[96px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Domaines</span>
                    <div class="w-6 h-6 rounded-md bg-[#faf5ff] dark:bg-[#7e22ce]/20 text-[#7e22ce] dark:text-[#c084fc] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $domainesCount }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] ml-1">secteurs</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Organisation</span>
                    <svg class="w-12 h-3.5 text-[#7e22ce] dark:text-[#c084fc]" viewBox="0 0 48 14" fill="none"><path d="M1 5 C 10 9, 20 2, 30 11 C 38 12, 44 8, 47 7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
                </div>
            </div>
        </div>

        {{-- ── Barre de Filtres Compacte ── --}}
        <form method="GET" action="{{ route('employes.index') }}" class="ks-sec p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Recherche</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, prénom, email..."
                           class="w-full h-8 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]" />
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Domaine</label>
                    <select name="domaine_id" class="w-full h-8 px-2 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                        <option value="">Tous les domaines</option>
                        @foreach($domaines as $domaine)
                            <option value="{{ $domaine->id }}" {{ request('domaine_id') == $domaine->id ? 'selected' : '' }}>{{ $domaine->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Site</label>
                    <select name="site_id" class="w-full h-8 px-2 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                        <option value="">Tous les sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Statut du compte</label>
                    <select name="account_status" class="w-full h-8 px-2 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                        <option value="all" {{ request('account_status') === 'all' ? 'selected' : '' }}>Tous les comptes</option>
                        <option value="with" {{ request('account_status') === 'with' ? 'selected' : '' }}>Avec compte actif</option>
                        <option value="without" {{ request('account_status') === 'without' ? 'selected' : '' }}>Sans compte</option>
                    </select>
                </div>
            </div>

            <div class="mt-2.5 pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630] flex items-center justify-between gap-2">
                <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                    {{ $employes->total() }} résultat(s) trouvé(s)
                </span>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('employes.index') }}" class="ks-btn-secondary !py-1 !px-2.5 !text-xs">
                        Réinitialiser
                    </a>
                    <button type="submit" class="ks-btn-primary !py-1 !px-3 !text-xs">
                        Filtrer
                    </button>
                </div>
            </div>
        </form>

        {{-- ── Grand Tableau Compact d'Employés ── --}}
        <div class="ks-sec overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] text-[10.5px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                            <th class="px-3 py-2.5 w-10 text-center">N°</th>
                            <th class="px-3.5 py-2.5">Collaborateur</th>
                            <th class="px-3 py-2.5">Email</th>
                            <th class="px-3 py-2.5">Domaine / Site</th>
                            <th class="px-3 py-2.5">Poste</th>
                            <th class="px-3 py-2.5">Compte</th>
                            <th class="px-3.5 py-2.5 text-right w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                        @forelse($employes as $emp)
                            @php
                                $p = $emp->personnel;
                                $nomComplet = $p ? ($p->prenom . ' ' . $p->nom) : ('Employé #' . $emp->id);
                                $initiales = $p ? strtoupper(mb_substr($p->prenom, 0, 1) . mb_substr($p->nom, 0, 1)) : 'EM';
                            @endphp

                            {{-- Ligne orpheline --}}
                            @if(!$p)
                                <tr class="bg-[#fee2e2]/40 dark:bg-[#b91c1c]/10">
                                    <td colspan="7" class="px-3.5 py-2 text-xs text-[#b91c1c] dark:text-[#f87171]">
                                        ⚠ Employé #{{ $emp->id }} — fiche personnel manquante.
                                        <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline ml-2">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="underline text-[11px] font-semibold hover:text-[#b91c1c]">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                @continue
                            @endif

                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24]/60 transition-colors">
                                <td class="px-3 py-2 text-center font-mono text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                                    {{ $loop->iteration + ($employes->currentPage() - 1) * $employes->perPage() }}
                                </td>

                                <td class="px-3.5 py-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-r from-violet-600/20 to-blue-600/20 border border-[#1c3e96]/30 text-[#1c3e96] dark:text-[#60a5fa] font-bold text-[10.5px] flex items-center justify-center shrink-0">
                                            {{ $initiales }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('employes.show', $emp) }}" class="font-bold text-[#241f1b] dark:text-[#f8fafc] hover:text-[#1c3e96] dark:hover:text-[#60a5fa] truncate block">
                                                {{ $p->prenom }} {{ $p->nom }}
                                            </a>
                                            <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">
                                                {{ $p->telephone ?: 'Sans tél' }} · {{ $p->genre ?: 'Non spécifié' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-3 py-2 text-[#6e6862] dark:text-[#a89f93] font-mono text-[11px]">
                                    {{ $p->email }}
                                </td>

                                <td class="px-3 py-2">
                                    <div class="text-[#241f1b] dark:text-[#f8fafc] font-semibold text-[11px]">
                                        {{ $emp->domaine->nom ?? '—' }}
                                    </div>
                                    <div class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">
                                        {{ $emp->site->name ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-3 py-2 text-[#241f1b] dark:text-[#f8fafc]">
                                    {{ $emp->poste ?? 'Collaborateur' }}
                                </td>

                                <td class="px-3 py-2">
                                    @if($p->user)
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/20 dark:text-[#4ade80]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#15803d]"></span>
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
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#1c3e96] dark:text-[#60a5fa] hover:bg-[#eef1fb] transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            Créer compte
                                        </button>
                                    @endif
                                </td>

                                <td class="px-3.5 py-2 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('employes.show', $emp) }}" class="p-1 rounded text-[#6e6862] hover:text-[#1c3e96] dark:text-[#a89f93] dark:hover:text-[#60a5fa] hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition" title="Consulter la fiche">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
                                        <a href="{{ route('employes.edit', $emp) }}" class="p-1 rounded text-[#6e6862] hover:text-[#b45309] dark:text-[#a89f93] dark:hover:text-[#fbbf24] hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition" title="Modifier">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" onsubmit="return confirm('Mettre cet employé à la corbeille ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-[#6e6862] hover:text-[#b91c1c] dark:text-[#a89f93] dark:hover:text-[#f87171] hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition" title="Mettre à la corbeille">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2 text-[#6e6862] dark:text-[#a89f93]">
                                        <div class="w-10 h-10 rounded-full bg-[#f1f0ed] dark:bg-[#181c24] flex items-center justify-center">
                                            <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/></svg>
                                        </div>
                                        <p class="text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">Aucun employé correspondant</p>
                                        <p class="text-[11px]">Modifiez vos critères de recherche ou ajoutez un nouvel employé.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employes->hasPages())
                <div class="p-2.5 border-t border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                    {{ $employes->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ── Modale Génération Mot de passe (Style Mon-Stage) ── --}}
    <div id="passwordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 bg-black/60 backdrop-blur-xs">
        <div class="bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-[14px] shadow-2xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    </div>
                    <h2 id="passwordModalTitle" class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">Générer un compte</h2>
                </div>
                <button type="button" onclick="closePasswordModal()" class="p-1 rounded-md text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-4">
                <p id="passwordModalDescription" class="text-xs text-[#6e6862] dark:text-[#a89f93] mb-3 leading-relaxed">
                    Définissez un mot de passe temporaire pour ce collaborateur. Si laissé vide, un code sécurisé sera généré et envoyé automatiquement par email.
                </p>

                <form id="passwordForm" method="POST" action="" class="space-y-3">
                    @csrf
                    <div>
                        <label for="customPassword" class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">
                            Mot de passe personnalisé <span class="text-[10px] lowercase font-normal">(optionnel)</span>
                        </label>
                        <input type="password" id="customPassword" name="custom_password"
                               placeholder="Laisser vide pour génération automatique"
                               class="w-full h-8 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                        <button type="button" onclick="closePasswordModal()" class="ks-btn-secondary flex-1 !py-1.5 !text-xs">
                            Annuler
                        </button>
                        <button type="submit" id="passwordSubmitBtn" class="ks-btn-primary flex-1 !py-1.5 !text-xs">
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