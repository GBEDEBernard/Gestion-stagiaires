<x-app-layout>
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de Bord</h1>

            <!-- Cartes statistiques principales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-10">
               <a href="{{ route('admin.users.index') }}" 
                    class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-blue-50 transition">
                        <p class="text-gray-500">👤 Utilisateurs</p>
                        <h2 class="text-3xl font-bold text-blue-600">{{ \App\Models\User::count() }}</h2>
                    </a>

                <!-- Stages -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-blue-50 transition">
                    <p class="text-gray-500">Stages</p>
                    <h2 class="text-3xl font-bold text-blue-600">{{ $totalStages }}</h2>
                </a>

                <!-- Inscrits -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-indigo-50 transition">
                    <p class="text-gray-500">Les stage à venir </p>
                    <h2 class="text-3xl font-bold text-indigo-600">{{ $inscritsGlobal }}</h2>
                </a>

                <!-- En cours -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-green-50 transition">
                    <p class="text-gray-500">Stage en cours</p>
                    <h2 class="text-3xl font-bold text-green-600">{{ $enCoursGlobal }}</h2>
                </a>

                <!-- Terminés -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-red-50 transition">
                    <p class="text-gray-500">Stage terminés</p>
                    <h2 class="text-3xl font-bold text-red-600">{{ $terminesGlobal }}</h2>
                </a>

                <!-- Types de stages -->
                <a href="{{ route('type_stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-purple-50 transition">
                    <p class="text-gray-500">Types de stages</p>
                    <h2 class="text-3xl font-bold text-purple-600">{{ $totalTypes }}</h2>
                </a>

                <!-- Badges -->
                <a href="{{ route('badges.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-yellow-50 transition">
                    <p class="text-gray-500">Badges</p>
                    <h2 class="text-3xl font-bold text-yellow-600">{{ $totalBadges }}</h2>
                </a>
               <!-- Services -->
                <a href="{{ route('services.index') }}" 
                class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-teal-50 transition">
                    <p class="text-gray-500">Services</p>
                    <h2 class="text-3xl font-bold text-teal-600">{{ $totalServices }}</h2>
                </a>

                <!-- Corbeille -->
                <a href="{{ route('corbeille.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-red-50 transition">
                    <p class="text-gray-500">🗑️ Corbeille</p>
                    <h2 class="text-3xl font-bold text-red-600">{{ $totalTrash }}</h2>
                </a>
            </div>

            <!-- Chart global En cours / Terminés -->
            <div class="bg-white shadow-xl rounded-xl p-6 mb-12 w-2/6 mx-auto">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Statistiques globales des stages</h2>
                <canvas id="chart-global" class="w-full h-36"></canvas>
            </div>

            <!-- Graphique en barres (services et stages) -->
            <div class="bg-white shadow-xl rounded-xl p-6 mb-12">
                <h2 class="text-xl font-semibold text-black">Comparaison Stages / Services</h2>
                <canvas id="chart-bar" class="w-full h-80"></canvas>
            </div>

            <!-- Section activités récentes -->
            <div class="bg-white shadow rounded-lg p-6 mb-10">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Dernières activités</h2>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    @forelse($activities as $activity)
                        <li>
                            <span class="text-blue-600 font-medium">
                                {{ $activity->user->name ?? 'Système' }}
                            </span>
                            → {{ $activity->description }}
                            <span class="text-sm text-gray-500">
                                ({{ $activity->created_at->diffForHumans() }})
                            </span>
                        </li>
                    @empty
                        <li class="text-gray-500">Aucune activité enregistrée.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('chart-global'), {
            type: 'doughnut',
            data: {
                labels: ['Inscrits', 'En cours', 'Terminés'],
                datasets: [{
                    data: [
                        {{ $inscritsGlobal }},
                        {{ $enCoursGlobal }},
                        {{ $terminesGlobal }}
                    ],
                    backgroundColor: ['#3b82f6', '#22c55e', '#ef4444'],
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        new Chart(document.getElementById('chart-bar'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($servicesStats->pluck('service')) !!},
                datasets: [
                    { label: 'Inscrits', data: {!! json_encode($servicesStats->pluck('inscrits')) !!}, backgroundColor: '#3b82f6' },
                    { label: 'En cours', data: {!! json_encode($servicesStats->pluck('enCours')) !!}, backgroundColor: '#22c55e' },
                    { label: 'Terminés', data: {!! json_encode($servicesStats->pluck('termines')) !!}, backgroundColor: '#ef4444' }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</x-app-layout>
