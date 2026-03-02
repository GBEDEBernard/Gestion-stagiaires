<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('etudiants.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modifier l'Étudiant</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Modifiez les informations de l'étudiant</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('etudiants.update', $etudiant) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $etudiant->nom) }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="Ex: Dupont">
                        @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $etudiant->prenom) }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="Ex: Jean">
                        @error('prenom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="genre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Genre <span class="text-red-500">*</span></label>
                        <select name="genre" id="genre" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                            <option value="Masculin" {{ old('genre', $etudiant->genre) == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                            <option value="Feminin" {{ old('genre', $etudiant->genre) == 'Feminin' ? 'selected' : '' }}>Feminin</option>
                        </select>
                        @error('genre')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $etudiant->email) }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="exemple@email.com">
                        @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $etudiant->telephone) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="Ex: +237 6XX XXX XXX">
                        @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ecole" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">École</label>
                        <input type="text" name="ecole" id="ecole" value="{{ old('ecole', $etudiant->ecole) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="Ex: Université de Douala">
                        @error('ecole')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('etudiants.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition font-medium shadow-lg shadow-emerald-600/20 flex items-center gap-2">
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