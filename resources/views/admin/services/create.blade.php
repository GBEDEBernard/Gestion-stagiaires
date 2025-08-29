<x-app-layout>
<div class="bg-blue-900 min-h-screen py-10">
    <div class="container max-w-2xl mx-auto p-8 bg-white shadow-2xl rounded-2xl border border-blue-200">

        <h1 class="text-3xl font-bold text-blue-700 mb-8">
            {{ isset($service) ? 'Modifier un Service' : 'Ajouter un Service' }}
        </h1>

        <form action="{{ isset($service) ? route('services.update', $service->id) : route('services.store') }}" method="POST">
            @csrf
            @if(isset($service))
                @method('PUT')
            @endif

            <div>
                <label for="nom" class="block text-sm font-semibold text-blue-600">Nom du service</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $service->nom ?? '') }}" required
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror">
                @error('nom') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 text-right">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    {{ isset($service) ? 'Mettre à jour' : 'Ajouter' }}
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
