<x-app-layout>
    <div class="min-h-screen  shadow">
        <div class="container hover:opacity-85 max-w-10xl mx-auto p-4 sm:p-6 lg:p-4 bg-white shadow-md rounded-md">

            <!-- Titre -->
            <h1 class="text-3xl font-bold text-blue-600 mb-6">Liste des Stages</h1>

            <!-- Formulaire de filtre -->
            <form method="GET" action="{{ route('stages.index') }}" class="mb-6 flex flex-wrap gap-8 items-end p-4 bg-white dark:to-black">
                <div>
                    <label class="block text-sm font-semibold mb-1">Statut</label>
                    <select name="statut"
                            class="p-2 rounded-md shadow-md bg-white border dark:text-gray-700 border-gray-300 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Tous</option>
                        <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminé" {{ request('statut') == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                        <option value="À venir" {{ request('statut') == 'À venir' ? 'selected' : '' }}>À venir</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Type de stage</label>
                    <select name="typestage"
                            class="p-2 rounded-md shadow-md bg-white border-2 border-radius dark:text-gray-700 border-gray-300 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Tous</option>
                        @foreach($typestages as $type)
                            <option value="{{ $type->id }}" {{ request('typestage') == $type->id ? 'selected' : '' }}>
                                {{ $type->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition shadow">
                    Filtrer
                </button>
            </form>

            <!-- Boutons Ajouter / Corbeille / Retour -->
            <div class="mb-4 flex justify-end gap-2">
                <a href="{{ url()->previous() }}"
                   class="inline-block px-5 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600 transition">
                    ← Retour
                </a>

                <a href="{{ route('stages.create') }}"
                   class="inline-block px-5 py-2 bg-blue-700 text-white font-semibold rounded-md hover:bg-blue-600 transition">
                    + Ajouter un stage
                </a>

                <a href="{{ route('stages.trash') }}"
                   class="inline-block px-5 py-2 bg-gray-700 text-white font-semibold rounded-md hover:bg-gray-500 transition">
                    🗑 Corbeille
                </a>
            </div>

            <!-- Tableau des stages -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-200 text-sm rounded-md border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left text-black tracking-wide">
                            <th class="px-2 py-3 border border-gray-300">Nom</th>
                            <th class="px-2 py-3 border border-gray-300">Prénom</th>
                            <th class="px-2 py-3 border border-gray-300">Emails</th>
                            <th class="px-2 py-3 border border-gray-300">Téléphones</th>
                            <th class="px-2 py-3 border border-gray-300">Type de stage</th>
                            <th class="px-2 py-3 border border-gray-300">Badges</th>
                            <th class="px-2 py-3 border border-gray-300">Écoles</th>
                            <th class="px-2 py-3 border border-gray-300">Services</th>
                            <th class="px-2 py-3 border border-gray-300">Thèmes</th>
                            <th class="px-2 py-3 border border-gray-300">Périodes</th>
                            <th class="px-2 py-3 border border-gray-300 w-40">Jours</th>
                            <th class="px-1 py-3 border border-gray-300 text-center">Statut</th>
                            <th class="px-2 py-3 border border-gray-300 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700">
                        @forelse ($stages as $stage)
                            <tr class="border-t hover:bg-gray-50 transition">
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->nom }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->prenom }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->email }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->telephone }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->typestage->libelle ?? '-' }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->badge->badge ?? '-' }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->etudiant->ecole }}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->service->nom}}</td>
                                <td class="px-2 py-2 border border-gray-300">{{ $stage->theme }}</td>
                                <td class="px-2 py-2 border border-gray-300 text-center">
                                    {{ \Carbon\Carbon::parse($stage->date_debut)->format('d/m/Y') }} 
                                    <span class="font-bold text-gray-500 mx-1">à</span> 
                                    {{ \Carbon\Carbon::parse($stage->date_fin)->format('d/m/Y') }}
                                </td>
                                <td class="px-2 py-2 border border-gray-300 w-40">
                                    @if($stage->jours->count() > 0)
                                        {{ $stage->jours->pluck('jour')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-2 py-2 border border-gray-300 text-center">
                                    @if($stage->statut == 'En cours')
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-green-600 font-semibold">En cours</span>
                                            <div class="relative w-20 h-2 bg-gray-200 rounded overflow-hidden flex-1">
                                                <div class="absolute left-0 top-0 h-full bg-green-500 animate-loading-short"></div>
                                            </div>
                                        </div>
                                    @elseif($stage->statut == 'À venir')
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-blue-600 font-semibold">À venir</span>
                                            <span class="dot-blue animate-pulse-dot"></span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-red-600 font-semibold">Terminé</span>
                                            <span class="dot-red animate-pulse-dot"></span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-300 text-center space-y-2">
                                    <a href="{{ route('stages.show', $stage->id) }}"
                                       class="inline-block px-2 py-1 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700 transition">
                                        Voir
                                    </a>
                                    <a href="{{ route('stages.edit', $stage->id) }}"
                                       class="inline-block px-2 py-1 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition"
                                       data-confirm-edit>
                                        Modifier
                                    </a>
                                    <form action="{{ route('stages.destroy', $stage->id) }}" method="POST" class="inline" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-block px-2 py-1 mt-2 bg-red-500 text-white font-semibold rounded hover:bg-red-700 transition">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4 text-gray-500">
                                    Aucun stage trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <div class="inline-flex items-center space-x-2 bg-gray-50 p-2 rounded-lg shadow">
                    {{ $stages->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Styles pour animations -->
    <style>
        @keyframes loading-short {
            0% { width: 0%; }
            50% { width: 100%; }
            100% { width: 0%; }
        }
        .animate-loading-short {
            animation: loading-short 6s linear infinite;
            height: 100%;
            top: 0;
            left: 0;
            position: absolute;
        }
        .dot-red, .dot-blue {
            display: inline-block;
            width: 5px;
            height: 10px;
            border-radius: 50%;
        }
        .dot-red { background-color: #f87171; }
        .dot-blue { background-color: #3b82f6; }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(0.5); opacity: 0.6; }
            50% { transform: scale(1.2); opacity: 1; }
        }
        .animate-pulse-dot { animation: pulse-dot 1s infinite; }
    </style>
</x-app-layout>
