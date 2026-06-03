<x-app-layout title="Tâche">

    @php
        $user = auth()->user();
        $isOwner = $task->owner_id === $user->id;
    @endphp

    <div class="max-w-6xl mx-auto px-6 py-10">

        <!-- Retour -->
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour aux tâches
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- COLONNE PRINCIPALE -->
            <div class="lg:col-span-2 space-y-6">

                <!-- En-tête tâche -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <x-priority-dot :priority="$task->priority" with-label />
                                <x-task-status-badge :status="$task->status" />
                                @if($task->isOverdue())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">En retard</span>
                                @endif
                            </div>
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $task->title }}</h1>
                        </div>

                        @if($isOwner)
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ encrypted_route('tasks.edit', $task) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 rounded-lg hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Éditer
                            </a>
                            <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}" data-confirm-delete onsubmit="return confirm('Supprimer cette tâche ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-700 bg-red-50 dark:bg-red-900/30 dark:text-red-300 rounded-lg hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <!-- Progression -->
                    <div class="mt-5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Avancement</span>
                        </div>
                        <x-progress-bar :percent="$task->last_progress_percent" />
                    </div>

                    @if($task->description)
                    <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</p>
                        <p class="text-slate-600 dark:text-slate-400 whitespace-pre-line">{{ $task->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Rapports liés -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Rapports liés</h2>
                    @forelse($task->dailyReports as $report)
                    <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-50 dark:border-slate-700/50' : '' }}">
                        <div class="w-2 h-2 mt-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $report->report_date->format('d/m/Y') }}</span>
                                @if(!is_null($report->task_progress_percent))
                                <span class="text-xs font-semibold text-blue-600">{{ $report->task_progress_percent }}%</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">{{ $report->summary }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 py-4 text-center">Aucun rapport rattaché pour l'instant. <span class="text-slate-300">(à venir : Phase 3)</span></p>
                    @endforelse
                </div>

                <!-- Fil de discussion (scaffold — interactivité en Phase 4) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Discussion</h2>
                    @forelse($task->messages as $message)
                    <div class="flex items-start gap-3 py-3">
                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300 flex-shrink-0">
                            {{ strtoupper(substr($message->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $message->user->name ?? 'Système' }}</span>
                                <span class="text-xs text-slate-400">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-line">{{ $message->body }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 py-4 text-center">Aucun message. <span class="text-slate-300">(chat à venir : Phase 4)</span></p>
                    @endforelse
                </div>
            </div>

            <!-- COLONNE LATÉRALE -->
            <div class="space-y-6">
                <!-- Avancement -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Avancement</p>
                    <p class="text-4xl font-bold {{ $task->last_progress_percent >= 100 ? 'text-emerald-600' : 'text-slate-900 dark:text-white' }}">{{ (int) $task->last_progress_percent }}%</p>
                </div>

                <!-- Détails -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 space-y-3 text-sm">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Détails</h3>
                    <div class="flex justify-between"><span class="text-slate-500">Statut</span><x-task-status-badge :status="$task->status" /></div>
                    <div class="flex justify-between"><span class="text-slate-500">Priorité</span><span class="text-slate-900 dark:text-white"><x-priority-dot :priority="$task->priority" with-label /></span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Propriétaire</span><span class="text-slate-900 dark:text-white">{{ $task->owner?->name ?? '—' }}</span></div>
                    @if($task->stage)
                    <div class="flex justify-between"><span class="text-slate-500">Stage</span><span class="text-slate-900 dark:text-white truncate ml-2">{{ $task->stage->theme ?? '—' }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-slate-500">Échéance</span><span class="text-slate-900 dark:text-white">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Créée le</span><span class="text-slate-900 dark:text-white">{{ $task->created_at->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Rapports</span><span class="text-slate-900 dark:text-white">{{ $task->dailyReports->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
