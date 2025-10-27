<x-app-layout>
    <div class="py-10 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6">

            <!-- En-tête du Dashboard -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Tableau de Bord</h1>

              
            </div>

            <!-- Cartes statistiques principales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-10">
                <!-- Utilisateurs -->
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-blue-50 dark:hover:bg-blue-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">👤 Utilisateurs</p>
                    <h2 class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\User::count() }}</h2>
                </a>

                <!-- Stages -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-blue-50 dark:hover:bg-blue-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Stages</p>
                    <h2 class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalStages }}</h2>
                </a>

                <!-- Les stages à venir -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-indigo-50 dark:hover:bg-indigo-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Les stages à venir</p>
                    <h2 class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $inscritsGlobal }}</h2>
                </a>

                <!-- Stage en cours -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-green-50 dark:hover:bg-green-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Stage en cours</p>
                    <h2 class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $enCoursGlobal }}</h2>
                </a>

                <!-- Stage terminés -->
                <a href="{{ route('stages.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-red-50 dark:hover:bg-red-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Stage terminés</p>
                    <h2 class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $terminesGlobal }}</h2>
                </a>

                <!-- Types de stages -->
                <a href="{{ route('type_stages.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-purple-50 dark:hover:bg-purple-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Types de stages</p>
                    <h2 class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $totalTypes }}</h2>
                </a>

                <!-- Badges -->
                <a href="{{ route('badges.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-yellow-50 dark:hover:bg-yellow-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Badges</p>
                    <h2 class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalBadges }}</h2>
                </a>

                <!-- Services -->
                <a href="{{ route('services.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-teal-50 dark:hover:bg-teal-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">Services</p>
                    <h2 class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ $totalServices }}</h2>
                </a>

                <!-- Corbeille -->
                <a href="{{ route('corbeille.index') }}" 
                   class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-center hover:shadow-2xl hover:bg-red-50 dark:hover:bg-red-900 transition">
                    <p class="text-gray-500 dark:text-gray-300">🗑️ Corbeille</p>
                    <h2 class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $totalTrash }}</h2>
                </a>
            </div>

            <!-- Graphiques -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 mb-12 w-2/6 mx-auto">
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mb-4 text-center">Statistiques globales des stages</h2>
                <canvas id="chart-global" class="w-full h-36"></canvas>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 mb-12">
                <h2 class="text-xl font-semibold text-black dark:text-gray-200">Comparaison Stages / Services</h2>
                <canvas id="chart-bar" class="w-full h-full"></canvas>
            </div>

            <!-- Dernières activités -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-10">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Dernières activités</h2>
                <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-300">
                    @forelse($activities as $activity)
                        <li>
                            <span class="text-blue-600 dark:text-blue-400 font-medium">
                                {{ $activity->user->name ?? 'Système' }}
                            </span>
                            → {{ $activity->description }}
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                ({{ $activity->created_at->diffForHumans() }})
                            </span>
                        </li>
                    @empty
                        <li class="text-gray-500 dark:text-gray-400">Aucune activité enregistrée.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Diagramme doughnut global
        new Chart(document.getElementById('chart-global'), {
            type: 'doughnut',
            data: {
                labels: ['Inscrits', 'En cours', 'Terminés'],
                datasets: [{
                    data: [{{ $inscritsGlobal }}, {{ $enCoursGlobal }}, {{ $terminesGlobal }}],
                    backgroundColor: ['#3b82f6', '#22c55e', '#ef4444'],
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // Diagramme en barres services / stages
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

        // 💡 Gestion du menu thème
        const html = document.documentElement;
        const menuBtn = document.getElementById("theme-menu-btn");
        const menu = document.getElementById("theme-menu");

        menuBtn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });

        menu.querySelectorAll("button[data-theme]").forEach(btn => {
            btn.addEventListener("click", () => {
                const theme = btn.getAttribute("data-theme");
                if(theme === "dark"){
                    html.classList.add("dark");
                    localStorage.setItem("theme", "dark");
                } else {
                    html.classList.remove("dark");
                    localStorage.setItem("theme", "light");
                }
                menu.classList.add("hidden");
            });
        });

        // Initialisation du thème au chargement
        if(localStorage.getItem("theme") === "dark"){
            html.classList.add("dark");
        }
    </script>
</x-app-layout>
