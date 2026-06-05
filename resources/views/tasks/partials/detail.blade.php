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

<div class="space-y-5" x-data="taskChat(@js($thread), @js($cfg))" x-init="init()">

    {{-- ============================= EN-TÊTE TÂCHE ============================= --}}
    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-2.5 flex-wrap">
                    <x-task-status-badge :status="$task->status" />
                    <x-priority-dot :priority="$task->priority" with-label />
                    @if($task->isOverdue())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-300">En retard</span>
                    @endif
                    @if($state === 'closed')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-slate-700/50 dark:text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 11h14v10H5z"/></svg>
                        Discussion fermée
                    </span>
                    @endif
                </div>
                <h2 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $task->title }}</h2>
                <div class="mt-2 flex items-center gap-2.5 text-xs text-slate-400 flex-wrap">
                    <span class="inline-flex items-center gap-1.5">
                        <x-avatar :name="$task->owner?->name ?? '?'" size="xs" />
                        {{ $task->owner?->name }}
                    </span>
                    @if($task->due_date)<span>· échéance {{ $task->due_date->format('d/m/Y') }}</span>@endif
                    <span>· {{ $task->dailyReports->count() }} rapport(s)</span>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Admin : clôturer / rouvrir --}}
                @if($isAdmin)
                    @if($task->isCompleted())
                    <form method="POST" action="{{ encrypted_route('tasks.reopen', $task) }}">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h12a1.5 1.5 0 011.5 1.5v6a1.5 1.5 0 01-1.5 1.5h-12a1.5 1.5 0 01-1.5-1.5v-6a1.5 1.5 0 011.5-1.5z"/></svg>
                            Rouvrir
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ encrypted_route('tasks.complete', $task) }}" onsubmit="return confirm('Clôturer définitivement cette tâche ? La discussion sera fermée.')">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm {{ $task->isAwaitingValidation() ? 'ring-2 ring-emerald-200 dark:ring-emerald-900/40' : '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Terminer la tâche
                        </button>
                    </form>
                    @endif
                @elseif($isReviewer && !$task->isCompleted())
                <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">
                    @csrf <input type="hidden" name="action" value="request_changes">
                    <button class="px-3 py-2 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 transition-colors">Corrections</button>
                </form>
                @endif

                {{-- Propriétaire : éditer / supprimer --}}
                @if($isOwner)
                    @unless($task->isCompleted())
                    <a href="{{ encrypted_route('tasks.edit', $task) }}" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-700 transition-colors" title="Éditer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.86 4.49l2.65 2.65M3 21l.66-3.6a2 2 0 01.54-1.06L16.3 3.94a1.5 1.5 0 012.12 0l1.64 1.64a1.5 1.5 0 010 2.12L7.72 19.8a2 2 0 01-1.06.54L3 21z"/></svg>
                    </a>
                    @endunless
                    <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}" onsubmit="return confirm('Supprimer cette tâche ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 transition-colors" title="Supprimer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.34 9m-4.8 0L9.26 9M19.2 6.2L18 19a2 2 0 01-2 1.8H8A2 2 0 016 19L4.8 6.2M9 6.2V4a1 1 0 011-1h4a1 1 0 011 1v2.2M3.5 6.2h17"/></svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Progression --}}
        <div class="mt-5 flex items-center gap-4">
            <div class="flex-1"><x-progress-bar :percent="$task->last_progress_percent" /></div>
            @if($points->count() >= 2)
            @php
                $w=120;$h=28;$n=$points->count();
                $coords=$points->map(fn($p,$i)=>round($n>1?$i*($w/($n-1)):0,1).','.round($h-($p/100*$h),1))->implode(' ');
            @endphp
            <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-28 h-7 text-emerald-500 flex-shrink-0" preserveAspectRatio="none">
                <polyline points="{{ $coords }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            @endif
        </div>

        @if($task->description)
        <p class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700/60 text-sm leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $task->description }}</p>
        @endif
    </div>

    {{-- ===================== DÉPÔT DE RAPPORT (propriétaire) ===================== --}}
    @if($isOwner && !$task->isCompleted())
    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                {{ $state === 'locked' ? 'Premier rapport' : 'Nouveau rapport' }}
            </h3>
            @if($state === 'locked')
            <span class="text-[11px] text-slate-400">Déposer le 1er rapport ouvrira la discussion</span>
            @endif
        </div>

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
    @endif

    {{-- ============================== DISCUSSION ============================== --}}
    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm overflow-hidden flex flex-col">

        {{-- En-tête discussion --}}
        <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100 dark:border-slate-700/60">
            <div class="flex items-center gap-2.5">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Discussion</h3>
                <template x-if="isOpen">
                    <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400">
                        <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span></span>
                        en direct
                    </span>
                </template>
            </div>
            {{-- Destinataires --}}
            <template x-if="recipients.length">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-400 hidden sm:inline">Destinataires</span>
                    <div class="flex -space-x-2">
                        <template x-for="r in recipients" :key="r.id">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-[10px] font-semibold text-white ring-2 ring-white dark:ring-slate-800"
                                  :style="'background:'+avatarColor(r.name)" :title="r.name" x-text="r.initials"></span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- État VERROUILLÉ : pas encore de rapport --}}
        <template x-if="isLocked">
            <div class="py-16 px-6 text-center">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700/40 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 11h14v10H5z"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">La discussion s'ouvrira au premier rapport</p>
                <p class="mt-1 text-xs text-slate-400">
                    {{ $isOwner ? 'Dépose ton premier rapport ci-dessus pour démarrer les échanges.' : 'En attente du premier rapport du producteur.' }}
                </p>
            </div>
        </template>

        {{-- DISCUSSION OUVERTE OU FERMÉE --}}
        <template x-if="!isLocked">
            <div class="flex flex-col">

                {{-- Rapport épinglé --}}
                <template x-if="pinned">
                    <div class="mx-4 mt-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-slate-50 to-white dark:from-slate-800 dark:to-slate-800/40 overflow-hidden">
                        <div class="border-l-[3px] border-slate-900 dark:border-white px-4 py-3.5">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 9V4h1a1 1 0 100-2H7a1 1 0 100 2h1v5a4 4 0 01-2 3.46V14h5v7l1 1 1-1v-7h5v-1.54A4 4 0 0116 9z"/></svg>
                                    Rapport épinglé
                                </span>
                                <span class="text-[11px] text-slate-400" x-text="pinned.date_human"></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-xs font-semibold text-white flex-shrink-0"
                                      :style="'background:'+avatarColor(pinned.author.name)" x-text="pinned.author.initials"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white" x-text="pinned.author.name"></span>
                                        <template x-if="pinned.progress !== null">
                                            <span class="text-xs font-semibold text-emerald-600" x-text="pinned.progress + '%'"></span>
                                        </template>
                                    </div>
                                    {{-- Vocal --}}
                                    <template x-if="pinned.is_voice">
                                        <div class="mt-2 flex items-center gap-2.5 rounded-lg bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 px-3 py-2 max-w-xs">
                                            <audio :src="pinned.voice_url" preload="none" x-ref="pinnedAudio"></audio>
                                            <button type="button" @click="toggleAudio($refs.pinnedAudio)" class="w-8 h-8 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </button>
                                            <div class="flex-1 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                            <span class="text-[11px] text-slate-400 tabular-nums" x-text="fmtDur(pinned.voice_duration)"></span>
                                        </div>
                                    </template>
                                    {{-- Texte --}}
                                    <template x-if="!pinned.is_voice">
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed" x-text="pinned.summary"></p>
                                    </template>
                                    <template x-if="pinned.blockers">
                                        <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">⚠ <span x-text="pinned.blockers"></span></p>
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
                <div x-ref="scroll" class="px-4 py-4 space-y-1 overflow-y-auto" style="height:46vh; min-height:300px;">
                    <template x-for="group in grouped" :key="group.key">
                        <div class="space-y-3">
                            {{-- Séparateur de date --}}
                            <div class="flex items-center justify-center my-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium text-slate-400 bg-slate-100 dark:bg-slate-700/40" x-text="group.label"></span>
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
                                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
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
                    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/30 text-center">
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 11h14v10H5z"/></svg>
                            Discussion clôturée. {{ $isAdmin ? 'Tu peux la rouvrir depuis l\'en-tête.' : 'Seul un administrateur peut la rouvrir.' }}
                        </p>
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

                        <form @submit.prevent="send()" class="flex items-end gap-2" x-show="recorder.state !== 'recording'">
                            {{-- Joindre image --}}
                            <button type="button" @click="$refs.imageInput.click()" :disabled="sending"
                                class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex-shrink-0 disabled:opacity-40" title="Joindre une image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 9.75h.008v.008H18V9.75zM2.25 6.75v10.5a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25z"/></svg>
                            </button>
                            {{-- Joindre fichier --}}
                            <button type="button" @click="$refs.fileInput.click()" :disabled="sending"
                                class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex-shrink-0 disabled:opacity-40" title="Joindre un fichier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                            </button>

                            <textarea x-ref="input" x-model="body" rows="1" placeholder="Écrire un message…"
                                @input="typingPing()"
                                @keydown.enter="if(!$event.shiftKey){ $event.preventDefault(); send() }"
                                class="flex-1 px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 resize-none transition max-h-32"></textarea>

                            {{-- Micro (si pas de texte) ou Envoyer --}}
                            <template x-if="!body.trim()">
                                <button type="button" @click="startRec()" :disabled="sending"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors flex-shrink-0 disabled:opacity-40 shadow-sm" title="Message vocal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/></svg>
                                </button>
                            </template>
                            <template x-if="body.trim()">
                                <button type="submit" :disabled="sending"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
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
