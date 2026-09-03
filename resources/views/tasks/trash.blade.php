<x-app-layout title="Corbeille des tâches">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-900 dark:text-slate-100">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Corbeille des tâches</h1>
                <p class="mt-2 text-lg text-slate-600 dark:text-slate-300">Tâches supprimées : restaurez-les ou supprimez-les définitivement.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('tasks.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Espace de travail
                </a>
            </div>
        </div>

        <!-- LISTE -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            @foreach(['Tâche', 'Propriétaire', 'Supprimée le', ''] as $th)
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($tasks as $task)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-5 py-4 text-sm text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <x-priority-dot :priority="$task->priority" />
                                    <span>{{ \Illuminate\Support\Str::limit($task->title, 60) }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $task->owner?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $task->deleted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ encrypted_route('tasks.restore', $task->id) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-lg hover:bg-emerald-100 transition">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M20 9A8 8 0 0 0 5.64 5.64L4 8m16 8l-1.64 2.36A8 8 0 0 1 4 15"/>
                                            </svg>
                                            Restaurer
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ encrypted_route('tasks.force-delete', $task->id) }}"
                                          onsubmit="return confirm('Supprimer définitivement cette tâche ? Cette action est irréversible.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 dark:bg-red-900/30 dark:text-red-300 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                            </svg>
                                            Supprimer définitivement
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </div>
                                <p class="mt-4 text-slate-500 dark:text-slate-400">La corbeille est vide.</p>
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
