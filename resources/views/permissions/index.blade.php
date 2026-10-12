<x-app-layout title="Mes demandes de permission">

    <style>
        /* Fond warm beige façon Mon-Stage */
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

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

        /* Boutons Kaisss / Mon-Stage */
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
         x-data="permissionsApp()"
         x-init="init()">

        {{-- ── FLASH MESSAGES COMPACTS ── --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="flex items-center justify-between gap-3 bg-[#ecfdf5] dark:bg-emerald-950/40 border border-[#a7f3d0] dark:border-emerald-800/60 text-[#15803d] dark:text-emerald-300 rounded-lg px-3.5 py-2.5 text-xs font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#15803d] dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show=false" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-[#fef2f2] dark:bg-rose-950/40 border border-[#fecaca] dark:border-rose-800/60 text-[#b91c1c] dark:text-rose-300 rounded-lg px-3.5 py-2.5 text-xs">
            <ul class="space-y-1">
                @foreach($errors->all() as $e)
                <li class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ── 1. EN-TÊTE COMPACT & BOUTON D'ACTION ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white">
                    Demandes de permission
                </h1>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Gérez vos absences, retards et autorisations auprès de vos responsables.
                </p>
            </div>
            <button type="button" @click="openModal()" class="ks-btn-primary ks-btn-xs !text-[13px] !px-3.5 !py-1.5 cursor-pointer shrink-0 whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle demande
            </button>
        </div>

        {{-- ── 2. CARTES KPI COMPACTES ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Total --}}
            <div class="ks-stat-card ks-stat-card--blue border shadow-none p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Total</span>
                        <div class="my-0.5 font-bold text-[#1c3e96] dark:text-blue-400 ks-num text-2xl leading-tight">{{ $stats['total'] }}</div>
                        <span class="badge ks-stat-change ks-stat-change--neutral">Historique complet</span>
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
                        <span class="badge ks-stat-change ks-stat-change--warning">À traiter</span>
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

        {{-- ── 3. FILTRES COMPACTS ── --}}
        <div class="ks-sec p-3">
            <form method="GET" action="{{ route('permissions.index') }}">
                <div class="flex flex-wrap gap-2.5 items-end">
                    <div class="flex-1 min-w-[130px]">
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Statut</label>
                        <select name="status" class="ks-input !h-8 text-xs">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvé</option>
                            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Refusé</option>
                            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Annulé</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Type</label>
                        <select name="type_id" class="ks-input !h-8 text-xs">
                            <option value="">Tous les types</option>
                            @foreach($types as $t)
                            <option value="{{ $t->id }}" {{ request('type_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-[125px]">
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Du</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="ks-input !h-8 text-xs">
                    </div>

                    <div class="w-[125px]">
                        <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400 mb-1 uppercase tracking-wider">Au</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="ks-input !h-8 text-xs">
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="submit" class="ks-btn-primary ks-btn-xs !h-8 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m1.7-4.3a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                            </svg>
                            Filtrer
                        </button>
                        @if(request()->hasAny(['status','type_id','from','to']))
                        <a href="{{ route('permissions.index') }}" class="ks-btn-secondary ks-btn-xs !h-8 cursor-pointer">
                            Effacer
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ── 4. LISTE DES DEMANDES COMPACTE ── --}}
        <div class="space-y-2.5">
            @forelse($requests as $req)
            @php
                $statusBadges = [
                    'pending'   => 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
                    'approved'  => 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                    'rejected'  => 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
                    'cancelled' => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700',
                ];
                $typePill = match($req->type->color ?? '') {
                    'red'    => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-300 dark:border-rose-900/40',
                    'amber'  => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-900/40',
                    'orange' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-900/40',
                    'purple' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-300 dark:border-indigo-900/40',
                    'green'  => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-900/40',
                    default  => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-300 dark:border-blue-900/40',
                };
                $badgeClass = $statusBadges[$req->status] ?? 'bg-slate-100 text-slate-700 border-slate-300';
            @endphp
            <div class="ks-sec p-3 hover:border-[#1c3e96] dark:hover:border-[#3b82f6] transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                    {{-- Type & Date --}}
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-md border flex items-center justify-center shrink-0 text-base {{ $typePill }}">
                            @php
                            $icons = ['calendar-x'=>'📅','clock'=>'⏰','log-out'=>'🚪','gift'=>'🎁'];
                            echo $icons[$req->type->icon] ?? '📋';
                            @endphp
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-xs font-bold text-[#241f1b] dark:text-white truncate">{{ $req->type->name }}</p>
                                <span class="text-[11px] font-mono text-[#6e6862] dark:text-slate-400">
                                    {{ $req->created_at->format('d/m/Y à H:i') }}
                                </span>
                            </div>
                            <p class="text-xs text-[#6e6862] dark:text-slate-400 line-clamp-1 mt-0.5">
                                {{ $req->fieldSummary() ?: 'Aucun détail précisé' }}
                            </p>

                            {{-- Signataires badges --}}
                            <div class="flex flex-wrap items-center gap-1 mt-1.5">
                                <span class="text-[10px] text-[#6e6862] dark:text-slate-400 uppercase font-semibold mr-1">Signataires :</span>
                                @foreach($req->recipients as $r)
                                <span class="inline-flex items-center gap-1 text-[10.5px] px-2 py-0.5 rounded border font-medium
                                    @if($r->status==='validated') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-800/40
                                    @elseif($r->status==='rejected') bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-300 dark:border-rose-800/40
                                    @elseif($r->status==='skipped') bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-700
                                    @else bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/40
                                    @endif">
                                    <span>{{ $r->signataire->nom }}</span>
                                    @if($r->status==='validated') <span class="font-bold">✓</span>
                                    @elseif($r->status==='rejected') <span class="font-bold">✕</span>
                                    @elseif($r->status==='skipped') <span>–</span>
                                    @else <span>···</span>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Statut & Actions --}}
                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        <span class="inline-flex px-2.5 py-0.5 rounded text-[11px] font-semibold border {{ $badgeClass }}">
                            {{ $req->statusLabel() }}
                        </span>

                        <button type="button" @click="viewDetail({{ $req->id }})"
                                title="Voir les détails"
                                class="ks-btn-secondary ks-btn-xs !h-7 !px-2 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Détails
                        </button>

                        @if($req->isPending())
                        <form method="POST" action="{{ route('permissions.cancel', $req->id) }}"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                            @csrf
                            <button type="submit" title="Annuler la demande"
                                    class="ks-btn-secondary ks-btn-xs !h-7 !px-2 text-rose-600 dark:text-rose-400 hover:border-rose-400 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Annuler
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Feedback / Commentaire de décision si présent --}}
                @if($req->decision_comment && in_array($req->status, ['approved','rejected']))
                <div class="mt-2.5 pt-2 border-t border-[#ecebe3] dark:border-[#222630] flex items-start gap-2 text-xs text-[#6e6862] dark:text-slate-400">
                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="italic">« {{ $req->decision_comment }} »</p>
                    <span class="ml-auto font-medium text-[11px] shrink-0">par {{ $req->decider?->name }}</span>
                </div>
                @endif
            </div>
            @empty
            <div class="ks-sec flex min-h-[300px] flex-col items-center justify-center p-8 text-center border-dashed border-[#dcdad2] dark:border-[#222630]">
                <div class="w-10 h-10 rounded-lg bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-400 flex items-center justify-center mb-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#241f1b] dark:text-white">Aucune demande trouvée</h3>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5 max-w-sm">
                    Vous n'avez soumis aucune demande correspondant aux critères sélectionnés.
                </p>
                <button type="button" @click="openModal()" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-3 mt-3 cursor-pointer">
                    Créer une demande
                </button>
            </div>
            @endforelse
        </div>

        {{-- ── 5. PAGINATION COMPACTE ── --}}
        @if($requests->hasPages())
        <div class="flex justify-center pt-2">
            {{ $requests->links() }}
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL NOUVELLE DEMANDE (Mon-Stage Style) ── --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="closeModal()"></div>

            {{-- Panel --}}
            <div class="relative bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-hidden flex flex-col"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                    <div>
                        <h2 class="text-sm font-bold text-[#241f1b] dark:text-white">Nouvelle demande de permission</h2>
                        <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">
                            <span x-show="step===1">Étape 1 sur 2 — Choix du type</span>
                            <span x-show="step===2">Étape 2 sur 2 — Saisie des informations</span>
                        </p>
                    </div>

                    {{-- Step indicator pills --}}
                    <div class="flex items-center gap-1.5 mr-6">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                              :class="step>=1 ? 'bg-[#1c3e96] text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'">1</span>
                        <span class="w-4 h-0.5" :class="step>=2 ? 'bg-[#1c3e96]' : 'bg-slate-200 dark:bg-slate-700'"></span>
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                              :class="step>=2 ? 'bg-[#1c3e96] text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'">2</span>
                    </div>

                    <button @click="closeModal()" type="button" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal body --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3.5">

                    {{-- ÉTAPE 1 — Choix du type --}}
                    <div x-show="step===1" class="space-y-2.5">
                        <p class="text-xs text-[#6e6862] dark:text-slate-400">Sélectionnez le type de motif de votre demande :</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($types as $t)
                            @php
                            $typeIcons = ['calendar-x'=>'📅','clock'=>'⏰','log-out'=>'🚪','gift'=>'🎁'];
                            @endphp
                            <button type="button" @click="selectType({{ $t->id }}, '{{ addslashes($t->name) }}', @js($t->fields_config))"
                                class="relative flex items-start gap-2.5 p-3 rounded-lg border text-left transition-all cursor-pointer"
                                :class="selectedTypeId==={{ $t->id }}
                                    ? 'border-[#1c3e96] bg-[#eef1fb] dark:bg-[#17233d] dark:border-blue-500 ring-1 ring-[#1c3e96]/30'
                                    : 'border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] hover:border-[#1c3e96] dark:hover:border-blue-500'">
                                <span class="w-8 h-8 rounded-md bg-white dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] flex items-center justify-center text-sm shrink-0">
                                    {{ $typeIcons[$t->icon] ?? '📋' }}
                                </span>
                                <div class="min-w-0 pr-4">
                                    <p class="text-xs font-bold text-[#241f1b] dark:text-white leading-tight">{{ $t->name }}</p>
                                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $t->description }}</p>
                                </div>
                                <div x-show="selectedTypeId==={{ $t->id }}" class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-[#1c3e96] dark:bg-blue-500 text-white flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ÉTAPE 2 — Formulaire dynamique --}}
                    <div x-show="step===2">
                        <form id="permissionForm" method="POST" action="{{ route('permissions.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="permission_type_id" :value="selectedTypeId">

                            {{-- Champs dynamiques configurés par le type --}}
                            <template x-for="field in selectedFields" :key="field.key">
                                <div>
                                    <label class="block text-xs font-semibold text-[#6e6862] dark:text-slate-400 mb-1" x-text="field.label + (field.required ? ' *' : '')"></label>
                                    <template x-if="field.type==='date'">
                                        <input type="date" :name="'fields_data['+field.key+']'" class="ks-input !h-8 text-xs" :required="field.required">
                                    </template>
                                    <template x-if="field.type==='time'">
                                        <input type="time" :name="'fields_data['+field.key+']'" class="ks-input !h-8 text-xs" :required="field.required">
                                    </template>
                                    <template x-if="field.type==='textarea'">
                                        <textarea :name="'fields_data['+field.key+']'" rows="3" class="ks-input resize-none text-xs" :required="field.required"></textarea>
                                    </template>
                                    <template x-if="field.type==='text'">
                                        <input type="text" :name="'fields_data['+field.key+']'" class="ks-input !h-8 text-xs" :required="field.required">
                                    </template>
                                </div>
                            </template>

                            {{-- Note optionnelle --}}
                            <div>
                                <label class="block text-xs font-semibold text-[#6e6862] dark:text-slate-400 mb-1">
                                    Note complémentaire <span class="font-normal opacity-75">(optionnel)</span>
                                </label>
                                <textarea name="note" rows="2" class="ks-input resize-none text-xs" placeholder="Précisez un détail ou une information utile..."></textarea>
                            </div>

                            {{-- Destinataires / Signataires --}}
                            <div>
                                <label class="block text-xs font-semibold text-[#6e6862] dark:text-slate-400 mb-1">
                                    Destinataires obligatoires <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mb-2">
                                    Cochez au moins un responsable pour valider la demande.
                                </p>
                                <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                    @foreach($signataires as $sig)
                                    <label class="flex items-center gap-2.5 p-2 rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] hover:border-[#1c3e96] cursor-pointer transition">
                                        <input type="checkbox" name="signataire_ids[]" value="{{ $sig->id }}"
                                               class="w-3.5 h-3.5 rounded text-[#1c3e96] border-[#dcdad2] dark:border-[#333a46] dark:bg-[#181c24] focus:ring-[#1c3e96]">
                                        <span class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($sig->nom, 0, 2)) }}
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-xs font-bold text-[#241f1b] dark:text-white truncate">{{ $sig->nom }}</span>
                                            <span class="block text-[10.5px] text-[#6e6862] dark:text-slate-400 truncate">{{ $sig->poste }}</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal footer --}}
                <div class="flex items-center justify-between px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                    <button type="button" x-show="step===2" @click="step=1" class="ks-btn-secondary ks-btn-xs cursor-pointer">
                        ← Retour
                    </button>
                    <div x-show="step===1" class="text-[11px] text-[#6e6862] dark:text-slate-400">
                        Cliquez sur un type pour avancer
                    </div>

                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" @click="closeModal()" class="ks-btn-secondary ks-btn-xs cursor-pointer">
                            Annuler
                        </button>
                        <button type="button" x-show="step===1" @click="goToStep2()"
                            :disabled="!selectedTypeId"
                            :class="selectedTypeId ? 'ks-btn-primary cursor-pointer' : 'opacity-50 cursor-not-allowed ks-btn-secondary'"
                            class="ks-btn-xs">
                            Suivant →
                        </button>
                        <button type="button" x-show="step===2" @click="submitForm()" class="ks-btn-primary ks-btn-xs cursor-pointer">
                            Soumettre la demande
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL DÉTAIL D'UNE DEMANDE (Mon-Stage Style) ── --}}
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
                    <h3 class="text-sm font-bold text-[#241f1b] dark:text-white" x-text="detail.type || 'Détails de la demande'"></h3>
                    <button @click="detailOpen=false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 space-y-3.5" x-show="!detailLoading">
                    {{-- Statut --}}
                    <div class="flex items-center justify-between py-1 border-b border-[#ecebe3] dark:border-[#222630]">
                        <span class="text-xs text-[#6e6862] dark:text-slate-400 font-semibold uppercase">Statut actuel</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded border"
                            :class="{
                                'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300': detail.status==='pending',
                                'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300': detail.status==='approved',
                                'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300': detail.status==='rejected',
                                'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300': detail.status==='cancelled'
                            }" x-text="detail.status_label"></span>
                    </div>

                    {{-- Champs dynamiques --}}
                    <div class="space-y-1.5">
                        <template x-for="field in (detail.fields_config||[])" :key="field.key">
                            <div x-show="detail.fields_data && detail.fields_data[field.key]" class="flex justify-between py-1.5 border-b border-[#ecebe3] dark:border-[#222630] text-xs">
                                <span class="text-[#6e6862] dark:text-slate-400" x-text="field.label"></span>
                                <span class="font-bold text-[#241f1b] dark:text-white" x-text="detail.fields_data ? detail.fields_data[field.key] : ''"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Note --}}
                    <div x-show="detail.note" class="bg-[#eef1fb] dark:bg-blue-950/30 border border-[#d8e0f5] dark:border-blue-900/40 rounded-lg p-3">
                        <p class="text-[10px] font-bold text-[#1c3e96] dark:text-blue-300 uppercase tracking-wider mb-0.5">Note transmise</p>
                        <p class="text-xs text-[#241f1b] dark:text-slate-200" x-text="detail.note"></p>
                    </div>

                    {{-- Décision --}}
                    <div x-show="detail.decided_at" class="bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-3">
                        <p class="text-[10px] font-bold text-[#6e6862] dark:text-slate-400 uppercase tracking-wider mb-1">Décision finale</p>
                        <p class="text-xs text-[#241f1b] dark:text-white">
                            Par <strong x-text="detail.decider"></strong> — <span class="text-[#6e6862] dark:text-slate-400" x-text="detail.decided_at"></span>
                        </p>
                        <p x-show="detail.decision_comment" class="text-xs text-[#6e6862] dark:text-slate-400 mt-1 italic" x-text="'« ' + detail.decision_comment + ' »'"></p>
                    </div>

                    {{-- Destinataires --}}
                    <div>
                        <p class="text-[10px] font-bold text-[#6e6862] dark:text-slate-400 uppercase tracking-wider mb-1.5">Destinataires assignés</p>
                        <div class="space-y-1.5">
                            <template x-for="r in (detail.recipients||[])" :key="r.nom">
                                <div class="flex items-center gap-2.5 p-2 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
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

                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400 text-right font-mono" x-text="'Soumis le ' + detail.created_at"></p>
                </div>

                {{-- Loading --}}
                <div x-show="detailLoading" class="py-12 text-center">
                    <div class="w-6 h-6 mx-auto border-2 border-[#1c3e96] border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-2">Chargement en cours...</p>
                </div>
            </div>
        </div>

    </div>

    <script>
    function permissionsApp() {
        return {
            modalOpen: false,
            detailOpen: false,
            detailLoading: false,
            step: 1,
            selectedTypeId: null,
            selectedTypeName: '',
            selectedFields: [],
            detail: {},

            init() {
                @if($errors->any())
                this.modalOpen = true;
                this.step = 2;
                this.selectedTypeId = {{ old('permission_type_id', 'null') }};
                @endif
            },

            openModal() {
                this.step = 1;
                this.selectedTypeId = null;
                this.selectedTypeName = '';
                this.selectedFields = [];
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
            },

            selectType(id, name, fields) {
                this.selectedTypeId = id;
                this.selectedTypeName = name;
                this.selectedFields = fields;
            },

            goToStep2() {
                if (!this.selectedTypeId) return;
                this.step = 2;
            },

            submitForm() {
                document.getElementById('permissionForm').submit();
            },

            async viewDetail(id) {
                this.detail = {};
                this.detailLoading = true;
                this.detailOpen = true;
                try {
                    const resp = await fetch(`/permissions/${id}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.detail = await resp.json();
                } catch(e) {
                    this.detail = { type: 'Erreur', status_label: 'Impossible de charger' };
                } finally {
                    this.detailLoading = false;
                }
            }
        }
    }
    </script>
</x-app-layout>
