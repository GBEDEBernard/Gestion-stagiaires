<x-app-layout title="Espace de travail">

    @php
        $user = auth()->user();
        $canCreate = $user->hasAnyRole(['etudiant', 'employe']);
        $qs = collect(['status' => $status, 'q' => request('q')])->filter()->all();
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{ openCreate: {{ (isset($errors) && $errors->any()) && $canCreate ? 'true' : 'false' }} }">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Espace de travail</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tes tâches, tes rapports et les échanges, au même endroit.</p>
            </div>
            @if($canCreate)
            <button @click="openCreate = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-lg hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nouvelle tâche
            </button>
            @endif
        </div>

        <!-- GRID MASTER / DETAIL -->
        <div class="grid grid-cols-1 lg:grid-cols-[340px_1fr] gap-6 items-start">

            <!-- ===================== COLONNE GAUCHE : LISTE ===================== -->
            <aside class="space-y-4 lg:sticky lg:top-6">
                <!-- Filtres -->
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ route('tasks.index', array_filter(['q' => request('q')])) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ empty($status) ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">Toutes</a>
                    @foreach(['pending' => 'À faire', 'in_progress' => 'En cours', 'blocked' => 'Bloquées', 'completed' => 'Terminées'] as $key => $label)
                    <a href="{{ route('tasks.index', array_filter(['status' => $key, 'q' => request('q')])) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ ($status ?? null) === $key ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">{{ $label }}</a>
                    @endforeach
                </div>

                <!-- Recherche -->
                <form method="GET" action="{{ route('tasks.index') }}">
                    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.2-5.2m1.7-4.3a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher…"
                            class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400">
                    </div>
                </form>

                <!-- Liste -->
                <div class="space-y-1.5 max-h-[70vh] overflow-y-auto pr-1">
                    @forelse($tasks as $t)
                    @php $active = $selected && $selected->id === $t->id; @endphp
                    <a href="{{ encrypted_route('tasks.show', $t) }}{{ $qs ? '?'.http_build_query($qs) : '' }}"
                       class="block rounded-xl border px-3.5 py-3 transition-colors {{ $active ? 'border-slate-300 dark:border-slate-500 bg-slate-50 dark:bg-slate-800' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-2 mb-1.5">
                            <x-priority-dot :priority="$t->priority" />
                            <span class="flex-1 min-w-0 text-sm font-medium text-slate-900 dark:text-white truncate">{{ $t->title }}</span>
                            @if($t->isOverdue())
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500" title="En retard"></span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <x-progress-bar :percent="$t->last_progress_percent" :show-label="false" class="flex-1" />
                            <span class="text-[11px] tabular-nums text-slate-400">{{ (int) $t->last_progress_percent }}%</span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-10 text-sm text-slate-400">
                        Aucune tâche.
                        @if($canCreate)<button @click="openCreate = true" class="block mx-auto mt-2 text-slate-700 dark:text-slate-300 underline">En créer une</button>@endif
                    </div>
                    @endforelse
                </div>
            </aside>

            <!-- ===================== COLONNE DROITE : DÉTAIL ===================== -->
            <main class="min-w-0">
                @if($selected)
                    @include('tasks.partials.detail', ['task' => $selected, 'todayReport' => $todayReport, 'thread' => $thread])
                @else
                <div class="rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 py-24 text-center">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-7 5h8a2 2 0 002-2V7l-5-4H6a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Sélectionne une tâche à gauche pour voir ses rapports et la discussion.</p>
                    @if($canCreate)
                    <button @click="openCreate = true" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-lg hover:bg-slate-800 transition-colors">Créer une tâche</button>
                    @endif
                </div>
                @endif
            </main>
        </div>

        <!-- MODAL CRÉATION -->
        @if($canCreate)
        <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
             x-transition.opacity @keydown.escape.window="openCreate = false">
            <div class="w-full max-w-md" @click.outside="openCreate = false">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Nouvelle tâche</h2>
                        <button @click="openCreate = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Titre <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required autofocus value="{{ old('title') }}"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400 focus:border-slate-400">{{ old('description') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Priorité</label>
                                <select name="priority" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400">
                                    @foreach(['low' => 'Basse', 'normal' => 'Normale', 'high' => 'Haute', 'urgent' => 'Urgente'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('priority', 'normal') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Échéance</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-1 focus:ring-slate-400">
                            </div>
                        </div>
                        @if(isset($errors) && $errors->any())
                        <div class="rounded-lg bg-red-50 border border-red-200 px-3.5 py-2.5 text-sm text-red-700">
                            <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif
                        <div class="flex justify-end gap-2 pt-1">
                            <button type="button" @click="openCreate = false" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Annuler</button>
                            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-lg hover:bg-slate-800 transition-colors">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 bg-slate-900 text-white text-sm rounded-xl shadow-lg">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

</x-app-layout>
