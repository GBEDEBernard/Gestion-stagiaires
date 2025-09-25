<x-app-layout>
<div class="py-10 max-w-3xl mx-auto px-6">
    <h1 class="text-2xl font-bold mb-6">Ajouter un signataire</h1>

    <form action="{{ route('signataires.store') }}" method="POST" class="bg-white rounded shadow p-6 space-y-4">
        @csrf

        <div>
            <label for="nom" class="block text-gray-700 font-medium">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                   class="w-full border rounded px-3 py-2 mt-1 @error('nom') border-red-500 @enderror">
            @error('nom') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="poste" class="block text-gray-700 font-medium">Poste</label>
            <input type="text" name="poste" id="poste" value="{{ old('poste') }}"
                   class="w-full border rounded px-3 py-2 mt-1 @error('poste') border-red-500 @enderror">
            @error('poste') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="sigle" class="block text-gray-700 font-medium">Sigle</label>
            <input type="text" name="sigle" id="sigle" value="{{ old('sigle') }}"
                   class="w-full border rounded px-3 py-2 mt-1 @error('sigle') border-red-500 @enderror">
            @error('sigle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="ordre" class="block text-gray-700 font-medium">Ordre (optionnel)</label>
            <input type="number" name="ordre" id="ordre" value="{{ old('ordre') }}"
                   class="w-full border rounded px-3 py-2 mt-1 @error('ordre') border-red-500 @enderror" min="1">
            @error('ordre') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('signataires.index') }}" class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">Annuler</a>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Ajouter</button>
        </div>
    </form>
</div>
</x-app-layout>
