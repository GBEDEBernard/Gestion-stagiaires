<x-app-layout>
    <!-- En-tête Premium avec statistiques -->
    <div class="mb-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-red-600 via-rose-600 to-pink-600 rounded-3xl p-6 sm:p-8 shadow-2xl">
            <!-- Fond décoratif -->
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-black/10 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-6">
                    <!-- Titre et description -->
                    <div class="flex items-center gap-4">
                        <div class="p-3 sm:p-4 bg-white/20 backdrop-blur-sm rounded-2xl">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Corbeille</h1>
                            <p class="text-red-100 text-sm sm:text-base mt-1">Gestion des éléments supprimés</p>
                        </div>
                    </div>

                    <!-- Stats rapides -->
                    <div class="flex flex-wrap gap-3">
                        <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl">
                            <p class="text-xs text-red-100 font-medium">Total</p>
                            <p class="text-xl font-bold text-white">{{ $stagesTrash->count() + $etudiantsTrash->count() + $badgesTrash->count() + $servicesTrash->count() + $usersTrash->count() }}</p>
                        </div>
                        <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl">
                            <p class="text-xs text-red-100 font-medium">Restaurables</p>
                            <p class="text-xl font-bold text-white">
                                {{ ($stagesTrash->count() > 0 ? 1 : 0) + ($etudiantsTrash->count() > 0 ? 1 : 0) + ($badgesTrash->count() > 0 ? 1 : 0) + ($servicesTrash->count() > 0 ? 1 : 0) + ($usersTrash->count() > 0 ? 1 : 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="space-y-6">
        <!-- Stages supprimés -->
        <x-trash-table
            :items="$stagesTrash"
            title="Stages supprimés"
            :columns="['Étudiant', 'Période', 'Supprimé']"
            :restoreRoute="'stages.restore'"
            :forceDeleteRoute="'stages.forceDelete'"
            :periodColumn="['date_debut','date_fin']"
            :relationColumn="'etudiant.nom'"
            icon="📋"
            color="blue" />

        <!-- Étudiants supprimés -->
        <x-trash-table
            :items="$etudiantsTrash"
            title="Étudiants supprimés"
            :columns="['Informations', 'Contact', 'Supprimé']"
            :restoreRoute="'etudiants.restore'"
            :forceDeleteRoute="'etudiants.forceDelete'"
            :fields="['nom', 'email']"
            icon="👨‍🎓"
            color="green" />

        <!-- Badges supprimés -->
        <x-trash-table
            :items="$badgesTrash"
            title="Badges supprimés"
            :columns="['Numéro de Badge', 'Supprimé']"
            :restoreRoute="'badges.restore'"
            :forceDeleteRoute="'badges.forceDelete'"
            :fields="['badge']"
            icon="🏷️"
            color="purple" />

        <!-- Services supprimés -->
        <x-trash-table
            :items="$servicesTrash"
            title="Services supprimés"
            :columns="['Nom du Service', 'Supprimé']"
            :restoreRoute="'services.restore'"
            :forceDeleteRoute="'services.forceDelete'"
            :fields="['nom']"
            icon="🏢"
            color="orange" />

        <!-- Utilisateurs supprimés -->
        <x-trash-table
            :items="$usersTrash"
            title="Utilisateurs supprimés"
            :columns="['Utilisateur', 'Email', 'Supprimé']"
            :restoreRoute="'users.restore'"
            :forceDeleteRoute="'users.forceDelete'"
            :fields="['name', 'email']"
            icon="👤"
            color="red" />
    </div>

    <!-- Message d'avertissement -->
    <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800 dark:text-amber-300">Attention</h3>
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                    La suppression définitive est irréversible. Les éléments supprimés ne pourront pas être récupérés.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>