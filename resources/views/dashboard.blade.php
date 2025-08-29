<x-app-layout>
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Titre -->
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de Bord</h1>

            <!-- Cartes statistiques principales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <!-- Stages -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-blue-50 transition">
                    <p class="text-gray-500">Stages</p>
                    <h2 class="text-3xl font-bold text-blue-600">{{ $totalStages }}</h2>
                </a>

                <!-- En cours -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-green-50 transition">
                    <p class="text-gray-500">En cours</p>
                    <h2 class="text-3xl font-bold text-green-600">{{ $enCoursGlobal }}</h2>
                </a>

                <!-- Terminés -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-red-50 transition">
                    <p class="text-gray-500">Terminés</p>
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
            </div>

            <!-- Chart global En cours / Terminés -->
            <div class="bg-white shadow-xl rounded-xl p-6 mb-12 w-2/6 mx-auto">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Statistiques globales des stages</h2>
                <canvas id="chart-global" class="w-full h-36"></canvas>
            </div>

          

            <!-- Répartition des stages par service -->
            <div class="bg-white shadow-xl rounded-xl p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Répartition des stages par service</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($servicesStats as $stats)
                        <div class="p-6 border rounded-xl shadow-lg w-full">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">{{ $stats['service'] }}</h3>
                            <canvas id="chart-{{ \Illuminate\Support\Str::slug($stats['service']) }}" class="h-36 w-full"></canvas>

                            <ul class="mt-4 text-sm text-gray-600 space-y-1">
                                <li><span class="text-blue-600 font-bold">{{ $stats['inscrits'] }}</span> inscrits</li>
                                <li><span class="text-green-600 font-bold">{{ $stats['enCours'] }}</span> en cours</li>
                                <li><span class="text-red-600 font-bold">{{ $stats['termines'] }}</span> terminés</li>
                            </ul>
                        </div>
                    @endforeach
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
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart global
        new Chart(document.getElementById('chart-global'), {
            type: 'doughnut',
            data: {
                labels: ['En cours', 'Terminés'],
                datasets: [{
                    data: [{{ $enCoursGlobal }}, {{ $terminesGlobal }}],
                    backgroundColor: ['#22c55e', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Charts par service
        @foreach($servicesStats as $stats)
        new Chart(document.getElementById("chart-{{ \Illuminate\Support\Str::slug($stats['service']) }}"), {
            type: 'doughnut',
            data: {
                labels: ['Inscrits', 'En cours', 'Terminés'],
                datasets: [{
                    data: [{{ $stats['inscrits'] }}, {{ $stats['enCours'] }}, {{ $stats['termines'] }}],
                    backgroundColor: ['#3b82f6', '#22c55e', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
        @endforeach
    </script>
</x-app-layout>
