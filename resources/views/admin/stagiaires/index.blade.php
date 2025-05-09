@extends('layouts.app')

@section('content')
<div class="bg-blue-900 hover:opacity-90 min-h-screen py-10">
    <div class="container max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 bg-white shadow-md rounded-md">
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
            <table class="min-w-full bg-white border border-gray-200 text-sm rounded-md">
                <thead>
                    <tr class="bg-gray-100 text-left text-blue-600 tracking-wide">
                        <th class="px-2 py-3 ">Nom</th>
                        <th class="px-2 py-3">Prénom</th>
                        <th class="px-2 py-3">Email</th>
                        <th class="px-2 py-3">Téléphone</th>
                        <th class="px-2 py-3">Type de stage</th>
                        <th class="px-2 py-3">Badge</th>
                        <th class="px-2 py-3">Ecole</th>
                        <th class="px-2 py-3">Thème</th>
                        <th class="px-2 py-3">Début</th>
                        <th class="px-2 py-3">Fin</th>
                        <th class="px-2 py-3">Jours</th>
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
                            <td class="px-2 py-2">{{ $stagiaire->typeStage->libelle }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->badge->badge }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->ecole }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->theme }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->date_debut }}</td>
                            <td class="px-2 py-2">{{ $stagiaire->date_fin }}</td>
                            <td class="px-2 py-2">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($stagiaire->jours as $jour)
                                        <li>{{ $jour->jour }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('stagiaires.edit', $stagiaire->id) }}"
                                   class="text-blue-600 hover:underline">Modifier</a>
                                <form action="{{ route('stagiaires.destroy', $stagiaire->id) }}" method="POST"
                                      class="inline ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                          onclick="return confirm('Tu es sûr de vouloir supprimer ce stagiaire?')"
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
@endsection
