<x-app-layout title="Tâches">

    @php
        $user = auth()->user();
        $canCreate = $user->hasAnyRole(['admin', 'etudiant', 'employe']);
        $canAssign = $user->hasAnyRole(['admin', 'superviseur']);
        $qs = collect(['status' => $status, 'q' => request('q')])->filter()->all();
        $totalTasks = collect($stats ?? [])->sum();
        $activeTasks = ($stats['pending'] ?? 0) + ($stats['in_progress'] ?? 0) + ($stats['blocked'] ?? 0);
        $completionRate = $totalTasks > 0 ? round((($stats['completed'] ?? 0) / $totalTasks) * 100) : 0;
        $filterItems = [
            '' => ['label' => 'Toutes', 'count' => $totalTasks],
            'pending' => ['label' => 'À faire', 'count' => $stats['pending'] ?? 0],
            'in_progress' => ['label' => 'En cours', 'count' => $stats['in_progress'] ?? 0],
            'blocked' => ['label' => 'Bloquées', 'count' => $stats['blocked'] ?? 0],
            'completed' => ['label' => 'Terminées', 'count' => $stats['completed'] ?? 0],
        ];
        $statusThemes = [
            '' => [
                'active'   => 'bg-[#1c3e96] dark:bg-blue-600 text-white border-[#1c3e96] dark:border-blue-600 shadow-sm',
                'inactive' => 'text-[#1c3e96] dark:text-blue-300 bg-[#eef1fb] dark:bg-blue-950/40 border-[#d8e0f5] dark:border-blue-900/50 hover:bg-[#e2e8f8] dark:hover:bg-blue-900/60',
                'dot'      => 'bg-[#1c3e96] dark:bg-blue-400',
                'badge'    => 'bg-blue-200/70 dark:bg-blue-900/70 text-[#1c3e96] dark:text-blue-200',
            ],
            'pending' => [
                'active'   => 'bg-amber-600 dark:bg-amber-600 text-white border-amber-600 shadow-sm',
                'inactive' => 'text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-900/50 hover:bg-amber-100 dark:hover:bg-amber-900/60',
                'dot'      => 'bg-amber-500 dark:bg-amber-400',
                'badge'    => 'bg-amber-200/70 dark:bg-amber-900/70 text-amber-900 dark:text-amber-200',
            ],
            'in_progress' => [
                'active'   => 'bg-blue-600 dark:bg-blue-600 text-white border-blue-600 shadow-sm',
                'inactive' => 'text-blue-800 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-900/50 hover:bg-blue-100 dark:hover:bg-blue-900/60',
                'dot'      => 'bg-blue-500 dark:bg-blue-400',
                'badge'    => 'bg-blue-200/70 dark:bg-blue-900/70 text-blue-900 dark:text-blue-200',
            ],
            'blocked' => [
                'active'   => 'bg-rose-600 dark:bg-rose-600 text-white border-rose-600 shadow-sm',
                'inactive' => 'text-rose-800 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900/50 hover:bg-rose-100 dark:hover:bg-rose-900/60',
                'dot'      => 'bg-rose-500 dark:bg-rose-400',
                'badge'    => 'bg-rose-200/70 dark:bg-rose-900/70 text-rose-900 dark:text-rose-200',
            ],
            'completed' => [
                'active'   => 'bg-emerald-600 dark:bg-emerald-600 text-white border-emerald-600 shadow-sm',
                'inactive' => 'text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-900/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60',
                'dot'      => 'bg-emerald-500 dark:bg-emerald-400',
                'badge'    => 'bg-emerald-200/70 dark:bg-emerald-900/70 text-emerald-900 dark:text-emerald-200',
            ],
        ];
    @endphp

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

        .ks-stat-card--green { --ks-stat-icon-bg: #ecfdf5; --ks-stat-icon-color: #15803d; }
        .ks-stat-card--blue  { --ks-stat-icon-bg: #eef1fb; --ks-stat-icon-color: #1c3e96; }
        .ks-stat-card--amber { --ks-stat-icon-bg: #fffbeb; --ks-stat-icon-color: #b45309; }

        /* Dark mode pour les icônes KPI : fond subtil semi-transparent, jamais de carré blanc */
        .dark .ks-stat-icon {
            background-color: rgba(255, 255, 255, 0.07) !important;
            color: #93c5fd !important;
        }
        .dark .ks-stat-card--green .ks-stat-icon {
            background-color: rgba(22, 101, 52, 0.25) !important;
            color: #86efac !important;
        }
        .dark .ks-stat-card--blue .ks-stat-icon {
            background-color: rgba(28, 62, 150, 0.25) !important;
            color: #93c5fd !important;
        }
        .dark .ks-stat-card--amber .ks-stat-icon {
            background-color: rgba(180, 83, 9, 0.25) !important;
            color: #fcd34d !important;
        }

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
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--neutral { color: #cbd5e1; background-color: rgba(71,85,105,.35); }

        /* Conteneurs de section */
        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.05);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border: 1px solid #222630 !important;
            box-shadow: none;
        }

        .ks-sec-head {
            padding: .65rem .85rem;
            border-bottom: 1px solid #dcdad2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .dark .ks-sec-head {
            border-bottom: 1px solid #222630 !important;
        }

        @keyframes ks-rise {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: none; }
        }
        .ks-rise { animation: ks-rise .35s cubic-bezier(.2,.8,.2,1) both; }

        /* Item de tâche interactif */
        .ks-task-item {
            position: relative;
            display: block;
            border-radius: 8px;
            padding: .65rem .75rem;
            background: #fff;
            border: 1px solid #ecebe3;
            transition: all .15s ease;
            text-decoration: none;
        }
        .dark .ks-task-item {
            background: #181c24;
            border-color: #222630;
        }
        .ks-task-item:hover {
            border-color: #bfbab2;
            background: #faf9f6;
            transform: translateY(-1px);
        }
        .dark .ks-task-item:hover {
            border-color: #333a46;
            background: #1e2430;
        }
        .ks-task-item.ks-task-active {
            background: #eef1fb !important;
            border-color: #1c3e96 !important;
            box-shadow: 0 2px 8px rgba(28,62,150,.1);
        }
        .dark .ks-task-item.ks-task-active {
            background: #17233d !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        /* Scrollbars discrètes */
        * { scrollbar-width: thin; scrollbar-color: rgba(28,62,150,.3) transparent; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: rgba(28,62,150,.3); border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background-color: rgba(28,62,150,.55); }
    </style>

    <div class="ws-root w-full space-y-3 pb-6" x-data="{}">

        {{-- 1. EN-TÊTE COMPACT & BOUTONS D'ACTION --}}
        <div class="ks-rise flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4" style="animation-delay:.02s">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white">
                    Espace de travail
                </h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($canCreate)
                <a href="{{ route('tasks.create') }}" class="ks-btn-primary ks-btn-xs !text-[13px] !px-3.5 !py-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                    </svg>
                    Nouvelle tâche
                </a>
                @endif
                @if($canAssign)
                <a href="{{ route('tasks.assign.form') }}" class="ks-btn-secondary ks-btn-xs !text-[12px] !px-3 !py-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                    </svg>
                    Assigner
                </a>
                @endif
                @if($user->hasAnyRole(['admin', 'superviseur']))
                <a href="{{ route('admin.tasks.tracking') }}" class="ks-btn-secondary ks-btn-xs !text-[12px] !px-3 !py-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Zm1-4h14M9 5v0m3 0v0"/>
                    </svg>
                    Suivi des tâches
                </a>
                <a href="{{ route('admin.reports.all') }}" class="ks-btn-secondary ks-btn-xs !text-[12px] !px-3 !py-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
                    </svg>
                    Rapports
                </a>
                @endif
                @if($user->hasRole('admin'))
                <a href="{{ route('tasks.trash') }}" class="ks-btn-secondary ks-btn-xs !text-[12px] !px-3 !py-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m-6 4v6m4-6v6"/>
                    </svg>
                    Corbeille
                </a>
                @endif
            </div>
        </div>

        {{-- 2. GRILLE COHÉRENTE : COLONNE GAUCHE SOUS 1 CARTE, DÉTAIL SOUS 2 CARTES --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3.5 items-start">

            {{-- BLOC GAUCHE (1/3) : TÂCHES ACTIVES + LISTE --}}
            <div class="lg:col-span-1 space-y-3.5 lg:sticky lg:top-[4.25rem] lg:self-start lg:max-h-[calc(100vh-5.25rem)] flex flex-col">
                {{-- Carte 1 : Tâches Actives --}}
                <div class="ks-rise ks-stat-card ks-stat-card--blue border shadow-none shrink-0" style="animation-delay:.04s">
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Tâches Actives</span>
                            <div class="my-0.5 font-bold text-[#241f1b] dark:text-white ks-num text-2xl leading-tight">{{ $activeTasks }}</div>
                            <span class="badge ks-stat-change ks-stat-change--neutral">À faire ou en cours</span>
                        </div>
                        <span class="ks-stat-icon !w-9 !h-9 bg-[#eef1fb] text-[#1c3e96] dark:!bg-blue-950/40 dark:!text-blue-300">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                    </div>
                </div>

                {{-- Volet de gauche : Recherche + Filtres + Liste des tâches --}}
                <aside class="ks-rise min-w-0 flex-1 min-h-0 flex flex-col" style="animation-delay:.06s">
                    <div class="ks-sec overflow-hidden flex-1 min-h-0 flex flex-col">

                        {{-- Recherche & Filtres --}}
                        <div class="p-2.5 border-b border-[#ecebe3] dark:border-[#222630] space-y-2 bg-[#faf9f6] dark:bg-[#181c24] shrink-0">
                            {{-- Formulaire recherche --}}
                            <form method="GET" action="{{ route('tasks.index') }}"
                                  x-data="{
                                      q: @js(request('q') ?? ''),
                                      timer: null,
                                      go() { this.$el.submit() },
                                      onInput() { clearTimeout(this.timer); this.timer = setTimeout(() => this.go(), 350) }
                                  }"
                                  @submit.prevent="go()">
                                @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                                @if(!empty($selectedParam))<input type="hidden" name="task" value="{{ $selectedParam }}">@endif
                                <div class="relative flex items-center">
                                    <svg class="pointer-events-none absolute left-2.5 h-3.5 w-3.5 text-[#6e6862] dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m1.7-4.3a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                                    </svg>
                                    <input type="text" name="q" x-model="q" @input="onInput()"
                                           placeholder="Rechercher une tâche, un nom…"
                                           autocomplete="off"
                                           class="h-8 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] text-xs text-[#241f1b] dark:text-white pl-8 pr-7 focus:outline-none focus:border-[#1c3e96]" />
                                    <button type="button" x-show="q !== ''" x-cloak @click="q = ''; go()"
                                            class="absolute right-2 text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white" title="Effacer">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            @if(request('q'))
                            <div class="flex items-center justify-between text-[11px] text-[#6e6862] dark:text-slate-400 px-0.5">
                                <span>{{ $resultCount }} résultat{{ $resultCount > 1 ? 's' : '' }}</span>
                                <a href="{{ route('tasks.index', array_filter(['status' => $status, 'task' => $selectedParam ?? null])) }}"
                                   class="text-[#1c3e96] dark:text-blue-400 hover:underline">Effacer</a>
                            </div>
                            @endif

                            {{-- Pilules de statut avec couleurs distinctes par statut --}}
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($filterItems as $key => $item)
                                @php
                                    $isActive = ($key === '' && empty($status)) || (($status ?? null) === $key);
                                    $th = $statusThemes[$key] ?? $statusThemes[''];
                                    $url = $key === ''
                                        ? route('tasks.index', array_filter(['q' => request('q'), 'task' => $selectedParam ?? null]))
                                        : route('tasks.index', array_filter(['status' => $key, 'q' => request('q'), 'task' => $selectedParam ?? null]));
                                @endphp
                                <a href="{{ $url }}"
                                   class="px-2 py-1 text-xs rounded-md transition-all font-semibold inline-flex items-center gap-1.5 border {{ $isActive ? $th['active'] : $th['inactive'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isActive ? 'bg-white' : $th['dot'] }}"></span>
                                    <span>{{ $item['label'] }}</span>
                                    <span class="text-[10px] px-1 py-0.2 rounded font-mono font-bold {{ $isActive ? 'bg-white/25 text-white' : $th['badge'] }}">
                                        {{ $item['count'] }}
                                    </span>
                                </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Liste scrollable des tâches --}}
                        <div class="overflow-y-auto p-1.5 space-y-1.5 flex-1 min-h-[220px]">
                            @forelse($tasks as $t)
                            @php
                                $active = $selected && $selected->id === $t->id;
                                $pct = max(0, min(100, (int) $t->last_progress_percent));
                                $statusBorder = match($t->status) {
                                    'completed'   => '#15803d',
                                    'blocked'     => '#b91c1c',
                                    'in_progress' => '#1c3e96',
                                    default       => '#bfbab2',
                                };
                            @endphp
                            <a href="{{ encrypted_route('tasks.show', $t) }}{{ $qs ? '?'.http_build_query($qs) : '' }}"
                               class="ks-task-item {{ $active ? 'ks-task-active' : '' }} group">

                                {{-- Liseré indicateur statut --}}
                                <div class="absolute left-0 inset-y-1.5 w-[3px] rounded-r" style="background: {{ $active ? '#1c3e96' : $statusBorder }};"></div>

                                <div class="pl-2">
                                    <div class="flex items-start justify-between gap-1.5 mb-1.5">
                                        <p class="text-xs font-semibold line-clamp-2 leading-tight {{ $active ? 'text-[#1c3e96] dark:text-blue-300' : 'text-[#241f1b] dark:text-slate-100 group-hover:text-[#1c3e96] dark:group-hover:text-blue-300' }}">
                                            {{ $t->title }}
                                        </p>
                                        <span class="font-mono shrink-0 text-[11px] font-bold {{ $active ? 'text-[#1c3e96] dark:text-blue-300' : 'text-[#6e6862] dark:text-slate-400' }}">
                                            {{ $pct }}%
                                        </span>
                                    </div>

                                    {{-- Micro barre de progression --}}
                                    <div class="h-[2px] w-full rounded-full bg-[#ecebe3] dark:bg-[#222630] overflow-hidden mb-1.5">
                                        <div class="h-full rounded-full transition-all duration-300" style="width:{{ $pct }}%; background: {{ $active ? '#1c3e96' : $statusBorder }};"></div>
                                    </div>

                                    <div class="flex items-center justify-between gap-2 text-[11px] text-[#6e6862] dark:text-slate-400">
                                        <span class="truncate">{{ $t->owner?->name ?? '—' }}</span>
                                        @if($t->due_date)
                                        <span class="font-mono shrink-0 {{ $t->isOverdue() && !$active ? 'text-rose-600 font-semibold' : '' }}">
                                            {{ $t->due_date->format('d/m/Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div class="flex min-h-[260px] flex-col items-center justify-center p-4 text-center">
                                <div class="w-9 h-9 rounded-lg bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-400 flex items-center justify-center mb-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m1.7-4.3a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-[#241f1b] dark:text-white">Aucune tâche</p>
                                <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">
                                    @if(request('q'))
                                        Rien ne correspond à « {{ request('q') }} ».
                                    @else
                                        Modifie le filtre ou crée une tâche.
                                    @endif
                                </p>
                                @if($canCreate)
                                <a href="{{ route('tasks.create') }}" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-2.5 mt-3">
                                    Créer une tâche
                                </a>
                                @endif
                            </div>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>

            {{-- BLOC DROIT (2/3) : 2 CARTES KPI (TERMINÉES + TAUX) + DÉTAIL DE LA TÂCHE --}}
            <div class="lg:col-span-2 space-y-3.5 lg:sticky lg:top-[4.25rem] lg:self-start">
                {{-- Les 2 autres cartes KPI côte à côte --}}
                <div class="ks-rise grid grid-cols-1 sm:grid-cols-2 gap-3.5" style="animation-delay:.04s">
                    {{-- Carte 2 : Terminées --}}
                    <div class="ks-stat-card ks-stat-card--green border shadow-none">
                        <div class="p-3 flex items-center justify-between">
                            <div>
                                <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Terminées</span>
                                <div class="my-0.5 font-bold text-[#15803d] dark:text-emerald-400 ks-num text-2xl leading-tight">{{ $stats['completed'] ?? 0 }}</div>
                                <span class="badge ks-stat-change ks-stat-change--positive">Validées</span>
                            </div>
                            <span class="ks-stat-icon !w-9 !h-9 bg-[#ecfdf5] text-[#15803d] dark:!bg-emerald-950/40 dark:!text-emerald-300">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </div>
                    </div>

                    {{-- Carte 3 : Taux d'achèvement --}}
                    <div class="ks-stat-card ks-stat-card--blue border shadow-none">
                        <div class="p-3 flex items-center justify-between">
                            <div>
                                <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[11px] uppercase tracking-wider">Taux d'achèvement</span>
                                <div class="my-0.5 font-bold text-[#1c3e96] dark:text-blue-400 ks-num text-2xl leading-tight">{{ $completionRate }}%</div>
                                <span class="badge ks-stat-change ks-stat-change--{{ $completionRate >= 75 ? 'positive' : ($completionRate >= 40 ? 'neutral' : 'negative') }}">
                                    {{ $totalTasks }} tâche(s) au total
                                </span>
                            </div>
                            <span class="ks-stat-icon !w-9 !h-9 bg-[#eef1fb] text-[#1c3e96] dark:!bg-blue-950/40 dark:!text-blue-300">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Volet de droite : Détail de la tâche sélectionnée ou état vide --}}
                <main class="ks-rise min-w-0" style="animation-delay:.08s">
                    @if($selected)
                        @include('tasks.partials.detail', ['task' => $selected, 'todayReport' => $todayReport])
                    @else
                    <div class="ks-sec flex min-h-[500px] items-center justify-center rounded-xl p-8 text-center border-dashed border-[#dcdad2] dark:border-[#222630] bg-[#fffefa]/60 dark:bg-[#131519]/60">
                        <div class="max-w-xs space-y-3">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-400">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-[#241f1b] dark:text-white">Sélectionne une tâche</h2>
                                <p class="mt-1 text-xs text-[#6e6862] dark:text-slate-400">
                                    Choisis un élément dans la liste de gauche pour afficher son détail, ses sous-tâches et son fil d'échanges.
                                </p>
                            </div>
                            @if($canCreate)
                            <a href="{{ route('tasks.create') }}" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-3 inline-flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                                </svg>
                                Créer une tâche
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </main>
            </div>

        </div>
    </div>

    {{-- TOAST NOTIFICATIONS --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)"
         class="fixed bottom-4 right-4 z-50 flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-3 py-2 text-xs font-semibold shadow-lg">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
         class="fixed bottom-4 right-4 z-50 rounded-lg bg-rose-600 text-white p-3 text-xs font-semibold shadow-lg max-w-sm space-y-1">
        @foreach($errors->all() as $error)
            <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

</x-app-layout>