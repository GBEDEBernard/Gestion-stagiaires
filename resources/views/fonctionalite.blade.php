@extends('bloglayouts')


@section('contenu')

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fonctionnalités - Gestion des Stagiaires</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="flex flex-col min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
   
                   

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 mt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" x-data="{ text: '' }" x-init="$nextTick(() => text = 'Découvrez les fonctionnalités puissantes')" x-text="text"></h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto" x-data="{ opacity: 0 }" x-init="$nextTick(() => opacity = 1)" :style="{ opacity }" x-transition.opacity>
                Notre plateforme offre des outils intuitifs pour gérer vos stagiaires efficacement, de l'inscription au suivi des performances.
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Fonctionnalités clés</h2>
            <div class="grid gap-8 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                     x-data="{ visible: false }" x-init="$nextTick(() => visible = true)" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Gestion des profils</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Créez et gérez facilement les profils des stagiaires avec toutes les informations nécessaires.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                     x-data="{ visible: false }" x-init="$nextTick(() => visible = true)" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:enter-delay="100">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Suivi en temps réel</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Suivez les présences, les progrès  des stagiaires en temps réel.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                     x-data="{ visible: false }" x-init="$nextTick(() => visible = true)" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:enter-delay="200">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m0 0h6m-6 0c0-1.1.9-2 2-2s2 .9 2 2m-4 0H3m12 0h6m-9 2v2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Rapports personnalisés</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Générez des rapports détaillés pour analyser les performances et optimiser l'encadrement.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                     x-data="{ visible: false }" x-init="$nextTick(() => visible = true)" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:enter-delay="300">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Planification des stages</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Organisez les calendriers et attribuez des tâches aux stagiaires en quelques clics.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                     x-data="{ visible: false }" x-init="$nextTick(() => visible = true)" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:enter-delay="400">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Communication intégrée</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Échangez avec les stagiaires .
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                     x-data="{ visible: false }" x-init="$nextTick(() => visible = true)" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:enter-delay="500">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-2.76-2.24-5-5-5S2 8.24 2 11m20 0c0-2.76-2.24-5-5-5s-5 2.24-5 5"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Sécurité des données</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Protégez les informations des stagiaires avec un chiffrement de haut niveau.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 bg-indigo-600 text-white text-center mb-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold mb-4" x-data="{ text: '' }" x-init="$nextTick(() => text = 'Prêt à transformer votre gestion des stagiaires ?')" x-text="text"></h2>
            <p class="text-lg mb-6 max-w-2xl mx-auto" x-data="{ opacity: 0 }" x-init="$nextTick(() => opacity = 1)" :style="{ opacity }" x-transition.opacity>
                Essayez notre plateforme dès aujourd'hui et découvrez une gestion simplifiée et efficace.
            </p>
            <a href="{{ route('register') }}" class="bg-white text-indigo-600 hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition transform hover:scale-105 duration-200">
                Commencer maintenant
            </a>
        </div>
    </section>

   
    <!-- JavaScript pour toggle menu mobile -->
    <script>
        const menuButton = document.getElementById('mobile-menu-button');
        const navMenu = document.getElementById('nav-menu');

        menuButton.addEventListener('click', () => {
            navMenu.classList.toggle('opacity-0');
            navMenu.classList.toggle('opacity-100');
            navMenu.classList.toggle('pointer-events-none');
            navMenu.classList.toggle('pointer-events-auto');
        });
    </script>
</body>
</html>
@endsection