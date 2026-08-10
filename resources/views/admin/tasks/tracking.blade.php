<x-app-layout title="Suivi des tâches">
    @php
        $user = auth()->user();
        $canCreate = $user->can('tasks.create');
        $canAssign = $user->can('tasks.assign');
        $canManage = $user->hasRole('admin');
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-900 dark:text-slate-100">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Suivi des tâches</h1>
                <p class="mt-2 text-lg text-slate-600 dark:text-slate-300">Avancement des tâches des producteurs (étudiants & employés).</p>
            </div>
            @if($canCreate || $canAssign)
            <div class="flex flex-wrap items-center gap-3">
                @if($canCreate)
                <a href="{{ route('tasks.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                    </svg>
                    Nouvelle tâche
                </a>
                @endif
                @if($canAssign)
                <a href="{{ route('tasks.assign.form') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                    </svg>
                    Assigner une tâche
                </a>
                @endif
                <a href="{{ route('tasks.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Espace de travail
                </a>
                @if($canManage)
                <a href="{{ route('tasks.trash') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18m-2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2m-6 4v6m4-6v6"/>
                    </svg>
                    Corbeille
                </a>
                @endif
            </div>
            @endif
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach([
                ['Total', $stats['total'], 'text-slate-900 dark:text-white'],
                ['En cours', $stats['in_progress'], 'text-blue-600'],
                ['Terminées', $stats['completed'], 'text-emerald-600'],
                ['En retard', $stats['overdue'], 'text-red-600'],
                ['Avancement moy.', $stats['avg'].'%', 'text-amber-600'],
            ] as [$label, $value, $color])
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs uppercase text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold {{ $color }}">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        <!-- FILTRES -->
        <form method="GET" action="{{ route('admin.tasks.tracking') }}"
              class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex flex-wrap gap-2">
                @foreach(['daily' => '📅 Jour', 'weekly' => '📊 Semaine', 'monthly' => '📈 Mois'] as $key => $label)
                <a href="{{ route('admin.tasks.tracking', array_filter(['period' => $key, 'status' => $status, 'date' => $date->format('Y-m-d')])) }}"
                   class="px-4 py-2 rounded-xl text-sm {{ $period === $key ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }} transition">{{ $label }}</a>
                @endforeach
            </div>

            <input type="hidden" name="period" value="{{ $period }}">
            <div class="flex flex-1 flex-wrap items-center gap-3 md:justify-end">
                <select name="status" class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
                    <option value="">Tous les statuts</option>
                    @foreach(['pending' => 'À faire', 'in_progress' => 'En cours', 'blocked' => 'Bloquée', 'changes_requested' => 'Corrections', 'completed' => 'Terminée'] as $k => $v)
                    <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                    class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher…"
                    class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">Filtrer</button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            @foreach(['Producteur', 'Tâche', 'Statut', 'Progression', 'Rapports', 'Messages', 'Échéance', ''] as $th)
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($tasks as $task)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition {{ $task->isOverdue() ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                            <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $task->owner?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <x-priority-dot :priority="$task->priority" />
                                    <span class="text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::limit($task->title, 40) }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4"><x-task-status-badge :status="$task->status" /></td>
                            <td class="px-5 py-4 w-40"><x-progress-bar :percent="$task->last_progress_percent" /></td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $task->daily_reports_count }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $task->messages_count }}</td>
                            <td class="px-5 py-4 text-sm {{ $task->isOverdue() ? 'text-red-600 font-medium' : 'text-slate-600 dark:text-slate-300' }}">
                                {{ $task->due_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ encrypted_route('tasks.show', $task) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-lg hover:bg-emerald-100 transition">
                                        Ouvrir
                                    </a>
                                    @if($canManage)
                                    <a href="{{ encrypted_route('tasks.edit', $task) }}"
                                       title="Modifier"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 2.65 2.65M4 20l.72-3.95c.07-.39.26-.74.54-1.02L16.3 3.94a1.5 1.5 0 0 1 2.12 0l1.64 1.64a1.5 1.5 0 0 1 0 2.12L9.03 18.74c-.28.28-.63.47-1.02.54L4 20Z"/>
                                        </svg>
                                    </a>
                                    <div class="relative" x-data="{confirmDelete:false}">
                                        <button type="button" title="Supprimer" @click="confirmDelete = true"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                            </svg>
                                        </button>
                                        <div x-show="confirmDelete" x-cloak x-transition.opacity
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="confirmDelete = false"></div>
                                            <div class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl"
                                                 x-show="confirmDelete" x-transition>
                                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50 dark:bg-red-900/30">
                                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                                    </svg>
                                                </div>
                                                <h3 class="mt-4 text-base font-semibold text-slate-900 dark:text-white">Supprimer cette tâche ?</h3>
                                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($task->title, 60) }}</p>
                                                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">La tâche sera déplacée dans la corbeille. Tu pourras la restaurer ou la supprimer définitivement.</p>
                                                <div class="mt-5 flex items-center justify-end gap-2">
                                                    <button type="button" @click="confirmDelete = false"
                                                            class="h-9 rounded-xl border border-slate-200 dark:border-slate-700 px-4 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <p class="text-slate-500 dark:text-slate-400">Aucune tâche sur cette période.</p>
                                @if($canCreate || $canAssign)
                                <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                                    @if($canCreate)
                                    <a href="{{ route('tasks.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                                        </svg>
                                        Nouvelle tâche
                                    </a>
                                    @endif
                                    @if($canAssign)
                                    <a href="{{ route('tasks.assign.form') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                                        </svg>
                                        Assigner une tâche
                                    </a>
                                    @endif
                                    <a href="{{ route('tasks.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        Espace de travail
                                    </a>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $tasks->links() }}</div>
        </div>
    </div>
</x-app-layout>
