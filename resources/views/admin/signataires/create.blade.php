<x-app-layout>
    <div class="max-w-lg mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('signataires.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau Signataire</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Ajoutez un nouveau signataire</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('signataires.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Jean Dupont">
                    @error('nom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="poste" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Poste <span class="text-red-500">*</span></label>
                    <input type="text" name="poste" id="poste" value="{{ old('poste') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Directeur Général">
                    @error('poste')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sigle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sigle <span class="text-red-500">*</span></label>
                    <input type="text" name="sigle" id="sigle" value="{{ old('sigle') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: DG">
                    @error('sigle')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ordre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ordre</label>
                    <input type="number" name="ordre" id="ordre" value="{{ old('ordre') }}" min="1"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: 1">
                    @error('ordre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('signataires.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-violet-500 to-purple-600 text-white rounded-xl hover:from-violet-600 hover:to-purple-700 transition font-medium shadow-lg shadow-violet-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>