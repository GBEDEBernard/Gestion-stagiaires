<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 hover:opacity-85">
        <h1 class="text-2xl font-bold text-red-600 mb-4">Corbeille des étudiants</h1>

        <!-- Boutons Retour / Ajouter -->
        <div class="flex gap-2 mb-4">
            <a href="{{ route('etudiants.index') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-xl shadow hover:bg-blue-700 transition">
                ← Retour à la liste
            </a>
            
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto">
            <table class="w-full mt-2 border-collapse bg-white shadow rounded-xl">
                <thead class="bg-red-100 text-red-700">
                    <tr>
                        <th class="p-3 text-left">Nom</th>
                        <th class="p-3 text-left">Prénom</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Téléphone</th>
                        <th class="p-3 text-left">École</th>
                        <th class="p-3 text-center">Actions</th>
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
                            <td class="px-4 py-2 text-center space-y-2">
                                <!-- Restaurer -->
                                <form method="POST" action="{{ route('etudiants.restore', $etudiant->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                        Restaurer
                                    </button>
                                </form>

                                <!-- Supprimer définitivement -->
                                <form method="POST" action="{{ route('etudiants.forceDelete', $etudiant->id) }}">
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
                            <td colspan="6" class="py-6 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-7 7-7-7" />
                                    </svg>
                                    <span class="text-lg font-medium">Aucun étudiant dans la corbeille.</span>
                                </div>
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
