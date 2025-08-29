<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 hover:opacity-85">
        <h1 class="text-2xl font-bold text-blue-600 mb-4">Liste des étudiants</h1>

        <a href="{{ route('etudiants.create') }}" 
           class="bg-red-500 text-white px-4 py-2 rounded-xl shadow hover:bg-red-600">
            + Ajouter un étudiant
        </a>

        <table class="w-full mt-6 border-collapse bg-white shadow rounded-xl">
            <thead class="bg-blue-100 text-blue-700">
                <tr>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Prénom</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Téléphone</th>
                    <th class="p-3 text-left">École</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($etudiants as $etudiant)
                <tr class="border-b">
                    <td class="p-3">{{ $etudiant->nom }}</td>
                    <td class="p-3">{{ $etudiant->prenom }}</td>
                    <td class="p-3">{{ $etudiant->email }}</td>
                    <td class="p-3">{{ $etudiant->telephone }}</td>
                    <td class="p-3">{{ $etudiant->ecole }}</td>
                    <td class="p-3 text-center">
                        <a href="{{ route('etudiants.edit', $etudiant) }}" class="text-blue-600 hover:underline">Modifier</a> |
                        <form action="{{ route('etudiants.destroy', $etudiant) }}" method="POST" class="inline-block">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline" 
                                    onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
