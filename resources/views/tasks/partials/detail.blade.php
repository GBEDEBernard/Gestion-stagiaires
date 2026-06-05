@php
    $user = auth()->user();
    $isOwner = $task->owner_id === $user->id;
    $isAdmin = $user->hasRole('admin');
    $isReviewer = $user->hasAnyRole(['admin', 'superviseur']) && !$isOwner;
    $canMessage = $isOwner || $isReviewer;
    $state = $thread['task']['discussion_state'] ?? $task->discussionState();

    $points = $task->dailyReports->reverse()
        ->filter(fn($r) => !is_null($r->task_progress_percent))
        ->map(fn($r) => (int) $r->task_progress_percent)->values();

    $cfg = [
        'threadUrl' => encrypted_route('tasks.thread', $task),
        'storeUrl'  => encrypted_route('tasks.messages.store', $task),
        'readUrl'   => encrypted_route('tasks.read', $task),
        'typingUrl' => encrypted_route('tasks.typing', $task),
        'emojis'    => \App\Http\Controllers\TaskMessageController::ALLOWED_EMOJIS,
    ];
@endphp

@php
    $pct = (int) $task->last_progress_percent;
    $ringR = 26; $ringC = 2 * M_PI * $ringR; $ringOff = $ringC * (1 - min(100, max(0, $pct)) / 100);
    $accent = match($task->priority) {
        'urgent' => 'from-rose-500 via-red-500 to-orange-400',
        'high'   => 'from-orange-400 to-amber-400',
        'low'    => 'from-slate-300 to-slate-400 dark:from-slate-600 dark:to-slate-500',
        default  => 'from-indigo-500 via-violet-500 to-sky-400',
    };
@endphp

<div class="space-y-6" x-data="taskChat(@js($thread), @js($cfg))" x-init="init()">

    {{-- ============================= EN-TÊTE TÂCHE ============================= --}}
    <section class="relative overflow-hidden rounded-3xl border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 shadow-sm transition-shadow hover:shadow-md">
        {{-- Liseré d'accent (couleur = priorité) --}}
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $accent }}"></div>

        <div class="p-6 sm:p-7 flex items-start gap-5">
            {{-- Anneau de progression (desktop) --}}
            <div class="relative hidden sm:flex flex-shrink-0 w-[72px] h-[72px] items-center justify-center">
                <svg class="w-[72px] h-[72px] -rotate-90" viewBox="0 0 64 64">
                    <defs>
                        <linearGradient id="ring{{ $task->id }}" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#10b981"/>
                        </linearGradient>
                    </defs>
                    <circle cx="32" cy="32" r="26" fill="none" stroke-width="6" class="stroke-slate-100 dark:stroke-slate-700/60"/>
                    <circle cx="32" cy="32" r="26" fill="none" stroke-width="6" stroke-linecap="round"
                            stroke="url(#ring{{ $task->id }})"
                            stroke-dasharray="{{ round($ringC, 2) }}" stroke-dashoffset="{{ round($ringOff, 2) }}"
                            style="transition:stroke-dashoffset .7s cubic-bezier(.4,0,.2,1);"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-base font-bold tabular-nums text-slate-900 dark:text-white">{{ $pct }}<span class="text-[10px] font-medium text-slate-400 align-top">%</span></span>
                </div>
            </div>

            {{-- Bloc central --}}
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <x-task-status-badge :status="$task->status" />
                    <x-priority-dot :priority="$task->priority" with-label />
                    @if($task->isOverdue())
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-300">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        En retard
                    </span>
                    @endif
                    @if($state === 'closed')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 dark:bg-slate-700/50 dark:text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 11h14v10H5z"/></svg>
                        Clôturée
                    </span>
                    @endif
                </div>

                <h2 class="text-xl sm:text-2xl font-semibold tracking-tight text-slate-900 dark:text-white leading-tight">{{ $task->title }}</h2>

                <div class="mt-2.5 flex items-center gap-x-3 gap-y-1 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-600 dark:text-slate-300">
                        <x-avatar :name="$task->owner?->name ?? '?'" size="xs" />
                        {{ $task->owner?->name }}
                    </span>
                    @if($task->due_date)
                    <span class="inline-flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-500 dark:text-red-400 font-medium' : '' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        {{ $task->due_date->translatedFormat('d M Y') }}
                    </span>
                    @endif
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                        {{ $task->dailyReports->count() }} rapport{{ $task->dailyReports->count() > 1 ? 's' : '' }}
                    </span>
                    @if($points->count() >= 2)
                    @php
                        $w=84;$h=20;$n=$points->count();
                        $coords=$points->map(fn($p,$i)=>round($n>1?$i*($w/($n-1)):0,1).','.round($h-($p/100*$h),1))->implode(' ');
                        $area='0,'.$h.' '.$coords.' '.$w.','.$h;
                    @endphp
                    <span class="inline-flex items-center gap-1.5" title="Tendance de progression">
                        <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-[72px] h-5" preserveAspectRatio="none">
                            <defs><linearGradient id="spark{{ $task->id }}" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#10b981" stop-opacity=".3"/><stop offset="100%" stop-color="#10b981" stop-opacity="0"/></linearGradient></defs>
                            <polygon points="{{ $area }}" fill="url(#spark{{ $task->id }})"/>
                            <polyline points="{{ $coords }}" fill="none" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    @endif
                </div>
            </div>

            {{-- Actions : primaire contextuelle + menu ⋯ --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($isAdmin && $task->isCompleted())
                    <form method="POST" action="{{ encrypted_route('tasks.reopen', $task) }}">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h12a1.5 1.5 0 011.5 1.5v6a1.5 1.5 0 01-1.5 1.5h-12a1.5 1.5 0 01-1.5-1.5v-6a1.5 1.5 0 011.5-1.5z"/></svg>
                            Rouvrir
                        </button>
                    </form>
                @elseif($isAdmin)
                    <form method="POST" action="{{ encrypted_route('tasks.complete', $task) }}" onsubmit="return confirm('Clôturer définitivement cette tâche ? La discussion sera fermée.')">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm {{ $task->isAwaitingValidation() ? 'ring-2 ring-emerald-300/60 dark:ring-emerald-500/40 animate-pulse' : '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Terminer
                        </button>
                    </form>
                @elseif($isReviewer && !$task->isCompleted())
                    <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">
                        @csrf <input type="hidden" name="action" value="request_changes">
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.759 6.759 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/></svg>
                            Corrections
                        </button>
                    </form>
                @endif

                @if($isOwner)
                <div class="relative" x-data="{ open:false }" @keydown.escape="open=false">
                    <button @click="open=!open" @click.outside="open=false"
                            class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-700 transition-colors" title="Plus d'actions">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6.75a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM12 13.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM12 20.25a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 mt-1.5 w-48 z-30 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl ring-1 ring-black/5 py-1.5">
                        @unless($task->isCompleted())
                        <a href="{{ encrypted_route('tasks.edit', $task) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.86 4.49l2.65 2.65M3 21l.66-3.6a2 2 0 01.54-1.06L16.3 3.94a1.5 1.5 0 012.12 0l1.64 1.64a1.5 1.5 0 010 2.12L7.72 19.8a2 2 0 01-1.06.54L3 21z"/></svg>
                            Éditer la tâche
                        </a>
                        @endunless
                        <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}" onsubmit="return confirm('Supprimer cette tâche ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.34 9m-4.8 0L9.26 9M19.2 6.2L18 19a2 2 0 01-2 1.8H8A2 2 0 016 19L4.8 6.2M9 6.2V4a1 1 0 011-1h4a1 1 0 011 1v2.2M3.5 6.2h17"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Progression (mobile, l'anneau étant masqué) --}}
        <div class="sm:hidden px-6 -mt-1 pb-1">
            <x-progress-bar :percent="$task->last_progress_percent" />
        </div>

        @if($task->description)
        <div class="px-6 sm:px-7 pb-6 sm:pb-7">
            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line border-t border-slate-100 dark:border-slate-700/60 pt-4">{{ $task->description }}</p>
        </div>
        @endif
    </section>

    {{-- ===================== DÉPÔT DE RAPPORT (propriétaire) ===================== --}}
    @if($isOwner && !$task->isCompleted())
    <section x-data="{ openReport: {{ $state === 'locked' ? 'true' : 'false' }} }"
             class="overflow-hidden rounded-3xl border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 shadow-sm">
        <button type="button" @click="openReport = !openReport"
                class="w-full flex items-center justify-between gap-3 px-6 py-4 text-left transition-colors hover:bg-slate-50/60 dark:hover:bg-slate-700/20">
            <span class="flex items-center gap-3 min-w-0">
                <span class="flex-shrink-0 w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/10 to-emerald-500/10 dark:from-indigo-500/25 dark:to-emerald-500/25 flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-900 dark:text-white">{{ $state === 'locked' ? 'Déposer le premier rapport' : 'Nouveau rapport' }}</span>
                    <span class="block text-[11px] text-slate-400 truncate">{{ $state === 'locked' ? 'Ouvrira la discussion de la tâche' : ($todayReport ? 'Tu as déjà rapporté aujourd’hui' : 'Mets à jour ton avancement du jour') }}</span>
                </span>
            </span>
            @if($state === 'locked')
            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-300">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span> à faire
            </span>
            @endif
            <svg class="w-5 h-5 flex-shrink-0 text-slate-400 transition-transform duration-300" :class="openReport ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
        </button>
        <div x-show="openReport" x-collapse>
            <div class="px-6 pb-6 pt-1">
        @if($todayReport)
        <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 p-4 border border-slate-100 dark:border-slate-700/50">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium text-slate-500">Rapport d'aujourd'hui · {{ (int) $todayReport->task_progress_percent }}%</span>
                <a href="{{ route('reports.edit', $todayReport->id) }}" class="text-xs font-medium text-slate-700 dark:text-slate-300 hover:underline">Modifier</a>
            </div>
            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line">{{ $todayReport->summary }}</p>
        </div>
        @else
        <form method="POST" action="{{ route('reports.store') }}" class="space-y-4"
              x-data="taskReport({ prog: {{ (int) $task->last_progress_percent }} })"
              @submit.prevent="submit($event)">
            @csrf
            <input type="hidden" name="status_action" value="submit">
            <input type="hidden" name="task_id" value="{{ $task->id }}">

            <textarea name="summary" rows="3" placeholder="Ce que tu as accompli aujourd'hui sur cette tâche…"
                x-ref="summary"
                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 resize-none transition"></textarea>

            {{-- Enregistrement vocal (optionnel, remplace le résumé écrit) --}}
            <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 px-3.5 py-2.5">
                {{-- idle --}}
                <template x-if="recorder.state === 'idle'">
                    <button type="button" @click="startRec()" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <span class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/></svg>
                        </span>
                        Enregistrer un rapport vocal
                    </button>
                </template>
                {{-- recording --}}
                <template x-if="recorder.state === 'recording'">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Enregistrement…</span>
                        <span class="text-sm tabular-nums text-slate-400" x-text="fmtDur(recorder.seconds)"></span>
                        <button type="button" @click="stopRec()" class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-white bg-slate-900 dark:bg-white dark:text-slate-900">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                            Stop
                        </button>
                    </div>
                </template>
                {{-- preview --}}
                <template x-if="recorder.state === 'preview'">
                    <div class="flex items-center gap-3">
                        <audio :src="recorder.url" controls class="h-9 flex-1 min-w-0"></audio>
                        <span class="text-xs tabular-nums text-slate-400" x-text="fmtDur(recorder.seconds)"></span>
                        <button type="button" @click="cancelRec()" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" title="Supprimer le vocal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <div>
                <label class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-300 mb-1.5">
                    <span>Progression</span><span class="font-semibold text-slate-900 dark:text-white tabular-nums" x-text="prog + '%'"></span>
                </label>
                <input type="range" name="task_progress_percent" min="0" max="100" step="5" x-model="prog" class="w-full accent-slate-900 dark:accent-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="blockers" placeholder="Blocages (optionnel)"
                    class="px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 transition">
                <input type="number" name="hours_declared" min="0" max="24" step="0.5" placeholder="Heures (optionnel)"
                    class="px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 transition">
            </div>

            <template x-if="error">
                <p class="text-xs text-red-600 dark:text-red-400" x-text="error"></p>
            </template>

            <div class="flex justify-end">
                <button type="submit" :disabled="sending"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!sending" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.27 3.27a.5.5 0 01.67-.6l16.5 8.25a.5.5 0 010 .9L4 20.33a.5.5 0 01-.67-.6L6 12zm0 0h6"/></svg>
                    <svg x-show="sending" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                    {{ $state === 'locked' ? 'Ouvrir la discussion' : 'Envoyer le rapport' }}
                </button>
            </div>
        </form>
        @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ============================== DISCUSSION ============================== --}}
    <div class="rounded-3xl border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 shadow-sm overflow-hidden flex flex-col">

        {{-- En-tête discussion --}}
        <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/30">
            <div class="flex items-center gap-3 min-w-0">
                <span class="flex-shrink-0 w-9 h-9 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 01-2.555-.337A5.97 5.97 0 015.41 20.97a5.97 5.97 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                </span>
                <div class="min-w-0 leading-tight">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Fil de discussion</h3>
                    <template x-if="isOpen">
                        <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400">
                            <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span></span>
                            en direct
                        </span>
                    </template>
                    <template x-if="isClosed"><span class="text-[11px] text-slate-400">clôturée</span></template>
                    <template x-if="isLocked"><span class="text-[11px] text-slate-400">verrouillée</span></template>
                </div>
            </div>
            {{-- Destinataires --}}
            <template x-if="recipients.length">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-400 hidden sm:inline">Destinataires</span>
                    <div class="flex -space-x-2">
                        <template x-for="r in recipients" :key="r.id">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-[10px] font-semibold text-white ring-2 ring-white dark:ring-slate-800 transition-transform hover:-translate-y-0.5"
                                  :style="'background:'+avatarColor(r.name)" :title="r.name" x-text="r.initials"></span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- État VERROUILLÉ : pas encore de rapport --}}
        <template x-if="isLocked">
            <div class="py-16 px-6 text-center">
                <div class="relative mx-auto w-16 h-16 mb-5">
                    <div class="absolute inset-0 rounded-full bg-slate-200 dark:bg-slate-700/40 animate-ping opacity-30" style="animation-duration:2.5s"></div>
                    <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-white dark:from-slate-700/50 dark:to-slate-800 flex items-center justify-center shadow-inner border border-slate-100 dark:border-slate-700">
                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25v-6a2.25 2.25 0 012.25-2.25z"/></svg>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">La discussion s'ouvrira au premier rapport</p>
                <p class="mt-1.5 text-xs text-slate-400 max-w-xs mx-auto leading-relaxed">
                    {{ $isOwner ? 'Dépose ton premier rapport ci-dessus pour démarrer les échanges avec ton encadrant.' : 'En attente du premier rapport du producteur.' }}
                </p>
            </div>
        </template>

        {{-- DISCUSSION OUVERTE OU FERMÉE --}}
        <template x-if="!isLocked">
            <div class="flex flex-col">

                {{-- Rapport épinglé (briefing) --}}
                <template x-if="pinned">
                    <div class="mx-4 mt-4 relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/60 dark:to-slate-800/20 shadow-sm">
                        <div class="absolute left-0 inset-y-0 w-1 bg-gradient-to-b from-indigo-500 to-emerald-500"></div>
                        <div class="pl-5 pr-4 py-4">
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 9V4h1a1 1 0 100-2H7a1 1 0 100 2h1v5a4 4 0 01-2 3.46V14h5v7l1 1 1-1v-7h5v-1.54A4 4 0 0116 9z"/></svg>
                                    Dernier rapport
                                </span>
                                <span class="text-[11px] text-slate-400" x-text="pinned.date_human"></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-xs font-semibold text-white flex-shrink-0 shadow-sm"
                                      :style="'background:'+avatarColor(pinned.author.name)" x-text="pinned.author.initials"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white" x-text="pinned.author.name"></span>
                                        <template x-if="pinned.progress !== null">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[11px] font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300" x-text="pinned.progress + '%'"></span>
                                        </template>
                                    </div>
                                    {{-- Vocal --}}
                                    <template x-if="pinned.is_voice">
                                        <div class="mt-2 voice-bubble flex items-center gap-2.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 px-3 py-2 max-w-xs shadow-sm">
                                            <audio :src="pinned.voice_url" preload="none" x-ref="pinnedAudio"></audio>
                                            <button type="button" @click="toggleAudio($refs.pinnedAudio)" class="w-9 h-9 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </button>
                                            <div class="flex-1 flex items-center gap-0.5 h-6 overflow-hidden">
                                                <template x-for="i in 20" :key="i">
                                                    <span class="w-0.5 rounded-full bg-slate-300 dark:bg-slate-600" :style="'height:'+(25 + Math.round(55 * Math.abs(Math.sin(i * 1.7))))+'%'"></span>
                                                </template>
                                            </div>
                                            <span class="text-[11px] text-slate-400 tabular-nums" x-text="fmtDur(pinned.voice_duration)"></span>
                                        </div>
                                    </template>
                                    {{-- Texte --}}
                                    <template x-if="!pinned.is_voice">
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed" x-text="pinned.summary"></p>
                                    </template>
                                    <template x-if="pinned.blockers">
                                        <p class="mt-2 inline-flex items-start gap-1.5 text-xs text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-2.5 py-1.5">
                                            <svg class="w-3.5 h-3.5 mt-px flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                        <span x-text="pinned.blockers"></span></p>
                                    </template>
                                    <template x-if="canMessage && isOpen">
                                        <button type="button" @click="replyToPinned()" class="mt-2 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4"/></svg>
                                            Répondre
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Fil des messages --}}
                <div x-ref="scroll" class="relative px-4 py-4 space-y-1 overflow-y-auto bg-gradient-to-b from-slate-50/40 via-transparent to-transparent dark:from-slate-900/20" style="height:48vh; min-height:320px;">
                    <template x-for="group in grouped" :key="group.key">
                        <div class="space-y-3">
                            {{-- Séparateur de date (collant) --}}
                            <div class="sticky top-0 z-10 flex items-center justify-center my-3 pointer-events-none">
                                <span class="px-3 py-0.5 rounded-full text-[11px] font-medium text-slate-500 dark:text-slate-300 bg-white/85 dark:bg-slate-800/85 backdrop-blur border border-slate-200/70 dark:border-slate-700/60 shadow-sm" x-text="group.label"></span>
                            </div>

                            <template x-for="m in group.items" :key="m.id">
                                <div>
                                    {{-- Changement de statut (système) --}}
                                    <template x-if="m.type === 'status_change'">
                                        <div class="flex items-center gap-2 text-[11px] text-slate-400 my-1">
                                            <span class="h-px flex-1 bg-slate-100 dark:bg-slate-700/60"></span>
                                            <span class="px-2.5 py-1 rounded-full bg-slate-50 dark:bg-slate-700/30 text-center" x-text="m.body"></span>
                                            <span class="h-px flex-1 bg-slate-100 dark:bg-slate-700/60"></span>
                                        </div>
                                    </template>

                                    {{-- Jalon de rapport (ancien rapport, non épinglé) --}}
                                    <template x-if="m.type === 'report_jalon'">
                                        <div class="flex justify-center my-1">
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-100 dark:border-slate-700/50 text-xs text-slate-500">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 012 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 012-2z"/></svg>
                                                <span x-text="m.body"></span>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Message humain (bulle) --}}
                                    <template x-if="m.type === 'message'">
                                        <div class="group flex gap-2.5" :class="m.mine ? 'flex-row-reverse' : ''">
                                            <template x-if="!m.mine">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[11px] font-semibold text-white flex-shrink-0 self-end"
                                                      :style="'background:'+avatarColor(m.user.name)" x-text="m.user.initials"></span>
                                            </template>
                                            <div class="max-w-[78%] flex flex-col" :class="m.mine ? 'items-end' : 'items-start'">
                                                <div class="flex items-center gap-2 mb-1 px-1" :class="m.mine ? 'flex-row-reverse' : ''">
                                                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400" x-text="m.mine ? 'Vous' : m.user.name"></span>
                                                    <span class="text-[10px] text-slate-400" x-text="m.time"></span>
                                                    <template x-if="m.edited"><span class="text-[10px] text-slate-400 italic">· modifié</span></template>
                                                </div>
                                                {{-- Citation --}}
                                                <template x-if="m.parent">
                                                    <div class="mb-1 max-w-full rounded-lg px-2.5 py-1.5 text-xs border-l-2 bg-slate-50 dark:bg-slate-900/50 border-slate-300 dark:border-slate-600"
                                                         :class="m.mine ? 'self-end' : 'self-start'">
                                                        <span class="block font-medium text-slate-500 dark:text-slate-400" x-text="m.parent.user_name"></span>
                                                        <span class="block text-slate-400 truncate" x-text="m.parent.excerpt"></span>
                                                    </div>
                                                </template>

                                                {{-- Image (hors bulle colorée) --}}
                                                <template x-if="m.attachment && m.attachment.type === 'image'">
                                                    <a :href="m.attachment.url" target="_blank" rel="noopener"
                                                       class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[260px]">
                                                        <img :src="m.attachment.url" :alt="m.attachment.name" loading="lazy" class="block max-h-64 w-full object-cover">
                                                    </a>
                                                </template>

                                                {{-- Bulle : vocal / fichier / texte --}}
                                                <template x-if="(m.attachment && m.attachment.type !== 'image') || m.body">
                                                    <div class="relative px-3.5 py-2 rounded-2xl text-sm leading-relaxed shadow-sm"
                                                         :class="m.mine
                                                            ? 'bg-slate-900 text-white rounded-tr-sm dark:bg-white dark:text-slate-900'
                                                            : 'bg-slate-100 dark:bg-slate-700/60 text-slate-800 dark:text-slate-100 rounded-tl-sm'">

                                                        {{-- Vocal --}}
                                                        <template x-if="m.attachment && m.attachment.type === 'audio'">
                                                            <div class="voice-bubble flex items-center gap-2.5 min-w-[190px] py-0.5">
                                                                <audio :src="m.attachment.url" preload="none"
                                                                       @play="m._playing = true" @pause="m._playing = false"
                                                                       @ended="m._playing = false; m._ap = 0"
                                                                       @timeupdate="m._ap = $event.target.duration ? ($event.target.currentTime / $event.target.duration * 100) : 0"></audio>
                                                                <button type="button" @click="togglePlay($event)"
                                                                        class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                                        :class="m.mine ? 'bg-white/20 dark:bg-slate-900/15' : 'bg-slate-900/10 dark:bg-white/15'">
                                                                    <svg x-show="!m._playing" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                                    <svg x-show="m._playing" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
                                                                </button>
                                                                <div class="flex-1 h-1 rounded-full relative" :class="m.mine ? 'bg-white/25 dark:bg-slate-900/20' : 'bg-slate-300/60 dark:bg-slate-500/40'">
                                                                    <div class="absolute inset-y-0 left-0 rounded-full bg-current opacity-80" :style="'width:'+(m._ap || 0)+'%'"></div>
                                                                </div>
                                                                <span class="text-[11px] tabular-nums opacity-70" x-text="fmtDur(m.attachment.duration)"></span>
                                                            </div>
                                                        </template>

                                                        {{-- Fichier --}}
                                                        <template x-if="m.attachment && m.attachment.type === 'file'">
                                                            <a :href="m.attachment.url" target="_blank" rel="noopener" download class="flex items-center gap-2.5 py-0.5">
                                                                <span class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                                                      :class="m.mine ? 'bg-white/20 dark:bg-slate-900/15' : 'bg-slate-900/10 dark:bg-white/15'">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                                </span>
                                                                <span class="min-w-0">
                                                                    <span class="block font-medium truncate max-w-[180px]" x-text="m.attachment.name"></span>
                                                                    <span class="block text-[11px] opacity-70" x-text="fmtSize(m.attachment.size)"></span>
                                                                </span>
                                                            </a>
                                                        </template>

                                                        {{-- Édition en ligne --}}
                                                        <template x-if="m._editing">
                                                            <div class="flex flex-col gap-1.5 min-w-[200px]">
                                                                <textarea x-model="m._draft" rows="2"
                                                                    @keydown.enter.prevent="saveEdit(m)" @keydown.escape="m._editing=false"
                                                                    class="w-full px-2 py-1.5 text-sm rounded-lg text-slate-900 dark:text-white bg-white/90 dark:bg-slate-900/80 border border-slate-300 dark:border-slate-600 resize-none"></textarea>
                                                                <div class="flex items-center gap-2 justify-end">
                                                                    <button type="button" @click="m._editing=false" class="text-[11px] opacity-70 hover:opacity-100">Annuler</button>
                                                                    <button type="button" @click="saveEdit(m)" class="text-[11px] font-semibold underline">Enregistrer</button>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        {{-- Texte (corps / légende) --}}
                                                        <template x-if="m.body && !m._editing">
                                                            <p class="whitespace-pre-line" :class="(m.attachment ? 'mt-1.5' : '')" x-text="m.body"></p>
                                                        </template>
                                                    </div>
                                                </template>

                                                {{-- Réactions --}}
                                                <template x-if="m.reactions && m.reactions.length">
                                                    <div class="flex flex-wrap gap-1 mt-1" :class="m.mine ? 'justify-end' : ''">
                                                        <template x-for="rx in m.reactions" :key="rx.emoji">
                                                            <button type="button" @click="react(m, rx.emoji)"
                                                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs border transition-colors"
                                                                    :class="rx.mine ? 'bg-slate-900/10 border-slate-300 dark:bg-white/15 dark:border-slate-500' : 'bg-slate-50 border-slate-200 dark:bg-slate-700/40 dark:border-slate-600 hover:bg-slate-100'">
                                                                <span x-text="rx.emoji"></span>
                                                                <span class="tabular-nums opacity-70" x-text="rx.count"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- Barre d'actions (au survol) --}}
                                            <template x-if="isOpen">
                                                <div class="relative self-center flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    {{-- Réagir --}}
                                                    <button type="button" @click="m._showReactions = !m._showReactions"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700" title="Réagir">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>
                                                    </button>
                                                    {{-- Répondre --}}
                                                    <template x-if="canMessage">
                                                        <button type="button" @click="setReply(m)" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700" title="Répondre">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4"/></svg>
                                                        </button>
                                                    </template>
                                                    {{-- Éditer (auteur, texte) --}}
                                                    <template x-if="m.mine && m.body && !(m.attachment)">
                                                        <button type="button" @click="startEdit(m)" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700" title="Modifier">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.86 4.49l2.65 2.65M3 21l.66-3.6a2 2 0 01.54-1.06L16.3 3.94a1.5 1.5 0 012.12 0l1.64 1.64a1.5 1.5 0 010 2.12L7.72 19.8a2 2 0 01-1.06.54L3 21z"/></svg>
                                                        </button>
                                                    </template>
                                                    {{-- Supprimer (auteur ou admin) --}}
                                                    <template x-if="m.mine || isAdmin">
                                                        <button type="button" @click="remove(m)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" title="Supprimer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.34 9m-4.8 0L9.26 9M19.2 6.2L18 19a2 2 0 01-2 1.8H8A2 2 0 016 19L4.8 6.2M9 6.2V4a1 1 0 011-1h4a1 1 0 011 1v2.2M3.5 6.2h17"/></svg>
                                                        </button>
                                                    </template>

                                                    {{-- Popover emojis --}}
                                                    <template x-if="m._showReactions">
                                                        <div @click.outside="m._showReactions = false"
                                                             class="absolute z-20 bottom-full mb-1 flex items-center gap-0.5 px-1.5 py-1 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg"
                                                             :class="m.mine ? 'right-0' : 'left-0'">
                                                            <template x-for="e in emojis" :key="e">
                                                                <button type="button" @click="react(m, e); m._showReactions = false"
                                                                        class="w-7 h-7 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-base leading-none" x-text="e"></button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!messages.length">
                        <p class="text-sm text-slate-400 py-8 text-center">Aucun message pour l'instant.</p>
                    </template>
                </div>

                {{-- Composer / Bannière fermée --}}
                <template x-if="isClosed">
                    <div class="px-5 py-5 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/30">
                        <div class="flex items-center justify-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-slate-200/70 dark:bg-slate-700/60 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25v-6a2.25 2.25 0 012.25-2.25z"/></svg>
                            </span>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                <span class="font-semibold text-slate-600 dark:text-slate-300">Discussion clôturée.</span>
                                {{ $isAdmin ? 'Tu peux la rouvrir depuis l\'en-tête.' : 'Seul un administrateur peut la rouvrir.' }}
                            </p>
                        </div>
                    </div>
                </template>

                @if($canMessage)
                <template x-if="isOpen">
                    <div class="border-t border-slate-100 dark:border-slate-700/60 px-4 py-3">
                        {{-- Indicateur de saisie (temps réel) --}}
                        <template x-if="typingUser">
                            <div class="flex items-center gap-1.5 mb-1.5 px-1 text-[11px] text-slate-400">
                                <span class="flex gap-0.5">
                                    <span class="w-1 h-1 rounded-full bg-slate-400 animate-bounce" style="animation-delay:0ms"></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-400 animate-bounce" style="animation-delay:150ms"></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-400 animate-bounce" style="animation-delay:300ms"></span>
                                </span>
                                <span x-text="typingUser + ' écrit…'"></span>
                            </div>
                        </template>

                        {{-- Barre de réponse citée --}}
                        <template x-if="replyTo">
                            <div class="flex items-center gap-2 mb-2 rounded-lg bg-slate-100 dark:bg-slate-700/40 px-3 py-2 border-l-2 border-slate-900 dark:border-white">
                                <div class="min-w-0 flex-1">
                                    <span class="block text-[11px] font-medium text-slate-500" x-text="'Réponse à ' + replyTo.user_name"></span>
                                    <span class="block text-xs text-slate-400 truncate" x-text="replyTo.excerpt"></span>
                                </div>
                                <button type="button" @click="clearReply()" class="p-1 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>

                        {{-- Barre d'enregistrement vocal (recording / preview) --}}
                        <template x-if="recorder.state === 'recording'">
                            <div class="flex items-center gap-3 mb-2 rounded-xl bg-red-50 dark:bg-red-900/15 px-3 py-2.5">
                                <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                                <span class="text-sm font-medium text-red-700 dark:text-red-300">Enregistrement…</span>
                                <span class="text-sm tabular-nums text-red-500/80" x-text="fmtDur(recorder.seconds)"></span>
                                <div class="ml-auto flex items-center gap-2">
                                    <button type="button" @click="cancelRec()" class="px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-white dark:hover:bg-slate-800">Annuler</button>
                                    <button type="button" @click="stopAndSendVoice()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-white bg-slate-900 dark:bg-white dark:text-slate-900">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.27 3.27a.5.5 0 01.67-.6l16.5 8.25a.5.5 0 010 .9L4 20.33a.5.5 0 01-.67-.6L6 12zm0 0h6"/></svg>
                                        Envoyer
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Champs cachés (image / fichier) --}}
                        <input type="file" x-ref="imageInput" accept="image/*" class="hidden" @change="sendFile($event.target, 'image')">
                        <input type="file" x-ref="fileInput" class="hidden" @change="sendFile($event.target, 'file')">

                        <form @submit.prevent="send()" x-show="recorder.state !== 'recording'"
                              class="flex items-end gap-1 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-1.5 shadow-sm transition focus-within:ring-2 focus-within:ring-slate-900/10 dark:focus-within:ring-white/10 focus-within:border-slate-400 dark:focus-within:border-slate-500">
                            {{-- Joindre image --}}
                            <button type="button" @click="$refs.imageInput.click()" :disabled="sending"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex-shrink-0 disabled:opacity-40" title="Joindre une image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 9.75h.008v.008H18V9.75zM2.25 6.75v10.5a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25z"/></svg>
                            </button>
                            {{-- Joindre fichier --}}
                            <button type="button" @click="$refs.fileInput.click()" :disabled="sending"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex-shrink-0 disabled:opacity-40" title="Joindre un fichier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                            </button>

                            <textarea x-ref="input" x-model="body" rows="1" placeholder="Écrire un message…"
                                @input="typingPing()"
                                @keydown.enter="if(!$event.shiftKey){ $event.preventDefault(); send() }"
                                class="flex-1 px-2 py-2 text-sm bg-transparent border-0 focus:ring-0 text-slate-900 dark:text-white placeholder:text-slate-400 resize-none transition max-h-32"></textarea>

                            {{-- Micro (si pas de texte) ou Envoyer --}}
                            <template x-if="!body.trim()">
                                <button type="button" @click="startRec()" :disabled="sending"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:text-white hover:bg-slate-900 dark:hover:bg-white dark:hover:text-slate-900 transition-colors flex-shrink-0 disabled:opacity-40" title="Message vocal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/></svg>
                                </button>
                            </template>
                            <template x-if="body.trim()">
                                <button type="submit" :disabled="sending"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl text-white bg-gradient-to-br from-slate-900 to-slate-700 dark:from-white dark:to-slate-200 dark:text-slate-900 hover:opacity-90 transition flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.27 3.27a.5.5 0 01.67-.6l16.5 8.25a.5.5 0 010 .9L4 20.33a.5.5 0 01-.67-.6L6 12zm0 0h6"/></svg>
                                </button>
                            </template>
                        </form>
                    </div>
                </template>
                @endif
            </div>
        </template>
    </div>
</div>

<script>
    // ---- Enregistreur vocal réutilisable (chat + rapport) ----
    window.voiceRecorder = function () {
        return {
            recorder: { rec: null, chunks: [], state: 'idle', blob: null, url: null, mime: '', seconds: 0, timer: null, stream: null },

            _recSupported() {
                return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia && window.MediaRecorder);
            },
            async startRec() {
                if (!this._recSupported()) { alert("L'enregistrement audio n'est pas supporté par ce navigateur."); return; }
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    let mime = '';
                    if (window.MediaRecorder.isTypeSupported('audio/webm')) mime = 'audio/webm';
                    else if (window.MediaRecorder.isTypeSupported('audio/ogg')) mime = 'audio/ogg';
                    else if (window.MediaRecorder.isTypeSupported('audio/mp4')) mime = 'audio/mp4';
                    const rec = new MediaRecorder(stream, mime ? { mimeType: mime } : undefined);
                    this.recorder.chunks = [];
                    this.recorder.blob = null;
                    rec.ondataavailable = (e) => { if (e.data && e.data.size) this.recorder.chunks.push(e.data); };
                    rec.onstop = () => {
                        const type = (rec.mimeType || mime || 'audio/webm').split(';')[0];
                        const blob = new Blob(this.recorder.chunks, { type });
                        this.recorder.blob = blob;
                        this.recorder.mime = type;
                        if (this.recorder.url) URL.revokeObjectURL(this.recorder.url);
                        this.recorder.url = URL.createObjectURL(blob);
                        this.recorder.state = 'preview';
                        if (this.recorder.stream) this.recorder.stream.getTracks().forEach(t => t.stop());
                        if (this.recorder.timer) { clearInterval(this.recorder.timer); this.recorder.timer = null; }
                    };
                    this.recorder.stream = stream;
                    this.recorder.rec = rec;
                    this.recorder.state = 'recording';
                    this.recorder.seconds = 0;
                    rec.start();
                    this.recorder.timer = setInterval(() => {
                        this.recorder.seconds++;
                        if (this.recorder.seconds >= 300) this.stopRec(); // garde-fou 5 min
                    }, 1000);
                } catch (e) {
                    alert('Micro indisponible ou permission refusée.');
                    this.cancelRec();
                }
            },
            stopRec() {
                if (this.recorder.rec && this.recorder.state === 'recording') {
                    try { this.recorder.rec.stop(); } catch (e) { /* noop */ }
                }
            },
            cancelRec() {
                if (this.recorder.timer) { clearInterval(this.recorder.timer); this.recorder.timer = null; }
                if (this.recorder.stream) { try { this.recorder.stream.getTracks().forEach(t => t.stop()); } catch (e) {} }
                if (this.recorder.url) URL.revokeObjectURL(this.recorder.url);
                this.recorder = { rec: null, chunks: [], state: 'idle', blob: null, url: null, mime: '', seconds: 0, timer: null, stream: null };
            },
            voiceExt() {
                const m = this.recorder.mime || 'audio/webm';
                if (m.includes('ogg')) return 'ogg';
                if (m.includes('mp4')) return 'm4a';
                return 'webm';
            },
            fmtDur(s) {
                s = parseInt(s || 0, 10);
                const m = Math.floor(s / 60), r = s % 60;
                return m + ':' + (r < 10 ? '0' : '') + r;
            },
        };
    };

    // ---- Formulaire de rapport (avec vocal optionnel) ----
    window.taskReport = function (cfg) {
        return {
            ...window.voiceRecorder(),
            prog: cfg.prog || 0,
            sending: false,
            error: '',
            csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',

            async submit(e) {
                const form = e.target;
                const fd = new FormData(form);
                if (this.recorder.blob) {
                    fd.append('voice', this.recorder.blob, 'report-voice.' + this.voiceExt());
                    fd.append('voice_duration', this.recorder.seconds);
                }
                const hasSummary = (fd.get('summary') || '').toString().trim().length > 0;
                if (!this.recorder.blob && !hasSummary) {
                    this.error = 'Ajoute un résumé écrit ou enregistre un message vocal.';
                    return;
                }
                this.sending = true;
                this.error = '';
                try {
                    const r = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: fd,
                    });
                    if (r.ok || r.redirected) { window.location.reload(); return; }
                    if (r.status === 422) {
                        const d = await r.json().catch(() => ({}));
                        this.error = (Object.values(d.errors || {}).flat()[0]) || d.message || 'Validation échouée.';
                    } else {
                        this.error = "Erreur lors de l'envoi du rapport.";
                    }
                } catch (e) {
                    this.error = 'Réseau indisponible, réessaie.';
                } finally {
                    this.sending = false;
                }
            },
        };
    };

    // ---- Composant principal : chat de la tâche ----
    window.taskChat = function (initial, cfg) {
        return {
            ...window.voiceRecorder(),
            payload: initial || { task: {}, messages: [], recipients: [], pinned_report: null, me: {} },
            cfg: cfg,
            body: '',
            replyTo: null,
            sending: false,
            pollTimer: null,
            typingUser: null,
            _typingTimer: null,
            _lastTypingSent: 0,
            emojis: cfg.emojis || ['👍', '❤️', '😂', '🎉', '🙏', '👏', '🔥', '✅', '👀', '😮'],
            isAdmin: @js($isAdmin),
            csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',

            // ---- État dérivé ----
            get state() { return this.payload.task?.discussion_state || 'locked'; },
            get isOpen() { return this.state === 'open'; },
            get isClosed() { return this.state === 'closed'; },
            get isLocked() { return this.state === 'locked'; },
            get pinned() { return this.payload.pinned_report; },
            get recipients() { return this.payload.recipients || []; },
            get messages() {
                const pinnedId = this.pinned ? this.pinned.id : null;
                // On masque le jalon du rapport déjà épinglé (évite le doublon).
                return (this.payload.messages || []).filter(m =>
                    !(m.type === 'report_jalon' && pinnedId && m.daily_report_id === pinnedId)
                );
            },
            get grouped() {
                const out = [];
                let cur = null;
                for (const m of this.messages) {
                    if (!cur || cur.key !== m.day_key) {
                        cur = { key: m.day_key, label: m.day_label, items: [] };
                        out.push(cur);
                    }
                    cur.items.push(m);
                }
                return out;
            },

            init() {
                this.$nextTick(() => this.scrollBottom());
                this.startPolling();
                this.setupEcho();
                this.markRead();
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) this.refresh();
                });
            },

            setupEcho() {
                // Phase 3: Echo real-time subscriptions (with polling fallback).
                // window.Echo is null when no WS server is configured/reachable.
                if (!window.Echo) return;

                const taskId = this.payload.task?.id;
                if (!taskId) return;

                try {
                    window.Echo.private(`task.${taskId}`)
                        .listen('message.created', () => {
                            this.typingUser = null;
                            this.refresh();
                        })
                        .listen('reaction.added', () => {
                            this.refresh();
                        })
                        .listen('message.read', () => {
                            this.refresh();
                        })
                        .listen('user.typing', (event) => {
                            if (event && event.user_id === this.payload.me?.id) return;
                            this.typingUser = event?.user_name || null;
                            clearTimeout(this._typingTimer);
                            this._typingTimer = setTimeout(() => { this.typingUser = null; }, 3500);
                        });
                } catch (e) {
                    // Echo indisponible (Reverb down, ngrok…) : le polling prend le relais.
                }
            },

            startPolling() {
                // Polling de secours (Phase 3 : remplacé/complété par Echo).
                this.pollTimer = setInterval(() => {
                    if (!document.hidden) this.refresh();
                }, 4000);
            },

            async refresh() {
                try {
                    const r = await fetch(this.cfg.threadUrl, { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const data = await r.json();
                    this.merge(data);
                } catch (e) { /* hors-ligne : on réessaiera */ }
            },

            merge(data) {
                const wasAtBottom = this.isAtBottom();
                const prevLast = this.lastId();

                // Préserve l'état d'UI éphémère (édition / picker / lecture) au refresh.
                const ui = {};
                for (const m of (this.payload.messages || [])) {
                    if (m._editing || m._showReactions || m._playing || m._ap) {
                        ui[m.id] = { _editing: m._editing, _draft: m._draft, _showReactions: m._showReactions, _playing: m._playing, _ap: m._ap };
                    }
                }

                this.payload = data;

                for (const m of (this.payload.messages || [])) {
                    if (ui[m.id]) Object.assign(m, ui[m.id]);
                }

                this.$nextTick(() => {
                    if (wasAtBottom || this.lastId() !== prevLast) this.scrollBottom();
                });
                if (this.lastId() > prevLast) this.markRead();
            },

            // Envoi générique (texte / vocal / fichier) — centralise CSRF, parent_id, scroll.
            async postMessage(fd) {
                if (this.sending || !this.isOpen) return false;
                this.sending = true;
                fd.append('ajax', '1');
                if (this.replyTo && this.replyTo.id) fd.append('parent_id', this.replyTo.id);
                try {
                    const r = await fetch(this.cfg.storeUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: fd,
                    });
                    if (r.ok) {
                        const data = await r.json();
                        this.payload.messages.push(data.message);
                        this.payload.task.last_message_id = data.last_message_id;
                        this.replyTo = null;
                        this.$nextTick(() => this.scrollBottom());
                        this.markRead();
                        return true;
                    }
                } catch (e) { /* silencieux */ }
                finally { this.sending = false; }
                return false;
            },

            async send() {
                const text = this.body.trim();
                if (!text || this.sending || !this.isOpen) return;
                const fd = new FormData();
                fd.append('body', text);
                if (await this.postMessage(fd)) this.body = '';
            },

            // Vocal : stoppe l'enregistrement, attend le blob puis envoie.
            async stopAndSendVoice() {
                this.stopRec();
                for (let i = 0; i < 50 && !this.recorder.blob; i++) {
                    await new Promise(r => setTimeout(r, 40));
                }
                await this.sendVoice();
            },
            async sendVoice() {
                if (!this.recorder.blob) { this.cancelRec(); return; }
                const fd = new FormData();
                fd.append('attachment', this.recorder.blob, 'voice.' + this.voiceExt());
                fd.append('attachment_type', 'audio');
                fd.append('attachment_duration', this.recorder.seconds);
                await this.postMessage(fd);
                this.cancelRec();
            },
            async sendFile(input, type) {
                const file = input.files && input.files[0];
                input.value = '';
                if (!file) return;
                if (file.size > 10 * 1024 * 1024) { alert('Fichier trop volumineux (max 10 Mo).'); return; }
                const fd = new FormData();
                fd.append('attachment', file);
                fd.append('attachment_type', type);
                await this.postMessage(fd);
            },

            // Réaction emoji (toggle).
            async react(m, emoji) {
                try {
                    const r = await fetch(this.cfg.storeUrl + '/' + m.id + '/react', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ emoji }),
                    });
                    if (r.ok) {
                        const data = await r.json();
                        m.reactions = data.reactions;
                    }
                } catch (e) { /* silencieux */ }
                m._showReactions = false;
            },

            // Édition de message.
            startEdit(m) {
                (this.payload.messages || []).forEach(x => { if (x !== m) x._editing = false; });
                m._draft = m.body || '';
                m._editing = true;
                m._showReactions = false;
                this.$nextTick(() => {});
            },
            async saveEdit(m) {
                const body = (m._draft || '').trim();
                if (!body) return;
                try {
                    const r = await fetch(this.cfg.storeUrl + '/' + m.id, {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ body }),
                    });
                    if (r.ok) {
                        const data = await r.json();
                        Object.assign(m, data.message);
                        m._editing = false;
                    }
                } catch (e) { /* silencieux */ }
            },
            async remove(m) {
                if (!confirm('Supprimer ce message ?')) return;
                try {
                    const r = await fetch(this.cfg.storeUrl + '/' + m.id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    });
                    if (r.ok) {
                        this.payload.messages = (this.payload.messages || []).filter(x => x.id !== m.id);
                    }
                } catch (e) { /* silencieux */ }
            },

            // Lecture audio dans une bulle.
            togglePlay(e) {
                const wrap = e.currentTarget.closest('.voice-bubble');
                const audio = wrap && wrap.querySelector('audio');
                if (!audio) return;
                if (audio.paused) {
                    document.querySelectorAll('.voice-bubble audio').forEach(a => { if (a !== audio) a.pause(); });
                    audio.play();
                } else {
                    audio.pause();
                }
            },

            // Signale la saisie (limité à 1 ping / 2,5 s).
            typingPing() {
                if (!this.isOpen || !this.cfg.typingUrl) return;
                const now = Date.now();
                if (now - this._lastTypingSent < 2500) return;
                this._lastTypingSent = now;
                fetch(this.cfg.typingUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                }).catch(() => {});
            },

            setReply(m) {
                if (!m || m.is_system) return;
                this.replyTo = { id: m.id, user_name: m.mine ? 'Vous' : m.user.name, excerpt: this.excerptOf(m) };
                this.$nextTick(() => this.$refs.input?.focus());
            },
            replyToPinned() {
                if (!this.pinned) return;
                const jalon = (this.payload.messages || []).find(m => m.type === 'report_jalon' && m.daily_report_id === this.pinned.id);
                this.replyTo = {
                    id: jalon ? jalon.id : null,
                    user_name: this.pinned.author.name,
                    excerpt: this.pinned.is_voice ? '🎤 Message vocal' : (this.pinned.summary || 'Rapport'),
                };
                this.$nextTick(() => this.$refs.input?.focus());
            },
            clearReply() { this.replyTo = null; },

            excerptOf(m) {
                if (m.attachment && m.attachment.type === 'audio') return '🎤 Message vocal';
                if (m.attachment && m.attachment.type === 'image') return '🖼️ Photo';
                if (m.attachment && m.attachment.type === 'file') return '📎 ' + (m.attachment.name || 'Fichier');
                return (m.body || '').slice(0, 80);
            },

            async markRead() {
                const last = this.lastId();
                if (!last) return;
                try {
                    await fetch(this.cfg.readUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'up_to=' + last,
                    });
                } catch (e) { /* silencieux */ }
            },

            // ---- utilitaires ----
            lastId() {
                const m = this.payload.messages || [];
                return m.length ? m[m.length - 1].id : 0;
            },
            scrollBottom() {
                const el = this.$refs.scroll;
                if (el) el.scrollTop = el.scrollHeight;
            },
            isAtBottom() {
                const el = this.$refs.scroll;
                if (!el) return true;
                return (el.scrollHeight - el.scrollTop - el.clientHeight) < 80;
            },
            avatarColor(name) {
                name = name || '?';
                let h = 0;
                for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) % 360;
                return 'hsl(' + h + ' 55% 48%)';
            },
            // fmtDur() est fourni par le mixin voiceRecorder (spread ci-dessus).
            fmtSize(b) {
                b = parseInt(b || 0, 10);
                if (b < 1024) return b + ' o';
                if (b < 1024 * 1024) return Math.round(b / 1024) + ' Ko';
                return (b / 1024 / 1024).toFixed(1) + ' Mo';
            },
            toggleAudio(el) {
                if (!el) return;
                if (el.paused) el.play(); else el.pause();
            },

            // Exposé au template Blade (rôles).
            canMessage: @js($canMessage),
        };
    };
</script>
