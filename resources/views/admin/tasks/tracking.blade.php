<x-app-layout title="Suivi des tâches">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-900 dark:text-slate-100">

        <!-- HEADER -->
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Suivi des tâches</h1>
            <p class="mt-2 text-lg text-slate-600 dark:text-slate-300">Avancement des tâches des producteurs (étudiants & employés).</p>
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
                                <a href="{{ encrypted_route('tasks.show', $task) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-lg hover:bg-emerald-100 transition">
                                    Ouvrir
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400">Aucune tâche sur cette période.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $tasks->links() }}</div>
        </div>
    </div>
</x-app-layout>
