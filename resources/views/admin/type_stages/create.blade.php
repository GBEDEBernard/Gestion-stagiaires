<x-app-layout>
    <div class="container mx-auto p-4 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Créer un nouveau type de stage</h1>

        <form action="{{ route('type_stages.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Libellé -->
            <div>
                <label for="libelle" class="block font-medium text-gray-700">Libellé</label>
                <input type="text" name="libelle" id="libelle" class="mt-1 block w-full p-2 border rounded"
                       value="{{ old('libelle') }}" required>
                @error('libelle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div>
                <label for="code" class="block font-medium text-gray-700">Code du type de stage</label>
                <input type="text" name="code" id="code" class="mt-1 block w-full p-2 border rounded"
                       value="{{ old('code') }}" required>
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Créer
            </button>
        </form>
    </div>
</x-app-layout>
