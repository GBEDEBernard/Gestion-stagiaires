@php
    $initialMode = old('mode', 'assign');
    $preselectedTaskId = $fixedTask ? (string) $fixedTask->id : ($preselectedTaskId ?? old('task_id', ''));
    $initialSelected = old('owner_ids', ($fixedTask ? ($taskHolders[$fixedTask->id] ?? []) : []));
@endphp

<x-app-layout :title="$fixedTask ? 'Réassigner la tâche' : 'Assigner une tâche'">

    <div class="max-w-3xl mx-auto px-6 py-12 space-y-8">

        <!-- HEADER -->
        <div class="flex flex-col items-start gap-2">
            <a href="{{ $fixedTask ? encrypted_route('tasks.show', $fixedTask) : route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                </svg>
                {{ $fixedTask ? 'Retour à la tâche « ' . Str::limit($fixedTask->title, 30) . ' »' : 'Retour au workspace' }}
            </a>
            <h1 class="text-3xl font-bold text-slate-900">{{ $fixedTask ? 'Réassigner la tâche' : 'Assigner une tâche' }}</h1>
            <p class="text-lg text-slate-500 max-w-xl">
                {{ $fixedTask ? 'Répartissez les sous-tâches ou assignez cette tâche à de nouveaux collaborateurs.' : 'Assignez une tâche à une ou plusieurs personnes et répartissez individuellement ses sous-tâches.' }}
            </p>
        </div>

        <!-- ERREURS -->
        @if(!empty($errors) && $errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <p class="text-sm font-semibold text-red-800 mb-2">
                {{ $errors->count() }} erreur(s) empêchent l'enregistrement
            </p>
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-sm text-red-700 flex items-start gap-2">
                    <span class="text-red-400">•</span>{{ $error }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- CARTE -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
             x-data="{
                 mode: '{{ $initialMode }}',
                 transferTask: '{{ $preselectedTaskId }}',
                 transferOwner: '{{ old('owner_id', '') }}',
                 selected: @js((array) $initialSelected),
                 holderIds: @js($taskHolders),
                 producers: @js($producers->map(fn($p) => [
                     'id'   => $p->id,
                     'name' => $p->name,
                     'type' => $p->profil() instanceof \App\Models\Etudiant ? 'etudiant' : 'employe',
                 ])->values()),
                 tasksData: @js($tasks->mapWithKeys(fn($t) => [
                     $t->id => [
                         'id' => $t->id,
                         'title' => $t->title,
                         'owner_id' => $t->owner_id,
                         'subtasks' => $t->subtasks->map(fn($st) => [
                             'id' => $st->id,
                             'title' => $st->title,
                             'is_completed' => (bool) $st->is_completed,
                             'assigned_to_user_id' => $st->assigned_to_user_id,
                         ])->values(),
                     ]
                 ])),
                 roleFilter: 'all',
                 hasHolder(taskId, userId) {
                     return (this.taskHolders(taskId) || []).includes(userId);
                 },
                 filteredProducers() {
                     if (this.roleFilter === 'all') return this.producers;
                     return this.producers.filter(p => p.type === this.roleFilter);
                 },
                 taskHolders(taskId) {
                     return this.holderIds[taskId] || [];
                 },
                 currentSubtasks() {
                     if (!this.transferTask || !this.tasksData[this.transferTask]) return [];
                     return this.tasksData[this.transferTask].subtasks || [];
                 },
                 eligibleUsersForSubtasks() {
                     // Uniquement les personnes sélectionnées comme destinataires
                     if (!this.selected || this.selected.length === 0) return [];
                     return this.producers.filter(p => this.selected.includes(p.id));
                 }
             }">

            {{-- Onglets --}}
            <div class="flex gap-1 p-2 border-b border-slate-100 bg-slate-50/50">
                <button type="button" @click="mode = 'assign'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200"
                        :class="mode === 'assign' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                    🎯 Assigner à une ou plusieurs personnes
                </button>
                <button type="button" @click="mode = 'transfer'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200"
                        :class="mode === 'transfer' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                    ↔️ Transférer / Réassigner
                </button>
            </div>

            {{-- ── ONGLET 1 : ASSIGNER À PLUSIEURS PERSONNES ── --}}
            <form x-show="mode === 'assign'" x-cloak method="POST" action="{{ route('tasks.assign') }}" class="space-y-6 p-8">
                @csrf
                <input type="hidden" name="mode" value="assign">

                @if($fixedTask)
                <input type="hidden" name="task_id" value="{{ $fixedTask->id }}">
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâche concernée
                    </label>
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 block">Tâche sélectionnée</span>
                            <span class="text-base font-bold text-slate-900 block mt-0.5">{{ $fixedTask->title }}</span>
                            <span class="text-xs text-slate-500 block mt-1">
                                Propriétaire : <strong>{{ $fixedTask->owner?->name ?? 'sans propriétaire' }}</strong> · Progression : <strong>{{ (int)$fixedTask->last_progress_percent }}%</strong>
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-100/90 text-indigo-800 shrink-0">
                            🔒 Tâche pré-sélectionnée
                        </span>
                    </div>
                </div>
                @else
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâche à assigner <span class="text-red-500">*</span>
                    </label>
                    <select name="task_id" x-model="transferTask" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/30 text-base transition-all duration-200"
                            @if($tasks->isEmpty()) disabled @endif>
                        <option value="">— Sélectionner une tâche —</option>
                        @foreach($tasks as $t)
                        <option value="{{ $t->id }}" {{ $preselectedTaskId == $t->id ? 'selected' : '' }}>
                            {{ $t->title }} — {{ $t->owner?->name ?? 'sans propriétaire' }} ({{ (int)$t->last_progress_percent }}%)
                        </option>
                        @endforeach
                    </select>
                    @if($tasks->isEmpty())
                    <p class="mt-2 text-sm text-amber-600">Aucune tâche en cours disponible à assigner.</p>
                    @endif
                    @error('task_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Destinataires de la tâche <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-slate-400 ml-1">(1 ou plusieurs)</span>
                    </label>
                    <p class="text-sm text-slate-500 mb-3" x-show="!transferTask">
                        Sélectionnez d'abord une tâche pour afficher les personnes éligibles.
                    </p>

                    {{-- Filtre Stagiaire / Employé --}}
                    <div x-show="transferTask" x-cloak class="flex items-center gap-2 mb-4">
                        <template x-for="f in ['all', 'etudiant', 'employe']" :key="f">
                            <button type="button"
                                    @click="roleFilter = f"
                                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 border"
                                    :class="roleFilter === f
                                        ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                                        : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300'"
                                    x-text="f === 'all' ? '👥 Tous' : (f === 'etudiant' ? '🎓 Stagiaires' : '💼 Employés')"></button>
                        </template>
                        <span class="ml-auto text-xs text-slate-400" x-text="filteredProducers().length + ' personne(s)'"></span>
                    </div>

                    <div x-show="transferTask" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <template x-for="producer in filteredProducers()" :key="producer.id">
                            <label
                               class="flex items-start gap-3 rounded-xl border cursor-pointer transition-all duration-200 px-4 py-3 hover:border-slate-300 hover:bg-slate-50"
                               :class="selected.includes(producer.id) ? 'border-slate-900 ring-2 ring-slate-900/10' : 'border-slate-200'">
                            <input type="checkbox" name="owner_ids[]"
                                   :value="producer.id"
                                   x-model.number="selected"
                                   class="mt-1 h-4 w-4 accent-slate-900 shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 break-words" x-text="producer.name"></p>
                                <p class="text-xs text-slate-400" x-text="producer.type === 'etudiant' ? 'Stagiaire' : 'Employé'"></p>
                            </div>
                            <span x-show="hasHolder(transferTask, producer.id)"
                                  class="text-[10px] font-semibold px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 shrink-0">déjà assignée</span>
                            </label>
                        </template>
                        <div x-show="filteredProducers().length === 0" x-transition class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center">
                            <p class="text-sm font-medium text-slate-600">Aucun destinataire trouvé pour ce filtre.</p>
                        </div>
                    </div>

                    @error('owner_ids')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ── RÉPARTITION INDIVIDUELLE DES SOUS-TÂCHES ── --}}
                <div x-show="transferTask && currentSubtasks().length > 0" x-cloak class="space-y-4 pt-4 border-t border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span>🧩</span> Répartition des sous-tâches
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Attribuez chaque sous-tâche non terminée à l'un des destinataires sélectionnés ci-dessus. Les sous-tâches déjà terminées sont verrouillées.
                        </p>
                        <p x-show="selected.length === 0" x-cloak class="mt-1.5 text-xs font-semibold text-amber-600 flex items-center gap-1">
                            ⚠️ Sélectionnez d'abord au moins un destinataire pour pouvoir attribuer des sous-tâches.
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="st in currentSubtasks()" :key="st.id">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border transition"
                                 :class="st.is_completed ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/80'">
                                <div class="min-w-0 flex items-center gap-2.5">
                                    <span class="text-base shrink-0" x-text="st.is_completed ? '✅' : '⬜'"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold break-words"
                                           :class="st.is_completed ? 'text-emerald-900 line-through opacity-70' : 'text-slate-800'"
                                           x-text="st.title"></p>
                                        <p x-show="st.is_completed" class="text-[11px] text-emerald-700 font-semibold">
                                            Terminée · Non réassignable
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <template x-if="st.is_completed">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>Terminée (Verrouillée)</span>
                                        </div>
                                    </template>
                                    <template x-if="!st.is_completed">
                                        <div class="w-full sm:w-64">
                                            <select :name="'subtask_assignments[' + st.id + ']'"
                                                    :disabled="selected.length === 0"
                                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-800 shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-400/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <option value="">— Non attribuée —</option>
                                                <template x-for="u in eligibleUsersForSubtasks()" :key="u.id">
                                                    <option :value="u.id"
                                                            :selected="st.assigned_to_user_id == u.id"
                                                            x-text="u.name + ' (' + (u.type === 'etudiant' ? 'Stagiaire' : 'Employé') + ')'"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ $fixedTask ? encrypted_route('tasks.show', $fixedTask) : route('tasks.index') }}"
                       class="px-5 py-3 text-sm font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 hover:border-slate-300 transition-all duration-200 shadow-sm">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                            :disabled="!transferTask"
                            :class="!transferTask ? 'opacity-40 cursor-not-allowed' : ''">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Assigner & Enregistrer la répartition
                        <span x-show="selected.length > 0" x-text="'(' + selected.length + ')'" class="opacity-70 text-xs"></span>
                    </button>
                </div>
            </form>

            {{-- ── ONGLET 2 : TRANSFÉRER / RÉASSIGNER ── --}}
            <form x-show="mode === 'transfer'" x-cloak method="POST" action="{{ route('tasks.assign') }}" class="space-y-6 p-8">
                @csrf
                <input type="hidden" name="mode" value="transfer">

                @if($fixedTask)
                <input type="hidden" name="task_id" value="{{ $fixedTask->id }}">
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâche concernée
                    </label>
                    <div class="rounded-xl border border-violet-100 bg-violet-50/60 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-violet-600 block">Tâche sélectionnée</span>
                            <span class="text-base font-bold text-slate-900 block mt-0.5">{{ $fixedTask->title }}</span>
                            <span class="text-xs text-slate-500 block mt-1">
                                Propriétaire actuel : <strong>{{ $fixedTask->owner?->name ?? '—' }}</strong> · Progression : <strong>{{ (int)$fixedTask->last_progress_percent }}%</strong>
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-violet-100/90 text-violet-800 shrink-0">
                            🔒 Tâche pré-sélectionnée
                        </span>
                    </div>
                </div>
                @else
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâche à transférer <span class="text-red-500">*</span>
                    </label>
                    <select name="task_id" x-model="transferTask" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/30 text-base transition-all duration-200"
                            @if($tasks->isEmpty()) disabled @endif>
                        <option value="">— Sélectionner une tâche —</option>
                        @foreach($tasks as $t)
                        <option value="{{ $t->id }}" {{ $preselectedTaskId == $t->id ? 'selected' : '' }}>
                            {{ $t->title }} — propriétaire : {{ $t->owner?->name ?? '—' }} ({{ (int)$t->last_progress_percent }}%)
                        </option>
                        @endforeach
                    </select>
                    @if($tasks->isEmpty())
                    <p class="mt-2 text-sm text-amber-600">Aucune tâche en cours disponible pour un transfert.</p>
                    @endif
                    @error('task_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Nouveau propriétaire <span class="text-red-500">*</span>
                    </label>
                    <select name="owner_id" x-model="transferOwner" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/30 text-base transition-all duration-200"
                            @if($producers->isEmpty()) disabled @endif>
                        <option value="">— Sélectionner un producteur —</option>
                        @foreach($producers as $producer)
                        <option value="{{ $producer->id }}" {{ old('owner_id') == $producer->id ? 'selected' : '' }}>
                            {{ $producer->name }}
                            @if($producer->profil() instanceof \App\Models\Etudiant)
                                (Stagiaire)
                            @elseif($producer->profil() instanceof \App\Models\Employe)
                                (Employé)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    @if($producers->isEmpty())
                    <p class="mt-2 text-sm text-amber-600">Aucun producteur actif disponible pour l'instant.</p>
                    @endif
                    @error('owner_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-xl bg-violet-50 border border-violet-100 px-4 py-3 text-sm text-violet-700 leading-relaxed">
                    La tâche change de propriétaire : progression et statut sont conservés.
                    L'ancien propriétaire sera prévenu, ainsi que le nouveau.
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('tasks.index') }}"
                       class="px-5 py-3 text-sm font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 hover:border-slate-300 transition-all duration-200 shadow-sm">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 text-sm font-semibold bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0-3-3m3 3-3 3m-11 4h12m0 0-3-3m3 3-3 3"></path>
                        </svg>
                        Transférer la tâche
                    </button>
                </div>
            </form>
        </div>

        <!-- INFORMATION -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-blue-700 leading-relaxed">
                Une <strong>seule tâche</strong> reste dans les espaces de travail : chaque personne
                assignée la voit, dépose ses propres rapports et travaille sur les sous-tâches qui lui sont attribuées.
                La progression globale est calculée <strong>automatiquement</strong> sur l'ensemble des sous-tâches terminées.
            </p>
        </div>

    </div>

</x-app-layout>