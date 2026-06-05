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
        <form method="POST" action="{{ route('reports.store') }}" class="space-y-4" x-data="{ prog: {{ (int) $task->last_progress_percent }} }">
            @csrf
            <input type="hidden" name="status_action" value="submit">
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <textarea name="summary" rows="3" required placeholder="Ce que tu as accompli aujourd'hui sur cette tâche…"
                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 resize-none transition"></textarea>
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
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.27 3.27a.5.5 0 01.67-.6l16.5 8.25a.5.5 0 010 .9L4 20.33a.5.5 0 01-.67-.6L6 12zm0 0h6"/></svg>
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
                                                </div>
                                                {{-- Citation --}}
                                                <template x-if="m.parent">
                                                    <div class="mb-1 max-w-full rounded-lg px-2.5 py-1.5 text-xs border-l-2 bg-slate-50 dark:bg-slate-900/50 border-slate-300 dark:border-slate-600"
                                                         :class="m.mine ? 'self-end' : 'self-start'">
                                                        <span class="block font-medium text-slate-500 dark:text-slate-400" x-text="m.parent.user_name"></span>
                                                        <span class="block text-slate-400 truncate" x-text="m.parent.excerpt"></span>
                                                    </div>
                                                </template>
                                                <div class="relative px-3.5 py-2 rounded-2xl text-sm whitespace-pre-line leading-relaxed shadow-sm"
                                                     :class="m.mine
                                                        ? 'bg-slate-900 text-white rounded-tr-sm dark:bg-white dark:text-slate-900'
                                                        : 'bg-slate-100 dark:bg-slate-700/60 text-slate-800 dark:text-slate-100 rounded-tl-sm'"
                                                     x-text="m.body"></div>
                                            </div>
                                            {{-- Action répondre (au survol) --}}
                                            <template x-if="canMessage && isOpen">
                                                <button type="button" @click="setReply(m)" class="self-center opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700" title="Répondre">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4"/></svg>
                                                </button>
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

                        <form @submit.prevent="send()" class="flex items-end gap-2">
                            <textarea x-ref="input" x-model="body" rows="1" placeholder="Écrire un message…"
                                @keydown.enter="if(!$event.shiftKey){ $event.preventDefault(); send() }"
                                class="flex-1 px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 resize-none transition max-h-32"></textarea>
                            <button type="submit" :disabled="sending || !body.trim()"
                                class="w-11 h-11 flex items-center justify-center rounded-xl text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.27 3.27a.5.5 0 01.67-.6l16.5 8.25a.5.5 0 010 .9L4 20.33a.5.5 0 01-.67-.6L6 12zm0 0h6"/></svg>
                            </button>
                        </form>
                    </div>
                </template>
                @endif
            </div>
        </template>
    </div>
</div>

<script>
    window.taskChat = function (initial, cfg) {
        return {
            payload: initial || { task: {}, messages: [], recipients: [], pinned_report: null, me: {} },
            cfg: cfg,
            body: '',
            replyTo: null,
            sending: false,
            pollTimer: null,
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
                if (typeof window.Echo === 'undefined') return;

                const taskId = this.payload.task?.id;
                if (!taskId) return;

                try {
                    window.Echo.private(`task.${taskId}`)
                        .listen('message.created', () => {
                            // New message: refresh to get full payload
                            this.refresh();
                        })
                        .listen('reaction.added', () => {
                            // Reaction: refresh to update reactions
                            this.refresh();
                        })
                        .listen('message.read', () => {
                            // Read receipt: subtle update (could be a typing indicator in future)
                            this.refresh();
                        })
                        .listen('user.typing', (event) => {
                            // Future: add typing indicator
                            // console.log(`${event.user_name} is typing...`);
                        });
                } catch (e) {
                    // Echo not available (Reverb down, ngrok issue, etc.)
                    // Polling will still work as fallback.
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
                this.payload = data;
                this.$nextTick(() => {
                    if (wasAtBottom || this.lastId() !== prevLast) this.scrollBottom();
                });
                if (this.lastId() > prevLast) this.markRead();
            },

            async send() {
                const text = this.body.trim();
                if (!text || this.sending || !this.isOpen) return;
                this.sending = true;

                const fd = new FormData();
                fd.append('body', text);
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
                        this.body = '';
                        this.replyTo = null;
                        this.$nextTick(() => this.scrollBottom());
                        this.markRead();
                    }
                } catch (e) { /* silencieux */ }
                finally { this.sending = false; }
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
            fmtDur(s) {
                s = parseInt(s || 0, 10);
                const m = Math.floor(s / 60), r = s % 60;
                return m + ':' + (r < 10 ? '0' : '') + r;
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
