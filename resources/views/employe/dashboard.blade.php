<x-app-layout>
    @php
        $user = auth()->user();
        $prenom = $user?->personnel?->prenom ?: ($user?->prenom ?: explode(' ', trim($user?->name ?? 'Employé'))[0]);
        $initiales = strtoupper(substr(preg_replace('/\s+/', '', $user?->name ?? 'EM'), 0, 2));
        $heure = now()->hour;
        $salut = $heure < 18 ? 'Bonjour' : 'Bonsoir';

        $hasIn = (bool) $todayAttendance?->first_check_in_at;
        $hasOut = (bool) $todayAttendance?->last_check_out_at;

        if (!$hasIn) {
            $nextTitre = 'Pointez votre arrivée pour débuter votre journée de travail';
            $nextCta = 'Pointer mon arrivée';
            $nextRoute = route('presence.pointage');
        } elseif (!$hasOut) {
            $nextTitre = 'Arrivée validée — pensez à enregistrer votre départ en fin de journée';
            $nextCta = 'Pointer mon départ';
            $nextRoute = route('presence.pointage');
        } else {
            $nextTitre = 'Journée terminée et enregistrée. À bientôt !';
            $nextCta = 'Voir mon historique';
            $nextRoute = route('presence.historique');
        }

        $myTasksActive = $myTasks->where('status', '!=', 'completed')->count();
        $tauxPresenceHebdo = $daysTrackedThisWeek > 0 ? round(($daysPresentThisWeek / $daysTrackedThisWeek) * 100) : 100;
    @endphp

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }
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

        .ks-sec-head {
            padding: .75rem 1rem;
            border-bottom: 1px solid #dcdad2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .dark .ks-sec-head {
            border-bottom-color: #222630 !important;
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
        .ks-label { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #6e6862; }
        .dark .ks-label { color: #a89f93; }
        .ks-track { background: #ecebe3; border-radius: 999px; overflow: hidden; }
        .dark .ks-track { background: #262a30; }
        .ks-btn-xs { min-height: 30px !important; padding: .3rem .7rem !important; font-size: .74rem !important; font-weight: 600; border-radius: 6px !important; flex: none; white-space: nowrap; }
        .dark .group.rounded-md { background-color: #15181f !important; border-color: #222630 !important; }

        /* Élimination stricte des fonds clairs en dark mode */
        .dark .bg-sky-50 { background-color: rgba(56, 189, 248, 0.16) !important; color: #7dd3fc !important; }
        .dark .text-sky-700 { color: #7dd3fc !important; }
        .dark .ring-sky-500\/20 { --tw-ring-color: rgba(56, 189, 248, 0.3) !important; }
        .dark .bg-\[\#eef1fb\] { background-color: rgba(59, 130, 246, 0.16) !important; color: #93c5fd !important; }
        .dark .ring-\[\#1c3e96\]\/20 { --tw-ring-color: rgba(96, 165, 250, 0.3) !important; }
        .dark .bg-\[\#f1f0ed\] { background-color: #1f232d !important; color: #94a3b8 !important; }
        .dark .bg-\[\#ecfdf5\] { background-color: rgba(16, 185, 129, 0.16) !important; color: #6ee7b7 !important; }
        .dark .bg-\[\#faf5ff\] { background-color: rgba(168, 85, 247, 0.16) !important; color: #d8b4fe !important; }
        .dark .bg-blue-50 { background-color: rgba(59, 130, 246, 0.16) !important; color: #93c5fd !important; }
        .dark .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.16) !important; color: #6ee7b7 !important; }
        .dark .bg-amber-50 { background-color: rgba(245, 158, 11, 0.16) !important; color: #fcd34d !important; }
        .dark .bg-rose-50 { background-color: rgba(244, 63, 94, 0.16) !important; color: #fda4af !important; }
        .dark .bg-purple-50 { background-color: rgba(168, 85, 247, 0.16) !important; color: #d8b4fe !important; }
        .dark .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.16) !important; color: #a5b4fc !important; }
        .dark .bg-slate-100 { background-color: #1f232d !important; color: #94a3b8 !important; }
        .dark .bg-white { background-color: #15181f !important; }
    </style>

    <div class="w-full space-y-3 pb-6">

        {{-- ── En-tête Responsive (Mobile / Tablette / Desktop) sans le surtitre ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc] truncate">
                    {{ $salut }}, {{ $prenom }}
                </h1>
            </div>

            <div class="flex items-center flex-wrap sm:flex-nowrap gap-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc] shrink-0">
                    <svg class="w-3.5 h-3.5 text-[#1c3e96] dark:text-[#60a5fa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <a href="{{ $nextRoute }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold shadow-xs transition-colors shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $nextCta }}</span>
                </a>
            </div>
        </div>

        {{-- ── 4 Cartes KPI Sparklines Compactes ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">

            {{-- 1. Pointage du jour --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[104px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Pointage jour</span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $hasIn ? 'bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/20 dark:text-[#4ade80]' : 'bg-[#fee2e2] text-[#b91c1c] dark:bg-[#b91c1c]/20 dark:text-[#f87171]' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $hasIn ? 'bg-[#15803d]' : 'bg-[#b91c1c]' }}"></span>
                        {{ $hasIn ? 'Enregistré' : 'Non pointé' }}
                    </span>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">
                            {{ $todayAttendance?->first_check_in_at?->format('H:i') ?? '--:--' }}
                        </span>
                        <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                            à {{ $todayAttendance?->last_check_out_at?->format('H:i') ?? '--:--' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Arrivée / Départ</span>
                    <svg class="w-12 h-3.5 text-[#15803d]" viewBox="0 0 48 14" fill="none">
                        <path d="M1 10 C 12 12, 24 4, 36 6 C 42 7, 46 2, 47 2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- 2. Événements semaine --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[104px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Événements</span>
                    <div class="w-6 h-6 rounded-md bg-[#eef1fb] dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $attendanceEventsThisWeek }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] ml-1">pointages</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Cette semaine</span>
                    <svg class="w-12 h-3.5 text-[#1c3e96] dark:text-[#60a5fa]" viewBox="0 0 48 14" fill="none">
                        <path d="M1 12 C 10 9, 20 13, 30 5 C 38 1, 44 4, 47 3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- 3. Assiduité semaine --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[104px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Présence hebdo</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-[#eef1fb] dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa]">
                        {{ $tauxPresenceHebdo }}%
                    </span>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $daysPresentThisWeek }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">/ {{ $daysTrackedThisWeek }} j validés</span>
                </div>
                <div class="w-full bg-[#ecebe3] dark:bg-[#262a30] rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full bg-[#1c3e96] dark:bg-[#60a5fa]" style="width: {{ $tauxPresenceHebdo }}%;"></div>
                </div>
            </div>

            {{-- 4. Tâches actives --}}
            <div class="ks-stat-card p-3 flex flex-col justify-between h-[104px]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Mes tâches</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-[#f5f3ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#c4b5fd]">
                        {{ $myTasksCompleted }} faites
                    </span>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc]">{{ $myTasksActive }}</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">actives</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">{{ $myTasksCreated }} créées</span>
                    <svg class="w-12 h-3.5 text-[#7c3aed] dark:text-[#c4b5fd]" viewBox="0 0 48 14" fill="none">
                        <path d="M1 7 C 14 3, 26 11, 38 4 C 42 2, 45 4, 47 3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── Grille 2 Colonnes : Tâches (Gauche) & Cadre Collaborateur (Droite) ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">

            {{-- Colonne Gauche : Mes Tâches (Col-span-8) --}}
            <div class="lg:col-span-8 space-y-3">
                <div class="ks-sec">
                    <div class="ks-sec-head">
                        <h2 class="text-sm font-semibold text-[#241f1b] dark:text-white">Mes tâches · {{ $myTasks->count() }}</h2>
                        <a href="{{ route('tasks.index') }}" class="text-xs font-medium text-[#1c3e96] dark:text-blue-400 hover:underline">Espace travail →</a>
                    </div>

                    @if ($myTasks->isEmpty())
                        <div class="p-6 text-center">
                            <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-md bg-[#eef1fb] text-[#1c3e96] dark:bg-[#1c3e96]/20 dark:text-[#60a5fa]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <p class="mt-3 text-sm font-semibold text-[#241f1b] dark:text-white">Journée légère, aucune tâche assignée</p>
                            <p class="mx-auto mt-1 max-w-xs text-xs text-[#6e6862] dark:text-gray-400">Créez une tâche ou consultez votre espace de travail pour collaborer.</p>
                            <a href="{{ route('tasks.index') }}" class="ks-btn-secondary ks-btn-xs mx-auto mt-3">Ouvrir l'espace de travail</a>
                        </div>
                    @else
                        @php
                            $ordre = ['in_progress' => 0, 'pending' => 1, 'changes_requested' => 2, 'blocked' => 3, 'awaiting_validation' => 4, 'completed' => 5];
                            $tasks6 = $myTasks->sortBy(fn($t) => [($ordre[$t->status] ?? 9), -($t->updated_at?->timestamp ?? 0)])->take(6);
                        @endphp
                        <div class="p-3 sm:p-4 space-y-3">
                            @foreach($tasks6 as $task)
                            @php
                                $myReports = $task->dailyReports->where('user_id', $user->id)->take(2);
                                $statusMeta = match ($task->status) {
                                    'completed' => ['Terminée', 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 ring-emerald-600/20 dark:ring-emerald-500/30'],
                                    'awaiting_validation' => ['En attente validation', 'bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 ring-purple-600/20 dark:ring-purple-500/30'],
                                    'blocked' => ['Bloquée', 'bg-rose-50 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 ring-rose-600/20 dark:ring-rose-500/30'],
                                    'in_progress' => ['En cours', 'bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 ring-blue-600/20 dark:ring-blue-500/30'],
                                    'changes_requested' => ['Corrections demandées', 'bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 ring-amber-600/20 dark:ring-amber-500/30'],
                                    default => ['À faire', 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 ring-slate-600/20 dark:ring-slate-700/40'],
                                };
                                $priorityMeta = match ($task->priority) {
                                    'urgent' => ['Urgente', 'bg-rose-50 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 ring-rose-600/20 dark:ring-rose-500/30'],
                                    'high' => ['Haute', 'bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 ring-amber-600/20 dark:ring-amber-500/30'],
                                    'low' => ['Basse', 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 ring-slate-400/20 dark:ring-slate-700/40'],
                                    default => ['Normale', 'bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 ring-indigo-500/20 dark:ring-indigo-500/30'],
                                };
                                $pp = min(100, max(0, $task->last_progress_percent ?? $task->base_progress_percent ?? 0));
                            @endphp
                            <div class="group rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] p-4 shadow-sm min-w-0">
                                <a href="{{ encrypted_route('tasks.show', $task) }}" class="block min-w-0">
                                    <div class="flex w-full flex-col gap-3 md:flex-row md:items-start md:justify-between min-w-0">
                                        <div class="flex-1 min-w-0 md:pr-4">
                                            <h3 class="text-[15px] font-semibold text-[#241f1b] dark:text-[#f8fafc] group-hover:text-[#1c3e96] dark:group-hover:text-[#60a5fa] transition-colors leading-snug break-words">{{ $task->title }}</h3>
                                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $priorityMeta[1] }}">{{ $priorityMeta[0] }}</span>
                                                @if ($task->owner_id === $user->id)
                                                <span class="inline-flex items-center rounded-full bg-[#eef1fb] dark:bg-[#1c3e96]/20 px-2.5 py-0.5 text-[11px] font-medium text-[#1c3e96] dark:text-[#60a5fa] ring-1 ring-inset ring-[#1c3e96]/20 dark:ring-[#60a5fa]/30">Créée par toi</span>
                                                @else
                                                <span class="inline-flex items-center rounded-full bg-sky-50 dark:bg-sky-500/20 px-2.5 py-0.5 text-[11px] font-medium text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-500/20 dark:ring-sky-400/30">Assignée à toi</span>
                                                @endif
                                            </div>
                                            <p class="mt-2 text-[13px] text-[#6e6862] dark:text-[#a89f93] leading-relaxed break-words">{{ $task->description ?: 'Aucune description fournie.' }}</p>
                                            <div class="mt-2.5 flex max-w-xs items-center gap-2">
                                                <div class="ks-track h-1.5 w-full"><div class="h-1.5 rounded-full {{ $task->status === 'completed' ? 'bg-[#15803d]' : 'bg-[#1c3e96] dark:bg-[#60a5fa]' }}" style="width:{{ $pp }}%"></div></div>
                                                <span class="text-xs font-semibold tabular-nums text-[#241f1b] dark:text-[#f8fafc]">{{ $pp }}%</span>
                                            </div>
                                            @if ($myReports->isNotEmpty())
                                             <div class="mt-3">
                                                <p class="ks-label !text-[10px]">Tes rapports déposés</p>
                                                <div class="mt-1.5 space-y-1.5">
                                                    @foreach ($myReports as $report)
                                                    <div class="flex items-center gap-2 text-xs text-[#6e6862] dark:text-[#a89f93]">
                                                        <svg class="h-3.5 w-3.5 shrink-0 text-[#15803d]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $report->report_date->isoFormat('DD MMM') }}</span><span>·</span><span class="font-semibold tabular-nums text-slate-800 dark:text-slate-200">{{ $report->task_progress_percent ?? 0 }}%</span><span>·</span><span class="truncate">{{ $report->summary ?: 'Rapport déposé' }}</span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-1 gap-x-4 gap-y-1 text-xs text-[#6e6862] dark:text-[#a89f93] shrink-0 md:text-right md:min-w-[170px]">
                                            <div>Priorité : <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ ['high'=>'Haute','low'=>'Basse','medium'=>'Normale','urgent'=>'Urgente'][$task->priority] ?? ucfirst($task->priority ?? 'Normale') }}</span></div>
                                            <div>Démarré le : <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] tabular-nums">{{ $task->started_at?->format('d/m/Y') ?: ($task->created_at?->format('d/m/Y') ?: '--') }}</span></div>
                                            <div class="col-span-2 md:col-span-1">date de fin : <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] tabular-nums">{{ $task->due_date?->format('d/m/Y') ?: 'Non définie' }}</span></div>
                                            <div class="col-span-2 md:col-span-1 mt-1 inline-flex items-center gap-1 font-semibold text-[#1c3e96] dark:text-[#60a5fa] md:justify-end">Ouvrir l'espace de travail <span aria-hidden="true">→</span></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Colonne Droite : Cadre du Collaborateur (Col-span-4) --}}
            <div class="lg:col-span-4 space-y-3">

                {{-- Fiche Cadre & Raccourcis --}}
                <div class="ks-sec">
                    <div class="ks-sec-head">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc]">Cadre de travail</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-[#eef1fb] dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa]">
                            {{ $user->roles->first()?->name === 'employe' ? 'Collaborateur' : ucfirst($user->roles->first()?->name ?? 'Employé') }}
                        </span>
                    </div>
                    <div class="p-3 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Domaine</span>
                            <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $user->domaine?->nom ?? 'Non défini' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Poste</span>
                            <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $user->personnel?->personnable?->poste ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Site de rattachement</span>
                            <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $user->personnel?->personnable?->site?->name ?? 'Site principal' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Télétravail autorisé</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $user->canWorkRemotely() ? 'bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/20 dark:text-[#4ade80]' : 'bg-[#f1f0ed] text-[#6e6862] dark:bg-[#1f232d] dark:text-[#94a3b8]' }}">
                                {{ $user->canWorkRemotely() ? 'Oui' : 'Non' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Accès directs --}}
                <div class="ks-sec">
                    <div class="ks-sec-head">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc]">Actions rapides</span>
                    </div>
                    <div class="p-2 space-y-1">
                        <a href="{{ route('presence.pointage') }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-[#ecfdf5] dark:bg-[#15803d]/20 text-[#15803d] dark:text-[#4ade80] flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span>Pointer ma présence</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-[#a89f93]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>

                        <a href="{{ route('presence.historique') }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                                </div>
                                <span>Historique des pointages</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-[#a89f93]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>

                        <a href="{{ route('permissions.index') }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-[#faf5ff] dark:bg-[#7e22ce]/20 text-[#7e22ce] dark:text-[#c084fc] flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                </div>
                                <span>Demandes de permission</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-[#a89f93]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>