<x-app-layout>
    <div class="max-w-lg mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('type_stages.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modifier le Type de Stage</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Modifiez les informations du type de stage</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('type_stages.update', $typeStage) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="libelle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Libellé <span class="text-red-500">*</span></label>
                    <input type="text" name="libelle" id="libelle" value="{{ old('libelle', $typeStage->libelle) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Stage académique">
                    @error('libelle')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $typeStage->code) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: STG-001">
                    @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('type_stages.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition font-medium shadow-lg shadow-amber-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>