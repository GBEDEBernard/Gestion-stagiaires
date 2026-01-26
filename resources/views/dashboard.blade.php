<x-app-layout>
    <div class="py-10 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6">

            <!-- En-tête du Dashboard -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">📊 Tableau de Bord</h1>
                <p class="text-gray-600 dark:text-gray-400">Bienvenue ! Voici vos statistiques en temps réel</p>
            </div>

            <!-- KPIs Principaux -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stagiaires Inscrits -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Stagiaires Inscrits</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ \App\Models\Etudiant::count() }}</h3>
                        </div>
                        <span class="text-3xl">👥</span>
                    </div>
                    <div class="flex items-center text-green-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12 7a1 1 0 110-2h.01a1 1 0 110 2H12zm-4 4a1 1 0 110-2h.01a1 1 0 110 2H8z"></path>
                        </svg>
                        +12% ce mois
                    </div>
                </div>

                <!-- Stages en Cours -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Stages en Cours</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $enCoursGlobal }}</h3>
                        </div>
                        <span class="text-3xl">▶️</span>
                    </div>
                    <div class="flex items-center text-green-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12 7a1 1 0 110-2h.01a1 1 0 110 2H12z"></path>
                        </svg>
                        Actif maintenant
                    </div>
                </div>

                <!-- Stages Terminés -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Stages Terminés</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $terminesGlobal }}</h3>
                        </div>
                        <span class="text-3xl">✅</span>
                    </div>
                    <div class="flex items-center text-blue-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"></path>
                        </svg>
                        Complétés
                    </div>
                </div>

                <!-- Taux de Présence -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Taux de Présence</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">87%</h3>
                        </div>
                        <span class="text-3xl">📈</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: 87%"></div>
                    </div>
                </div>
            </div>

            <!-- Statistiques Temporelles & Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Évolution des Inscriptions -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">📈 Évolution des Inscriptions</h2>
                        <select class="text-sm bg-blue-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded">
                            <option>Cette semaine</option>
                            <option>Ce mois</option>
                            <option>Cette année</option>
                        </select>
                    </div>
                    <canvas id="chart-inscriptions" height="80"></canvas>
                </div>

                <!-- Indicateurs de Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">⚡ Performance</h2>

                    <!-- Taux de Réussite -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux de Réussite</span>
                            <span class="text-sm font-bold text-green-600">92%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>

                    <!-- Taux de Présence -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux de Présence</span>
                            <span class="text-sm font-bold text-blue-600">87%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 87%"></div>
                        </div>
                    </div>

                    <!-- Taux d'Abandon -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux d'Abandon</span>
                            <span class="text-sm font-bold text-red-600">8%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: 8%"></div>
                        </div>
                    </div>

                    <!-- Conversions -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux de Conversion</span>
                            <span class="text-sm font-bold text-purple-600">78%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaisons et Activités -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Comparaison Période -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Comparaison Période</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-gray-700 rounded">
                            <span class="text-gray-700 dark:text-gray-300">Inscriptions ce mois</span>
                            <span class="text-xl font-bold text-green-600">+23%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-gray-700 rounded">
                            <span class="text-gray-700 dark:text-gray-300">Stages actuels vs mois dernier</span>
                            <span class="text-xl font-bold text-green-600">+15%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-gray-700 rounded">
                            <span class="text-gray-700 dark:text-gray-300">Taux de complétion</span>
                            <span class="text-xl font-bold text-blue-600">↑ 8%</span>
                        </div>
                    </div>
                </div>

                <!-- Distribution par Type de Stage -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">🎯 Distribution par Type</h2>
                    <canvas id="chart-types" height="80"></canvas>
                </div>
            </div>

            <!-- Statistiques par Service -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Répartition par Service</h2>
                <canvas id="chart-bar" height="80"></canvas>
            </div>

            <!-- Tableau des Derniers Stagiaires -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">👥 Derniers Stagiaires Inscrits</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Nom</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Stage</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Date Inscription</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse(\App\Models\Etudiant::latest()->take(5)->get() as $etudiant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $etudiant->nom }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $etudiant->email }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $etudiant->stages->first()->titre ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $etudiant->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Actif
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Aucun stagiaire inscrit</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique d'évolution des inscriptions
        const ctx1 = document.getElementById('chart-inscriptions').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Nouvelles Inscriptions',
                    data: [12, 19, 8, 15, 22, 18, 14],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            color: 'rgba(107, 114, 128, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Graphique distribution par type de stage
        const ctx2 = document.getElementById('chart-types').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Technique', 'Commercial', 'Administratif', 'Support'],
                datasets: [{
                    data: [35, 25, 20, 20],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique en barres services / stages
        const ctx3 = document.getElementById('chart-bar').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: {!! json_encode($servicesStats->pluck('service') ?? []) !!},
                datasets: [{
                    label: 'Inscrits',
                    data: {!! json_encode($servicesStats->pluck('inscrits') ?? []) !!},
                    backgroundColor: '#3b82f6'
                },
                {
                    label: 'En cours',
                    data: {!! json_encode($servicesStats->pluck('enCours') ?? []) !!},
                    backgroundColor: '#22c55e'
                },
                {
                    label: 'Terminés',
                    data: {!! json_encode($servicesStats->pluck('termines') ?? []) !!},
                    backgroundColor: '#ef4444'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            color: 'rgba(107, 114, 128, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gestion du thème Dark Mode
        const html = document.documentElement;
        const menuBtn = document.getElementById("theme-menu-btn");
        const menu = document.getElementById("theme-menu");

        if (menuBtn && menu) {
            menuBtn.addEventListener("click", () => {
                menu.classList.toggle("hidden");
            });

            menu.querySelectorAll("button[data-theme]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const theme = btn.getAttribute("data-theme");
                    if (theme === "dark") {
                        html.classList.add("dark");
                        localStorage.setItem("theme", "dark");
                    } else {
                        html.classList.remove("dark");
                        localStorage.setItem("theme", "light");
                    }
                    menu.classList.add("hidden");
                });
            });
        }

        // Initialisation du thème au chargement
        if (localStorage.getItem("theme") === "dark") {
            html.classList.add("dark");
        }
    </script>
    @endpush
</x-app-layout>