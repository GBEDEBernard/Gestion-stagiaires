<x-app-layout :title="$task->title">

    @php
        $user = auth()->user();
        $isOwner = $task->owner_id === $user->id;
        $isAdmin = $user->hasRole('admin');
        $isReviewer = $user->hasAnyRole(['admin', 'superviseur']) && !$isOwner;
        $canCreate = $user->hasAnyRole(['etudiant', 'employe']);
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap');

        .show-root * { font-family: 'DM Sans', system-ui, sans-serif; }
        .ws-mono { font-family: 'DM Mono', monospace; }

        @keyframes sh-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .sh-1 { animation: sh-in .35s cubic-bezier(.16,1,.3,1) both; }
        .sh-2 { animation: sh-in .35s .06s cubic-bezier(.16,1,.3,1) both; }
        .sh-3 { animation: sh-in .35s .12s cubic-bezier(.16,1,.3,1) both; }
        .sh-4 { animation: sh-in .35s .18s cubic-bezier(.16,1,.3,1) both; }

        @keyframes live-dot {
            0%,100% { opacity:.6; transform:scale(1); }
            50%      { opacity:0;  transform:scale(2); }
        }
        .live-dot { animation: live-dot 1.8s ease-in-out infinite; }

        .sh-card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(0,0,0,.06);
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 6px 24px rgba(0,0,0,.05);
        }

        .sh-input {
            background: rgba(0,0,0,.03);
            border: 1px solid rgba(0,0,0,.07);
            transition: border-color .15s, background .15s, box-shadow .15s;
        }
        .sh-input:focus {
            background: #fff;
            border-color: rgba(0,0,0,.2);
            box-shadow: 0 0 0 3px rgba(0,0,0,.05);
            outline: none;
        }

        .sh-btn {
            background: #0a0a0a; color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.15), 0 4px 14px rgba(0,0,0,.1);
            transition: transform .18s, background .18s;
        }
        .sh-btn:hover { background: #222; transform: translateY(-1px); }
        .sh-btn:disabled { opacity:.35; cursor:not-allowed; transform:none; }

        .sh-btn-ghost {
            background: transparent;
            border: 1px solid rgba(0,0,0,.1);
            color: rgba(0,0,0,.6);
            transition: background .15s;
        }
        .sh-btn-ghost:hover { background: rgba(0,0,0,.04); }

        .sh-btn-ok {
            background: #059669; color: #fff;
            box-shadow: 0 1px 3px rgba(5,150,105,.2), 0 4px 14px rgba(5,150,105,.15);
            transition: transform .18s, background .18s;
        }
        .sh-btn-ok:hover { background: #047857; transform:translateY(-1px); }

        .sh-btn-warn {
            background: rgba(245,158,11,.08); color: #b45309;
            border: 1px solid rgba(245,158,11,.2);
            transition: background .15s;
        }
        .sh-btn-warn:hover { background: rgba(245,158,11,.14); }

        .sh-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,.08) transparent;
        }
        .sh-scroll::-webkit-scrollbar { width: 4px; }
        .sh-scroll::-webkit-scrollbar-track { background: transparent; }
        .sh-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.1); border-radius: 99px; }

        .msg-mine  { background:#0a0a0a; color:#fff; border-radius:16px 16px 4px 16px; }
        .msg-other { background:rgba(0,0,0,.04); color:#0a0a0a; border:1px solid rgba(0,0,0,.06); border-radius:16px 16px 16px 4px; }

        .sh-range { accent-color: #0a0a0a; }

        .line-clamp-2 { display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden; }
    </style>

    <div class="show-root -m-3 sm:-m-4 md:-m-6 min-h-screen" style="background:#f7f7f6;"
         x-data="taskChat(@js($thread), @js($cfg))" x-init="init()">

        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-5 sm:py-8">

            {{-- ── BREADCRUMB ──────────────────────────────── --}}
            <div class="sh-1 mb-5 flex items-center gap-2 text-sm" style="color:rgba(0,0,0,.4);">
                <a href="{{ route('tasks.index') }}" class="transition hover:text-black flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
                    Tâches
                </a>
                <span style="color:rgba(0,0,0,.2);">/</span>
                <span class="truncate max-w-[200px]" style="color:rgba(0,0,0,.6);">{{ $task->title }}</span>
            </div>

            {{-- ── CARD PRINCIPALE ─────────────────────────── --}}
            <div class="sh-card sh-1 rounded-2xl overflow-hidden mb-4">

                {{-- Priority stripe --}}
                @php
                    $stripe = match($task->priority) {
                        'urgent' => 'linear-gradient(90deg,#ef4444,#f97316)',
                        'high'   => 'linear-gradient(90deg,#f59e0b,#ef4444)',
                        'low'    => 'linear-gradient(90deg,rgba(0,0,0,.12),rgba(0,0,0,.2))',
                        default  => 'linear-gradient(90deg,#3b82f6,#10b981)',
                    };
                    $pct = max(0, min(100, (int)$task->last_progress_percent));
                    $ringR = 24; $ringC = 2*M_PI*$ringR;
                    $ringOff = $ringC*(1-($pct/100));
                    $prioMeta = match($task->priority) {
                        'urgent'=>['Urgente','#ef4444'],
                        'high'  =>['Haute','#f59e0b'],
                        'low'   =>['Basse','rgba(0,0,0,.35)'],
                        default =>['Normale','#3b82f6'],
                    };
                    $stateMeta = match($task->discussionState()) {
                        'open'  =>['Discussion ouverte','#10b981'],
                        'closed'=>['Clôturée','rgba(0,0,0,.35)'],
                        default =>['En attente du premier rapport','#f59e0b'],
                    };
                    $reportCount = $task->dailyReports->count();
                @endphp
                <div class="h-[3px]" style="background:{{ $stripe }};"></div>

                <div class="p-5 sm:p-6">
                    {{-- Badges row --}}
                    <div class="flex flex-wrap items-center gap-1.5 mb-4">
                        <x-task-status-badge :status="$task->status" />

                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-medium"
                              style="background:rgba(0,0,0,.04); color:{{ $prioMeta[1] }}; border:1px solid {{ $prioMeta[1] }}25;">
                            {{ $prioMeta[0] }}
                        </span>

                        @if($task->isOverdue())
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-medium"
                              style="background:rgba(239,68,68,.06); color:#dc2626; border:1px solid rgba(239,68,68,.15);">
                            <span class="h-1.5 w-1.5 rounded-full" style="background:#ef4444;"></span>
                            En retard
                        </span>
                        @endif

                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-medium"
                              style="background:rgba(0,0,0,.04); color:{{ $stateMeta[1] }};">
                            @if($task->discussionState()==='open')
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="live-dot absolute h-full w-full rounded-full" style="background:{{ $stateMeta[1] }};"></span>
                                <span class="relative h-1.5 w-1.5 rounded-full" style="background:{{ $stateMeta[1] }};"></span>
                            </span>
                            @else
                            <span class="h-1.5 w-1.5 rounded-full" style="background:{{ $stateMeta[1] }};"></span>
                            @endif
                            {{ $stateMeta[0] }}
                        </span>
                    </div>

                    {{-- Title + ring --}}
                    <div class="flex items-start gap-4 mb-5">
                        <div class="hidden shrink-0 sm:flex items-center justify-center" style="width:60px;height:60px;">
                            <div class="relative" style="width:60px;height:60px;">
                                <svg class="-rotate-90" width="60" height="60" viewBox="0 0 60 60">
                                    <circle cx="30" cy="30" r="{{ $ringR }}" fill="none" stroke-width="4" stroke="rgba(0,0,0,.08)"/>
                                    <circle cx="30" cy="30" r="{{ $ringR }}" fill="none" stroke-width="4" stroke-linecap="round"
                                            stroke="#0a0a0a"
                                            stroke-dasharray="{{ round($ringC,2) }}"
                                            stroke-dashoffset="{{ round($ringOff,2) }}"
                                            style="transition:stroke-dashoffset .8s cubic-bezier(.16,1,.3,1);"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="ws-mono text-xs font-semibold">{{ $pct }}<span style="font-size:8px;color:rgba(0,0,0,.4);">%</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-semibold leading-tight" style="letter-spacing:-.025em;">{{ $task->title }}</h1>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if($isAdmin)
                                @if($task->isCompleted())
                                <form method="POST" action="{{ encrypted_route('tasks.reopen', $task) }}">
                                    @csrf
                                    <button class="sh-btn-warn inline-flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-medium">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75M3.75 10.5h12A1.5 1.5 0 0 1 17.25 12v6a1.5 1.5 0 0 1-1.5 1.5h-12a1.5 1.5 0 0 1-1.5-1.5v-6a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                                        Rouvrir
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ encrypted_route('tasks.complete', $task) }}"
                                      onsubmit="return confirm('Clôturer définitivement ?')">
                                    @csrf
                                    <button class="sh-btn-ok inline-flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-medium">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        Terminer
                                    </button>
                                </form>
                                @endif
                            @elseif($isReviewer && !$task->isCompleted())
                            <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">
                                @csrf <input type="hidden" name="action" value="request_changes">
                                <button class="sh-btn-warn inline-flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-medium">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                                    Corrections
                                </button>
                            </form>
                            @endif

                            @if($isOwner)
                            <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
                                <button @click="open=!open" @click.outside="open=false"
                                        class="sh-btn-ghost flex h-9 w-9 items-center justify-center rounded-xl">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7.25a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Zm0 6a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Zm0 6a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Z"/></svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top.right
                                     class="absolute right-0 z-30 mt-1.5 w-44 overflow-hidden rounded-xl border p-1.5"
                                     style="background:#fff;border-color:rgba(0,0,0,.08);box-shadow:0 4px 24px rgba(0,0,0,.14);">
                                    @unless($task->isCompleted())
                                    <a href="{{ encrypted_route('tasks.edit', $task) }}"
                                       class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-slate-50" style="color:rgba(0,0,0,.7);">
                                        <svg class="h-3.5 w-3.5" style="color:rgba(0,0,0,.35);" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 2.65 2.65M4 20l.72-3.95c.07-.39.26-.74.54-1.02L16.3 3.94a1.5 1.5 0 0 1 2.12 0l1.64 1.64a1.5 1.5 0 0 1 0 2.12L9.03 18.74c-.28.28-.63.47-1.02.54L4 20Z"/></svg>
                                        Modifier
                                    </a>
                                    @endunless
                                    <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}"
                                          onsubmit="return confirm('Supprimer cette tâche ?')">
                                        @csrf @method('DELETE')
                                        <button class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium transition hover:bg-red-50" style="color:#dc2626;">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/></svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Meta chips --}}
                    <div class="flex flex-wrap gap-2 mb-5">
                        <div class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.06);">
                            <x-avatar :name="$task->owner?->name ?? '?'" size="xs" />
                            <span style="color:rgba(0,0,0,.7);">{{ $task->owner?->name ?? '—' }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.06);color:rgba(0,0,0,.6);">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4.5 9.5h15M6.75 5h10.5A2.25 2.25 0 0 1 19.5 7.25v10.5A2.25 2.25 0 0 1 17.25 20H6.75a2.25 2.25 0 0 1-2.25-2.25V7.25A2.25 2.25 0 0 1 6.75 5Z"/></svg>
                            {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Sans échéance' }}
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.06);color:rgba(0,0,0,.6);">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
                            {{ $reportCount }} rapport{{ $reportCount > 1 ? 's' : '' }}
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-[10px] font-semibold uppercase tracking-[.14em]" style="color:rgba(0,0,0,.4);">Progression</span>
                        <span class="ws-mono text-sm font-semibold">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden" style="background:rgba(0,0,0,.07);">
                        <div class="h-full rounded-full transition-all duration-700" style="width:{{ $pct }}%;background:#0a0a0a;"></div>
                    </div>

                    {{-- Description --}}
                    @if($task->description)
                    <div class="mt-4 rounded-xl px-4 py-3" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.05);">
                        <p class="text-sm leading-7 whitespace-pre-line" style="color:rgba(0,0,0,.65);">{{ $task->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── RAPPORT DU JOUR ─────────────────────────── --}}
            @if($isOwner && !$task->isCompleted())
            @php $state = $thread['task']['discussion_state'] ?? $task->discussionState(); @endphp
            <div class="sh-card sh-2 rounded-2xl overflow-hidden mb-4" x-data="{open:{{ $state==='locked'?'true':'false' }}}">
                <button @click="open=!open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left transition"
                        onmouseenter="this.style.background='rgba(0,0,0,.02)'" onmouseleave="this.style.background='transparent'">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[.16em]" style="color:#059669;">{{ $state==='locked'?'Premier rapport':'Rapport du jour' }}</p>
                        <h3 class="mt-0.5 text-base font-semibold" style="letter-spacing:-.01em;">{{ $todayReport?'Rapport déposé':'Ajouter une mise à jour' }}</h3>
                    </div>
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl transition"
                          :style="open?'background:#0a0a0a;color:#fff;transform:rotate(180deg);':'background:rgba(0,0,0,.04);color:rgba(0,0,0,.5);'">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </button>
                <div x-show="open" x-collapse>
                    <div class="border-t px-5 py-5" style="border-color:rgba(0,0,0,.06);">
                        @if($todayReport)
                        <div class="rounded-xl p-4" style="background:rgba(5,150,105,.05);border:1px solid rgba(5,150,105,.15);">
                            <div class="flex items-center justify-between mb-3">
                                <span class="flex items-center gap-2 text-sm font-medium" style="color:#059669;">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    Aujourd'hui · {{ (int)$todayReport->task_progress_percent }}%
                                </span>
                                <a href="{{ route('reports.edit', $todayReport->id) }}" class="text-sm font-medium" style="color:rgba(0,0,0,.4);">Modifier →</a>
                            </div>
                            <p class="text-sm leading-7 whitespace-pre-line" style="color:rgba(0,0,0,.7);">{{ $todayReport->summary }}</p>
                        </div>
                        @else
                        <form method="POST" action="{{ route('reports.store') }}" class="space-y-4" x-data="{prog:{{ (int)$task->last_progress_percent }}}">
                            @csrf
                            <input type="hidden" name="status_action" value="submit">
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Résumé <span style="color:#ef4444;">*</span></label>
                                <textarea name="summary" rows="4" required placeholder="Ce que tu as accompli aujourd'hui…"
                                    class="sh-input w-full rounded-xl px-3.5 py-2.5 text-sm leading-6 resize-none"></textarea>
                            </div>
                            <div class="rounded-xl p-4" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.06);">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-[10px] font-semibold uppercase tracking-[.14em]" style="color:rgba(0,0,0,.4);">Progression</label>
                                    <span class="ws-mono text-sm font-semibold px-2.5 py-0.5 rounded-lg" style="background:#0a0a0a;color:#fff;" x-text="prog+'%'"></span>
                                </div>
                                <input type="range" name="task_progress_percent" min="0" max="100" step="5" x-model="prog" class="sh-range w-full">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Blocages</label>
                                    <input type="text" name="blockers" placeholder="Optionnel" class="sh-input h-10 w-full rounded-xl px-3.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Heures déclarées</label>
                                    <input type="number" name="hours_declared" min="0" max="24" step="0.5" placeholder="Optionnel" class="sh-input h-10 w-full rounded-xl px-3.5 text-sm">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="sh-btn inline-flex h-10 items-center gap-2 rounded-xl px-4 text-sm font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/></svg>
                                    {{ $state==='locked'?'Ouvrir la discussion':'Envoyer le rapport' }}
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ── DISCUSSION ──────────────────────────────── --}}
            <div class="sh-card sh-3 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 py-4 border-b" style="border-color:rgba(0,0,0,.06);">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold" style="letter-spacing:-.01em;">Discussion</h2>
                        <template x-if="isOpen">
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-2 py-0.5 text-[11px] font-medium" style="background:rgba(16,185,129,.08);color:#059669;">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="live-dot absolute h-full w-full rounded-full" style="background:#10b981;"></span>
                                    <span class="relative h-1.5 w-1.5 rounded-full" style="background:#10b981;"></span>
                                </span>
                                en direct
                            </span>
                        </template>
                    </div>
                    <template x-if="recipients.length">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-medium uppercase tracking-[.14em]" style="color:rgba(0,0,0,.35);">Destinataires</span>
                            <div class="flex -space-x-1.5">
                                <template x-for="r in recipients" :key="r.id">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-[10px] font-bold text-white ring-2 ring-white"
                                          :style="'background:'+avatarColor(r.name)" :title="r.name" x-text="r.initials"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Locked --}}
                <template x-if="isLocked">
                    <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl mb-3" style="background:rgba(245,158,11,.08);">
                            <svg class="h-5 w-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 11h14v10H5V11Z"/></svg>
                        </div>
                        <p class="text-sm font-semibold">En attente du premier rapport</p>
                        <p class="mt-1 text-xs" style="color:rgba(0,0,0,.4);">{{ $isOwner ? 'Dépose ton premier rapport pour démarrer les échanges.' : 'En attente du premier rapport du producteur.' }}</p>
                    </div>
                </template>

                <template x-if="!isLocked">
                    <div>
                        {{-- Pinned --}}
                        <template x-if="pinned">
                            <div class="mx-4 mt-4">
                                <div class="rounded-xl overflow-hidden p-4" style="background:rgba(0,0,0,.02);border:1px solid rgba(0,0,0,.07);">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background:#0a0a0a;">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M16 9V4h1a1 1 0 1 0 0-2H7a1 1 0 1 0 0 2h1v5a4 4 0 0 1-2 3.46V14h5v7l1 1 1-1v-7h5v-1.54A4 4 0 0 1 16 9Z"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span class="text-[10px] font-semibold uppercase tracking-[.14em]" style="color:rgba(0,0,0,.35);">Rapport épinglé</span>
                                                <span class="text-[11px]" style="color:rgba(0,0,0,.35);" x-text="pinned.date_human"></span>
                                                <template x-if="pinned.progress !== null">
                                                    <span class="ws-mono text-[11px] font-medium px-2 py-0.5 rounded-lg" style="background:rgba(16,185,129,.08);color:#059669;" x-text="pinned.progress+'%'"></span>
                                                </template>
                                            </div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[9px] font-bold text-white"
                                                      :style="'background:'+avatarColor(pinned.author.name)" x-text="pinned.author.initials"></span>
                                                <span class="text-sm font-medium" x-text="pinned.author.name"></span>
                                            </div>
                                            <template x-if="!pinned.is_voice">
                                                <p class="text-sm leading-6 whitespace-pre-line" style="color:rgba(0,0,0,.65);" x-text="pinned.summary"></p>
                                            </template>
                                            <template x-if="pinned.blockers">
                                                <p class="mt-2 text-sm rounded-lg px-3 py-2" style="background:rgba(245,158,11,.06);color:#b45309;border:1px solid rgba(245,158,11,.15);" x-text="pinned.blockers"></p>
                                            </template>
                                            <template x-if="canMessage && isOpen">
                                                <button @click="replyToPinned()" class="mt-2 text-xs font-medium transition" style="color:rgba(0,0,0,.4);"
                                                        onmouseenter="this.style.color='#0a0a0a'" onmouseleave="this.style.color='rgba(0,0,0,.4)'">↩ Répondre</button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Messages --}}
                        <div x-ref="scroll" class="sh-scroll overflow-y-auto px-4 pb-4" style="height:52vh;min-height:320px;">
                            <template x-for="group in grouped" :key="group.key">
                                <div class="space-y-2">
                                    <div class="sticky top-0 z-10 flex justify-center py-3">
                                        <span class="rounded-full px-3 py-1 text-[10px] font-semibold" style="background:rgba(255,255,255,.95);color:rgba(0,0,0,.4);border:1px solid rgba(0,0,0,.07);backdrop-filter:blur(8px);" x-text="group.label"></span>
                                    </div>
                                    <template x-for="m in group.items" :key="m.id">
                                        <div>
                                            <template x-if="m.type==='status_change'">
                                                <div class="my-3 flex items-center gap-3">
                                                    <div class="h-px flex-1" style="background:rgba(0,0,0,.07);"></div>
                                                    <span class="rounded-full px-3 py-1 text-[10px] font-medium" style="background:rgba(0,0,0,.04);color:rgba(0,0,0,.4);" x-text="m.body"></span>
                                                    <div class="h-px flex-1" style="background:rgba(0,0,0,.07);"></div>
                                                </div>
                                            </template>
                                            <template x-if="m.type==='report_jalon'">
                                                <div class="flex justify-center my-2">
                                                    <div class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-medium" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.07);color:rgba(0,0,0,.5);">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
                                                        <span x-text="m.body"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="m.type==='message'">
                                                <div class="group flex gap-2 mt-2" :class="m.mine?'flex-row-reverse':''">
                                                    <template x-if="!m.mine">
                                                        <span class="mt-5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white"
                                                              :style="'background:'+avatarColor(m.user.name)" x-text="m.user.initials"></span>
                                                    </template>
                                                    <div class="flex max-w-[78%] flex-col" :class="m.mine?'items-end':'items-start'">
                                                        <div class="mb-1 flex items-center gap-1.5 px-1" :class="m.mine?'flex-row-reverse':''">
                                                            <span class="text-[10px] font-medium" style="color:rgba(0,0,0,.4);" x-text="m.mine?'Vous':m.user.name"></span>
                                                            <span class="text-[10px]" style="color:rgba(0,0,0,.3);" x-text="m.time"></span>
                                                        </div>
                                                        <template x-if="m.parent">
                                                            <div class="mb-1 max-w-full rounded-lg px-3 py-1.5 text-xs" style="border-left:2px solid rgba(0,0,0,.2);background:rgba(0,0,0,.04);" :class="m.mine?'self-end':'self-start'">
                                                                <span class="block font-medium" style="color:rgba(0,0,0,.5);" x-text="m.parent.user_name"></span>
                                                                <span class="block truncate" style="color:rgba(0,0,0,.4);" x-text="m.parent.excerpt"></span>
                                                            </div>
                                                        </template>
                                                        <div class="px-3.5 py-2.5 text-sm leading-6" :class="m.mine?'msg-mine':'msg-other'">
                                                            <template x-if="m.body"><p class="whitespace-pre-line" x-text="m.body"></p></template>
                                                            <template x-if="m.attachment&&m.attachment.type==='audio'">
                                                                <audio controls class="h-8 max-w-full mt-1" :src="m.attachment.url"></audio>
                                                            </template>
                                                            <template x-if="m.attachment&&m.attachment.type==='image'">
                                                                <a :href="m.attachment.url" target="_blank" class="mt-2 block overflow-hidden rounded-lg">
                                                                    <img :src="m.attachment.url" class="max-h-56 w-full object-cover">
                                                                </a>
                                                            </template>
                                                            <template x-if="m.attachment&&m.attachment.type==='file'">
                                                                <a :href="m.attachment.url" target="_blank" class="mt-2 flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs font-medium" style="background:rgba(255,255,255,.12);">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a2 2 0 0 0 2 2h4M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"/></svg>
                                                                    <span class="truncate" x-text="m.attachment.name||'Fichier'"></span>
                                                                </a>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <template x-if="canMessage&&isOpen">
                                                        <button @click="setReply(m)" class="self-center rounded-lg p-1.5 opacity-0 group-hover:opacity-100 transition" style="color:rgba(0,0,0,.4);"
                                                                onmouseenter="this.style.background='rgba(0,0,0,.06)'" onmouseleave="this.style.background='transparent'">
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 0 1 5 5v2M3 10l4-4M3 10l4 4"/></svg>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!messages.length">
                                <div class="flex min-h-[240px] items-center justify-center text-center">
                                    <div>
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl mx-auto mb-3" style="background:rgba(0,0,0,.04);">
                                            <svg class="h-5 w-5" style="color:rgba(0,0,0,.25);" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m9-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium" style="color:rgba(0,0,0,.5);">Aucun message pour l'instant.</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Closed --}}
                        <template x-if="isClosed">
                            <div class="border-t px-5 py-3 text-center" style="border-color:rgba(0,0,0,.06);background:rgba(0,0,0,.02);">
                                <p class="text-xs" style="color:rgba(0,0,0,.4);">Discussion clôturée.{{ $isAdmin ? ' Tu peux la rouvrir depuis les actions.' : '' }}</p>
                            </div>
                        </template>

                        {{-- Composer --}}
                        @if($canMessage)
                        <template x-if="isOpen">
                            <div class="border-t p-4" style="border-color:rgba(0,0,0,.06);background:rgba(255,255,255,.7);backdrop-filter:blur(8px);">
                                <template x-if="replyTo">
                                    <div class="mb-3 flex items-center gap-2 rounded-xl px-3 py-2" style="background:rgba(0,0,0,.04);border:1px solid rgba(0,0,0,.07);">
                                        <div class="min-w-0 flex-1">
                                            <span class="block text-[10px] font-semibold uppercase tracking-[.12em]" style="color:rgba(0,0,0,.4);" x-text="'↩ '+replyTo.user_name"></span>
                                            <span class="block truncate text-xs" style="color:rgba(0,0,0,.5);" x-text="replyTo.excerpt"></span>
                                        </div>
                                        <button @click="clearReply()" class="rounded-lg p-1.5" style="color:rgba(0,0,0,.4);"
                                                onmouseenter="this.style.background='rgba(0,0,0,.06)'" onmouseleave="this.style.background='transparent'">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                                <form @submit.prevent="send()" class="flex items-end gap-2">
                                    <textarea x-ref="input" x-model="body" rows="1" placeholder="Écrire un message…"
                                        @keydown.enter="if(!$event.shiftKey){$event.preventDefault();send()}"
                                        class="sh-input flex-1 rounded-xl px-3.5 py-2.5 text-sm leading-5 resize-none"
                                        style="max-height:120px;min-height:42px;"></textarea>
                                    <button type="submit" :disabled="sending||!body.trim()"
                                            class="sh-btn flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-xl">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </template>
                        @endif
                    </div>
                </template>
            </div>

        </div>{{-- /max-w-3xl --}}

        {{-- Toast --}}
        @if(session('success'))
        <div x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,3500)"
             class="fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-white"
             style="background:#0a0a0a;box-shadow:0 4px 24px rgba(0,0,0,.25);max-width:340px;">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full" style="background:rgba(16,185,129,.2);">
                <svg class="h-3.5 w-3.5" style="color:#10b981;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </span>
            {{ session('success') }}
        </div>
        @endif
    </div>

    <script>
    window.taskChat = function(initial, cfg) {
        return {
            payload: initial||{task:{},messages:[],recipients:[],pinned_report:null,me:{}},
            cfg, body:'', replyTo:null, sending:false,
            csrf: document.querySelector('meta[name="csrf-token"]')?.content||'',
            get state()    { return this.payload.task?.discussion_state||'locked'; },
            get isOpen()   { return this.state==='open'; },
            get isClosed() { return this.state==='closed'; },
            get isLocked() { return this.state==='locked'; },
            get pinned()   { return this.payload.pinned_report; },
            get recipients(){ return this.payload.recipients||[]; },
            get messages() {
                const pid=this.pinned?.id;
                return (this.payload.messages||[]).filter(m=>!(m.type==='report_jalon'&&pid&&m.daily_report_id===pid));
            },
            get grouped() {
                const out=[]; let cur=null;
                for(const m of this.messages){ if(!cur||cur.key!==m.day_key){cur={key:m.day_key,label:m.day_label,items:[]};out.push(cur);} cur.items.push(m); }
                return out;
            },
            init() { this.$nextTick(()=>this.scrollBottom()); this.startPolling(); this.setupEcho(); this.markRead(); document.addEventListener('visibilitychange',()=>{ if(!document.hidden) this.refresh(); }); },
            setupEcho() { if(typeof window.Echo==='undefined') return; const id=this.payload.task?.id; if(!id) return; try{ window.Echo.private(`task.${id}`).listen('message.created',()=>this.refresh()).listen('reaction.added',()=>this.refresh()).listen('message.read',()=>this.refresh()); }catch(e){} },
            startPolling() { setInterval(()=>{ if(!document.hidden) this.refresh(); },4000); },
            async refresh() { try{ const r=await fetch(this.cfg.threadUrl,{headers:{'Accept':'application/json'}}); if(r.ok) this.merge(await r.json()); }catch(e){} },
            merge(data) { const b=this.isAtBottom(),p=this.lastId(); this.payload=data; this.$nextTick(()=>{ if(b||this.lastId()!==p) this.scrollBottom(); }); if(this.lastId()>p) this.markRead(); },
            async send() {
                const text=this.body.trim(); if(!text||this.sending||!this.isOpen) return; this.sending=true;
                const fd=new FormData(); fd.append('body',text); fd.append('ajax','1'); if(this.replyTo?.id) fd.append('parent_id',this.replyTo.id);
                try{ const r=await fetch(this.cfg.storeUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json'},body:fd}); if(r.ok){ const d=await r.json(); this.payload.messages.push(d.message); this.payload.task.last_message_id=d.last_message_id; this.body=''; this.replyTo=null; this.$nextTick(()=>this.scrollBottom()); this.markRead(); } }catch(e){} finally{this.sending=false;}
            },
            setReply(m) { if(!m||m.is_system) return; this.replyTo={id:m.id,user_name:m.mine?'Vous':m.user.name,excerpt:this.excerptOf(m)}; this.$nextTick(()=>this.$refs.input?.focus()); },
            replyToPinned() { if(!this.pinned) return; const j=(this.payload.messages||[]).find(m=>m.type==='report_jalon'&&m.daily_report_id===this.pinned.id); this.replyTo={id:j?.id||null,user_name:this.pinned.author.name,excerpt:this.pinned.is_voice?'Message vocal':(this.pinned.summary||'Rapport')}; this.$nextTick(()=>this.$refs.input?.focus()); },
            clearReply() { this.replyTo=null; },
            excerptOf(m) { if(m.attachment?.type==='audio') return 'Message vocal'; if(m.attachment?.type==='image') return 'Photo'; if(m.attachment?.type==='file') return 'Fichier '+(m.attachment.name||''); return (m.body||'').slice(0,80); },
            async markRead() { const l=this.lastId(); if(!l) return; try{ await fetch(this.cfg.readUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},body:'up_to='+l}); }catch(e){} },
            lastId() { const m=this.payload.messages||[]; return m.length?m[m.length-1].id:0; },
            scrollBottom() { const el=this.$refs.scroll; if(el) el.scrollTop=el.scrollHeight; },
            isAtBottom() { const el=this.$refs.scroll; if(!el) return true; return(el.scrollHeight-el.scrollTop-el.clientHeight)<80; },
            avatarColor(n) { n=n||'?'; let h=0; for(let i=0;i<n.length;i++) h=(h*31+n.charCodeAt(i))%360; return 'hsl('+h+' 55% 48%)'; },
            fmtDur(s) { s=parseInt(s||0,10); const m=Math.floor(s/60),r=s%60; return m+':'+(r<10?'0':'')+r; },
            fmtSize(b) { b=parseInt(b||0,10); if(!b) return ''; if(b<1024) return b+' o'; if(b<1048576) return Math.round(b/1024)+' Ko'; return (b/1048576).toFixed(1)+' Mo'; },
            canMessage: @js($canMessage),
        };
    };
    </script>

</x-app-layout>
