<x-app-layout>


<div class="bg-blue-900 hover:opacity-90 min-h-screen py-8">
    <div class="container max-w-10xl mx-auto p-4 sm:p-6 lg:p-8 bg-white shadow-md rounded-md">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Liste des Stagiaires</h1>

        <!-- Bouton pour ajouter un stagiaire -->
        <div class="mb-4 text-right">
            <a href="{{ route('stagiaires.create') }}"
               class="inline-block px-5 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                + Ajouter un stagiaire
            </a>
        </div>

        <!-- Tableau responsive -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white border border-gray-200 text-sm rounded-md border-2">
                <thead>
                    <tr class="bg-gray-100 text-left text-black tracking-wide">
                        <th class="px-2 py-3">Nom</th>
                        <th class="px-2 py-3">Prénom</th>
                        <th class="px-2 py-3">Email</th>
                        <th class="px-2 py-3">Téléphone</th>
                        <th class="px-2 py-3">Type de stage</th>
                        <th class="px-2 py-3">Badge</th>
                        <th class="px-2 py-3">Ecole</th>
                        <th class="px-2 py-3">Thème</th>
                        <th class="px-2 py-3">Période</th>
                        <th class="px-2 py-3">Jours</th>
                        <th class="px-2 py-3">Statut</th>
                        <th class="px-2 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach ($stagiaires as $stagiaire)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-2 py-2">{{ $stagiaire->nom }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->prenom }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->email }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->telephone }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->typestage->libelle }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->badge->badge }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->ecole }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->theme }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->date_debut }}  <h1 class="text-center font-extabold">à</h1> {{ $stagiaire->date_fin }}</td>
                            <td class="px-2 py-2">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($stagiaire->jours as $jour)
                                        <li>{{ $jour->jour }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-2 py-2">
                                @if (now()->between($stagiaire->date_debut, $stagiaire->date_fin))
                                    <span class="text-green-600 font-semibold">En cours</span>
                                @else
                                    <span class="text-red-600 font-semibold">Terminé</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap ">
                                <a href="{{ route('stagiaires.edit', $stagiaire->id) }}"
                                   class="text-blue-600 hover:underline ml-2">Modifier</a> <br><br>
                                <form action="{{ route('stagiaires.destroy', $stagiaire->id) }}" method="POST"
                                      class="inline ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce stagiaire ?')"
                                            class="text-red-500 hover:text-red-600 font-semibold">
                                            
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 text-center">
            {{ $stagiaires->links() }}
        </div>
    </div>
</div>
</x-app-layout>