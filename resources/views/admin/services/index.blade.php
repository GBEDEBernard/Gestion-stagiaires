<x-app-layout>
<div class="hover:opacity-85 min-h-screen py-4">
    <div class="container max-w-4xl mx-auto p-6 bg-white shadow-2xl rounded-2xl border border-blue-200">

        <h1 class="text-3xl font-bold text-blue-700 mb-6">Liste des Services</h1>

        <div class="mb-4 text-right">
            <a href="{{ route('services.create') }}" 
               class="inline-block px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
               + Ajouter un service
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white border border-gray-200 text-sm rounded-md border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-black tracking-wide">
                        <th class="px-4 py-3 border border-gray-300">Nom</th>
                        <th class="px-4 py-3 border border-gray-300 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($services as $service)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-4 py-2 border border-gray-300">{{ $service->nom }}</td>
                            <td class="px-4 py-2 border border-gray-300 text-center space-x-2">
                                <a href="{{ route('services.edit', $service->id) }}" 
                                   class="inline-block px-3 py-1 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                                    Modifier
                                </a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="inline-block px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-700 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-gray-500">Aucun service trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            {{ $services->links() }}
        </div>
    </div>
</div>
</x-app-layout>
