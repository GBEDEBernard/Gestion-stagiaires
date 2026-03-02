<x-app-layout>
    <div class="max-w-lg mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('services.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ isset($service) ? 'Modifier' : 'Nouveau' }} Service</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">{{ isset($service) ? 'Modifiez les informations du service' : 'Ajoutez un nouveau service' }}</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ isset($service) ? route('services.update', $service) : route('services.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if(isset($service))
                @method('PUT')
                @endif

                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom du service <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $service->nom ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Développement, Marketing, RH...">
                    @error('nom')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('services.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition font-medium shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ isset($service) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>