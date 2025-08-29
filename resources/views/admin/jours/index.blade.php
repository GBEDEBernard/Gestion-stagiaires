<x-app-layout>
    <!-- Importation de la police Inter pour une apparence professionnelle -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <div class=" hover:opacity-85 max-w-7xl mx-auto px-4 sm:px-2 lg:px-8 py-12 min-h-screen font-['Inter']">
        <!-- Entête -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Liste des Jours</h1>
            <a href="{{ route('jours.create') }}"
               class="mt-4 sm:mt-0 bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 font-medium text-sm">
                Ajouter un jour
            </a>
        </div>

        <!-- Message de succès -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tableau pour les écrans larges -->
        <div class="hidden lg:block bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Jour</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($jours as $jour)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 font-medium">{{ $jour->id }}</td>
                            <td class="px-6 py-4">{{ $jour->jour }}</td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('jours.edit', $jour->id) }}"
                                   class="text-yellow-600 hover:text-yellow-700 font-medium hover:underline">
                                    Modifier
                                </a>
                                <form action="{{ route('jours.destroy', $jour->id) }}" method="POST"
                                      class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce jour ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-700 font-medium hover:underline">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Version mobile : cartes -->
        <div class="lg:hidden space-y-5">
            @foreach ($jours as $jour)
                <div class="bg-white rounded-lg shadow-md p-5">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $jour->jour }}</h2>
                    <p class="text-sm text-gray-500 mt-1">ID: {{ $jour->id }}</p>
                    <div class="mt-4 flex justify-end gap-3">
                        <a href="{{ route('jours.edit', $jour->id) }}"
                           class="text-yellow-600 hover:text-yellow-700 font-medium text-sm hover:underline">
                            Modifier
                        </a>
                        <form action="{{ route('jours.destroy', $jour->id) }}" method="POST"
                              onsubmit="return confirm('Voulez-vous vraiment supprimer ce jour ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-700 font-medium text-sm hover:underline">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>