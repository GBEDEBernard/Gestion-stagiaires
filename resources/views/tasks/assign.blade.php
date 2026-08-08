@php
    $user = auth()->user();
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
                Choisissez un producteur (étudiant ou employé), puis sélectionnez une de ses tâches à désigner.
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

        <!-- FORMULAIRE -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ ownerId: '{{ old('owner_id', '') }}' }">
            <form method="POST" action="{{ route('tasks.assign') }}" class="space-y-6 p-8">
                @csrf

                <!-- Producteur -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Producteur <span class="text-red-500">*</span>
                    </label>
                    <select name="owner_id" x-model="ownerId" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/30 text-base transition-all duration-200"
                            @if($producers->isEmpty()) disabled @endif>
                        <option value="">— Sélectionner un producteur —</option>
                        @foreach($producers as $producer)
                        <option value="{{ $producer->id }}" {{ old('owner_id') == $producer->id ? 'selected' : '' }}>
                            {{ $producer->name }}
                            @if($producer->profil() instanceof \App\Models\Etudiant)
                                (Étudiant)
                            @elseif($producer->profil() instanceof \App\Models\Employe)
                                (Employé)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    @if($producers->isEmpty())
                    <p class="mt-2 text-sm text-amber-600">
                        Aucun producteur actif disponible pour l'instant.
                    </p>
                    @endif
                    @error('owner_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tâches du producteur -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Tâches en cours du producteur <span class="text-red-500">*</span>
                    </label>

                    <p class="text-sm text-slate-500 mb-3" x-show="!ownerId">Sélectionnez d'abord un producteur pour afficher ses tâches.</p>

                    @foreach($producers as $producer)
                    <div x-show="ownerId == '{{ $producer->id }}'" x-cloak>
                        @php $tasks = $tasksByOwner[$producer->id] ?? collect(); @endphp
                        @if($tasks->isEmpty())
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center">
                            <p class="text-sm font-medium text-slate-600">Aucune tâche en cours à assigner.</p>
                            <p class="mt-1 text-xs text-slate-400">Ce producteur n'a aucune tâche non terminée pour le moment.</p>
                        </div>
                        @else
                        <div class="space-y-2.5">
                            @foreach($tasks as $task)
                            <label class="group flex items-start gap-3 rounded-xl border cursor-pointer transition-all duration-200 px-4 py-3.5
                                          hover:border-slate-300 hover:bg-slate-50"
                                   :class="oldCheck === '{{ $task->id }}' ? 'border-slate-900 ring-2 ring-slate-900/10' : 'border-slate-200'"
                                   x-data="{ oldCheck: '{{ old('task_id') }}', checked: {{ old('task_id') == $task->id ? 'true' : 'false' }} }"
                                   @click="checked = true; oldCheck = '{{ $task->id }}'">
                                <input type="radio" name="task_id" value="{{ $task->id }}"
                                       x-model="checked"
                                       :checked="checked"
                                       class="mt-1 h-4 w-4 accent-slate-900 shrink-0"
                                       @if(old('task_id') == $task->id) checked @endif>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-sm font-semibold text-slate-900 break-words">{{ $task->title }}</span>
                                        <x-task-status-badge :status="$task->status" />
                                    </div>
                                    <div class="mt-2 flex items-center gap-3">
                                        <x-progress-bar :percent="$task->last_progress_percent" :show-label="false" class="flex-1" />
                                        <span class="text-xs font-medium text-slate-500 w-10 text-right">{{ $task->last_progress_percent }}%</span>
                                    </div>
                                    <div class="mt-1.5 flex items-center gap-4 text-xs text-slate-400">
                                        <span>{{ match($task->priority) {
                                            'urgent' => '🔴 Urgente',
                                            'high'   => '🟠 Haute',
                                            'low'    => '⚪ Basse',
                                            default  => '🔵 Normale',
                                        } }}</span>
                                        <span>Échéance : {{ $task->due_date?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach

                    @error('task_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('tasks.index') }}"
                       class="px-5 py-3 text-sm font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 hover:border-slate-300 transition-all duration-200 shadow-sm">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                            @if($producers->isEmpty()) disabled @endif>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Assigner la tâche
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
                La tâche désignée par un administrateur/superviseur permet au producteur de
                soumettre des rapports en <strong>télétravail</strong> (à domicile), à condition que
                le flag «&nbsp;Télétravail autorisé&nbsp;» soit aussi activé sur son compte.
            </p>
        </div>

    </div>

</x-app-layout>