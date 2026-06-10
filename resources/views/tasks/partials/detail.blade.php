@php
    $user = auth()->user();
    $isOwner     = $task->owner_id === $user->id;
    $isAdmin     = $user->hasRole('admin');
    $isReviewer  = $user->hasAnyRole(['admin', 'superviseur']) && !$isOwner;
    $canComment  = $isOwner || $isReviewer;
    $isFirstReport = $task->dailyReports->isEmpty();

    $points = $task->dailyReports->reverse()
        ->filter(fn($r) => !is_null($r->task_progress_percent))
        ->map(fn($r) => (int) $r->task_progress_percent)->values();

    $pct    = max(0, min(100, (int) $task->last_progress_percent));
    $ringR  = 22; $ringC = 2 * M_PI * $ringR;
    $ringOff = $ringC * (1 - ($pct / 100));
    $reportCount = $task->dailyReports->count();

    $priorityBarGradient = match($task->priority) {
        'urgent' => 'linear-gradient(90deg,#ef4444,#f97316)',
        'high'   => 'linear-gradient(90deg,#f59e0b,#ef4444)',
        'low'    => 'linear-gradient(90deg,rgba(0,0,0,.1),rgba(0,0,0,.18))',
        default  => 'linear-gradient(90deg,#6366f1,#3b82f6)',
    };
    $priorityMeta = match($task->priority) {
        'urgent' => ['label' => 'Urgente', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.08)'],
        'high'   => ['label' => 'Haute',   'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.08)'],
        'low'    => ['label' => 'Basse',   'color' => 'rgba(0,0,0,.35)', 'bg' => 'rgba(0,0,0,.04)'],
        default  => ['label' => 'Normale', 'color' => '#6366f1', 'bg' => 'rgba(99,102,241,.08)'],
    };
    $stateMeta = match($task->status) {
        'completed'          => ['label' => 'Terminée',              'color' => '#10b981', 'bg' => 'rgba(16,185,129,.08)'],
        'awaiting_validation'=> ['label' => 'En attente validation', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.08)'],
        'blocked'            => ['label' => 'Bloquée',               'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.08)'],
        'in_progress'        => ['label' => 'En cours',              'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,.08)'],
        'changes_requested'  => ['label' => 'Corrections demandées', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.08)'],
        default              => ['label' => 'À faire',               'color' => 'rgba(0,0,0,.4)', 'bg' => 'rgba(0,0,0,.04)'],
    };
@endphp

<style>
@keyframes d-in { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
@keyframes d-pulse { 0%,100%{opacity:.7;transform:scale(1)} 50%{opacity:0;transform:scale(2.2)} }

.d-in    { animation: d-in .36s cubic-bezier(.16,1,.3,1) both; }
.d-in-2  { animation: d-in .36s .06s cubic-bezier(.16,1,.3,1) both; }
.d-in-3  { animation: d-in .36s .12s cubic-bezier(.16,1,.3,1) both; }
.d-pulse { animation: d-pulse 2s ease-in-out infinite; }

.d-panel {
    background: #fff;
    border: 1px solid rgba(0,0,0,.07);
    box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 4px 20px rgba(0,0,0,.04);
    border-radius: 16px;
    overflow: hidden;
}
.d-input {
    background: rgba(0,0,0,.03);
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 10px;
    transition: border-color .15s, background .15s, box-shadow .15s;
    width: 100%;
    font-size: .875rem;
    padding: .625rem .875rem;
    line-height: 1.5;
}
.d-input:focus { outline:none; background:#fff; border-color:rgba(0,0,0,.22); box-shadow:0 0 0 3px rgba(0,0,0,.05); }
.d-btn-dark {
    display:inline-flex; align-items:center; gap:.375rem;
    background:#0f0f0f; color:#fff; font-size:.8125rem; font-weight:600;
    padding:.5rem 1rem; border-radius:10px; border:none; cursor:pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,.18), 0 4px 12px rgba(0,0,0,.1);
    transition: background .15s, transform .15s;
}
.d-btn-dark:hover { background:#222; transform:translateY(-1px); }
.d-btn-ghost {
    display:inline-flex; align-items:center; gap:.375rem;
    background:transparent; border:1px solid rgba(0,0,0,.1); color:rgba(0,0,0,.6);
    font-size:.8125rem; font-weight:500; padding:.5rem 1rem; border-radius:10px; cursor:pointer;
    transition: background .15s;
}
.d-btn-ghost:hover { background:rgba(0,0,0,.04); }
.d-btn-green {
    display:inline-flex; align-items:center; gap:.375rem;
    background:#059669; color:#fff; font-size:.8125rem; font-weight:600;
    padding:.5rem 1rem; border-radius:10px; border:none; cursor:pointer;
    transition: background .15s, transform .15s;
}
.d-btn-green:hover { background:#047857; transform:translateY(-1px); }
.d-btn-amber {
    display:inline-flex; align-items:center; gap:.375rem;
    background:rgba(245,158,11,.1); color:#b45309; font-size:.8125rem; font-weight:600;
    padding:.5rem 1rem; border-radius:10px; border:1px solid rgba(245,158,11,.25); cursor:pointer;
    transition: background .15s;
}
.d-btn-amber:hover { background:rgba(245,158,11,.18); }
.d-range { accent-color:#0f0f0f; width:100%; }
.d-section-label {
    font-size:.65rem; font-weight:700; letter-spacing:.12em;
    text-transform:uppercase; color:rgba(0,0,0,.35);
    margin-bottom:.375rem; display:block;
}
</style>

<div class="d-in space-y-0">

{{-- ══════════════════════════════════════════════════════
     GRAND CONTENEUR UNIFIÉ
══════════════════════════════════════════════════════ --}}
<div class="d-panel d-in">

    {{-- ── Barre de priorité ──────────────────────────── --}}
    <div class="h-[3px]" style="background:{{ $priorityBarGradient }};"></div>

    {{-- ── EN-TÊTE TÂCHE ──────────────────────────────── --}}
    <div class="px-6 pt-5 pb-5">

        <div class="flex items-start justify-between gap-4">

            {{-- Titre + badges --}}
            <div class="flex items-start gap-4 min-w-0">

                {{-- Ring progression --}}
                <div class="hidden sm:flex shrink-0 relative" style="width:56px;height:56px;">
                    <svg class="-rotate-90" width="56" height="56" viewBox="0 0 56 56">
                        <circle cx="28" cy="28" r="{{ $ringR }}" fill="none" stroke-width="4" stroke="rgba(0,0,0,.07)"/>
                        <circle cx="28" cy="28" r="{{ $ringR }}" fill="none" stroke-width="4" stroke-linecap="round"
                                stroke="#0f0f0f"
                                stroke-dasharray="{{ round($ringC,2) }}"
                                stroke-dashoffset="{{ round($ringOff,2) }}"
                                style="transition:stroke-dashoffset .8s cubic-bezier(.16,1,.3,1);"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="ws-mono text-xs font-semibold">{{ $pct }}%</span>
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-semibold leading-snug" style="letter-spacing:-.02em;color:#0a0a0a;">
                        {{ $task->title }}
                    </h2>
                    {{-- Badges inline --}}
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
                        <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-semibold"
                              style="background:rgba(239,68,68,.07);color:#dc2626;">
                            ⚠ En retard
                        </span>
                        @endif
                    </div>
                    {{-- Meta infos --}}
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs" style="color:rgba(0,0,0,.45);">
                        <span class="inline-flex items-center gap-1.5">
                            <x-avatar :name="$task->owner?->name ?? '?'" size="xs" />
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
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                @if($isAdmin)
                    @if($task->isCompleted())
                    <form method="POST" action="{{ encrypted_route('tasks.reopen', $task) }}">
                        @csrf
                        <button class="d-btn-amber">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75M3.75 10.5h12A1.5 1.5 0 0 1 17.25 12v6a1.5 1.5 0 0 1-1.5 1.5h-12a1.5 1.5 0 0 1-1.5-1.5v-6a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                            Rouvrir
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ encrypted_route('tasks.complete', $task) }}"
                          onsubmit="return confirm('Clôturer cette tâche définitivement ?')">
                        @csrf
                        <button class="d-btn-green">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            Terminer
                        </button>
                    </form>
                    @endif
                @elseif($isReviewer && !$task->isCompleted())
                <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">
                    @csrf <input type="hidden" name="action" value="request_changes">
                    <button class="d-btn-amber">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                        Corrections
                    </button>
                </form>
                @endif

                @if($isOwner)
                <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
                    <button type="button" @click="open=!open" @click.outside="open=false"
                            class="d-btn-ghost h-9 w-9 p-0 justify-center rounded-xl">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7.25a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Zm0 6a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Zm0 6a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Z"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 z-30 mt-1.5 w-44 rounded-xl border p-1.5"
                         style="background:#fff;border-color:rgba(0,0,0,.08);box-shadow:0 4px 24px rgba(0,0,0,.13);">
                        @unless($task->isCompleted())
                        <a href="{{ encrypted_route('tasks.edit', $task) }}"
                           class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-slate-50 transition" style="color:rgba(0,0,0,.7);">
                            <svg class="h-3.5 w-3.5 opacity-50" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 2.65 2.65M4 20l.72-3.95c.07-.39.26-.74.54-1.02L16.3 3.94a1.5 1.5 0 0 1 2.12 0l1.64 1.64a1.5 1.5 0 0 1 0 2.12L9.03 18.74c-.28.28-.63.47-1.02.54L4 20Z"/></svg>
                            Modifier
                        </a>
                        @endunless
                        <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-red-50 transition text-left" style="color:#dc2626;">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="mt-5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="d-section-label" style="margin-bottom:0;">Progression</span>
                <span class="ws-mono text-sm font-bold" style="color:#0a0a0a;">{{ $pct }}%</span>
            </div>
            <div class="h-1.5 rounded-full overflow-hidden" style="background:rgba(0,0,0,.07);">
                <div class="h-full rounded-full transition-all duration-700" style="width:{{ $pct }}%;background:#0a0a0a;"></div>
            </div>
            @if($points->count() >= 2)
            @php
                $w=100;$h=28;$n=$points->count();
                $coords=$points->map(fn($p,$i)=>round($n>1?$i*($w/($n-1)):0,1).','.round($h-($p/100*$h),1))->implode(' ');
                $area='0,'.$h.' '.$coords.' '.$w.','.$h;
            @endphp
            <svg class="mt-2 w-full" height="28" viewBox="0 0 100 28" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="spk{{ $task->id }}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10b981" stop-opacity=".12"/>
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon points="{{ $area }}" fill="url(#spk{{ $task->id }})"/>
                <polyline points="{{ $coords }}" fill="none" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            @endif
        </div>

        {{-- Description --}}
        @if($task->description)
        <div class="mt-4 rounded-xl px-4 py-3 text-sm leading-relaxed whitespace-pre-line"
             style="background:rgba(0,0,0,.025);border:1px solid rgba(0,0,0,.05);color:rgba(0,0,0,.6);">{{ $task->description }}</div>
        @endif
    </div>

    {{-- ── SÉPARATEUR + SECTION RAPPORTS ─────────────────── --}}
    <div class="border-t" style="border-color:rgba(0,0,0,.07);"></div>

    {{-- Titre section rapports --}}
    <div class="flex items-center justify-between px-6 py-4">
        <div>
            <h3 class="text-sm font-semibold" style="color:#0a0a0a;">Rapports d'activité académiques</h3>
            <p class="text-xs mt-0.5" style="color:rgba(0,0,0,.4);">{{ $reportCount }} rapport{{ $reportCount !== 1 ? 's' : '' }} déposé{{ $reportCount !== 1 ? 's' : '' }}</p>
        </div>
        @if($isOwner && !$task->isCompleted() && !$isFirstReport)
        <span class="text-xs font-medium px-2.5 py-1 rounded-lg" style="background:rgba(5,150,105,.08);color:#059669;">
            ↓ Rapport du jour en bas
        </span>
        @endif
    </div>

    {{-- ── LISTE DES RAPPORTS ──────────────────────────────── --}}
    @if($task->dailyReports->isEmpty())
    <div class="px-6 pb-8 text-center">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl mb-3" style="background:rgba(0,0,0,.04);">
            <svg class="h-6 w-6 opacity-30" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
        </div>
        <p class="text-sm font-medium opacity-60">Aucun rapport pour l'instant</p>
        <p class="text-xs opacity-40 mt-1">Le premier rapport débloquera les annotations.</p>
    </div>
    @else
    <div class="divide-y" style="border-color:rgba(0,0,0,.05);">
        @foreach($task->dailyReports as $report)
        @php
            $authorName = $report->etudiant?->user?->name ?? $report->user?->name ?? 'Producteur';
            $initials   = strtoupper(substr($authorName, 0, 2));
        @endphp

        <div class="px-6 py-6">

            {{-- En-tête rapport --}}
            <div class="flex items-center justify-between gap-4 mb-5">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white shrink-0"
                          style="background:#0a0a0a;">{{ $initials }}</span>
                    <div>
                        <p class="text-sm font-semibold" style="color:#0a0a0a;">{{ $authorName }}</p>
                        <time class="text-xs" style="color:rgba(0,0,0,.4);">{{ $report->report_date->format('l j F Y') }}</time>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-1.5">
                    @if($report->hours_declared > 0)
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg" style="background:rgba(0,0,0,.05);color:rgba(0,0,0,.6);">
                        {{ $report->hours_declared }}h déclarées
                    </span>
                    @endif
                    @if($report->task_progress_percent !== null)
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg" style="background:rgba(16,185,129,.08);color:#059669;">
                        {{ $report->task_progress_percent }}% progression
                    </span>
                    @endif
                    @if($report->status === 'reviewed')
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg" style="background:rgba(16,185,129,.1);color:#065f46;">Relu ✓</span>
                    @elseif($report->status === 'submitted')
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg" style="background:rgba(59,130,246,.08);color:#1d4ed8;">Soumis</span>
                    @else
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg" style="background:rgba(245,158,11,.08);color:#92400e;">Brouillon</span>
                    @endif
                </div>
            </div>

            {{-- Corps du rapport : sections numérotées --}}
            <div class="space-y-3 pl-12">

                @if($report->introduction)
                <div>
                    <span class="d-section-label">1. Introduction</span>
                    <p class="text-sm leading-relaxed whitespace-pre-line" style="color:rgba(0,0,0,.7);">{{ $report->introduction }}</p>
                </div>
                @endif

                <div>
                    <span class="d-section-label">{{ $report->introduction ? '2.' : '1.' }} Travail réalisé</span>
                    <p class="text-sm leading-relaxed whitespace-pre-line" style="color:rgba(0,0,0,.7);">{{ $report->summary }}</p>
                </div>

                @if($report->blockers)
                <div>
                    <span class="d-section-label" style="color:rgba(220,38,38,.5);">{{ $report->introduction ? '3.' : '2.' }} Difficultés rencontrées</span>
                    <p class="text-sm leading-relaxed whitespace-pre-line" style="color:rgba(185,28,28,.8);">{{ $report->blockers }}</p>
                </div>
                @endif

                @if($report->next_steps)
                <div>
                    @php $secNum = 1 + ($report->introduction?1:0) + ($report->blockers?1:0); @endphp
                    <span class="d-section-label" style="color:rgba(67,56,202,.5);">{{ $secNum + 1 }}. Prochaines étapes</span>
                    <p class="text-sm leading-relaxed whitespace-pre-line" style="color:rgba(55,48,163,.8);">{{ $report->next_steps }}</p>
                </div>
                @endif
            </div>

            {{-- Actions reviewer --}}
            @if($isReviewer && !$task->isCompleted() && $report->status === 'submitted')
            <div class="flex items-center gap-2 mt-5 pl-12">
                <form method="POST" action="{{ route('reports.comments.store', $report->id) }}" class="inline">
                    @csrf
                    <input type="hidden" name="comment" value="Rapport relu et validé.">
                    <button type="submit" class="d-btn-green" style="font-size:.75rem;padding:.4rem .875rem;">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                        Valider le rapport
                    </button>
                </form>
                <form method="POST" action="{{ route('reports.comments.store', $report->id) }}" class="inline">
                    @csrf
                    <input type="hidden" name="comment" value="Des modifications sont demandées.">
                    <button type="submit" class="d-btn-amber" style="font-size:.75rem;padding:.4rem .875rem;">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/></svg>
                        Demander des corrections
                    </button>
                </form>
            </div>
            @endif

            {{-- Annotations & Commentaires --}}
            <div class="mt-5 pl-12">
                <div class="rounded-xl overflow-hidden" style="background:rgba(0,0,0,.025);border:1px solid rgba(0,0,0,.06);">

                    {{-- Header annotations --}}
                    <div class="flex items-center justify-between px-4 py-2.5" style="border-bottom:1px solid rgba(0,0,0,.05);">
                        <span class="d-section-label" style="margin:0;">Annotations & Commentaires</span>
                        @if($report->reviews->count() > 0)
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(0,0,0,.06);color:rgba(0,0,0,.45);">
                            {{ $report->reviews->count() }}
                        </span>
                        @endif
                    </div>

                    {{-- Liste commentaires --}}
                    @if($report->reviews->count() > 0)
                    <div class="px-4 py-3 space-y-2">
                        @foreach($report->reviews as $review)
                        @php
                            $rName = $review->reviewer?->name ?? 'Système';
                            $isMe  = $review->reviewer_id === $user->id;
                            $badge = match($review->action ?? '') {
                                'approved' => '<span style="font-size:9px;font-weight:700;letter-spacing:.08em;background:rgba(16,185,129,.12);color:#065f46;padding:1px 5px;border-radius:4px;">✓ Validation</span>',
                                'rejected' => '<span style="font-size:9px;font-weight:700;letter-spacing:.08em;background:rgba(245,158,11,.12);color:#92400e;padding:1px 5px;border-radius:4px;">⚠ Correction</span>',
                                default    => '',
                            };
                        @endphp
                        <div class="flex items-start gap-2.5">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[9px] font-bold text-white shrink-0"
                                  style="background:{{ $isMe ? '#0a0a0a' : '#64748b' }};">
                                {{ strtoupper(substr($rName,0,2)) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xs font-semibold" style="color:#0a0a0a;">{{ $rName }}</span>
                                    {!! $badge !!}
                                    <span class="text-[10px]" style="color:rgba(0,0,0,.3);">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs leading-5 mt-0.5 whitespace-pre-line" style="color:rgba(0,0,0,.6);">{{ $review->comment }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="px-4 py-3 text-[11px] italic" style="color:rgba(0,0,0,.35);">Aucune annotation pour le moment.</p>
                    @endif

                    {{-- Formulaire commentaire --}}
                    @if(!$task->isCompleted())
                    <div class="px-4 py-3" style="border-top:1px solid rgba(0,0,0,.05);">
                        <form method="POST" action="{{ route('reports.comments.store', $report->id) }}"
                              class="flex items-end gap-2">
                            @csrf
                            <textarea name="comment" rows="1" required
                                      placeholder="Écrire une annotation..."
                                      class="d-input resize-none text-xs leading-5"
                                      style="min-height:34px;max-height:100px;padding:.5rem .75rem;"
                                      oninput="this.style.height='';this.style.height=this.scrollHeight+'px'"></textarea>
                            <button type="submit" class="d-btn-dark shrink-0" style="padding:.45rem .875rem;font-size:.75rem;">
                                Envoyer
                            </button>
                        </form>
                    </div>
                    @endif

                </div>
            </div>

        </div>
        @endforeach
    </div>
    @endif

    {{-- ── FORMULAIRE RAPPORT DU JOUR ──────────────────────── --}}
    @if($isOwner && !$task->isCompleted())
    <div class="border-t" style="border-color:rgba(0,0,0,.07);"></div>

    <div class="px-6 py-5" x-data="{ open: {{ $isFirstReport ? 'true' : 'false' }}, prog: {{ (int) $task->last_progress_percent }} }">

        {{-- Toggle --}}
        <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between gap-4 text-left group">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest" style="color:#059669;">
                    {{ $isFirstReport ? 'Premier rapport' : 'Rapport du jour' }}
                </span>
                <p class="text-sm font-semibold mt-0.5" style="color:#0a0a0a;">
                    {{ $todayReport ? 'Rapport déposé aujourd\'hui — Modifier ↗' : 'Déposer un rapport d\'activité' }}
                </p>
            </div>
            <span class="flex h-8 w-8 items-center justify-center rounded-xl shrink-0 transition-all"
                  :style="open ? 'background:#0a0a0a;color:#fff;transform:rotate(180deg)' : 'background:rgba(0,0,0,.05);color:rgba(0,0,0,.5)'">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                </svg>
            </span>
        </button>

        <div x-show="open" x-collapse>
            <div class="mt-4">

                @if($todayReport)
                {{-- Rapport déjà soumis aujourd'hui --}}
                <div class="rounded-xl p-4 mb-3" style="background:rgba(5,150,105,.05);border:1px solid rgba(5,150,105,.15);">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold" style="color:#059669;">✓ Aujourd'hui · {{ (int)$todayReport->task_progress_percent }}%</span>
                        <a href="{{ route('reports.edit', $todayReport->id) }}"
                           class="text-xs font-medium hover:underline" style="color:rgba(0,0,0,.5);">Modifier →</a>
                    </div>
                    @if($todayReport->introduction)
                    <div class="mb-2"><span class="d-section-label">Introduction</span>
                        <p class="text-sm leading-relaxed" style="color:rgba(0,0,0,.7);">{{ $todayReport->introduction }}</p></div>
                    @endif
                    <div class="mb-2"><span class="d-section-label">Travail réalisé</span>
                        <p class="text-sm leading-relaxed" style="color:rgba(0,0,0,.7);">{{ $todayReport->summary }}</p></div>
                    @if($todayReport->blockers)
                    <div class="mb-2"><span class="d-section-label" style="color:rgba(220,38,38,.5);">Difficultés</span>
                        <p class="text-sm leading-relaxed" style="color:rgba(185,28,28,.8);">{{ $todayReport->blockers }}</p></div>
                    @endif
                    @if($todayReport->next_steps)
                    <div><span class="d-section-label" style="color:rgba(67,56,202,.5);">Prochaines étapes</span>
                        <p class="text-sm leading-relaxed" style="color:rgba(55,48,163,.8);">{{ $todayReport->next_steps }}</p></div>
                    @endif
                </div>
                @else
                {{-- Formulaire nouveau rapport --}}
                <form method="POST" action="{{ route('reports.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="status_action" value="submit">
                    <input type="hidden" name="task_id" value="{{ $task->id }}">

                    <div>
                        <label class="d-section-label">Introduction</label>
                        <textarea name="introduction" rows="2" class="d-input resize-none"
                                  placeholder="Contexte, objectifs du jour..."></textarea>
                    </div>

                    <div>
                        <label class="d-section-label">Travail réalisé <span style="color:#ef4444;">*</span></label>
                        <textarea name="summary" rows="4" required class="d-input resize-none"
                                  placeholder="Ce que tu as accompli aujourd'hui…"></textarea>
                    </div>

                    <div class="rounded-xl px-4 py-3" style="background:rgba(0,0,0,.03);border:1px solid rgba(0,0,0,.06);">
                        <div class="flex items-center justify-between mb-2">
                            <label class="d-section-label" style="margin:0;">Progression</label>
                            <span class="ws-mono text-sm font-bold px-2 py-0.5 rounded-lg" style="background:#0a0a0a;color:#fff;" x-text="prog+'%'"></span>
                        </div>
                        <input type="range" name="task_progress_percent" min="0" max="100" step="5"
                               x-model="prog" class="d-range">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="d-section-label">Difficultés rencontrées</label>
                            <input type="text" name="blockers" placeholder="Optionnel" class="d-input">
                        </div>
                        <div>
                            <label class="d-section-label">Heures déclarées</label>
                            <input type="number" name="hours_declared" min="0" max="24" step="0.5"
                                   placeholder="ex : 7.5" class="d-input">
                        </div>
                    </div>

                    <div>
                        <label class="d-section-label">Prochaines étapes</label>
                        <textarea name="next_steps" rows="2" class="d-input resize-none"
                                  placeholder="Prochaines actions prévues..."></textarea>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="d-btn-dark">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/></svg>
                            {{ $isFirstReport ? 'Soumettre le premier rapport' : 'Soumettre le rapport' }}
                        </button>
                    </div>
                </form>
                @endif

            </div>
        </div>
    </div>
    @endif

</div>{{-- /d-panel --}}
</div>{{-- /d-in --}}

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('canMessage', @js($canComment));
    });
</script>
@endpush
