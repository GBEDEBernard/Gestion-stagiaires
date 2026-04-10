<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <form action="{{ isset($task) ? encrypted_route('tasks.update', $task) : route('tasks.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        @if(isset($task))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="stage_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stage <span class="text-red-500">*</span></label>
                <select name="stage_id" id="stage_id" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white">
                    <option value="">Selectionner un stage</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}" {{ old('stage_id', $task->stage_id ?? '') == $stage->id ? 'selected' : '' }}>
                            {{ $stage->theme ?: 'Stage sans theme' }} - {{ $stage->etudiant->nom ?? '-' }} {{ $stage->etudiant->prenom ?? '' }}{{ $stage->site?->name ? ' - ' . $stage->site->name : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $task->title ?? '') }}" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white">
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white">{{ old('description', $task->description ?? '') }}</textarea>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priorite <span class="text-red-500">*</span></label>
                <select name="priority" id="priority"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white">
                    @foreach(['low' => 'Basse', 'normal' => 'Normale', 'high' => 'Haute', 'urgent' => 'Urgente'] as $value => $label)
                        <option value="{{ $value }}" {{ old('priority', $task->priority ?? 'normal') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut <span class="text-red-500">*</span></label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white">
                    @foreach(['pending' => 'En attente', 'in_progress' => 'En cours', 'completed' => 'Terminee', 'blocked' => 'Bloquee'] as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $task->status ?? 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Echeance</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white">
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">
                <ul class="space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('tasks.index') }}"
                class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-3 bg-gradient-to-r from-amber-600 to-amber-700 text-white rounded-xl hover:from-amber-700 hover:to-amber-800 transition font-medium shadow-lg shadow-amber-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ isset($task) ? 'Mettre a jour' : 'Enregistrer la tache' }}
            </button>
        </div>
    </form>
</div>
