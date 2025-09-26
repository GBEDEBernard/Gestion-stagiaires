<x-app-layout>
<div class="min-h-screen py-4 shadow">
    <div class="container max-w-10xl mx-auto p-4 sm:p-6 lg:p-8 bg-white shadow-md rounded-md">
        
        <!-- Titre -->
        <h1 class="text-3xl font-bold text-red-600 mb-6">Corbeille des Stages</h1>

        <!-- Bouton retour à la liste -->
        <div class="mb-4 text-right">
            <a href="{{ route('stages.index') }}"
               class="inline-block px-5 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                ← Retour à la liste
            </a>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white border border-gray-200 text-sm rounded-md border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-black tracking-wide">
                        <th class="px-2 py-3 border border-gray-300">Nom</th>
                        <th class="px-2 py-3 border border-gray-300">Prénom</th>
                        <th class="px-2 py-3 border border-gray-300">Thème</th>
                        <th class="px-2 py-3 border border-gray-300">Période</th>
                        <th class="px-2 py-3 border border-gray-300 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($stages as $stage)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->nom }}</td>
                            <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->prenom }}</td>
                            <td class="px-2 py-2 border border-gray-300">{{ $stage->theme }}</td>
                            <td class="px-2 py-2 border border-gray-300 text-center">
                                {{ \Carbon\Carbon::parse($stage->date_debut)->format('d/m/Y') }} 
                                <span class="font-bold text-gray-500 mx-1">à</span> 
                                {{ \Carbon\Carbon::parse($stage->date_fin)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 border border-gray-300 text-center space-y-2">
                                <!-- Restaurer -->
                                <form method="POST" action="{{ route('stages.restore', $stage->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                        Restaurer
                                    </button>
                                </form>

                                <!-- Supprimer définitivement -->
                                <form method="POST" action="{{ route('stages.forceDelete', $stage->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Supprimer Définitivement
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                Aucun stage dans la corbeille.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 text-center">
            {{ $stages->links() }}
        </div>
    </div>
</div>
</x-app-layout>
