@php
    $user = auth()->user();
    $isOwner = $task->owner_id === $user->id;
    $isReviewer = $user->hasAnyRole(['admin', 'superviseur']) && !$isOwner;
    $canMessage = $isOwner || $isReviewer;
    $points = $task->dailyReports->reverse()->filter(fn($r) => !is_null($r->task_progress_percent))->map(fn($r) => (int) $r->task_progress_percent)->values();
@endphp

<div class="space-y-6">

    <!-- EN-TÊTE -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <x-task-status-badge :status="$task->status" />
                    <x-priority-dot :priority="$task->priority" with-label />
                    @if($task->isOverdue())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-300">En retard</span>
                    @endif
                </div>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $task->title }}</h2>
                <div class="mt-1.5 flex items-center gap-3 text-xs text-slate-400">
                    <span>{{ $task->owner?->name }}</span>
                    @if($task->due_date)<span>· échéance {{ $task->due_date->format('d/m/Y') }}</span>@endif
                    <span>· {{ $task->dailyReports->count() }} rapport(s)</span>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                @if($isOwner)
                <a href="{{ encrypted_route('tasks.edit', $task) }}" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Éditer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.86 4.49l2.65 2.65M3 21l.66-3.6a2 2 0 01.54-1.06L16.3 3.94a1.5 1.5 0 012.12 0l1.64 1.64a1.5 1.5 0 010 2.12L7.72 19.8a2 2 0 01-1.06.54L3 21z"/></svg>
                </a>
                <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}" onsubmit="return confirm('Supprimer cette tâche ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 transition-colors" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.34 9m-4.8 0L9.26 9M19.2 6.2L18 19a2 2 0 01-2 1.8H8A2 2 0 016 19L4.8 6.2M9 6.2V4a1 1 0 011-1h4a1 1 0 011 1v2.2M3.5 6.2h17"/></svg>
                    </button>
                </form>
                @elseif($isReviewer)
                <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">
                    @csrf <input type="hidden" name="action" value="request_changes">
                    <button class="px-3 py-2 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 transition-colors">Corrections</button>
                </form>
                <form method="POST" action="{{ encrypted_route('tasks.review', $task) }}">
                    @csrf <input type="hidden" name="action" value="approve">
                    <button class="px-3 py-2 text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-300 transition-colors">Valider</button>
                </form>
                @endif
            </div>
        </div>

        <!-- Progression -->
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
        <p class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $task->description }}</p>
        @endif
    </div>

    <!-- RAPPORT DU JOUR (propriétaire) -->
    @if($isOwner && !$task->isCompleted())
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Rapport du jour</h3>
        @if($todayReport)
        <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 p-4">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium text-slate-500">Déjà soumis aujourd'hui · {{ (int) $todayReport->task_progress_percent }}%</span>
                <a href="{{ route('reports.edit', $todayReport->id) }}" class="text-xs font-medium text-slate-700 dark:text-slate-300 hover:underline">Modifier</a>
            </div>
            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line">{{ $todayReport->summary }}</p>
        </div>
        @else
        <form method="POST" action="{{ route('reports.store') }}" class="space-y-4" x-data="{ prog: {{ (int) $task->last_progress_percent }} }">
            @csrf
            <input type="hidden" name="status_action" value="submit">
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <div>
                <textarea name="summary" rows="3" required placeholder="Ce que tu as fait aujourd'hui sur cette tâche…"
                    class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400 resize-none"></textarea>
            </div>
            <div>
                <label class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-300 mb-1.5">
                    <span>Progression</span><span class="font-semibold text-slate-900 dark:text-white" x-text="prog + '%'"></span>
                </label>
                <input type="range" name="task_progress_percent" min="0" max="100" step="5" x-model="prog" class="w-full accent-slate-900 dark:accent-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="blockers" placeholder="Blocages (optionnel)"
                    class="px-3.5 py-2.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400">
                <input type="number" name="hours_declared" min="0" max="24" step="0.5" placeholder="Heures (optionnel)"
                    class="px-3.5 py-2.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-lg hover:bg-slate-800 transition-colors">Soumettre le rapport</button>
            </div>
        </form>
        @endif
    </div>
    @endif

    <!-- RAPPORTS LIÉS -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Rapports</h3>
        @forelse($task->dailyReports as $report)
        <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-700/50' : '' }}">
            <div class="mt-1 w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 012 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 012-2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $report->report_date->format('d/m/Y') }}</span>
                    @if(!is_null($report->task_progress_percent))<span class="text-xs font-semibold text-emerald-600">{{ (int) $report->task_progress_percent }}%</span>@endif
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 whitespace-pre-line">{{ $report->summary }}</p>
                @if($report->blockers)<p class="mt-1 text-xs text-amber-600">⚠ {{ $report->blockers }}</p>@endif
            </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 py-4 text-center">Aucun rapport pour cette tâche.</p>
        @endforelse
    </div>

    <!-- DISCUSSION -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Discussion</h3>
        <div class="space-y-4 mb-5">
            @forelse($task->messages as $message)
                @if($message->isSystem())
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <span class="h-px flex-1 bg-slate-100 dark:bg-slate-700"></span>
                    <span class="px-2.5 py-1 rounded-full bg-slate-50 dark:bg-slate-700/40">{{ $message->body }}</span>
                    <span class="h-px flex-1 bg-slate-100 dark:bg-slate-700"></span>
                </div>
                @else
                @php $mine = $message->user_id === $task->owner_id; @endphp
                <div class="flex items-start gap-3 {{ $mine ? 'flex-row-reverse' : '' }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold text-white flex-shrink-0 {{ $mine ? 'bg-slate-700' : 'bg-slate-400' }}">
                        {{ strtoupper(substr($message->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="max-w-[80%] {{ $mine ? 'text-right' : '' }}">
                        <div class="flex items-center gap-2 {{ $mine ? 'justify-end' : '' }}">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200">{{ $message->user->name ?? 'Inconnu' }}</span>
                            <span class="text-[11px] text-slate-400">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-1 inline-block px-3.5 py-2 rounded-2xl text-sm whitespace-pre-line {{ $mine ? 'bg-slate-900 text-white rounded-tr-sm dark:bg-slate-700' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-800 dark:text-slate-100 rounded-tl-sm' }}">{{ $message->body }}</div>
                    </div>
                </div>
                @endif
            @empty
            <p class="text-sm text-slate-400 py-3 text-center">Aucun message. Démarre la discussion ci-dessous.</p>
            @endforelse
        </div>

        @if($canMessage)
        <form method="POST" action="{{ encrypted_route('tasks.messages.store', $task) }}" class="flex items-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-700">
            @csrf
            <textarea name="body" rows="1" required placeholder="Écrire un message…"
                class="flex-1 px-3.5 py-2.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400 resize-none"></textarea>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-lg hover:bg-slate-800 transition-colors flex-shrink-0">Envoyer</button>
        </form>
        @endif
    </div>
</div>
