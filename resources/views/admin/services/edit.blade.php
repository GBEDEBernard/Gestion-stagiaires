<x-app-layout>
<div class="bg-blue-900 min-h-screen py-10">
    <div class="container max-w-4xl mx-auto p-8 bg-white shadow-2xl rounded-2xl border border-blue-200">
        <h1 class="text-3xl font-bold text-blue-700 mb-8">Modifier le Service</h1>

        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                <label for="nom" class="block text-sm font-semibold text-blue-600">Nom du service</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $service->nom) }}"
                    class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror">
                @error('nom') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 text-right">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
