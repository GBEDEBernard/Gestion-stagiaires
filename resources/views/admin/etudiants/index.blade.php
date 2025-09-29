<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 hover:opacity-85 font-serif">
        <!-- Header avec titre et boutons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-2">
            <h1 class="text-2xl font-bold text-blue-600">Liste des étudiants</h1>
            <div class="flex gap-2">
                <a href="{{ url()->previous() }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-xl shadow hover:bg-gray-600 transition">
                    ← Retour
                </a>
                <a href="{{ route('etudiants.create') }}" 
                   class="bg-blue-700 text-white px-4 py-2 rounded-xl shadow hover:bg-blue-500 transition">
                    + Ajouter un étudiant
                </a>
                <a href="{{ route('etudiants.trash') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-xl shadow hover:bg-gray-800 transition">
                    🗑 Corbeille
                </a>
            </div>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto">
            <table class="w-full mt-2 border-collapse bg-white shadow rounded-xl">
                <thead class="bg-blue-100 text-blue-700">
                    <tr>
                        <th class="p-3 text-left">Nom</th>
                        <th class="p-3 text-left">Prénom</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Téléphone</th>
                        <th class="p-3 text-left">École</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($etudiants as $etudiant)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-3">{{ $etudiant->nom }}</td>
                            <td class="p-3">{{ $etudiant->prenom }}</td>
                            <td class="p-3">{{ $etudiant->email }}</td>
                            <td class="p-3">{{ $etudiant->telephone ?? '-' }}</td>
                            <td class="p-3">{{ $etudiant->ecole ?? '-' }}</td>
                            <td class="p-3 text-right space-x-2">
                                <a href="{{ route('etudiants.edit', $etudiant) }}" 
                                   class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition" data-confirm-edit> 
                                    Modifier
                                </a>
                                <form action="{{ route('etudiants.destroy', $etudiant) }}" method="POST" class="inline-block" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">
                                Aucun étudiant trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 text-center">
            {{ $etudiants->links() }}
        </div>
    </div>
</x-app-layout>
