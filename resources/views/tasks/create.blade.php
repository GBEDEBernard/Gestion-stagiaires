<x-app-layout title="Créer une tâche">

    @php
        $currentPriority = old('priority', 'normal');
        $priorityOptions = [
            'low'    => ['label' => 'Basse',   'hint' => 'Suivi léger',           'dot' => 'bg-slate-400'],
            'normal' => ['label' => 'Normale',  'hint' => 'Priorité standard',     'dot' => 'bg-cyan-500'],
            'high'   => ['label' => 'Haute',    'hint' => 'À traiter vite',        'dot' => 'bg-amber-500'],
            'urgent' => ['label' => 'Urgente',  'hint' => 'Attention immediate',   'dot' => 'bg-rose-500'],
        ];
    @endphp

    <style>
        @keyframes editRise {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .edit-rise > * { animation: editRise .5s cubic-bezier(.2,.8,.2,1) both; }
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
         x-data="subtaskForm()">
        <div class="mx-auto w-full max-w-6xl px-3 py-4 sm:px-5 sm:py-6 lg:px-7">
            <div class="edit-rise grid gap-5 lg:grid-cols-[320px_minmax(0,1fr)]">

                {{-- ── SIDEBAR ── --}}
                <aside class="space-y-4">
                    <a href="{{ route('tasks.index') }}"
                       class="inline-flex h-10 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 hover:text-slate-950 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19 8 12l7-7"/></svg>
                        Retour aux tâches
                    </a>

                    <section class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/90 shadow-[0_18px_60px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.03] backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                        <div class="h-1 bg-gradient-to-r from-cyan-500 via-emerald-500 to-slate-950 dark:to-white"></div>
                        <div class="p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-400">Création</p>
                            <h1 class="mt-2 text-2xl font-semibold leading-tight tracking-tight text-slate-950 dark:text-white">Créer une tâche</h1>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Remplis le formulaire et ajoute au moins une sous-tâche pour définir les étapes de travail.</p>

                            {{-- Barre de progression (toujours 0 à la création) --}}
                            <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                                    <span>Progression</span>
                                    <span class="tabular-nums">0%</span>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                    <div class="h-full w-0 rounded-full bg-slate-950 transition-all duration-700 dark:bg-white"></div>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Calculée automatiquement depuis les sous-tâches</p>
                            </div>

                            <dl class="mt-5 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Propriétaire</dt>
                                    <dd class="max-w-[170px] truncate font-semibold text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Sous-tâches</dt>
                                    <dd class="font-semibold text-slate-700 dark:text-slate-200" x-text="subtasks.length"></dd>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-400">Création</dt>
                                    <dd class="font-semibold text-slate-700 dark:text-slate-200">{{ now()->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </section>
                </aside>

                {{-- ── FORMULAIRE PRINCIPAL ── --}}
                <main class="min-w-0 space-y-5">

                    {{-- Erreurs globales --}}
                    @if($errors->any())
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        {{-- §1 Informations principales --}}
                        <section class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/95 shadow-[0_18px_60px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.03] backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                            <div class="border-b border-slate-200/70 p-5 dark:border-white/10 sm:p-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Formulaire</p>
                                <h2 class="mt-1 text-xl font-semibold tracking-tight text-slate-950 dark:text-white">Informations principales</h2>
                            </div>

                            <div class="space-y-6 p-5 sm:p-6">

                                {{-- Titre --}}
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Titre <span class="text-rose-500">*</span></label>
                                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Nom de la tâche…"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:focus:border-white/20">
                                </div>

                                {{-- Description (obligatoire) --}}
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Description <span class="text-rose-500">*</span></label>
                                    <textarea name="description" rows="5" required placeholder="Décrivez les objectifs et le périmètre de la tâche…"
                                        class="w-full resize-none rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-7 text-slate-950 transition placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:focus:border-white/20">{{ old('description') }}</textarea>
                                </div>

                                {{-- Priorité --}}
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

                                {{-- Période --}}
                                <div>
                                    <div class="mb-3 flex items-center justify-between gap-4">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Période</label>
                                        <span class="text-xs text-slate-400">Les dates des sous-tâches doivent s'y inscrire</span>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Date de début</label>
                                            <input type="date" name="start_date" id="task_start_date" value="{{ old('start_date') }}"
                                                x-model="taskStartDate"
                                                @change="validateSubtaskDates()"
                                                class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Date de fin <span class="text-slate-300 dark:text-slate-600">(échéance)</span></label>
                                            <input type="date" name="due_date" id="task_due_date" value="{{ old('due_date') }}"
                                                x-model="taskEndDate"
                                                @change="validateSubtaskDates()"
                                                class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                        </div>
                                    </div>
                                </div>

                                {{-- PDF --}}
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Cahier des charges <span class="text-slate-300 dark:text-slate-600">(PDF, optionnel)</span></label>
                                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 transition hover:border-slate-400 hover:bg-white dark:border-white/10 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                                        <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="text-sm text-slate-500 dark:text-slate-400" x-text="pdfName || 'Choisir un fichier PDF…'"></span>
                                        <input type="file" name="pdf" accept=".pdf" class="sr-only" @change="pdfName = $event.target.files[0]?.name || ''">
                                    </label>
                                </div>

                            </div>
                        </section>

                        {{-- §2 Sous-tâches (OBLIGATOIRES) --}}
                        <section class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white/95 shadow-[0_18px_60px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.03] backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                            <div class="border-b border-slate-200/70 p-5 dark:border-white/10 sm:p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Étapes de travail</p>
                                        <h2 class="mt-1 text-xl font-semibold tracking-tight text-slate-950 dark:text-white">
                                            Sous-tâches <span class="text-rose-500">*</span>
                                        </h2>
                                        <p class="mt-1 text-xs text-slate-500">Au moins une sous-tâche est requise. La progression sera calculée automatiquement.</p>
                                    </div>
                                    <button type="button" @click="addSubtask()"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                                        Ajouter
                                    </button>
                                </div>
                            </div>

                            <div class="p-5 sm:p-6 space-y-3">
                                {{-- Message si aucune sous-tâche --}}
                                <div x-show="subtasks.length === 0" x-cloak
                                    class="rounded-2xl border border-dashed border-rose-300 bg-rose-50 p-6 text-center dark:border-rose-500/30 dark:bg-rose-500/10">
                                    <svg class="mx-auto mb-2 h-8 w-8 text-rose-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    <p class="text-sm font-semibold text-rose-700 dark:text-rose-400">Aucune sous-tâche ajoutée</p>
                                    <p class="mt-1 text-xs text-rose-500">Cliquez sur « Ajouter » pour créer les étapes de la tâche.</p>
                                </div>

                                {{-- Liste des sous-tâches --}}
                                <template x-for="(st, index) in subtasks" :key="st.id">
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400" x-text="'Sous-tâche ' + (index + 1)"></span>
                                            <button type="button" @click="removeSubtask(index)"
                                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-500/10">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <div class="space-y-3">
                                            {{-- Titre de la sous-tâche --}}
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Titre <span class="text-rose-500">*</span></label>
                                                <input type="text"
                                                    :name="'subtasks[' + index + '][title]'"
                                                    x-model="st.title"
                                                    required
                                                    placeholder="Ex : Créer le modèle de données…"
                                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white">
                                            </div>

                                            {{-- Dates --}}
                                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Date de début</label>
                                                    <input type="date"
                                                        :name="'subtasks[' + index + '][start_date]'"
                                                        x-model="st.start_date"
                                                        :min="taskStartDate || ''"
                                                        :max="taskEndDate || ''"
                                                        @change="validateSubtaskDate(index)"
                                                        class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white"
                                                        :class="st.dateError ? 'border-rose-400' : ''">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Date de fin</label>
                                                    <input type="date"
                                                        :name="'subtasks[' + index + '][end_date]'"
                                                        x-model="st.end_date"
                                                        :min="st.start_date || taskStartDate || ''"
                                                        :max="taskEndDate || ''"
                                                        @change="validateSubtaskDate(index)"
                                                        class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-950 transition focus:border-slate-400 focus:ring-4 focus:ring-slate-950/[0.05] dark:border-white/10 dark:bg-white/[0.04] dark:text-white"
                                                        :class="st.dateError ? 'border-rose-400' : ''">
                                                </div>
                                            </div>

                                            {{-- Erreur date --}}
                                            <p x-show="st.dateError" x-text="st.dateError" class="text-xs text-rose-500"></p>
                                        </div>
                                    </div>
                                </template>

                                {{-- Note sur la progression --}}
                                <div x-show="subtasks.length > 0" x-cloak
                                    class="rounded-xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400">
                                    <strong x-text="subtasks.length + ' sous-tâche(s)'"></strong> — La progression sera calculée automatiquement : chaque sous-tâche terminée = <span x-text="subtasks.length > 0 ? Math.round(100/subtasks.length) + '%' : '0%'"></span> de la tâche.
                                </div>
                            </div>
                        </section>

                        {{-- Actions --}}
                        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                            <a href="{{ route('tasks.index') }}"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-950 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white">
                                Annuler
                            </a>
                            <button type="submit"
                                :disabled="subtasks.length === 0 || hasDateErrors"
                                :class="subtasks.length === 0 || hasDateErrors ? 'opacity-40 cursor-not-allowed' : ''"
                                class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(15,23,42,.22)] transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                                Créer la tâche
                            </button>
                        </div>
                    </form>
                </main>

            </div>
        </div>
    </div>

    <script>
    function subtaskForm() {
        return {
            subtasks: @json(collect(old('subtasks', []))->values()),
            taskStartDate: '{{ old('start_date', '') }}',
            taskEndDate: '{{ old('due_date', '') }}',
            pdfName: '',
            counter: 0,

            get hasDateErrors() {
                return this.subtasks.some(st => st.dateError);
            },

            addSubtask() {
                this.subtasks.push({
                    id: ++this.counter,
                    title: '',
                    start_date: '',
                    end_date: '',
                    dateError: '',
                });
            },

            removeSubtask(index) {
                this.subtasks.splice(index, 1);
            },

            validateSubtaskDate(index) {
                const st = this.subtasks[index];
                st.dateError = '';

                if (this.taskStartDate && st.start_date && st.start_date < this.taskStartDate) {
                    st.dateError = 'Antérieure au début de la tâche principale.';
                    return;
                }
                if (this.taskEndDate && st.start_date && st.start_date > this.taskEndDate) {
                    st.dateError = 'Postérieure à la fin de la tâche principale.';
                    return;
                }
                if (st.start_date && st.end_date && st.end_date < st.start_date) {
                    st.dateError = 'La date de fin est antérieure à la date de début.';
                    return;
                }
                if (this.taskEndDate && st.end_date && st.end_date > this.taskEndDate) {
                    st.dateError = 'Dépasse la date de fin de la tâche principale.';
                    return;
                }
            },

            validateSubtaskDates() {
                this.subtasks.forEach((_, i) => this.validateSubtaskDate(i));
            },
        };
    }
    </script>

</x-app-layout>
