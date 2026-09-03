@php
    $user = auth()->user();
    $isOwner       = $task->owner_id === $user->id;
    $isAdmin       = $user->hasRole('admin');
    $isParticipant = $task->isParticipant($user->id);
    $isReviewer    = $user->hasAnyRole(['admin', 'superviseur']) && !$isParticipant;
    $canComment    = $isParticipant || $isReviewer;

    // Réassignation des sous-tâches : admin, superviseur ou propriétaire de la tâche.
    $canReassignSubtask = $user->hasAnyRole(['admin', 'superviseur']) || $isOwner;

    // Participants de la tâche éligibles à la réassignation d'une sous-tâche.
    $reassignCandidates = isset($group)
        ? $group->merge($task->subtasks->pluck('assignedTo')->filter())->unique('id')->values()
        : collect([$task->owner])->filter();

    $myReports = $task->dailyReports->filter(fn($r) => $r->user_id === (int) $user->id);
    $isFirstReport = $myReports->isEmpty();

    $points = $task->dailyReports->reverse()
        ->filter(fn($r) => !is_null($r->task_progress_percent))
        ->map(fn($r) => (int) $r->task_progress_percent)->values();

    $pct     = $task->computeProgressFromSubtasks();
    $nextStep = $task->nextStepLabel();
    $ringR  = 22; $ringC = 2 * M_PI * $ringR;
    $ringOff = $ringC * (1 - ($pct / 100));
    $reportCount = $task->dailyReports->count();

    $priorityBarGradient = match($task->priority) {
        'urgent' => 'linear-gradient(90deg,#ef4444,#f97316)',
        'high'   => 'linear-gradient(90deg,#f59e0b,#ef4444)',
        'low'    => 'linear-gradient(90deg,rgba(128,128,128,.2),rgba(128,128,128,.35))',
        default  => 'linear-gradient(90deg,#6366f1,#3b82f6)',
    };
    $priorityMeta = match($task->priority) {
        'urgent' => ['label' => 'Urgente', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.08)'],
        'high'   => ['label' => 'Haute',   'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.08)'],
        'low'    => ['label' => 'Basse',   'color' => 'var(--ws-text-faint)', 'bg' => 'rgba(128,128,128,.1)'],
        default  => ['label' => 'Normale', 'color' => '#6366f1', 'bg' => 'rgba(99,102,241,.08)'],
    };
    $stateMeta = match($task->status) {
        'completed'           => ['label' => 'Terminée',              'color' => '#10b981', 'bg' => 'rgba(16,185,129,.08)'],
        'awaiting_validation' => ['label' => 'En attente validation', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.08)'],
        'blocked'             => ['label' => 'Bloquée',               'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.08)'],
        'in_progress'         => ['label' => 'En cours',              'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,.08)'],
        'changes_requested'   => ['label' => 'Corrections demandées', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.08)'],
        default               => ['label' => 'À faire',               'color' => 'var(--ws-text-muted)', 'bg' => 'rgba(128,128,128,.1)'],
    };
@endphp

<style>
/* ── Animations ── */
@keyframes d-in    { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
@keyframes d-pulse { 0%,100%{opacity:.7;transform:scale(1)} 50%{opacity:0;transform:scale(2.2)} }
.d-in   { animation: d-in .36s cubic-bezier(.16,1,.3,1) both; }
.d-in-2 { animation: d-in .36s .06s cubic-bezier(.16,1,.3,1) both; }
.d-in-3 { animation: d-in .36s .12s cubic-bezier(.16,1,.3,1) both; }
.d-pulse{ animation: d-pulse 2s ease-in-out infinite; }

/* ── Dark overrides généraux ── */
.ws-root.dark .bg-white,
.ws-root.dark .d-in>.bg-white        { background: #18181b; }
.ws-root.dark .text-black             { color: #fff; }
.ws-root.dark .text-black\/70,
.ws-root.dark .text-black\/60,
.ws-root.dark .text-black\/45,
.ws-root.dark .text-black\/50,
.ws-root.dark .text-black\/40         { color: rgba(255,255,255,.70); }
.ws-root.dark .border-black\/7,
.ws-root.dark .border-black\/8,
.ws-root.dark .border-black\/6,
.ws-root.dark .border-black\/5,
.ws-root.dark .divide-black\/5 > *    { border-color: rgba(255,255,255,.08); }
.ws-root.dark .bg-black\/4,
.ws-root.dark .bg-black\/2\.5,
.ws-root.dark .bg-black\/5,
.ws-root.dark .bg-black\/3,
.ws-root.dark .bg-black\/6,
.ws-root.dark .bg-black\/7            { background: rgba(255,255,255,.05); }
.ws-root.dark .bg-black               { background: #fff; color: #0a0a0a; }
.ws-root.dark .bg-slate-50            { background: rgba(255,255,255,.04); }
.ws-root.dark .bg-slate-50 svg        { opacity: .5; }
.ws-root.dark .d-section-label        { color: rgba(255,255,255,.55); }
.ws-root.dark .shadow-sm              { box-shadow: 0 1px 3px rgba(0,0,0,.3), 0 8px 32px rgba(0,0,0,.25); }
.ws-root.dark .d-ring-track           { stroke: rgba(255,255,255,.12); }
.ws-root.dark .d-ring-fill            { stroke: #fff; }
.ws-root.dark .text-emerald-700       { color: #34d399; }
.ws-root.dark .bg-emerald-50          { background: rgba(5,150,105,.15); }
.ws-root.dark .border-emerald-200     { border-color: rgba(5,150,105,.25); }

/* ── d-input light ── */
.d-input {
    display: block;
    width: 100%;
    background: rgba(0,0,0,.03);
    border: 1.5px solid rgba(0,0,0,.09);
    border-radius: .75rem;
    padding: .625rem .875rem;
    font-size: .875rem;
    line-height: 1.5;
    color: #0a0a0a;
    transition: background .15s, border-color .15s, box-shadow .15s;
}
.d-input::placeholder { color: rgba(0,0,0,.32); }
.d-input:focus {
    background: #fff;
    border-color: rgba(0,0,0,.22);
    box-shadow: 0 0 0 3px rgba(0,0,0,.05);
    outline: none;
}

/* ── d-input dark ── */
.ws-root.dark .d-input {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.12);
    color: #ffffff;
}
.ws-root.dark .d-input::placeholder { color: rgba(255,255,255,.35); }
.ws-root.dark .d-input:focus {
    background: rgba(255,255,255,.09);
    border-color: rgba(255,255,255,.25);
    box-shadow: 0 0 0 3px rgba(255,255,255,.06);
    color: #ffffff;
}

/* ── d-range ── */
.d-range { accent-color: #0a0a0a; width: 100%; }
.ws-root.dark .d-range { accent-color: #fff; }
.ws-root.dark input[type="range"].d-range { background: rgba(255,255,255,.1); }

/* ── Boutons ── */
.d-btn-dark {
    display: inline-flex; align-items: center; gap: .5rem;
    height: 2.5rem; padding: 0 1.125rem;
    background: #0a0a0a; color: #fff;
    font-size: .875rem; font-weight: 500;
    border: none; border-radius: .75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.10);
    transition: background .18s, transform .18s, box-shadow .18s;
    cursor: pointer;
}
.d-btn-dark:hover { background: #222; transform: translateY(-1px); }
.ws-root.dark .d-btn-dark { background: #fff; color: #0a0a0a; }
.ws-root.dark .d-btn-dark:hover { background: #e5e5e5; }

.d-btn-ghost {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .375rem .75rem;
    background: transparent; color: var(--ws-text-muted);
    font-size: .8125rem; font-weight: 500;
    border: none; border-radius: .625rem;
    cursor: pointer; transition: background .15s, color .15s;
}
.d-btn-ghost:hover { background: rgba(0,0,0,.05); }
.ws-root.dark .d-btn-ghost { color: rgba(255,255,255,.50); }
.ws-root.dark .d-btn-ghost:hover { background: rgba(255,255,255,.06); color: rgba(255,255,255,.85); }

.d-btn-menu {
    display: inline-flex; align-items: center; justify-content: center;
    height: 2.25rem; width: 2.25rem; padding: 0;
    background: rgba(0,0,0,.06); color: rgba(0,0,0,.70);
    border: 1px solid rgba(0,0,0,.12);
    border-radius: .625rem;
    cursor: pointer; transition: background .15s, border-color .15s, color .15s;
}
.d-btn-menu:hover { background: rgba(0,0,0,.12); color: rgba(0,0,0,.9); border-color: rgba(0,0,0,.2); }
.ws-root.dark .d-btn-menu { background: rgba(255,255,255,.09); color: rgba(255,255,255,.75); border-color: rgba(255,255,255,.18); }
.ws-root.dark .d-btn-menu:hover { background: rgba(255,255,255,.15); color: #fff; }

.d-btn-green {
    display: inline-flex; align-items: center; gap: .375rem;
    height: 2rem; padding: 0 .875rem;
    background: #059669; color: #fff;
    font-size: .8125rem; font-weight: 500;
    border: none; border-radius: .625rem;
    cursor: pointer; transition: background .15s;
}
.d-btn-green:hover { background: #047857; }

.d-btn-amber {
    display: inline-flex; align-items: center; gap: .375rem;
    height: 2rem; padding: 0 .875rem;
    background: #d97706; color: #fff;
    font-size: .8125rem; font-weight: 500;
    border: none; border-radius: .625rem;
    cursor: pointer; transition: background .15s;
}
.d-btn-amber:hover { background: #b45309; }

/* ── Labels de section ── */
.d-section-label {
    display: block;
    font-size: .6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: rgba(0,0,0,.45);
    margin-bottom: .375rem;
}

/* ── Formulaire rapport : champs avec label flottant ── */
.d-field { position: relative; }
.d-field-label {
    display: block;
    font-size: .6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: rgba(0,0,0,.50);
    margin-bottom: .375rem;
}
.ws-root.dark .d-field-label { color: rgba(255,255,255,.65); }

/* ── Zone progression ── */
.d-progress-shell {
    background: rgba(0,0,0,.03);
    border: 1.5px solid rgba(0,0,0,.08);
    border-radius: .875rem;
    padding: 1rem 1.125rem;
}
.ws-root.dark .d-progress-shell {
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.10);
}
.d-progress-track {
    height: 6px;
    background: rgba(0,0,0,.08);
    border-radius: 99px;
    overflow: hidden;
    margin: .625rem 0 .5rem;
}
.ws-root.dark .d-progress-track { background: rgba(255,255,255,.12); }
.d-progress-fill {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, #6366f1, #3b82f6);
    transition: width .3s ease;
}

/* ── Badge pct ── */
.d-pct-badge {
    display: inline-flex; align-items: center;
    height: 1.625rem; padding: 0 .625rem;
    background: #0a0a0a; color: #fff;
    font-size: .75rem; font-family: 'DM Mono', monospace; font-weight: 500;
    border-radius: .5rem;
}
.ws-root.dark .d-pct-badge { background: #fff; color: #0a0a0a; }

/* ── Carte rapport déposé ── */
.d-report-done {
    background: rgba(5,150,105,.06);
    border: 1.5px solid rgba(5,150,105,.18);
    border-radius: .875rem;
    padding: 1rem 1.25rem;
}
.ws-root.dark .d-report-done {
    background: rgba(5,150,105,.12);
    border-color: rgba(5,150,105,.28);
}

/* ── Divider ── */
.d-divider { height:1px; background: rgba(0,0,0,.06); }
.ws-root.dark .d-divider { background: rgba(255,255,255,.08); }

/* ── Rapport form wrapper ── */
.d-form-section {
    background: rgba(99,102,241,.03);
    border: 1.5px solid rgba(99,102,241,.10);
    border-radius: 1rem;
    padding: 1.5rem;
}
.ws-root.dark .d-form-section {
    background: rgba(99,102,241,.06);
    border-color: rgba(99,102,241,.18);
}
</style>

<div class="space-y-0 d-in">
    <div class="bg-white border border-black/7 rounded-2xl shadow-sm overflow-hidden d-in">

        {{-- Barre priorité --}}
        <div class="h-[3px]" style="background:{{ $priorityBarGradient }};"></div>

        {{-- ── EN-TÊTE TÂCHE ── --}}
        <div class="px-6 pt-5 pb-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    {{-- Ring progression --}}
                    <div class="hidden sm:flex shrink-0 relative w-14 h-14">
                        <svg class="-rotate-90 w-14 h-14" viewBox="0 0 56 56">
                            <circle class="d-ring-track" cx="28" cy="28" r="{{ $ringR }}" fill="none" stroke-width="4" stroke="rgba(0,0,0,.07)"/>
                            <circle class="d-ring-fill" cx="28" cy="28" r="{{ $ringR }}" fill="none" stroke-width="4" stroke-linecap="round"
                                    stroke="#0f0f0f"
                                    stroke-dasharray="{{ round($ringC,2) }}"
                                    stroke-dashoffset="{{ round($ringOff,2) }}"
                                    style="transition:stroke-dashoffset .8s cubic-bezier(.16,1,.3,1);"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="font-mono text-xs font-semibold">{{ $pct }}%</span>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-xl font-semibold tracking-tight text-black">{{ $task->title }}</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-semibold"
                                  style="background:{{ $stateMeta['bg'] }};color:{{ $stateMeta['color'] }};">
                                <span class="d-pulse inline-block h-1.5 w-1.5 rounded-full" style="background:{{ $stateMeta['color'] }};"></span>
                                {{ $stateMeta['label'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-semibold"
                                  style="background:{{ $priorityMeta['bg'] }};color:{{ $priorityMeta['color'] }};">
                                {{ $priorityMeta['label'] }}
                            </span>
                            @if($task->isOverdue())
                            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-semibold bg-red-50 text-red-600">⚠ En retard</span>
                            @endif
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-black/45">
                            <span class="inline-flex items-center gap-1.5">
                                <x-avatar :name="$task->owner?->name ?? '?'" :src="$task->owner?->avatar_url" size="xs" />
                                {{ $task->owner?->name ?? 'Sans propriétaire' }}
                            </span>
                            @if($task->due_date)
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4.5 9.5h15M6.75 5h10.5A2.25 2.25 0 0 1 19.5 7.25v10.5A2.25 2.25 0 0 1 17.25 20H6.75a2.25 2.25 0 0 1-2.25-2.25V7.25A2.25 2.25 0 0 1 6.75 5Z"/></svg>
                                {{ $task->due_date->format('d/m/Y') }}
                            </span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
                                {{ $reportCount }} rapport{{ $reportCount !== 1 ? 's' : '' }}
                            </span>
                            @if(isset($group) && $group->count() > 1)
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold bg-indigo-50 text-indigo-600">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                {{ $group->count() }} personne{{ $group->count() > 1 ? 's' : '' }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    @if($isAdmin)
                        @if($task->isCompleted())
                        <form method="POST" action="{{ encrypted_route('tasks.reopen', $task) }}">@csrf
                            <button class="d-btn-amber">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75M3.75 10.5h12A1.5 1.5 0 0 1 17.25 12v6a1.5 1.5 0 0 1-1.5 1.5h-12a1.5 1.5 0 0 1-1.5-1.5v-6a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                                Rouvrir
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ encrypted_route('tasks.complete', $task) }}" onsubmit="return confirm('Clôturer cette tâche ?')">@csrf
                            <button class="d-btn-green">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                Terminer
                            </button>
                        </form>
                        @endif
                    @elseif($isReviewer && !$task->isCompleted())
                    <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">@csrf
                        <input type="hidden" name="action" value="request_changes">
                        <button class="d-btn-amber">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                            Corrections
                        </button>
                    </form>
                    @endif

                    @if($isAdmin && !$task->isCompleted())
                    <a href="{{ encrypted_route('tasks.assign.form', $task->id) }}"
                       title="Transférer ou réassigner cette tâche"
                       class="d-btn-ghost h-9 px-3 rounded-xl">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                        Réassigner
                    </a>
                    @endif

                    @if($isOwner || $isAdmin)
                    <div class="relative" x-data="{open:false, confirmDelete:false}" @keydown.escape.window="open=false; confirmDelete=false">
                        <button type="button" @click="open=!open" @click.outside="open=false"
                                class="d-btn-menu h-9 w-9 p-0 justify-center rounded-xl">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 7.25a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Zm0 6a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Zm0 6a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Z"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.right
                             class="absolute right-0 z-30 mt-1.5 w-44 rounded-xl border border-black/8 p-1.5 bg-white shadow-lg">
                            @unless($task->isCompleted())
                            <a href="{{ encrypted_route('tasks.edit', $task) }}"
                               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-black/70 hover:bg-slate-50 transition">
                                <svg class="h-3.5 w-3.5 opacity-50" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 2.65 2.65M4 20l.72-3.95c.07-.39.26-.74.54-1.02L16.3 3.94a1.5 1.5 0 0 1 2.12 0l1.64 1.64a1.5 1.5 0 0 1 0 2.12L9.03 18.74c-.28.28-.63.47-1.02.54L4 20Z"/></svg>
                                Modifier
                            </a>
                            @endunless
                            <button type="button" @click="open=false; confirmDelete=true"
                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/></svg>
                                Supprimer
                            </button>
                        </div>

                        {{-- Modal de confirmation --}}
                        <div x-show="confirmDelete" x-cloak x-transition.opacity
                             class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="confirmDelete = false"></div>
                            <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                                 x-show="confirmDelete" x-transition>
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50">
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-semibold text-black">Supprimer cette tâche ?</h3>
                                <p class="mt-1.5 text-sm leading-6 text-black/60">{{ \Illuminate\Support\Str::limit($task->title, 60) }}</p>
                                <p class="mt-1 text-xs text-black/45">La tâche sera déplacée dans la corbeille. Tu pourras la restaurer ou la supprimer définitivement.</p>
                                <div class="mt-5 flex items-center justify-end gap-2">
                                    <button type="button" @click="confirmDelete = false"
                                            class="h-9 rounded-xl border border-black/10 px-4 text-sm font-medium text-black/70 hover:bg-slate-50 transition">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex h-9 items-center gap-1.5 rounded-xl bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Barre progression --}}
            <div class="mt-5">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="d-section-label mb-0">Progression</span>
                    <span class="font-mono text-sm font-bold text-black">{{ $pct }}%</span>
                </div>
                <div class="h-1.5 rounded-full overflow-hidden bg-black/7">
                    <div class="h-full rounded-full transition-all duration-700 bg-black" style="width:{{ $pct }}%;"></div>
                </div>
                @if($points->count() >= 2)
                @php
                    $w=100;$h=28;$n=$points->count();
                    $coords=$points->map(fn($p,$i)=>round($n>1?$i*($w/($n-1)):0,1).','.round($h-($p/100*$h),1))->implode(' ');
                    $area='0,'.$h.' '.$coords.' '.$w.','.$h;
                @endphp
                <svg class="mt-2 w-full" height="28" viewBox="0 0 100 28" preserveAspectRatio="none">
                    <defs><linearGradient id="spk{{ $task->id }}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10b981" stop-opacity=".12"/>
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                    </linearGradient></defs>
                    <polygon points="{{ $area }}" fill="url(#spk{{ $task->id }})"/>
                    <polyline points="{{ $coords }}" fill="none" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @endif
            </div>

            @if($task->description)
            <div class="mt-4 rounded-xl px-4 py-3 text-sm leading-relaxed whitespace-pre-line bg-black/2.5 border border-black/5 text-black/60 dark:bg-white/[0.03] dark:border-white/10 dark:text-white/70">
                {{ $task->description }}
            </div>
            @endif


            {{-- ── Cahier des charges PDF ── --}}
            @if($task->pdf_path)
            <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-black/6 bg-black/2.5 p-3.5 dark:border-white/10 dark:bg-white/[0.02]">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-black/40 dark:text-white/40">Cahier des charges</p>
                        <a href="{{ Storage::disk('public')->url($task->pdf_path) }}" target="_blank" class="text-sm font-semibold text-black hover:underline dark:text-white truncate block">
                            Consulter le document PDF
                        </a>
                    </div>
                </div>
                <a href="{{ Storage::disk('public')->url($task->pdf_path) }}" download class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-black/10 bg-white px-3 py-1.5 text-xs font-semibold text-black hover:bg-black/5 dark:border-white/10 dark:bg-white/[0.05] dark:text-white">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Télécharger
                </a>
            </div>
            @endif
        </div>

        <div class="d-divider"></div>

        {{-- ── SECTION SOUS-TÂCHES ── --}}
        <div class="px-6 py-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-black dark:text-white flex items-center gap-2">
                        <span>📋</span> Sous-tâches ({{ $task->subtasks->where('is_completed', true)->count() }} / {{ $task->subtasks->count() }})
                    </h3>
                    <p class="text-xs mt-0.5 text-black/40 dark:text-white/40">
                        Progression calculée automatiquement sur l'ensemble des sous-tâches.
                    </p>
                </div>
                @if($task->owner_id === $user->id || $user->hasAnyRole(['admin', 'superviseur']) || $task->assignees->contains($user->id))
                <a href="{{ encrypted_route('tasks.edit', $task) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    Gérer les sous-tâches →
                </a>
                @endif
            </div>

            <div class="space-y-2.5">
                @forelse($task->subtasks as $st)
                @php
                    $isMySubtask = (int) $st->assigned_to_user_id === (int) $user->id;
                    $itemsCount = $st->items()->count();
                    $itemsDone = $st->items()->where('is_completed', true)->count();
                    $personalProg = $st->personalProgress();
                @endphp
                <div class="flex flex-col p-3.5 rounded-xl border transition
                            {{ $st->is_completed ? 'border-emerald-200 bg-emerald-50/40 dark:border-emerald-900/30 dark:bg-emerald-950/10' : ($isMySubtask ? 'border-blue-200 bg-blue-50/30 ring-1 ring-blue-500/20 dark:border-blue-900/30 dark:bg-blue-950/10' : 'border-black/6 bg-black/2.5 dark:border-white/10 dark:bg-white/[0.02]') }}">

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $st->is_completed ? 'bg-emerald-500 text-white' : 'border-2 border-blue-400 dark:border-blue-400 text-transparent' }}">
                                @if($st->is_completed)
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @else
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-black dark:text-white {{ $st->is_completed ? 'line-through opacity-60' : '' }}">
                                        {{ $st->title }}
                                    </p>

                                    @if($isMySubtask)
                                        <span class="rounded-md bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 px-2 py-0.5 text-[11px] font-bold">
                                            👤 Attribuée à vous
                                        </span>
                                    @elseif($st->assignedTo)
                                        <span class="rounded-md bg-black/5 text-black/60 dark:bg-white/10 dark:text-white/60 px-2 py-0.5 text-[11px] font-medium">
                                            👤 {{ $st->assignedTo->name }}
                                        </span>
                                    @else
                                        <span class="rounded-md border border-dashed border-black/20 dark:border-white/20 px-2 py-0.5 text-[11px] text-black/40 dark:text-white/40">
                                            Non attribuée
                                        </span>
                                    @endif

                                    @if($st->is_completed)
                                        <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                                            Terminée le {{ $st->completed_at?->format('d/m') }} {{ $st->completedBy ? 'par ' . $st->completedBy->name : '' }}
                                        </span>
                                    @endif
                                </div>

                                @if($st->start_date || $st->end_date)
                                <p class="text-xs text-black/40 dark:text-white/40 mt-1">
                                    📅 {{ $st->start_date?->format('d/m/Y') ?? '?' }} → {{ $st->end_date?->format('d/m/Y') ?? '?' }}
                                </p>
                                @endif

                                {{-- Barre de progression personnelle --}}
                                @if($itemsCount > 0)
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $personalProg >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $personalProg }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-semibold {{ $personalProg >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400' }}">{{ $personalProg }}%</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($st->is_completed)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 px-3 py-1 text-xs font-bold border border-emerald-200 dark:border-emerald-800/40">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Terminée
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 px-3 py-1 text-xs font-semibold border border-blue-200 dark:border-blue-800/40">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    En cours
                                </span>
                            @endif

                            @if(!$st->is_completed && $canReassignSubtask)
                            <form method="POST" action="{{ route('tasks.subtasks.assign', [$task, $st]) }}" x-data onchange="this.submit()">
                                @csrf
                                <select name="assigned_to_user_id"
                                        title="Réassigner cette sous-tâche"
                                        class="rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-gray-900 px-2 py-1 text-[11px] font-semibold text-slate-700 dark:text-slate-200 focus:border-slate-400 focus:ring-1 focus:ring-slate-400/30 outline-none transition"
                                        @if($reassignCandidates->isEmpty()) disabled @endif>
                                    <option value="">— Non attribuée —</option>
                                    @foreach($reassignCandidates as $cand)
                                    <option value="{{ $cand->id }}"
                                            {{ (int) $st->assigned_to_user_id === (int) $cand->id ? 'selected' : '' }}>
                                        {{ $cand->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                            @endif

                            @if($isAdmin && $st->is_completed)
                            <form method="POST" action="{{ route('tasks.subtasks.reopen', [$task, $st]) }}" onsubmit="return confirm('Réouvrir cette sous-tâche ?')">
                                @csrf
                                <button type="submit" class="rounded-lg border border-amber-300 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 hover:bg-amber-100 transition" title="Réouvrir (Admin)">
                                    🔓 Réouvrir
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    {{-- ── ITEMS PERSONNELS (niveau 2) ── --}}
                    @if($itemsCount > 0 || ($isMySubtask && !$st->is_completed))
                    <div class="mt-3 ml-8 space-y-1.5">
                        @forelse($st->items as $item)
                        <div class="flex items-center justify-between gap-2 py-1 px-2.5 rounded-lg hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition group">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($isMySubtask)
                                <form method="POST" action="{{ route('tasks.subtask_items.toggle', [$task, $st, $item]) }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="flex h-4 w-4 items-center justify-center rounded {{ $item->is_completed ? 'bg-emerald-500 text-white' : 'border border-blue-400 dark:border-blue-400 text-transparent hover:border-blue-600' }} transition">
                                        @if($item->is_completed)
                                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                </form>
                                @else
                                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded {{ $item->is_completed ? 'bg-emerald-500 text-white' : 'border border-slate-300 dark:border-slate-600 text-transparent' }}">
                                    @if($item->is_completed)
                                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </span>
                                @endif
                                <p class="text-xs font-medium text-black/70 dark:text-white/70 {{ $item->is_completed ? 'line-through opacity-50' : '' }}">
                                    {{ $item->title }}
                                </p>
                            </div>
                            @if(!$st->is_completed && $isMySubtask)
                            <form method="POST" action="{{ route('tasks.subtask_items.destroy', [$task, $st, $item]) }}" class="opacity-0 group-hover:opacity-100 transition shrink-0" onsubmit="return confirm('Supprimer cet item ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @empty
                        @endforelse

                        {{-- Formulaire ajout item — uniquement pour l'utilisateur assigné --}}
                        @if(!$st->is_completed && $isMySubtask)
                        <div x-data="{ open: false, title: '', saving: false,
                            addItem() {
                                if (!this.title.trim() || this.saving) return;
                                this.saving = true;
                                fetch('{{ route('tasks.subtask_items.store', [$task, $st]) }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                    body: JSON.stringify({ title: this.title })
                                }).then(async (r) => {
                                    let d = { success: false, message: '' };
                                    const ct = r.headers.get('content-type') || '';
                                    if (ct.includes('application/json')) {
                                        d = await r.json();
                                    } else {
                                        d = { success: r.ok };
                                    }
                                    if (!r.ok && !d.message) d.message = 'Erreur lors de l\'ajout.';
                                    if (d.success || d.message) location.reload();
                                }).catch(() => { location.reload(); });
                            }
                        }" class="pt-1">
                            <button x-show="!open" @click="open = true" type="button" class="flex items-center gap-1.5 text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Ajouter un item
                            </button>
                            <form x-show="open" @submit.prevent="addItem(); open = false; title = '';" class="flex items-center gap-2 mt-1">
                                <input x-model="title" type="text" required placeholder="Titre de l'item..." class="flex-1 text-xs px-2.5 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 bg-white dark:bg-gray-900 text-black dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <button type="submit" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 px-2">Ajouter</button>
                                <button type="button" @click="open = false" class="text-[11px] text-black/40 dark:text-white/40 hover:text-black/60 dark:hover:text-white/60">✕</button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-xs text-black/40 py-2">Aucune sous-tâche pour le moment.</p>
                @endforelse
            </div>
        </div>

        <div class="d-divider"></div>

        {{-- ── SECTION ASSIGNÉES (T-008) ── --}}
        @if(isset($group) && $group->count() > 1)
        <div class="px-6 py-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm font-semibold text-black">Assignée à {{ $group->count() }} personne{{ $group->count() > 1 ? 's' : '' }}</h3>
                    <p class="text-xs mt-0.5 text-black/40">
                        La progression totale = le % déjà fait avant l'équipe + la progression de chacun, le tout divisé par ({{ $task->base_progress_percent !== null ? $group->count() + 1 : $group->count() }}).
                    </p>
                </div>
            </div>

            <div class="grid gap-2 sm:grid-cols-2">
                @foreach($group as $memberUser)
                @php
                    $memberPct = 0;
                    $memberLatest = $task->dailyReports
                        ->filter(fn($r) => $r->user_id === (int) $memberUser->id && $r->task_progress_percent !== null)
                        ->sortByDesc('id')
                        ->first();
                    if ($memberLatest) { $memberPct = (int) $memberLatest->task_progress_percent; }
                @endphp
                <div class="rounded-xl border border-black/6 bg-black/2.5 px-4 py-3
                            {{ (int) $memberUser->id === (int) $user->id ? 'ring-2 ring-black/5' : '' }}">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="$memberUser->name ?? '?'" :src="$memberUser?->avatar_url" size="sm" />
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-black truncate">
                                {{ $memberUser->name ?? 'Inconnu' }}
                            </p>
                            <span class="text-[11px] font-semibold rounded-lg px-2 py-0.5
                                         {{ (int) $memberUser->id === (int) $user->id ? 'bg-indigo-50 text-indigo-600' : 'bg-black/5 text-black/45' }}">
                                {{ (int) $memberUser->id === (int) $user->id ? 'Vous' : 'Participant' }} · {{ $memberPct }}%
                            </span>
                        </div>
                    </div>

                    <div class="mt-2.5 flex items-center gap-2">
                        <div class="flex-1 h-1 rounded-full overflow-hidden bg-black/7">
                            <div class="h-full rounded-full" style="width:{{ $memberPct }}%; background:#0a0a0a;"></div>
                        </div>
                        <span class="ws-mono text-[11px] font-bold text-black w-8 text-right">{{ $memberPct }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="d-divider"></div>
        @endif

        {{-- ── SECTION RAPPORTS ── --}}
        <div class="flex items-center justify-between px-6 py-4">
            <div>
                <h3 class="text-sm font-semibold text-black">Rapports d'activité académiques</h3>
                <p class="text-xs mt-0.5 text-black/40">{{ $reportCount }} rapport{{ $reportCount !== 1 ? 's' : '' }} déposé{{ $reportCount !== 1 ? 's' : '' }}</p>
            </div>
            @if($isParticipant && !$task->isCompleted() && !$isFirstReport)
            <span class="text-xs font-medium px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700">↓ Rapport du jour en bas</span>
            @endif
        </div>

        @if($task->dailyReports->isEmpty())
        <div class="px-6 pb-8 text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl mb-3 bg-black/4">
                <svg class="h-6 w-6 opacity-30" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
            </div>
            <p class="text-sm font-medium opacity-60">Aucun rapport pour l'instant</p>
        </div>
        @else
        <div class="divide-y divide-black/5">
            @foreach($task->dailyReports as $report)
            @php
                $authorUser = $report->etudiant?->user ?? $report->user;
                $authorName = $authorUser?->name ?? 'Producteur';
                $authorAvatarUrl = $authorUser?->avatar_url;
                $initials   = strtoupper(substr($authorName, 0, 2));
            @endphp

            <div class="px-6 py-6">
                {{-- En-tête rapport --}}
                <div class="flex items-center justify-between gap-4 mb-5">
                    <div class="flex items-center gap-3">
                        @if($authorAvatarUrl)
                        <img src="{{ $authorAvatarUrl }}" alt="{{ $authorName }}" class="inline-flex h-9 w-9 rounded-full object-cover shrink-0">
                        @else
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white shrink-0 bg-black">{{ $initials }}</span>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-black">{{ $authorName }}</p>
                            <time class="text-xs text-black/40">{{ $report->report_date->format('l j F Y') }}</time>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        @if($report->hours_declared > 0)
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-black/5 text-black/60">{{ $report->hours_declared }}h</span>
                        @endif
                        @if($report->task_progress_percent !== null)
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700">{{ $report->task_progress_percent }}%</span>
                        @endif
                        @if($report->status === 'reviewed')
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800">Relu ✓</span>
                        @elseif($report->status === 'submitted')
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700">Soumis</span>
                        @else
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800">Brouillon</span>
                        @endif
                    </div>
                </div>

                {{-- Corps rapport --}}
                <div class="space-y-3 pl-12">
                    @if($report->introduction)
                    <div>
                        <span class="d-section-label">1. Introduction</span>
                        <p class="text-sm leading-relaxed whitespace-pre-line text-black/70">{{ $report->introduction }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="d-section-label">{{ $report->introduction ? '2.' : '1.' }} Travail réalisé</span>
                        <p class="text-sm leading-relaxed whitespace-pre-line text-black/70">{{ $report->summary }}</p>
                    </div>
                    @if($report->blockers)
                    <div>
                        <span class="d-section-label text-red-500/50">{{ $report->introduction ? '3.' : '2.' }} Difficultés</span>
                        <p class="text-sm leading-relaxed whitespace-pre-line text-red-700/80">{{ $report->blockers }}</p>
                    </div>
                    @endif
                    @if($report->next_steps)
                    @php $secNum = 1 + ($report->introduction?1:0) + ($report->blockers?1:0); @endphp
                    <div>
                        <span class="d-section-label text-indigo-500/50">{{ $secNum+1 }}. Prochaines étapes</span>
                        <p class="text-sm leading-relaxed whitespace-pre-line text-indigo-900/80">{{ $report->next_steps }}</p>
                    </div>
                    @endif
                </div>

                {{-- Actions reviewer --}}
                @if($isReviewer && !$task->isCompleted() && $report->status === 'submitted')
                <div class="flex items-center gap-2 mt-5 pl-12">
                    <form method="POST" action="{{ route('reports.comments.store', $report->id) }}" class="inline">
                        @csrf <input type="hidden" name="comment" value="Rapport relu et validé.">
                        <button class="d-btn-green text-xs py-1.5 px-3">✓ Valider</button>
                    </form>
                    <form method="POST" action="{{ route('reports.comments.store', $report->id) }}" class="inline">
                        @csrf <input type="hidden" name="comment" value="Des modifications sont demandées.">
                        <button class="d-btn-amber text-xs py-1.5 px-3">⚠ Corrections</button>
                    </form>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif

        {{-- ── FORMULAIRE RAPPORT DU JOUR ── --}}
        @if($isParticipant && !$task->isCompleted())
        <div class="d-divider"></div>

        @php
            // Collecter les items de l'utilisateur : uniquement ceux des sous-tâches
            // qui lui sont assignées + les sous-tâches libres (jamais celles d'autrui).
            $myItems = collect();
            foreach ($task->subtasks as $st) {
                $isMine = (int) $st->assigned_to_user_id === (int) $user->id;
                $isFree = is_null($st->assigned_to_user_id);
                if ($isMine || $isFree) {
                    foreach ($st->items as $item) {
                        $myItems->push([
                            'id' => $item->id,
                            'title' => $item->title,
                            'is_completed' => (bool) $item->is_completed,
                            'subtask_id' => $st->id,
                            'subtask_title' => $st->title,
                        ]);
                    }
                }
            }

            // Fallback : si pas d'items, utiliser les sous-tâches directement —
            // restreint aux sous-tâches de l'utilisateur + libres uniquement.
            $useItems = $myItems->isNotEmpty();
            if (!$useItems) {
                $subtasksPayload = $task->subtasks
                    ->filter(fn($st) => (int) $st->assigned_to_user_id === (int) $user->id || is_null($st->assigned_to_user_id))
                    ->map(fn($st) => [
                        'id' => $st->id,
                        'title' => $st->title,
                        'is_completed' => (bool) $st->is_completed,
                        'is_mine' => (int) $st->assigned_to_user_id === (int) $user->id,
                        'assigned_name' => $st->assignedTo?->name,
                        'assigned_to_user_id' => $st->assigned_to_user_id,
                    ])->values();
            } else {
                $subtasksPayload = $myItems->values();
            }
            $subtasksTotal = $useItems ? $myItems->count() : $subtasksPayload->count();
            $subtasksDone = $useItems ? $myItems->where('is_completed', true)->count() : $subtasksPayload->where('is_completed', true)->count();
            // Rien à valider : l'utilisateur n'a ni items ni sous-tâches à sa charge.
            $hasNothingToValidate = $subtasksTotal === 0;
        @endphp

        <div class="px-6 py-5" x-data="{
            open: {{ $isFirstReport ? 'true' : 'false' }},
            edit: false,
            useItems: {{ $useItems ? 'true' : 'false' }},
            subtasks: @js($subtasksPayload),
            checkedSubtasks: [],
            subtasksTotal: {{ $subtasksTotal }},
            subtasksDone: {{ $subtasksDone }},
            basePercent: {{ $task->computeProgressFromSubtasks() }},
            get livePercent() {
                if (this.subtasksTotal === 0) return this.basePercent;
                const countDone = this.subtasksDone + this.checkedSubtasks.length;
                return Math.min(100, Math.round((countDone / this.subtasksTotal) * 100));
            },
            get mySubtasks() {
                return this.subtasks;
            },
            get nextStepTitle() {
                const checked = this.checkedSubtasks.map(Number);
                const remaining = this.subtasks.find(s => !s.is_completed && !checked.includes(Number(s.id)));
                return remaining ? remaining.title : null;
            }
        }">

            {{-- Toggle header --}}
            <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between gap-4 text-left group">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl shrink-0"
                         :style="open
                            ? 'background: linear-gradient(135deg,#6366f1,#3b82f6); color:#fff;'
                            : 'background:rgba(99,102,241,.10); color:#6366f1;'">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L6.75 20.54l-4 1 1-4 13.112-13.053Z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-[.15em]"
                              style="color:#6366f1;">{{ $isFirstReport ? 'Premier rapport' : 'Rapport du jour' }}</span>
                        <p class="text-sm font-semibold mt-0.5 text-black">
                            {{ $todayReport ? "Rapport déposé aujourd'hui — Modifier ↗" : "Déposer un rapport d'activité" }}
                        </p>
                    </div>
                </div>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl shrink-0 transition-all"
                      :style="open
                        ? 'background:rgba(99,102,241,.12);color:#6366f1;transform:rotate(180deg)'
                        : 'background:rgba(0,0,0,.05);color:rgba(0,0,0,.40)'">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                    </svg>
                </span>
            </button>

            {{-- Corps collapsible --}}
            <div x-show="open" x-collapse>
                <div class="mt-5">

                    {{-- ── Rapport déjà déposé aujourd'hui ── --}}
                    @if($todayReport)
                    <div class="d-report-done">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <span class="text-sm font-semibold text-emerald-700">Rapport soumis · {{ (int)$todayReport->task_progress_percent }}%</span>
                            </div>
                            <button type="button" @click="edit=!edit"
                               class="text-xs font-medium text-black/50 hover:text-black/80 transition hover:underline"
                               x-text="edit ? 'Annuler' : 'Modifier →'">
                            </button>
                        </div>
                        <div x-show="!edit" class="space-y-3">
                            @if($todayReport->introduction)
                            <div>
                                <span class="d-section-label">Introduction</span>
                                <p class="text-sm text-black/70 leading-relaxed">{{ $todayReport->introduction }}</p>
                            </div>
                            @endif
                            <div>
                                <span class="d-section-label">Travail réalisé</span>
                                <p class="text-sm text-black/70 leading-relaxed">{{ $todayReport->summary }}</p>
                            </div>
                            @if($todayReport->blockers)
                            <div>
                                <span class="d-section-label" style="color:rgba(239,68,68,.55);">Difficultés</span>
                                <p class="text-sm leading-relaxed" style="color:rgba(185,28,28,.75);">{{ $todayReport->blockers }}</p>
                            </div>
                            @endif
                            @if($todayReport->next_steps)
                            <div>
                                <span class="d-section-label" style="color:rgba(99,102,241,.6);">Prochaines étapes</span>
                                <p class="text-sm leading-relaxed" style="color:rgba(55,48,163,.75);">{{ $todayReport->next_steps }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- ── Formulaire édition inline ── --}}
                        <form x-show="edit" method="POST" action="{{ route('reports.update', $todayReport->id) }}" class="space-y-5">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status_action" value="submit">
                            <input type="hidden" name="task_id" value="{{ $task->id }}">

                            {{-- Validation des items / sous-tâches --}}
                            @if($hasNothingToValidate)
                            <div class="d-field">
                                <div class="rounded-xl border border-dashed border-indigo-200 bg-indigo-50/40 dark:border-indigo-900/30 dark:bg-indigo-950/10 px-4 py-4">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                        <span class="text-indigo-500">☑</span>
                                        Aucune sous-tâche ni item à valider pour vous
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        Vous n'êtes assigné à aucune sous-tâche libre, et il n'y a pas d'item vous concernant sur cette tâche.
                                        Créez une nouvelle tâche, ou réassignez des sous-tâches à de nouveaux collaborateurs depuis la page
                                        « Assigner une tâche ».
                                    </p>
                                </div>
                            </div>
                            @elseif($myItems->isNotEmpty() || $task->subtasks->isNotEmpty())
                            <div class="d-field">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="d-field-label mb-0 flex items-center gap-1.5 text-slate-900 dark:text-white font-bold">
                                        <span class="text-indigo-600">☑</span>
                                        @if($useItems)
                                            Mes items à valider
                                        @else
                                            Sous-tâches de la tâche
                                        @endif
                                    </label>
                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/30"
                                          x-text="(subtasksDone + checkedSubtasks.length) + ' / ' + subtasksTotal + ' terminées'">
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                                    @if($useItems)
                                        Cochez les items que vous avez terminés aujourd'hui. Ils seront validés à l'envoi du rapport.
                                    @else
                                        Cochez les sous-tâches que vous avez terminées aujourd'hui. Elles seront validées à l'envoi du rapport.
                                    @endif
                                </p>

                                <div class="space-y-2">
                                    <template x-for="st in mySubtasks" :key="st.id">
                                        <label class="flex items-start gap-3 p-3 rounded-xl border transition cursor-pointer"
                                               :class="st.is_completed ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900 dark:bg-emerald-950/20 dark:border-emerald-900/40 dark:text-emerald-300' : (checkedSubtasks.includes(st.id) ? 'bg-indigo-50/60 border-indigo-300 ring-2 ring-indigo-500/15 dark:bg-indigo-950/30 dark:border-indigo-700' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50 dark:bg-white/[0.02] dark:border-white/10 dark:hover:bg-white/[0.05]')">
                                            <input type="checkbox"
                                                   name="completed_subtask_ids[]"
                                                   :value="st.id"
                                                   :disabled="st.is_completed"
                                                   :checked="st.is_completed"
                                                   x-model="checkedSubtasks"
                                                   class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 accent-indigo-600">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-sm font-semibold"
                                                          :class="st.is_completed ? 'line-through opacity-70' : 'text-slate-900 dark:text-white'"
                                                          x-text="st.title"></span>
                                                    @if($useItems)
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500"
                                                          x-text="'↳ ' + st.subtask_title"></span>
                                                    @endif
                                                    <template x-if="st.is_completed">
                                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                            ✓ Déjà validée
                                                        </span>
                                                    </template>
                                                    <template x-if="!st.is_completed">
                                                        <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full transition"
                                                              :class="checkedSubtasks.includes(st.id) ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300'"
                                                              x-text="checkedSubtasks.includes(st.id) ? 'À valider' : 'En cours'">
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            @endif

                            {{-- Travail réalisé --}}
                            <div class="d-field">
                                <label class="d-field-label">
                                    Travail réalisé
                                    <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea name="summary" rows="4" required
                                          class="d-input resize-none"
                                          placeholder="Décris précisément ce que tu as accompli aujourd'hui…">{{ $todayReport->summary }}</textarea>
                            </div>

                            {{-- Progression automatique de la tâche (redessinée) --}}
                            <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 p-4 dark:border-white/10 dark:from-white/[0.04] dark:via-white/[0.02] dark:to-indigo-950/20 shadow-sm">
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-bold text-xs shadow-sm">
                                            %
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 block">
                                                Progression de la tâche
                                            </span>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">
                                                Augmente automatiquement selon les sous-tâches sélectionnées
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-baseline justify-end gap-1.5">
                                            <span class="font-mono text-2xl font-black text-slate-900 dark:text-white" x-text="livePercent + '%'"></span>
                                            <span x-show="checkedSubtasks.length > 0" x-cloak
                                                  class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-1.5 py-0.5 rounded-md border border-emerald-200/60"
                                                  x-text="'+' + (livePercent - basePercent) + '%'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-200/80 dark:bg-white/10 p-0.5 shadow-inner">
                                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 via-blue-500 to-emerald-500 transition-all duration-500 ease-out shadow-sm"
                                         :style="`width: ${livePercent}%`"></div>
                                </div>

                                <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="font-mono">0%</span>
                                    <span x-show="checkedSubtasks.length > 0" x-cloak class="font-semibold text-indigo-600 dark:text-indigo-400"
                                          x-text="checkedSubtasks.length + ' sous-tâche(s) sélectionnée(s)'"></span>
                                    <span x-show="checkedSubtasks.length === 0" class="text-slate-400">
                                        {{ $task->subtasks->isEmpty() ? 'Aucune sous-tâche définie' : 'Cochez une sous-tâche pour faire progresser' }}
                                    </span>
                                    <span class="font-mono">100%</span>
                                </div>
                            </div>

                            {{-- Difficultés + Heures --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="d-field">
                                    <label class="d-field-label">Difficultés rencontrées <span class="normal-case tracking-normal font-normal opacity-60">(optionnel)</span></label>
                                    <input type="text" name="blockers"
                                           class="d-input"
                                           value="{{ $todayReport->blockers }}"
                                           placeholder="Ex : accès API indisponible…">
                                </div>
                                <div class="d-field">
                                    <label class="d-field-label">Heures travaillées</label>
                                    <input type="number" name="hours_declared"
                                           min="0" max="24" step="0.5"
                                           class="d-input"
                                           value="{{ $todayReport->hours_declared }}"
                                           placeholder="Ex : 7.5">
                                </div>
                            </div>

                            {{-- Prochaine étape (remplace l'ancien champ texte libre) --}}
                            <div class="d-field">
                                <label class="d-field-label flex items-center gap-1.5 text-slate-900 dark:text-white font-bold mb-2">
                                    <span class="text-blue-600 dark:text-blue-400">🎯</span> Prochaine étape
                                </label>
                                <template x-if="nextStepTitle">
                                    <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/70 p-3.5 text-sm dark:border-blue-900/30 dark:bg-blue-950/20">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-bold shadow-sm">🎯</span>
                                        <div class="min-w-0">
                                            <span class="text-[10px] uppercase tracking-wider text-blue-600 dark:text-blue-400 font-bold block">Étape suivante calculée</span>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-white truncate block" x-text="nextStepTitle"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!nextStepTitle">
                                    <div class="flex items-center gap-2.5 rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3 text-xs font-semibold text-emerald-800 dark:border-emerald-900/30 dark:bg-emerald-950/20 dark:text-emerald-300">
                                        <span class="text-base">✅</span> Toutes les sous-tâches sont terminées.
                                    </div>
                                </template>
                                <input type="hidden" name="next_steps" :value="nextStepTitle ? nextStepTitle : 'Toutes les sous-tâches sont terminées'">
                            </div>

                            {{-- Footer formulaire --}}
                            <div class="flex items-center justify-between pt-1" style="border-top:1.5px solid rgba(99,102,241,.10);">
                                <button type="button" @click="edit=false"
                                        class="text-sm font-medium transition rounded-lg px-4 py-2.5"
                                        style="background:rgba(0,0,0,.05);color:rgba(0,0,0,.55);">
                                    Annuler
                                </button>
                                <button type="submit" class="d-btn-dark">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/>
                                    </svg>
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ── Formulaire nouveau rapport ── --}}
                    @else
                    <div class="d-form-section">

                        {{-- En-tête formulaire --}}
                        <div class="flex items-center gap-2 mb-5 pb-4" style="border-bottom:1.5px solid rgba(99,102,241,.12);">
                            <svg class="h-4 w-4 shrink-0" style="color:#6366f1;" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/>
                            </svg>
                            <p class="text-[11px] font-semibold uppercase tracking-[.14em]" style="color:#6366f1;">
                                Nouveau rapport — {{ now()->isoFormat('dddd D MMMM') }}
                            </p>
                        </div>

<form method="POST" action="{{ route('reports.store') }}" class="space-y-5">
                            @csrf
                            <input type="hidden" name="status_action" value="submit">
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <input type="hidden" name="latitude" id="report-latitude" value="">
                            <input type="hidden" name="longitude" id="report-longitude" value="">
                            <input type="hidden" name="accuracy_meters" id="report-accuracy" value="">
                            <input type="hidden" name="location_method" id="report-location-method" value="">

                            {{-- Validation des items (remplace l'ancien bloc sous-tâches) --}}
                            @if($myItems->isNotEmpty() || $task->subtasks->isNotEmpty())
                            <div class="d-field">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="d-field-label mb-0 flex items-center gap-1.5 text-slate-900 dark:text-white font-bold">
                                        <span class="text-indigo-600">☑</span>
                                        @if($useItems)
                                            Mes items à valider
                                        @else
                                            Sous-tâches de la tâche
                                        @endif
                                    </label>
                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/30"
                                          x-text="(subtasksDone + checkedSubtasks.length) + ' / ' + subtasksTotal + ' terminées'">
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                                    @if($useItems)
                                        Cochez les items que vous avez terminés aujourd'hui. Ils seront validés à l'envoi du rapport.
                                    @else
                                        Cochez les sous-tâches que vous avez terminées aujourd'hui. Elles seront validées à l'envoi du rapport.
                                    @endif
                                </p>

                                <div class="space-y-2">
                                    <template x-for="st in mySubtasks" :key="st.id">
                                        <label class="flex items-start gap-3 p-3 rounded-xl border transition cursor-pointer"
                                               :class="st.is_completed ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900 dark:bg-emerald-950/20 dark:border-emerald-900/40 dark:text-emerald-300' : (checkedSubtasks.includes(st.id) ? 'bg-indigo-50/60 border-indigo-300 ring-2 ring-indigo-500/15 dark:bg-indigo-950/30 dark:border-indigo-700' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50 dark:bg-white/[0.02] dark:border-white/10 dark:hover:bg-white/[0.05]')">
                                            <input type="checkbox"
                                                   name="completed_subtask_ids[]"
                                                   :value="st.id"
                                                   :disabled="st.is_completed"
                                                   :checked="st.is_completed"
                                                   x-model="checkedSubtasks"
                                                   class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 accent-indigo-600">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-sm font-semibold"
                                                          :class="st.is_completed ? 'line-through opacity-70' : 'text-slate-900 dark:text-white'"
                                                          x-text="st.title"></span>
                                                    @if($useItems)
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500"
                                                          x-text="'↳ ' + st.subtask_title"></span>
                                                    @endif
                                                    <template x-if="st.is_completed">
                                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                            ✓ Déjà validée
                                                        </span>
                                                    </template>
                                                    <template x-if="!st.is_completed">
                                                        <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full transition"
                                                              :class="checkedSubtasks.includes(st.id) ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300'"
                                                              x-text="checkedSubtasks.includes(st.id) ? 'À valider' : 'En cours'">
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            @endif

                            {{-- Travail réalisé --}}
                            <div class="d-field">
                                <label class="d-field-label">
                                    Travail réalisé
                                    <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea name="summary" rows="4" required
                                          class="d-input resize-none"
                                          placeholder="Décris précisément ce que tu as accompli aujourd'hui…"></textarea>
                            </div>

                            {{-- Progression automatique de la tâche (redessinée) --}}
                            <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 p-4 dark:border-white/10 dark:from-white/[0.04] dark:via-white/[0.02] dark:to-indigo-950/20 shadow-sm">
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-bold text-xs shadow-sm">
                                            %
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 block">
                                                Progression de la tâche
                                            </span>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">
                                                Augmente automatiquement selon les sous-tâches sélectionnées
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-baseline justify-end gap-1.5">
                                            <span class="font-mono text-2xl font-black text-slate-900 dark:text-white" x-text="livePercent + '%'"></span>
                                            <span x-show="checkedSubtasks.length > 0" x-cloak
                                                  class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-1.5 py-0.5 rounded-md border border-emerald-200/60"
                                                  x-text="'+' + (livePercent - basePercent) + '%'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-200/80 dark:bg-white/10 p-0.5 shadow-inner">
                                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 via-blue-500 to-emerald-500 transition-all duration-500 ease-out shadow-sm"
                                         :style="`width: ${livePercent}%`"></div>
                                </div>

                                <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="font-mono">0%</span>
                                    <span x-show="checkedSubtasks.length > 0" x-cloak class="font-semibold text-indigo-600 dark:text-indigo-400"
                                          x-text="checkedSubtasks.length + ' sous-tâche(s) sélectionnée(s)'"></span>
                                    <span x-show="checkedSubtasks.length === 0" class="text-slate-400">
                                        {{ $task->subtasks->isEmpty() ? 'Aucune sous-tâche définie' : 'Cochez une sous-tâche pour faire progresser' }}
                                    </span>
                                    <span class="font-mono">100%</span>
                                </div>
                            </div>

                            {{-- Difficultés + Heures --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="d-field">
                                    <label class="d-field-label">Difficultés rencontrées <span class="normal-case tracking-normal font-normal opacity-60">(optionnel)</span></label>
                                    <input type="text" name="blockers"
                                           class="d-input"
                                           placeholder="Ex : accès API indisponible…">
                                </div>
                                <div class="d-field">
                                    <label class="d-field-label">Heures travaillées</label>
                                    <input type="number" name="hours_declared"
                                           min="0" max="24" step="0.5"
                                           class="d-input"
                                           placeholder="Ex : 7.5">
                                </div>
                            </div>

                            {{-- Prochaine étape (remplace l'ancien champ texte libre) --}}
                            <div class="d-field">
                                <label class="d-field-label flex items-center gap-1.5 text-slate-900 dark:text-white font-bold mb-2">
                                    <span class="text-blue-600 dark:text-blue-400">🎯</span> Prochaine étape
                                </label>
                                <template x-if="nextStepTitle">
                                    <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/70 p-3.5 text-sm dark:border-blue-900/30 dark:bg-blue-950/20">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-bold shadow-sm">🎯</span>
                                        <div class="min-w-0">
                                            <span class="text-[10px] uppercase tracking-wider text-blue-600 dark:text-blue-400 font-bold block">Étape suivante calculée</span>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-white truncate block" x-text="nextStepTitle"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!nextStepTitle">
                                    <div class="flex items-center gap-2.5 rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3 text-xs font-semibold text-emerald-800 dark:border-emerald-900/30 dark:bg-emerald-950/20 dark:text-emerald-300">
                                        <span class="text-base">✅</span> Toutes les sous-tâches sont terminées.
                                    </div>
                                </template>
                                <input type="hidden" name="next_steps" :value="nextStepTitle ? nextStepTitle : 'Toutes les sous-tâches sont terminées'">
                            </div>

                            {{-- Footer formulaire --}}
                            <div class="flex items-center justify-between pt-1" style="border-top:1.5px solid rgba(99,102,241,.10);">
                                <p class="text-xs" style="color:rgba(0,0,0,.35);">
                                    <svg class="inline h-3 w-3 mr-1 opacity-60" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                                    Un seul rapport par jour
                                </p>
                                <button type="submit" class="d-btn-dark">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/>
                                    </svg>
                                    {{ $isFirstReport ? 'Soumettre le premier rapport' : 'Soumettre le rapport' }}
                                </button>
                            </div>

                        </form>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endif

        {{-- ── DISCUSSION D'ÉQUIPE (T-009) : FAB + chat global --}}
        @include('tasks.partials.team-chat', ['task' => $task, 'user' => $user, 'chat' => $chat ?? null])

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('canMessage', @js($canComment));
    });

    // ── Capture GPS pour le formulaire de rapport (T-006) ──
    (function () {
        function fillReportLocation(position) {
            const latField = document.getElementById('report-latitude');
            const lngField = document.getElementById('report-longitude');
            const accField = document.getElementById('report-accuracy');
            const locField = document.getElementById('report-location-method');
            if (!latField || !lngField) return;
            latField.value = position.coords.latitude;
            lngField.value = position.coords.longitude;
            accField.value = Math.round(position.coords.accuracy || 0);
            locField.value = 'geolocation';
        }

        function setLocationError() {
            const locField = document.getElementById('report-location-method');
            if (locField) locField.value = 'gps-unavailable';
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(fillReportLocation, setLocationError, {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0,
            });
        } else {
            setLocationError();
        }
    })();
</script>
@endpush
