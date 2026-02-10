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
                <!-- Total Stages -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Stages</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalStages }}</h3>
                        </div>
                        <span class="text-3xl">📚</span>
                    </div>
                    <div class="flex items-center text-blue-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        </svg>
                        Tous les stages
                    </div>
                </div>

                <!-- Stages en Cours -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Stages en Cours</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $enCoursGlobal }}</h3>
                        </div>
                        <span class="text-3xl">▶️</span>
                    </div>
                    <div class="flex items-center text-green-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16z"></path>
                        </svg>
                        Actifs maintenant
                    </div>
                </div>

                <!-- Stages Terminés -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Stages Terminés</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $terminesGlobal }}</h3>
                        </div>
                        <span class="text-3xl">✅</span>
                    </div>
                    <div class="flex items-center text-purple-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                        </svg>
                        Complétés
                    </div>
                </div>

                <!-- Taux de Présence -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Taux de Présence</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $tauxPresence }}%</h3>
                        </div>
                        <span class="text-3xl">📈</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-orange-500 h-2 rounded-full transition-all" style="width: {{ $tauxPresence }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Statistiques Temporelles & Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Évolution des Inscriptions -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">📈 Évolution des Inscriptions</h2>
                   
                        <div class="flex gap-2">
                            <button onclick="changeChartPeriod('jour')" id="btn-jour" class="px-3 py-1 text-xs font-medium rounded bg-blue-500 text-white hover:bg-blue-600 transition">
                                Par Jour
                            </button>
                            <button onclick="changeChartPeriod('semaine')" id="btn-semaine" class="px-3 py-1 text-xs font-medium rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                Par Semaine
                            </button>
                            <button onclick="changeChartPeriod('mois')" id="btn-mois" class="px-3 py-1 text-xs font-medium rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                Par Mois
                            </button>
                        </div>
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
                            <span class="text-sm font-bold text-green-600">{{ $tauxReussite }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $tauxReussite }}%"></div>
                        </div>
                    </div>

                    <!-- Taux de Présence -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux de Présence</span>
                            <span class="text-sm font-bold text-blue-600">{{ $tauxPresence }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $tauxPresence }}%"></div>
                        </div>
                    </div>

                    <!-- Taux d'Abandon -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux d'Abandon</span>
                            <span class="text-sm font-bold text-red-600">{{ $tauxAbandon }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: {{ $tauxAbandon }}%"></div>
                        </div>
                    </div>

                    <!-- Conversions -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taux de Conversion</span>
                            <span class="text-sm font-bold text-purple-600">{{ $tauxConversion }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ $tauxConversion }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaisons et Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Comparaison Période -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Comparaison Période</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-gray-700 rounded hover:shadow transition">
                            <span class="text-gray-700 dark:text-gray-300">Inscriptions ce mois</span>
                            <span class="text-xl font-bold {{ $evolutionInscriptionsMois >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $evolutionInscriptionsMois >= 0 ? '+' : '' }}{{ $evolutionInscriptionsMois }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-gray-700 rounded hover:shadow transition">
                            <span class="text-gray-700 dark:text-gray-300">Stages actuels vs mois dernier</span>
                            <span class="text-xl font-bold {{ $evolutionStages >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $evolutionStages >= 0 ? '+' : '' }}{{ $evolutionStages }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-gray-700 rounded hover:shadow transition">
                            <span class="text-gray-700 dark:text-gray-300">Taux de complétion</span>
                            <span class="text-xl font-bold text-blue-600">↑ {{ $tauxCompletion }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Distribution par Type de Stage -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">🎯 Distribution par Type</h2>
                    <canvas id="chart-types" height="280"></canvas>
                </div>
            </div>

            <!-- Statistiques par Service -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Répartition par Service</h2>
                <canvas id="chart-services" height="90"></canvas>
            </div>

            <!-- Tableau des Derniers Stagiaires -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
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
                            @forelse($derniersEtudiants as $etudiant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $etudiant->nom }}</td>
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
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="mt-2">Aucun stagiaire inscrit</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Corbeille -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">🗑️ Corbeille</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        {{ $totalTrash }} éléments
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow transition">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Stages</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $stagesTrash->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow transition">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Étudiants</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $etudiantsTrash->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow transition">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Badges</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $badgesTrash->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow transition">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Services</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $servicesTrash->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        // Configuration globale
        Chart.defaults.font.family = "'Inter', sans-serif";
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? 'rgba(107, 114, 128, 0.2)' : 'rgba(107, 114, 128, 0.1)';

        // Données des trois périodes
        const dataJour = {
            labels: @json($labelsJour),
            data: @json($evolutionJour),
            title: '30 derniers jours'
        };

        const dataSemaine = {
            labels: @json($labelsSemaine),
            data: @json($evolutionSemaine),
            title: '12 dernières semaines'
        };

        const dataMois = {
            labels: @json($labelsMois),
            data: @json($evolutionMois),
            title: '12 derniers mois'
        };

        let chartInscriptions;
        let currentPeriod = 'jour';

        // 1. Fonction pour créer/mettre à jour le graphique
        function createOrUpdateChart(period) {
            const ctxInscriptions = document.getElementById('chart-inscriptions');
            if (!ctxInscriptions) return;

            const periodData = period === 'jour' ? dataJour : 
                              period === 'semaine' ? dataSemaine : dataMois;

            // Détruire le graphique existant
            if (chartInscriptions) {
                chartInscriptions.destroy();
            }

            // Créer nouveau graphique
            chartInscriptions = new Chart(ctxInscriptions, {
                type: 'line',
                data: {
                    labels: periodData.labels,
                    datasets: [{
                        label: 'Nouvelles Inscriptions',
                        data: periodData.data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    const value = context.parsed.y;
                                    return value + ' inscription' + (value > 1 ? 's' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                font: { size: 11 },
                                stepSize: 1
                            },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                font: { size: 10 },
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: { display: false }
                        }
                    },
                    animation: {
                        duration: 750,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // 2. Fonction pour changer de période
        function changeChartPeriod(period) {
            currentPeriod = period;
            
            // Mettre à jour les boutons
            ['jour', 'semaine', 'mois'].forEach(p => {
                const btn = document.getElementById('btn-' + p);
                if (p === period) {
                    btn.className = 'px-3 py-1 text-xs font-medium rounded bg-blue-500 text-white hover:bg-blue-600 transition';
                } else {
                    btn.className = 'px-3 py-1 text-xs font-medium rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition';
                }
            });

            // Recréer le graphique
            createOrUpdateChart(period);
        }

        // Initialiser le graphique au chargement
        createOrUpdateChart('jour');

        // 2. Graphique Distribution par Type
        const ctxTypes = document.getElementById('chart-types');
        if (ctxTypes) {
            const typesLabelsData = @json($typesLabels);
            const typesValuesData = @json($typesData);
            
            new Chart(ctxTypes, {
                type: 'doughnut',
                data: {
                    labels: typesLabelsData,
                    datasets: [{
                        data: typesValuesData,
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6',
                            '#ec4899'
                        ],
                        borderColor: '#fff',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12
                        }
                    }
                }
            });
        }

        // 3. Graphique Répartition par Service
        const ctxServices = document.getElementById('chart-services');
        if (ctxServices) {
            const servicesLabels = @json($servicesStats->pluck('service'));
            const enCoursData = @json($servicesStats->pluck('enCours'));
            const terminesData = @json($servicesStats->pluck('termines'));
            const inscritsData = @json($servicesStats->pluck('inscrits'));
            
            new Chart(ctxServices, {
                type: 'bar',
                data: {
                    labels: servicesLabels,
                    datasets: [
                        {
                            label: 'En Cours',
                            data: enCoursData,
                            backgroundColor: '#22c55e',
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'Terminés',
                            data: terminesData,
                            backgroundColor: '#a855f7',
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'À Venir',
                            data: inscritsData,
                            backgroundColor: '#f97316',
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                font: { size: 11 },
                                stepSize: 1
                            },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                font: { size: 11 }
                            },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 15,
                                font: { size: 12 },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12
                        }
                    }
                }
            });
        }

        // Gestion du thème Dark Mode
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.classList.add("dark");
        }
    </script>
    @endpush
</x-app-layout>