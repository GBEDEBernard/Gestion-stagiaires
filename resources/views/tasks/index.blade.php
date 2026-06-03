<x-app-layout title="Mes tâches">

    @php $canCreate = auth()->user()->hasAnyRole(['etudiant', 'employe']); @endphp

    <div class="max-w-5xl mx-auto px-6 py-12 space-y-10" x-data="{ openCreate: {{ $errors->any() && $canCreate ? 'true' : 'false' }} }">

        <!-- HEADER -->
        <div class="flex flex-col items-center gap-6 md:flex-row md:justify-between">
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Mes tâches</h1>
                <p class="text-lg text-slate-500 dark:text-slate-400 max-w-xl">
                    Crée tes tâches, rattache tes rapports et suis ta progression jour après jour.
                </p>
            </div>

            @if($canCreate)
            <button @click="openCreate = true"
                class="inline-flex items-center gap-2 px-5 py-3 text-sm font-medium bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle tâche
            </button>
            @endif
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['À faire', $stats['pending'], 'text-slate-600', 'pending'],
                ['En cours', $stats['in_progress'], 'text-blue-600', 'in_progress'],
                ['Bloquées', $stats['blocked'], 'text-red-600', 'blocked'],
                ['Terminées', $stats['completed'], 'text-emerald-600', 'completed'],
            ] as [$label, $count, $color, $key])
            <a href="{{ route('tasks.index', ['status' => $key]) }}"
               class="bg-white dark:bg-slate-800 rounded-xl border {{ ($status ?? null) === $key ? 'border-slate-900 dark:border-slate-300' : 'border-slate-100 dark:border-slate-700' }} p-4 shadow-sm hover:shadow-md transition">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $label }}</p>
                <p class="text-2xl font-bold {{ $color }} dark:text-white mt-1">{{ $count }}</p>
            </a>
            @endforeach
        </div>

        <!-- FILTRES -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-800 rounded-lg p-1">
                <a href="{{ route('tasks.index') }}"
                   class="px-3 py-1.5 text-sm rounded-md transition-all {{ empty($status) ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    Toutes
                </a>
                @foreach(['pending' => 'À faire', 'in_progress' => 'En cours', 'blocked' => 'Bloquées', 'completed' => 'Terminées'] as $key => $label)
                <a href="{{ route('tasks.index', ['status' => $key]) }}"
                   class="px-3 py-1.5 text-sm rounded-md transition-all {{ ($status ?? null) === $key ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('tasks.index') }}" class="flex-1 min-w-[200px]">
                @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une tâche…"
                    class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-slate-500 focus:border-transparent">
            </form>
        </div>

        <!-- LISTE -->
        <div class="space-y-4">
            @forelse($tasks as $task)
            <a href="{{ encrypted_route('tasks.show', $task) }}"
               class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-500 transition-all duration-200 shadow-sm hover:shadow-md p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <x-priority-dot :priority="$task->priority" />
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white truncate">{{ $task->title }}</h3>
                            <x-task-status-badge :status="$task->status" />
                            @if($task->isOverdue())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">En retard</span>
                            @endif
                        </div>
                        @if($task->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $task->description }}</p>
                        @endif
                        <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
                            @if($task->due_date)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $task->due_date->format('d/m/Y') }}
                            </span>
                            @endif
                            <span>Créée {{ $task->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="w-32 flex-shrink-0">
                        <x-progress-bar :percent="$task->last_progress_percent" />
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-16">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <p class="text-lg text-slate-500 dark:text-slate-400">Aucune tâche pour l'instant</p>
                @if($canCreate)
                <button @click="openCreate = true" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition">
                    Créer ma première tâche
                </button>
                @endif
            </div>
            @endforelse
        </div>

        <div>{{ $tasks->links() }}</div>

        <!-- MODAL CRÉATION -->
        @if($canCreate)
        <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
             x-transition.opacity @keydown.escape.window="openCreate = false">
            <div class="relative w-full max-w-md" @click.outside="openCreate = false">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Nouvelle tâche</h2>
                        <button @click="openCreate = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Titre <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required autofocus value="{{ old('title') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Priorité</label>
                                <select name="priority"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                                    @foreach(['low' => 'Basse', 'normal' => 'Normale', 'high' => 'Haute', 'urgent' => 'Urgente'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('priority', 'normal') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Échéance</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                            </div>
                        </div>

                        @if($errors->any())
                        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700 text-sm">
                            <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="openCreate = false"
                                class="px-5 py-3 text-sm font-medium border border-slate-200 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-5 py-3 text-sm font-medium bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition shadow-md">
                                Créer la tâche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>


</x-app-layout>
