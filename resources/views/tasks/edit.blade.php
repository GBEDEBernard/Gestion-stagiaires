<x-app-layout title="Modifier la tâche">

    <div class="max-w-2xl mx-auto px-6 py-12">

        <a href="{{ encrypted_route('tasks.show', $task) }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour à la tâche
        </a>

        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-8">Modifier la tâche</h1>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-sm border border-slate-100 dark:border-slate-700">
            <form method="POST" action="{{ encrypted_route('tasks.update', $task) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title', $task->title) }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Priorité</label>
                        <select name="priority"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                            @foreach(['low' => 'Basse', 'normal' => 'Normale', 'high' => 'Haute', 'urgent' => 'Urgente'] as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', $task->priority) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Statut</label>
                        <select name="status"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                            @foreach(['pending' => 'À faire', 'in_progress' => 'En cours', 'blocked' => 'Bloquée', 'completed' => 'Terminée'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $task->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Échéance</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                    </div>
                </div>

                @if($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700 text-sm">
                    <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ encrypted_route('tasks.show', $task) }}"
                        class="px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
