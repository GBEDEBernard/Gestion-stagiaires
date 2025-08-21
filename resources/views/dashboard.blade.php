<x-app-layout>
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de Bord</h1>

            <!-- Cartes stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                <!-- Stagiaires -->
                <a href="{{ route('stagiaires.index') }}" 
                   class="bg-white shadow rounded-lg p-6 text-center hover:shadow-lg hover:bg-blue-50 transition">
                    <p class="text-gray-500">Stagiaires</p>
                    <h2 class="text-3xl font-bold text-blue-600">{{ $totalStagiaires }}</h2>
                </a>

                <!-- En cours -->
                <a href="{{ route('stagiaires.index') }}" 
                   class="bg-white shadow rounded-lg p-6 text-center hover:shadow-lg hover:bg-green-50 transition">
                    <p class="text-gray-500">En cours</p>
                    <h2 class="text-3xl font-bold text-green-600">{{ $enCours }}</h2>
                </a>

                <!-- Types de stages -->
                <a href="{{ route('type_stages.index') }}" 
                   class="bg-white shadow rounded-lg p-6 text-center hover:shadow-lg hover:bg-purple-50 transition">
                    <p class="text-gray-500">Types de stages</p>
                    <h2 class="text-3xl font-bold text-purple-600">{{ $totalTypes }}</h2>
                </a>

                <!-- Badges -->
                <a href="{{ route('badges.index') }}" 
                   class="bg-white shadow rounded-lg p-6 text-center hover:shadow-lg hover:bg-red-50 transition">
                    <p class="text-gray-500">Badges</p>
                    <h2 class="text-3xl font-bold text-red-600">{{ $totalBadges }}</h2>
                </a>
            </div>

            <!-- Section activité -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Dernières activités</h2>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li>Stagiaire <b>Jean Dupont</b> ajouté hier</li>
                    <li>Badge <b>#12</b> attribué à Marie</li>
                    <li>Nouveau type de stage enregistré</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
