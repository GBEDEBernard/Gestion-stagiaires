<x-app-layout title="Gestion des permissions">

    <style>
        /* Fond warm beige façon Mon-Stage */
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
            --ks-green: #15803d;
            --ks-green-bg: #ecfdf5;
            --ks-red: #b91c1c;
            --ks-red-bg: #fee2e2;
            --ks-amber: #b45309;
            --ks-amber-bg: #fef3c7;
        }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6e6862;
        }
        .dark .ks-label { color: #a89f93; }

        /* Boutons Mon-Stage */
        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 600;
            padding: .45rem .9rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 500;
            padding: .45rem .9rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }

        /* Cartes KPI compactes */
        .ks-stat-card {
            border: 1px solid #dcdad2;
            border-radius: 10px;
            background-color: #fffefa;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .dark .ks-stat-card {
            background: #131519 !important;
            border: 1px solid #222630 !important;
        }
        .ks-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
        }

        .ks-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: none;
            background-color: var(--ks-stat-icon-bg, #eef1fb);
            color: var(--ks-stat-icon-color, #1c3e96);
        }
        .ks-stat-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

        .ks-stat-card--blue  { --ks-stat-icon-bg: #eef1fb; --ks-stat-icon-color: #1c3e96; }
        .ks-stat-card--amber { --ks-stat-icon-bg: #fffbeb; --ks-stat-icon-color: #b45309; }
        .ks-stat-card--green { --ks-stat-icon-bg: #ecfdf5; --ks-stat-icon-color: #15803d; }
        .ks-stat-card--red   { --ks-stat-icon-bg: #fef2f2; --ks-stat-icon-color: #b91c1c; }

        .dark .ks-stat-card--blue .ks-stat-icon  { background-color: rgba(28, 62, 150, 0.25) !important; color: #93c5fd !important; }
        .dark .ks-stat-card--amber .ks-stat-icon { background-color: rgba(180, 83, 9, 0.25) !important; color: #fcd34d !important; }
        .dark .ks-stat-card--green .ks-stat-icon { background-color: rgba(22, 101, 52, 0.25) !important; color: #86efac !important; }
        .dark .ks-stat-card--red .ks-stat-icon   { background-color: rgba(185, 28, 28, 0.25) !important; color: #fca5a5 !important; }

        .ks-stat-change {
            border-radius: 4px;
            font-size: .72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .45rem;
        }
        .ks-stat-change--positive { color: #166534; background-color: #dcfce7; }
        .dark .ks-stat-change--positive { color: #86efac; background-color: rgba(22,101,52,.35); }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fee2e2; }
        .dark .ks-stat-change--negative { color: #fca5a5; background-color: rgba(185,28,28,.35); }
        .ks-stat-change--warning  { color: #92400e; background-color: #fef3c7; }
        .dark .ks-stat-change--warning  { color: #fcd34d; background-color: rgba(180,83,9,.35); }
        .ks-stat-change--neutral  { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--neutral  { color: #cbd5e1; background-color: rgba(71,85,105,.35); }

        /* Conteneurs de section */
        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        .ks-input {
            width: 100%;
            border-radius: 6px;
            border: 1px solid #dcdad2;
            background-color: #ffffff;
            color: #241f1b;
            font-size: .8rem;
            padding: .35rem .65rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .ks-input:focus {
            outline: none;
            border-color: #1c3e96;
        }
        .dark .ks-input {
            border-color: #222630;
            background-color: #15181f;
            color: #ffffff;
        }
        .dark .ks-input:focus {
            border-color: #3b82f6;
        }
    </style>

    <div class="w-full space-y-3 pb-6"
         x-data="adminPermissionsApp()"
         x-init="init()">

        {{-- ── 1. EN-TÊTE COMPACT ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white">
                    Gestion des permissions
                </h1>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Vue d'ensemble et décision sur les demandes soumises par les stagiaires.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] font-medium text-[#6e6862] dark:text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Direct / Temps réel
                </span>
            </div>
        </div>

        {{-- ── 2. CARTES KPI COMPACTES ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Total --}}
            <div class="ks-stat-card ks-stat-card--blue border shadow-none p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Total</span>
                        <div class="my-0.5 font-bold text-[#1c3e96] dark:text-blue-400 ks-num text-2xl leading-tight">{{ $stats['total'] }}</div>
                        <span class="badge ks-stat-change ks-stat-change--neutral">Toutes périodes</span>
                    </div>
                    <span class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
            </div>

            {{-- En attente --}}
            <div class="ks-stat-card ks-stat-card--amber border shadow-none p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">En attente</span>
                        <div class="my-0.5 font-bold text-[#b45309] dark:text-amber-400 ks-num text-2xl leading-tight">{{ $stats['pending'] }}</div>
                        <span class="badge ks-stat-change ks-stat-change--warning">À décider</span>
                    </div>
                    <span class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
            </div>

            {{-- Approuvées --}}
            <div class="ks-stat-card ks-stat-card--green border shadow-none p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Approuvées</span>
                        <div class="my-0.5 font-bold text-[#15803d] dark:text-emerald-400 ks-num text-2xl leading-tight">{{ $stats['approved'] }}</div>
                        <span class="badge ks-stat-change ks-stat-change--positive">Validées</span>
                    </div>
                    <span class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
            </div>

            {{-- Refusées --}}
            <div class="ks-stat-card ks-stat-card--red border shadow-none p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Refusées</span>
                        <div class="my-0.5 font-bold text-[#b91c1c] dark:text-rose-400 ks-num text-2xl leading-tight">{{ $stats['rejected'] }}</div>
                        <span class="badge ks-stat-change ks-stat-change--negative">Rejetées</span>
                    </div>
                    <span class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
            </div>
        </div>

        {{-- ── 3. FILTRES AVANCÉS COMPACTS ── --}}
        <div class="ks-sec p-3">
            <form method="GET" action="{{ route('admin.permissions.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5 items-end">
                    {{-- Stagiaire --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Stagiaire</label>
                        <select name="user_id" class="ks-input !h-8 text-xs">
                            <option value="">Tous les stagiaires</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Statut</label>
                        <select name="status" class="ks-input !h-8 text-xs">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvé</option>
                            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Refusé</option>
                            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Annulé</option>
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Type</label>
                        <select name="type_id" class="ks-input !h-8 text-xs">
                            <option value="">Tous les types</option>
                            @foreach($types as $t)
                            <option value="{{ $t->id }}" {{ request('type_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Du --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Du</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="ks-input !h-8 text-xs">
                    </div>

                    {{-- Au --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Au</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="ks-input !h-8 text-xs">
                    </div>

                    {{-- Actions Filtrer / Reset --}}
                    <div class="flex items-center gap-1.5">
                        <button type="submit" class="ks-btn-primary ks-btn-xs !h-8 w-full cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m1.7-4.3a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                            </svg>
                            Filtrer
                        </button>
                        @if(request()->hasAny(['status','type_id','user_id','from','to']))
                        <a href="{{ route('admin.permissions.index') }}" class="ks-btn-secondary ks-btn-xs !h-8 shrink-0 cursor-pointer">
                            Effacer
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ── 4. TABLEAU DES DEMANDES COMPACT ── --}}
        <div class="ks-sec overflow-hidden">
            {{-- En-tête du tableau --}}
            <div class="flex items-center justify-between px-3.5 py-2.5 border-b border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#241f1b] dark:text-white">
                        {{ $requests->total() }} demande{{ $requests->total() > 1 ? 's' : '' }} répertoriée(s)
                    </span>
                    @if(request()->hasAny(['status','type_id','user_id','from','to']))
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300">
                        Filtres actifs
                    </span>
                    @endif
                </div>
                <span class="text-[11px] text-[#6e6862] dark:text-slate-400 font-mono">
                    Page {{ $requests->currentPage() }} sur {{ $requests->lastPage() }}
                </span>
            </div>

            {{-- Table Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6]/70 dark:bg-[#181c24]/70 text-[#6e6862] dark:text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="px-3.5 py-2.5">Stagiaire</th>
                            <th class="px-3 py-2.5">Type</th>
                            <th class="px-3 py-2.5">Motif / Résumé</th>
                            <th class="px-3 py-2.5">Destinataires</th>
                            <th class="px-3 py-2.5">Date</th>
                            <th class="px-3 py-2.5 text-center">Statut</th>
                            <th class="px-3.5 py-2.5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        @forelse($requests as $req)
                        @php
                        $statusBadges = [
                            'pending'   => 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
                            'approved'  => 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                            'rejected'  => 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
                            'cancelled' => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700',
                        ];
                        $badgeClass = $statusBadges[$req->status] ?? 'bg-slate-100 text-slate-700 border-slate-300';
                        $canDecide = $req->isRecipient(auth()->user());
                        @endphp
                        <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                            {{-- Stagiaire --}}
                            <td class="px-3.5 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($req->user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#241f1b] dark:text-white truncate max-w-[140px]">{{ $req->user->name }}</p>
                                        <p class="text-[10.5px] text-[#6e6862] dark:text-slate-400 truncate max-w-[140px]">{{ $req->user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-[#dcdad2] dark:border-[#222630] font-semibold text-[11px] text-[#241f1b] dark:text-slate-200 bg-[#faf9f6] dark:bg-[#181c24]">
                                    {{ $req->type->name }}
                                </span>
                            </td>

                            {{-- Détails --}}
                            <td class="px-3 py-2.5 max-w-[200px]">
                                <p class="text-[11.5px] text-[#6e6862] dark:text-slate-300 truncate">{{ $req->fieldSummary() ?: '—' }}</p>
                                @if($req->note)
                                <p class="text-[10px] text-[#6e6862] dark:text-slate-400 italic truncate mt-0.5">💬 {{ $req->note }}</p>
                                @endif
                            </td>

                            {{-- Destinataires --}}
                            <td class="px-3 py-2.5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($req->recipients as $r)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] px-1.5 py-0.5 rounded border font-medium
                                        @if($r->status==='validated') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-800/40
                                        @elseif($r->status==='rejected') bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-300 dark:border-rose-800/40
                                        @elseif($r->status==='skipped') bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-700
                                        @else bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/40
                                        @endif"
                                        title="{{ $r->signataire->poste }}">
                                        {{ Str::limit($r->signataire->nom, 12) }}
                                        @if($r->status==='validated') <span class="font-bold">✓</span>
                                        @elseif($r->status==='rejected') <span class="font-bold">✕</span>
                                        @elseif($r->status==='skipped') <span>–</span>
                                        @else <span>···</span>
                                        @endif
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <p class="font-medium text-[#241f1b] dark:text-white font-mono text-[11px]">{{ $req->created_at->format('d/m/Y') }}</p>
                                <p class="text-[10px] text-[#6e6862] dark:text-slate-400 font-mono">{{ $req->created_at->format('H:i') }}</p>
                            </td>

                            {{-- Statut --}}
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold border {{ $badgeClass }}">
                                    {{ $req->statusLabel() }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" @click="viewDetail({{ $req->id }})"
                                            title="Consulter les détails"
                                            class="ks-btn-secondary ks-btn-xs !h-7 !px-2 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir
                                    </button>

                                    @if($req->isPending() && $canDecide)
                                    <button type="button" @click="openDecide({{ $req->id }}, '{{ addslashes($req->user->name) }}', '{{ addslashes($req->type->name) }}')"
                                            class="ks-btn-primary ks-btn-xs !h-7 !px-2.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Décider
                                    </button>
                                    @elseif($req->isPending())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700 cursor-not-allowed"
                                          title="Consultation seule : seul le signataire désigné peut décider.">
                                        Lecture seule
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-xs text-[#6e6862] dark:text-slate-400">
                                Aucune demande ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                @forelse($requests as $req)
                @php
                $statusBadges = [
                    'pending'   => 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
                    'approved'  => 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                    'rejected'  => 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
                    'cancelled' => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700',
                ];
                $badgeClass = $statusBadges[$req->status] ?? 'bg-slate-100 text-slate-700 border-slate-300';
                $canDecide = $req->isRecipient(auth()->user());
                @endphp
                <div class="p-3 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($req->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#241f1b] dark:text-white">{{ $req->user->name }}</p>
                                <p class="text-[10.5px] text-[#6e6862] dark:text-slate-400 font-mono">{{ $req->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded text-[10.5px] font-semibold border {{ $badgeClass }}">
                            {{ $req->statusLabel() }}
                        </span>
                    </div>

                    <div class="text-xs">
                        <span class="font-semibold text-[#241f1b] dark:text-white">{{ $req->type->name }} :</span>
                        <span class="text-[#6e6862] dark:text-slate-400">{{ $req->fieldSummary() ?: '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <div class="flex flex-wrap gap-1">
                            @foreach($req->recipients->take(2) as $r)
                            <span class="text-[10px] px-1.5 py-0.2 rounded border font-medium {{ $r->status==='validated' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ Str::limit($r->signataire->nom, 10) }}
                            </span>
                            @endforeach
                            @if($req->recipients->count() > 2)
                            <span class="text-[10px] text-[#6e6862]">+{{ $req->recipients->count()-2 }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="viewDetail({{ $req->id }})" class="ks-btn-secondary ks-btn-xs !h-6 !px-2">
                                Voir
                            </button>
                            @if($req->isPending() && $canDecide)
                            <button type="button" @click="openDecide({{ $req->id }}, '{{ addslashes($req->user->name) }}', '{{ addslashes($req->type->name) }}')"
                                    class="ks-btn-primary ks-btn-xs !h-6 !px-2">
                                Décider
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-[#6e6862] dark:text-slate-400">
                    Aucune demande trouvée.
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($requests->hasPages())
            <div class="p-3 border-t border-[#ecebe3] dark:border-[#222630] flex justify-center bg-[#faf9f6]/40 dark:bg-[#181c24]/40">
                {{ $requests->links() }}
            </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL DÉTAIL (Admin Mon-Stage Style) ── --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="detailOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="detailOpen=false"></div>

            <div class="relative bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-center justify-between px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                    <div>
                        <h3 class="text-sm font-bold text-[#241f1b] dark:text-white" x-text="detail.type || 'Détails de la demande'"></h3>
                        <p class="text-[11px] text-[#6e6862] dark:text-slate-400" x-text="detail.user || ''"></p>
                    </div>
                    <button @click="detailOpen=false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 space-y-3.5" x-show="!detailLoading">
                    {{-- Stagiaire & Statut --}}
                    <div class="flex items-center justify-between pb-2 border-b border-[#ecebe3] dark:border-[#222630]">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0"
                                x-text="(detail.user || '??').substring(0,2).toUpperCase()"></div>
                            <div>
                                <p class="text-xs font-bold text-[#241f1b] dark:text-white" x-text="detail.user"></p>
                                <p class="text-[10.5px] text-[#6e6862] dark:text-slate-400" x-text="detail.user_email"></p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded border"
                            :class="{
                                'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300': detail.status==='pending',
                                'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300': detail.status==='approved',
                                'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300': detail.status==='rejected',
                                'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300': detail.status==='cancelled'
                            }" x-text="detail.status_label"></span>
                    </div>

                    {{-- Notice lecture seule si non-destinataire --}}
                    <div x-show="detail.status==='pending' && detail.can_decide === false"
                        class="p-2.5 rounded-lg bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300 flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Consultation en <strong>lecture seule</strong> : seul le destinataire désigné pour cette demande peut l'approuver ou la refuser.</span>
                    </div>

                    {{-- Informations de la demande --}}
                    <div class="rounded-lg border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
                        <div class="px-3 py-1.5 bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630]">
                            <p class="text-[10px] font-bold text-[#6e6862] dark:text-slate-400 uppercase tracking-wider">Champs de la demande</p>
                        </div>
                        <div class="divide-y divide-[#ecebe3] dark:divide-[#222630] p-1">
                            <template x-for="field in (detail.fields_config||[])" :key="field.key">
                                <div x-show="detail.fields_data && detail.fields_data[field.key]"
                                     class="flex justify-between items-center px-2 py-1.5 text-xs">
                                    <span class="text-[#6e6862] dark:text-slate-400" x-text="field.label"></span>
                                    <span class="font-bold text-[#241f1b] dark:text-white" x-text="detail.fields_data ? detail.fields_data[field.key] : ''"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div x-show="detail.note" class="bg-[#eef1fb] dark:bg-blue-950/30 border border-[#d8e0f5] dark:border-blue-900/40 rounded-lg p-2.5">
                        <p class="text-[10px] font-bold text-[#1c3e96] dark:text-blue-300 uppercase tracking-wider mb-0.5">Note du stagiaire</p>
                        <p class="text-xs text-[#241f1b] dark:text-slate-200" x-text="detail.note"></p>
                    </div>

                    {{-- Destinataires & validation --}}
                    <div>
                        <p class="text-[10px] font-bold text-[#6e6862] dark:text-slate-400 uppercase tracking-wider mb-1.5">Signataires assignés</p>
                        <div class="space-y-1.5">
                            <template x-for="r in (detail.recipients||[])" :key="r.nom">
                                <div class="flex items-center gap-2 p-2 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                                    <div class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0"
                                         x-text="r.nom.substring(0,2).toUpperCase()"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-[#241f1b] dark:text-white truncate" x-text="r.nom"></p>
                                        <p class="text-[10.5px] text-[#6e6862] dark:text-slate-400 truncate" x-text="r.poste"></p>
                                    </div>
                                    <span class="text-[10.5px] px-2 py-0.5 rounded font-semibold border"
                                        :class="{
                                            'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300': r.state==='validated',
                                            'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300': r.state==='rejected',
                                            'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300': r.state==='skipped',
                                            'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300': r.state==='pending'
                                        }" x-text="r.status"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Décision rendue --}}
                    <div x-show="detail.decided_at" class="bg-[#faf9f6] dark:bg-[#181c24] rounded-lg p-2.5 border border-[#dcdad2] dark:border-[#222630]">
                        <p class="text-[10px] font-bold text-[#6e6862] dark:text-slate-400 uppercase tracking-wider mb-0.5">Décision enregistrée</p>
                        <p class="text-xs text-[#241f1b] dark:text-white">
                            Par <strong x-text="detail.decider"></strong> — <span class="text-[#6e6862] dark:text-slate-400" x-text="detail.decided_at"></span>
                        </p>
                        <p x-show="detail.decision_comment" class="text-xs text-[#6e6862] dark:text-slate-400 italic mt-0.5" x-text="'« ' + detail.decision_comment + ' »'"></p>
                    </div>

                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400 text-right font-mono" x-text="'Soumis le ' + detail.created_at"></p>
                </div>

                <div x-show="detailLoading" class="py-12 text-center">
                    <div class="w-6 h-6 mx-auto border-2 border-[#1c3e96] border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-2">Chargement en cours...</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL DÉCISION (Mon-Stage Style) ── --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="decideOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="decideOpen=false"></div>

            <div class="relative bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-xl shadow-2xl w-full max-w-md overflow-hidden"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-[#241f1b] dark:text-white">Rendre une décision</h2>
                        <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">Cette action notifie immédiatement le stagiaire.</p>
                    </div>
                    <button @click="decideOpen=false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 space-y-3.5">
                    {{-- Rappel stagiaire & type --}}
                    <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0"
                            x-text="(decideTarget.user || 'XX').substring(0,2).toUpperCase()"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#241f1b] dark:text-white truncate" x-text="decideTarget.user"></p>
                            <p class="text-[11px] text-[#6e6862] dark:text-slate-400 truncate" x-text="decideTarget.type"></p>
                        </div>
                    </div>

                    {{-- Choix Approuver / Refuser --}}
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="decideChoice='approve'"
                            :class="decideChoice==='approve'
                                ? 'border-[#15803d] bg-[#ecfdf5] dark:bg-emerald-950/50 text-[#15803d] dark:text-emerald-300 ring-2 ring-emerald-500/25 font-bold'
                                : 'border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-slate-300 hover:border-emerald-500'"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-lg border text-xs font-semibold transition cursor-pointer">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                  :class="decideChoice==='approve' ? 'bg-emerald-600' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'">✓</span>
                            <span>Approuver</span>
                        </button>

                        <button type="button" @click="decideChoice='reject'"
                            :class="decideChoice==='reject'
                                ? 'border-[#b91c1c] bg-[#fef2f2] dark:bg-rose-950/50 text-[#b91c1c] dark:text-rose-300 ring-2 ring-rose-500/25 font-bold'
                                : 'border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-slate-300 hover:border-rose-500'"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-lg border text-xs font-semibold transition cursor-pointer">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                  :class="decideChoice==='reject' ? 'bg-rose-600' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'">✕</span>
                            <span>Refuser</span>
                        </button>
                    </div>

                    {{-- Commentaire --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#6e6862] dark:text-slate-400 mb-1">
                            Motif ou commentaire
                            <span class="font-normal opacity-75" x-text="decideChoice==='reject' ? '(recommandé en cas de refus)' : '(optionnel)'"></span>
                        </label>
                        <textarea x-model="decideComment" rows="3"
                            :placeholder="decideChoice==='reject' ? 'Veuillez préciser la raison du refus...' : 'Note ou consigne pour le stagiaire...'"
                            class="ks-input resize-none text-xs"></textarea>
                    </div>

                    {{-- Note informationnelle --}}
                    <div class="p-2.5 rounded-lg bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300 flex items-start gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>La première décision validée est définitive. Tous les destinataires seront informés.</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                    <button type="button" @click="decideOpen=false" class="ks-btn-secondary ks-btn-xs cursor-pointer">
                        Annuler
                    </button>
                    <button type="button" @click="submitDecision()"
                        :disabled="!decideChoice || submitting"
                        :class="decideChoice
                            ? (decideChoice==='approve' ? 'bg-[#15803d] hover:bg-emerald-800 text-white' : 'bg-[#b91c1c] hover:bg-rose-800 text-white')
                            : 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500'"
                        class="ks-btn-xs px-3 py-1.5 font-bold cursor-pointer rounded-md transition">
                        <span x-text="submitting ? 'Validation...' : (decideChoice==='approve' ? 'Confirmer l\'approbation' : (decideChoice==='reject' ? 'Confirmer le refus' : 'Choisir une action'))"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
    function adminPermissionsApp() {
        return {
            detailOpen: false,
            detailLoading: false,
            detail: {},

            decideOpen: false,
            decideId: null,
            decideTarget: { user: '', type: '' },
            decideChoice: null,
            decideComment: '',
            submitting: false,

            init() {},

            async viewDetail(id) {
                this.detail = {};
                this.detailLoading = true;
                this.detailOpen = true;
                try {
                    const resp = await fetch(`/admin/permissions/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    this.detail = await resp.json();
                } catch(e) {
                    this.detail = { type: 'Erreur', status_label: 'Impossible de charger', user: '' };
                } finally {
                    this.detailLoading = false;
                }
            },

            openDecide(id, userName, typeName) {
                this.decideId = id;
                this.decideTarget = { user: userName, type: typeName };
                this.decideChoice = null;
                this.decideComment = '';
                this.submitting = false;
                this.decideOpen = true;
            },

            async submitDecision() {
                if (!this.decideChoice || this.submitting) return;

                this.submitting = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const resp = await fetch(`/admin/permissions/${this.decideId}/decide`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            decision: this.decideChoice,
                            comment: this.decideComment || null,
                        })
                    });

                    const data = await resp.json();

                    if (resp.ok && data.success) {
                        this.decideOpen = false;
                        this.showToast(
                            this.decideChoice === 'approve' ? 'Permission approuvée avec succès ✓' : 'Permission refusée.',
                            this.decideChoice === 'approve' ? 'emerald' : 'red'
                        );
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.showToast(data.message || 'Une erreur s\'est produite.', 'red');
                        this.submitting = false;
                    }
                } catch(e) {
                    this.showToast('Erreur réseau. Veuillez réessayer.', 'red');
                    this.submitting = false;
                }
            },

            showToast(message, color = 'emerald') {
                const toast = document.createElement('div');
                const isEmerald = color === 'emerald';
                toast.className = `fixed top-4 right-4 z-[9999] flex items-center gap-2 px-3.5 py-2.5 rounded-lg border shadow-lg text-xs font-semibold transition-all duration-200 ${
                    isEmerald
                        ? 'bg-[#ecfdf5] border-[#a7f3d0] text-[#15803d]'
                        : 'bg-[#fef2f2] border-[#fecaca] text-[#b91c1c]'
                }`;
                toast.innerHTML = `<span>${message}</span>`;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-4px)';
                    setTimeout(() => toast.remove(), 250);
                }, 2600);
            }
        }
    }
    </script>
</x-app-layout>
