<x-app-layout>

    <div class="max-w-2xl mx-auto p-6 bg-blue-900 hover:opacity-90">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Modifier le type de stage</h1>

        <form action="{{ route('type_stages.update', $typeStage->id) }}" method="POST" class="space-y-6 bg-white shadow-md rounded-xl p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="libelle" class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                <input
                    type="text"
                    name="libelle"
                    id="libelle"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    value="{{ old('libelle', $typeStage->libelle) }}"
                    required
                >
                @error('libelle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
