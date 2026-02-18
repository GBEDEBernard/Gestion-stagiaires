<x-app-layout>
    <div class="max-w-lg mx-auto px-4 py-8 bg-blue-900 hover:opacity-90 rounded">
        <h1 class="text-2xl font-bold mb-6 text-white">Modifier le numéro de Badge</h1>

        <form action="{{ encrypted_route('badges.update', $badge) }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            <div>
                <label for="badge" class="block text-sm font-medium text-gray-700">Numéro de Badge</label>
                <input type="text" name="badge" id="badge"
                       value="{{ old('badge', $badge->badge) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('badge')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('badges.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                    Annuler
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
