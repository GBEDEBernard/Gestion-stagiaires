<x-app-layout title="Modifier la tâche">

    @php
        $currentPriority = old('priority', $task->priority);
        $currentStatus = old('status', in_array($task->status, ['pending', 'in_progress', 'blocked', 'completed'], true) ? $task->status : 'pending');
        $pct = $task->computeProgressFromSubtasks();
        $nextStep = $task->nextStepLabel();
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
            'completed' => ['label' => 'Terminée', 'hint' => 'Clôturée', 'icon' => 'm5 13 4 4L19 7'],
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

    <div class="-m-3 sm:-m-4 md:-m-6 min-h-[calc(100vh-4rem)] bg-zinc-50 text-slate-950 dark:bg-[#090d12] dark:text-white"
         x-data="{
             taskStartDate: '{{ old('start_date', $task->start_date?->format('Y-m-d')) }}',
             taskDueDate: '{{ old('due_date', $task->due_date?->format('Y-m-d')) }}',
             newStTitle: '',
             newStStart: '',
             newStEnd: '',
             newStUser: '',
             newStError: '',
             validateNewSt() {
                 this.newStError = '';
                 if (this.taskStartDate && this.newStStart && this.newStStart < this.taskStartDate) {
                     this.newStError = 'La date de début ne peut pas être antérieure à la tâche principale (' + this.taskStartDate + ').';
                     return false;
                 }
                 if (this.taskDueDate && this.newStStart && this.newStStart > this.taskDueDate) {
                     this.newStError = 'La date de début ne peut pas dépasser la date de fin de la tâche (' + this.taskDueDate + ').';
                     return false;
                 }
                 if (this.newStStart && this.newStEnd && this.newStEnd < this.newStStart) {
                     this.newStError = 'La date de fin ne peut pas être antérieure à la date de début.';
                     return false;
                 }
                 if (this.taskDueDate && this.newStEnd && this.newStEnd > this.taskDueDate) {
                     this.newStError = 'La date de fin ne peut pas dépasser l\'échéance de la tâche (' + this.taskDueDate + ').';
                     return false;
                 }
                 return true;
             }
         }">
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
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Mettez à jour les paramètres de la tâche et gérez ses sous-tâches.</p>

                            <!-- Progression automatique -->
                            <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                                    <span>Progression auto</span>
                                    <span class="tabular-nums font-bold text-slate-800 dark:text-white">{{ $pct }}%</span>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Calculée à partir des sous-tâches terminées.</p>
                            </div>

                            <!-- Prochaine étape -->
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm dark:border-white/10 dark:bg-white/[0.02]">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Prochaine étape</div>
                                <div class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">
                                    @if($nextStep)
                                        🎯 {{ $nextStep }}
                                    @else
                                        <span class="text-emerald-600 dark:text-emerald-400">✅ Toutes les sous-tâches sont terminées</span>
                                    @endif
                                </div>
                            </div>

                            <dl class="mt-5 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Propriétaire</dt>
                                    <dd class="max-w-[170px] truncate font-semibold text-slate-700 dark:text-slate-200">{{ $task->owner?->name ?? 'Non défini' }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Sous-tâches</dt>
                                    <dd class="font-semibold text-slate-700 dark:text-slate-200">
                                        {{ $task->subtasks->where('is_completed', true)->count() }} / {{ $task->subtasks->count() }}
                                    </dd>
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

                <main class="min-w-0 space-y-6">

                    <!-- SECTION 1 : FORMULAIRE PRINCIPAL -->
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

                        <form method="POST" action="{{ encrypted_route('tasks.update', $task) }}" enctype="multipart/form-data" class="space-y-6 p-5 sm:p-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Titre <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" required value="{{ old('title', $task->title) }}"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:focus:border-white/20">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Description <span class="text-rose-500">*</span></label>
                                <textarea name="description" rows="5" required placeholder="Détails de la tâche…"
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
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Date de début</label>
                                    <input type="date" name="start_date" x-model="taskStartDate"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                </div>
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Date de fin <span class="text-slate-300 dark:text-slate-600">(échéance)</span></label>
                                    <input type="date" name="due_date" x-model="taskDueDate"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                </div>
                            </div>

                            {{-- Fichier PDF (Cahier des charges) --}}
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Fichier PDF / Cahier des charges</label>
                                @if($task->pdf_path)
                                <div class="mb-3 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3.5 dark:border-white/10 dark:bg-white/[0.02]">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/10">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ Storage::disk('public')->url($task->pdf_path) }}" target="_blank" class="block truncate text-sm font-semibold text-slate-900 hover:underline dark:text-white">
                                                Voir le document PDF actuel
                                            </a>
                                            <span class="text-xs text-slate-400">PDF attaché</span>
                                        </div>
                                    </div>
                                    <label class="flex items-center gap-2 text-xs font-semibold text-rose-600 cursor-pointer">
                                        <input type="checkbox" name="remove_pdf" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        Supprimer
                                    </label>
                                </div>
                                @endif

                                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 transition hover:border-slate-400 hover:bg-white dark:border-white/10 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                                    <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ $task->pdf_path ? 'Remplacer par un nouveau PDF…' : 'Choisir un fichier PDF…' }}</span>
                                    <input type="file" name="pdf" accept=".pdf" class="sr-only">
                                </label>
                            </div>

                            @if($errors->any())
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                                <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
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
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- SECTION 2 : GESTION DES SOUS-TÂCHES -->
                    <section class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/95 shadow-[0_18px_60px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.03] backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                        <div class="border-b border-slate-200/70 p-5 dark:border-white/10 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Étapes de travail</p>
                                    <h2 class="mt-1 text-xl font-semibold tracking-tight text-slate-950 dark:text-white">
                                        Sous-tâches ({{ $task->subtasks->count() }})
                                    </h2>
                                    <p class="mt-1 text-xs text-slate-500">Ajoutez, modifiez ou supprimez les sous-tâches. La progression s'ajuste automatiquement.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 space-y-4">

                            <!-- Liste des sous-tâches actuelles -->
                            <div class="space-y-2.5">
                                @forelse($task->subtasks as $st)
                                <div x-data="{ edit: false }" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border p-4 transition duration-150 {{ $st->is_completed ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-500/20 dark:bg-emerald-500/5' : 'border-slate-200 bg-slate-50/80 dark:border-white/10 dark:bg-white/[0.02]' }}">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $st->is_completed ? 'bg-emerald-600 text-white' : 'border-2 border-slate-300 text-transparent dark:border-white/20' }}">
                                            @if($st->is_completed)
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900 dark:text-white {{ $st->is_completed ? 'line-through text-slate-500' : '' }}">
                                                    {{ $st->title }}
                                                </p>
                                                @if($st->is_completed)
                                                    <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                        Terminée le {{ $st->completed_at?->format('d/m/Y') }} par {{ $st->completedBy?->name ?? 'Système' }}
                                                    </span>
                                                @endif
                                                @if($st->assignedTo)
                                                    <span class="rounded-md bg-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-300">
                                                        👤 {{ $st->assignedTo->name }}
                                                    </span>
                                                @else
                                                    <span class="rounded-md border border-dashed border-slate-300 px-2 py-0.5 text-[11px] text-slate-400">
                                                        Non attribuée
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-1 flex items-center gap-3 text-xs text-slate-400">
                                                @if($st->start_date || $st->end_date)
                                                <span>
                                                    📅 {{ $st->start_date?->format('d/m/Y') ?? '?' }} → {{ $st->end_date?->format('d/m/Y') ?? '?' }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-center">
                                        @if(auth()->user()->hasRole('admin') && $st->is_completed)
                                        <form method="POST" action="{{ route('tasks.subtasks.reopen', [$task, $st]) }}" onsubmit="return confirm('Réouvrir cette sous-tâche ?')">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-amber-300 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                                🔓 Réouvrir
                                            </button>
                                        </form>
                                        @endif

                                        @unless($st->is_completed)
                                        <button type="button" @click="edit = !edit"
                                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-200 hover:text-slate-700 dark:hover:bg-white/10 dark:hover:text-slate-200" title="Modifier">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 2.65 2.65M4 20l.72-3.95c.07-.39.26-.74.54-1.02L16.3 3.94a1.5 1.5 0 0 1 2.12 0l1.64 1.64a1.5 1.5 0 0 1 0 2.12L9.03 18.74c-.28.28-.63.47-1.02.54L4 20Z"/></svg>
                                        </button>
                                        @endunless

                                        <form method="POST" action="{{ route('tasks.subtasks.destroy', [$task, $st]) }}" onsubmit="return confirm('Supprimer cette sous-tâche ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10" title="Supprimer">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Formulaire inline de modification de la sous-tâche --}}
                                @unless($st->is_completed)
                                <form x-show="edit" x-cloak method="POST" action="{{ route('tasks.subtasks.update', [$task, $st]) }}" class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                        @csrf @method('PUT')
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-3">Modifier la sous-tâche</p>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Titre</label>
                                                <input type="text" name="title" required value="{{ $st->title }}"
                                                       class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                            </div>
                                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Date début</label>
                                                    <input type="date" name="start_date" value="{{ $st->start_date?->toDateString() }}"
                                                           class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Date fin</label>
                                                    <input type="date" name="end_date" value="{{ $st->end_date?->toDateString() }}"
                                                           class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                                </div>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-1">
                                                <button type="button" @click="edit = false"
                                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/[0.04]">Annuler</button>
                                                <button type="submit"
                                                        class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-900">Enregistrer</button>
                                            </div>
                                        </div>
                                    </form>
                                @endunless
                                @empty
                                <div class="rounded-2xl border border-dashed border-rose-300 bg-rose-50 p-6 text-center dark:border-rose-500/30 dark:bg-rose-500/10">
                                    <p class="text-sm font-semibold text-rose-700 dark:text-rose-400">Aucune sous-tâche pour le moment</p>
                                    <p class="mt-1 text-xs text-rose-500">Ajoutez au moins une sous-tâche pour permettre le calcul de progression.</p>
                                </div>
                                @endforelse
                            </div>

                            <!-- Formulaire d'ajout d'une sous-tâche -->
                            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/[0.02]">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white mb-3">
                                    + Ajouter une sous-tâche
                                </h3>

                                <form method="POST" action="{{ route('tasks.subtasks.store', $task) }}" @submit="if(!validateNewSt()) { $event.preventDefault(); }">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Titre de la sous-tâche <span class="text-rose-500">*</span></label>
                                            <input type="text" name="title" x-model="newStTitle" required placeholder="Ex : Intégration du composant…"
                                                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                        </div>

                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Date début</label>
                                                <input type="date" name="start_date" x-model="newStStart" @change="validateNewSt()"
                                                    :min="taskStartDate || ''" :max="taskDueDate || ''"
                                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Date fin</label>
                                                <input type="date" name="end_date" x-model="newStEnd" @change="validateNewSt()"
                                                    :min="newStStart || taskStartDate || ''" :max="taskDueDate || ''"
                                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Assignée à (optionnel)</label>
                                                <select name="assigned_to_user_id" x-model="newStUser"
                                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                                    <option value="">— Non assignée —</option>
                                                    @foreach($assignees as $assignee)
                                                    <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <p x-show="newStError" x-text="newStError" class="text-xs text-rose-500 font-semibold"></p>

                                        <div class="flex justify-end pt-2">
                                            <button type="submit"
                                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-900">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                Ajouter la sous-tâche
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

</x-app-layout>
