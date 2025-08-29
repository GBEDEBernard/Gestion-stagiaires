<x-app-layout>

<div class=" hover:opacity-90 max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-4 h-[400px] shadow-xl">
    <h1 class="text-2xl font-bold text-white mb-6">Types de Stages</h1>

    <div class="mb-6 flex justify-end">
        <a href="{{ route('type_stages.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md shadow transition duration-200">
            + Nouveau type de stage
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y  bg-white shadow rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xl font-bold text-gray-800 uppercase tracking-wider">Libellé</th>
                    <th class="px-6 py-3 text-center text-xl font-medium text-gray-800 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($type_stages as $typeStage)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $typeStage->libelle }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('type_stages.edit', $typeStage) }}"
                               class="text-yellow-500 hover:text-yellow-600 font-semibold mr-4" data-confirm-edit>
                                Modifier
                            </a>

                            <form action="{{ route('type_stages.destroy', $typeStage) }}" method="POST" class="inline" data-confirm-delete>
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    
                                        class="text-red-500 hover:text-red-600 font-semibold">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($type_stages->isEmpty())
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                            Aucun type de stage trouvé.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
