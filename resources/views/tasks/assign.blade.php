@php
    $initialMode = old('mode', 'assign');
    $preselectedTaskId = (string) (old('task_id', request('task_id', '')));
    $preselectedOwners = old('owner_ids', []);
@endphp

<x-app-layout title="Assigner une tâche">

    <div class="max-w-3xl mx-auto px-6 py-12 space-y-8">

        <!-- HEADER -->
        <div class="flex flex-col items-start gap-2">
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                </svg>
                Retour au workspace
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Assigner une tâche</h1>
            <p class="text-lg text-slate-500 max-w-xl">
                Assignez une tâche à une ou plusieurs personnes — une seule tâche partagée, chacun travaille et rapporte dessus.
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
                 selected: @js((array) old('owner_ids', [])),
                 holderIds: @js($taskHolders),
                 producers: @js($producers->map(fn($p) => [
                     'id'   => $p->id,
                     'name' => $p->name,
                     'type' => $p->profil() instanceof \App\Models\Etudiant ? 'etudiant' : 'employe',
                 ])->values()),
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

                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâche à assigner <span class="text-red-500">*</span>
                    </label>
                    <select name="task_id" x-model="transferTask" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/30 text-base transition-all duration-200"
                            @if($tasks->isEmpty()) disabled @endif>
                        <option value="">— Sélectionner une tâche —</option>
                        @foreach($tasks as $task)
                        <option value="{{ $task->id }}" {{ $preselectedTaskId == $task->id ? 'selected' : '' }}>
                            {{ $task->title }} — {{ $task->owner?->name ?? 'sans propriétaire' }} ({{ $task->last_progress_percent }}%)
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

                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Destinataires <span class="text-red-500">*</span>
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

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('tasks.index') }}"
                       class="px-5 py-3 text-sm font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 hover:border-slate-300 transition-all duration-200 shadow-sm">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                            :disabled="!transferTask || selected.length === 0"
                            :class="!transferTask || selected.length === 0 ? 'opacity-40 cursor-not-allowed' : ''">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Assigner la tâche
                        <span x-show="selected.length > 0" x-text="'(' + selected.length + ')'" class="opacity-70"></span>
                    </button>
                </div>
            </form>

            {{-- ── ONGLET 2 : TRANSFÉRER / RÉASSIGNER ── --}}
            <form x-show="mode === 'transfer'" x-cloak method="POST" action="{{ route('tasks.assign') }}" class="space-y-6 p-8">
                @csrf
                <input type="hidden" name="mode" value="transfer">

                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâche à transférer <span class="text-red-500">*</span>
                    </label>
                    <select name="task_id" x-model="transferTask" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/30 text-base transition-all duration-200"
                            @if($tasks->isEmpty()) disabled @endif>
                        <option value="">— Sélectionner une tâche —</option>
                        @foreach($tasks as $task)
                        <option value="{{ $task->id }}" {{ $preselectedTaskId == $task->id ? 'selected' : '' }}>
                            {{ $task->title }} — propriétaire : {{ $task->owner?->name ?? '—' }} ({{ $task->last_progress_percent }}%)
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
                assignée la voit, dépose ses propres rapports et discute dans le même fil. Le
                pourcentage global = la progression <strong>déjà faite avant l'équipe</strong> (figée
                au moment de l'assignation) + la <strong>progression de chacun</strong>, le tout
                divisé par (n&nbsp;personnes&nbsp;+&nbsp;1). Une personne sans rapport compte pour
                0&nbsp;%. La tâche désignée permet au producteur de soumettre des rapports en
                <strong>télétravail</strong> (flag «&nbsp;Télétravail autorisé&nbsp;» activé sur son compte).
            </p>
        </div>

    </div>

</x-app-layout>