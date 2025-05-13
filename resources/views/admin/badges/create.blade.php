<x-app-layout>

<div class="max-w-lg mx-auto px-4 py-8 bg-blue-900 hover:opacity-90">
    <h1 class="text-2xl font-bold mb-6">Ajouter un Numéro de Badge</h1>

    <form action="{{ route('badges.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <div>
            <label for="badge" class="block text-sm font-medium text-gray-700">badge</label>
            <input type="text" name="badge" id="badge" value="{{ old('badge') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" required>
            @error('badge')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg">Enregistrer</button>
        </div>
    </form>
</div>
</x-app-layout>