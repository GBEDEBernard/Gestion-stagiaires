<!-- resources/views/type_stages/create.blade.php -->

<x-app-layout>

    <div class="container mx-auto p-4 bg-blue-900 hover:opacity-90">
        <h1 class="text-xl font-bold">Créer un nouveau type de stage</h1>

        <form action="{{ route('type_stages.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="libelle" class="block text-sm font-medium text-gray-700">Libellé</label>
                <input type="text" name="libelle" id="libelle" class="mt-1 block w-full p-2 border border-gray-300 rounded" value="{{ old('libelle') }}" required>
                @error('libelle')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Créer</button>
        </form>
    </div>
</x-app-layout>

