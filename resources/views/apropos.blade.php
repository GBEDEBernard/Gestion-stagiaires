@extends('bloglayouts')


@section('contenu')

<body class="bg-gray-100 font-sans">
   
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-20 mt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-extrabold sm:text-5xl">À propos de notre projet</h2>
            <p class="mt-4 text-lg max-w-2xl mx-auto">
                Découvrez comment notre solution de gestion des stagiaires simplifie et optimise le suivi des stagiaires pour les entreprises.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-3xl font-bold text-red-900">Notre Mission</h3>
                <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                    Notre projet vise à fournir une plateforme intuitive et efficace pour gérer les stagiaires, en facilitant leur intégration, au sein des organisations <strong class="text-blue-900">Technology Forever Group (TFG)</strong>.
                </p>
            </div>
        </div>
    </section>

    <!-- pourquoi nous  section -->
    <section class="py-16 bg-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold text-gray-900 text-center">Pourquoi choisir notre solution ?</h3>
            <div class="mt-12 grid gap-8 lg:grid-cols-3 sm:grid-cols-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-semibold text-indigo-900">Simplicité</h4>
                    <p class="mt-2 text-gray-600">
                        Une interface utilisateur claire et facile à prendre en main pour une gestion sans tracas.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-semibold text-indigo-900">Efficacité</h4>
                    <p class="mt-2 text-gray-600">
                        Automatisation des tâches répétitives pour gagner du temps et réduire les erreurs.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-semibold text-indigo-900">Flexibilité</h4>
                    <p class="mt-2 text-gray-600">
                        Une solution adaptable à tous types d'entreprises, quelle que soit leur taille.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- group Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold text-gray-900 text-center">Notre Équipe</h3>
            <p class="mt-4 text-lg text-black text-center max-w-3xl mx-auto">
                Nous sommes une équipe passionnée, dédiée à la création de solutions innovantes pour la gestion des ressources humaines.
            </p>
            <div class="mt-12 grid gap-8 lg:grid-cols-3 sm:grid-cols-1">
                <div class="text-center">
                    <div class="h-24 w-24 mx-auto bg-gray-300 rounded-full"></div>
                    <h4 class="mt-4 text-xl font-semibold text-gray-900">Nom Prénom</h4>
                    <p class="text-gray-600">Fondateur & Développeur</p>
                </div>
                <div class="text-center">
                    <div class="h-24 w-24 mx-auto bg-gray-300 rounded-full"></div>
                    <h4 class="mt-4 text-xl font-semibold text-gray-900">Nom Prénom</h4>
                    <p class="text-gray-600">Designer UX/UI</p>
                </div>
                <div class="text-center">
                    <div class="h-24 w-24 mx-auto bg-gray-300 rounded-full"></div>
                    <h4 class="mt-4 text-xl font-semibold text-gray-900">Nom Prénom</h4>
                    <p class="text-gray-600">Responsable Marketing</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->

</body>

@endsection