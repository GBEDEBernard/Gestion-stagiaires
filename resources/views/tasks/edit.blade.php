<x-app-layout title="Modifier la tâche">

    @php
        $currentPriority = old('priority', $task->priority);
        $currentStatus = old('status', in_array($task->status, ['pending', 'in_progress', 'blocked', 'completed'], true) ? $task->status : 'pending');
        $pct = max(0, min(100, (int) $task->last_progress_percent));
        $priorityOptions = [
            'low' => ['label' => 'Basse', 'hint' => 'Suivi léger', 'dot' => 'bg-slate-400'],
            'normal' => ['label' => 'Normale', 'hint' => 'Priorité standard', 'dot' => 'bg-cyan-500'],
            'high' => ['label' => 'Haute', 'hint' => 'À traiter vite', 'dot' => 'bg-amber-500'],
            'urgent' => ['label' => 'Urgente', 'hint' => 'Attention immediate', 'dot' => 'bg-rose-500'],
        ];
        $statusOptions = [
            'pending' => ['label' => 'À faire', 'hint' => 'La tâche est planifiée', 'icon' => 'M8 7h8M8 12h8M8 17h5'],
            'in_progress' => ['label' => 'En cours', 'hint' => 'Le travail est actif', 'icon' => 'M12 6v6l4 2'],
            'blocked' => ['label' => 'Bloquée', 'hint' => 'Un obstacle ralentit le travail', 'icon' => 'M12 9v4m0 4h.01'],
            'completed' => ['label' => 'Terminée', 'hint' => 'Marquer à 100%', 'icon' => 'm5 13 4 4L19 7'],
        ];
    @endphp

    <style>
        @keyframes editRise {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .edit-rise > * {
            animation: editRise .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        .edit-rise > *:nth-child(2) { animation-delay: .06s; }
        .edit-choice input:checked + span {
            border-color: rgb(15 23 42 / .9);
            background: rgb(15 23 42);
            color: white;
            box-shadow: 0 18px 42px rgb(15 23 42 / .18);
            transform: translateY(-1px);
        }

        .dark .edit-choice input:checked + span {
            border-color: rgb(255 255 255 / .9);
            background: white;
            color: rgb(15 23 42);
        }
    </style>

    <div class="-m-3 sm:-m-4 md:-m-6 min-h-[calc(100vh-4rem)] bg-zinc-50 text-slate-950 dark:bg-[#090d12] dark:text-white">
        <div class="mx-auto w-full max-w-6xl px-3 py-4 sm:px-5 sm:py-6 lg:px-7">
            <div class="edit-rise grid gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">

                <aside class="space-y-4">
                    <a href="{{ encrypted_route('tasks.show', $task) }}"
                       class="inline-flex h-10 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 hover:text-slate-950 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19 8 12l7-7"/></svg>
                        Retour à la tâche
                    </a>

                    <section class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/90 shadow-[0_18px_60px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.03] backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                        <div class="h-1 bg-gradient-to-r from-cyan-500 via-emerald-500 to-slate-950 dark:to-white"></div>
                        <div class="p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-400">Édition</p>
                            <h1 class="mt-2 text-2xl font-semibold leading-tight tracking-tight text-slate-950 dark:text-white">Modifier la tâche</h1>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Ajuste les informations visibles dans le workspace sans changer le fil de discussion.</p>

                            <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                                    <span>Progression</span>
                                    <span class="tabular-nums">{{ $pct }}%</span>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                    <div class="h-full rounded-full bg-slate-950 transition-all duration-700 dark:bg-white" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <dl class="mt-5 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Propriétaire</dt>
                                    <dd class="max-w-[170px] truncate font-semibold text-slate-700 dark:text-slate-200">{{ $task->owner?->name ?? 'Non défini' }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Rapports</dt>
                                    <dd class="font-semibold text-slate-700 dark:text-slate-200">{{ $task->dailyReports()->count() }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Création</dt>
                                    <dd class="font-semibold text-slate-700 dark:text-slate-200">{{ $task->created_at?->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </section>
                </aside>

                <main class="min-w-0">
                    <section class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/95 shadow-[0_18px_60px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.03] backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                        <div class="border-b border-slate-200/70 p-5 dark:border-white/10 sm:p-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Formulaire</p>
                                    <h2 class="mt-1 text-xl font-semibold tracking-tight text-slate-950 dark:text-white">Informations principales</h2>
                                </div>
                                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Sauvegarde manuelle
                                </span>
                            </div>
                        </div>

                        <form method="POST" action="{{ encrypted_route('tasks.update', $task) }}" class="space-y-6 p-5 sm:p-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Titre <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" required value="{{ old('title', $task->title) }}"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:focus:border-white/20">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Description</label>
                                <textarea name="description" rows="7"
                                    class="w-full resize-none rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-7 text-slate-950 transition placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:focus:border-white/20">{{ old('description', $task->description) }}</textarea>
                            </div>

                            <div>
                                <div class="mb-3 flex items-center justify-between gap-4">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Priorité</label>
                                    <span class="text-xs text-slate-400">Impact sur le tri visuel</span>
                                </div>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-4">
                                    @foreach($priorityOptions as $value => $option)
                                    <label class="edit-choice cursor-pointer">
                                        <input type="radio" name="priority" value="{{ $value }}" class="sr-only" {{ $currentPriority === $value ? 'checked' : '' }}>
                                        <span class="flex h-full min-h-[92px] flex-col justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3 text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:bg-white hover:shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:text-slate-200 dark:hover:bg-white/[0.06]">
                                            <span class="flex items-center justify-between">
                                                <span class="text-sm font-semibold">{{ $option['label'] }}</span>
                                                <span class="h-2.5 w-2.5 rounded-full {{ $option['dot'] }}"></span>
                                            </span>
                                            <span class="text-xs opacity-70">{{ $option['hint'] }}</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <div class="mb-3 flex items-center justify-between gap-4">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Statut</label>
                                    <span class="text-xs text-slate-400">Cycle de vie</span>
                                </div>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach($statusOptions as $value => $option)
                                    <label class="edit-choice cursor-pointer">
                                        <input type="radio" name="status" value="{{ $value }}" class="sr-only" {{ $currentStatus === $value ? 'checked' : '' }}>
                                        <span class="flex h-full items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:bg-white hover:shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:text-slate-200 dark:hover:bg-white/[0.06]">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/80 text-current shadow-sm dark:bg-black/20">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $option['icon'] }}"/></svg>
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block text-sm font-semibold">{{ $option['label'] }}</span>
                                                <span class="mt-1 block text-xs opacity-70">{{ $option['hint'] }}</span>
                                            </span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Échéance</label>
                                    <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                </div>
                            </div>

                            @if($errors->any())
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                                <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                            @endif

                            <div class="flex flex-col-reverse gap-2 border-t border-slate-200/70 pt-5 dark:border-white/10 sm:flex-row sm:items-center sm:justify-end">
                                <a href="{{ encrypted_route('tasks.show', $task) }}"
                                    class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-950 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white">
                                    Annuler
                                </a>
                                <button type="submit"
                                    class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(15,23,42,.22)] transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </div>

</x-app-layout>
