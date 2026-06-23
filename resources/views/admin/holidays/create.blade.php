<x-app-layout>
<div class="max-w-2xl mx-auto">
    <div class="mb-8 ml-4">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.holidays.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau jour férié</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Ajoutez un jour férié au calendrier</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl ml-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.holidays.store') }}">
                @csrf

                <div class="mb-5">
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Libellé <span class="text-red-500">*</span></label>
                    <input type="text" id="label" name="label" value="{{ old('label') }}" required
                        placeholder="Ex: Fête de l'Indépendance"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    @error('label')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description <span class="text-gray-400 text-xs">(optionnelle)</span></label>
                    <textarea id="description" name="description" rows="3"
                        placeholder="Description ou note sur ce jour férié..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl hover:from-purple-600 hover:to-indigo-700 transition shadow-lg shadow-purple-600/20 font-medium">
                        Enregistrer
                    </button>
                    <a href="{{ route('admin.holidays.index') }}"
                        class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
