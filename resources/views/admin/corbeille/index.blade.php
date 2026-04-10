<x-app-layout>
<<<<<<< HEAD
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
=======
    <div class="mb-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-rose-600 to-pink-600 p-6 sm:p-8 shadow-2xl">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 h-80 w-80 rounded-full bg-black/10 blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex flex-col items-start justify-between gap-6 sm:flex-row">
                    <div class="flex items-center gap-4">
                        <div class="rounded-2xl bg-white/20 p-3 backdrop-blur-sm sm:p-4">
                            <svg class="h-8 w-8 text-white sm:h-10 sm:w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> e9635ab
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
<<<<<<< HEAD
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
=======
                            <h1 class="text-2xl font-bold text-white sm:text-3xl">Corbeille</h1>
                            <p class="mt-1 text-sm text-red-100 sm:text-base">Gestion des elements supprimes</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="rounded-xl bg-white/20 px-4 py-2 backdrop-blur-sm">
                            <p class="text-xs font-medium text-red-100">Total</p>
                            <p class="text-xl font-bold text-white">{{ $stagesTrash->count() + $etudiantsTrash->count() + $badgesTrash->count() + $servicesTrash->count() + $usersTrash->count() }}</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm">
                            <p class="text-xs font-medium text-red-100">Sections actives</p>
>>>>>>> e9635ab
                            <p class="text-xl font-bold text-white">
                                {{ ($stagesTrash->count() > 0 ? 1 : 0) + ($etudiantsTrash->count() > 0 ? 1 : 0) + ($badgesTrash->count() > 0 ? 1 : 0) + ($servicesTrash->count() > 0 ? 1 : 0) + ($usersTrash->count() > 0 ? 1 : 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
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
=======
    <div class="space-y-6">
        {{-- jb -> Chaque bloc passe ici avec le vrai nom de route et la
        bonne methode HTTP pour eviter les erreurs silencieuses dans la corbeille. --}}
        <x-trash-table
            :items="$stagesTrash"
            title="Stages supprimes"
            :columns="['Etudiant', 'Periode', 'Supprime']"
            :restoreRoute="'stages.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'stages.forceDelete'"
            :periodColumn="['date_debut', 'date_fin']"
>>>>>>> e9635ab
            :relationColumn="'etudiant.nom'"
            icon="📋"
            color="blue" />

<<<<<<< HEAD
        <!-- Étudiants supprimés -->
        <x-trash-table
            :items="$etudiantsTrash"
            title="Étudiants supprimés"
            :columns="['Informations', 'Contact', 'Supprimé']"
            :restoreRoute="'etudiants.restore'"
=======
        <x-trash-table
            :items="$etudiantsTrash"
            title="Etudiants supprimes"
            :columns="['Informations', 'Contact', 'Supprime']"
            :restoreRoute="'etudiants.restore'"
            :restoreMethod="'PUT'"
>>>>>>> e9635ab
            :forceDeleteRoute="'etudiants.forceDelete'"
            :fields="['nom', 'email']"
            icon="👨‍🎓"
            color="green" />

<<<<<<< HEAD
        <!-- Badges supprimés -->
        <x-trash-table
            :items="$badgesTrash"
            title="Badges supprimés"
            :columns="['Numéro de Badge', 'Supprimé']"
            :restoreRoute="'badges.restore'"
            :forceDeleteRoute="'badges.forceDelete'"
=======
        <x-trash-table
            :items="$badgesTrash"
            title="Badges supprimes"
            :columns="['Numero de badge', 'Supprime']"
            :restoreRoute="'badges.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'badges.force-delete'"
>>>>>>> e9635ab
            :fields="['badge']"
            icon="🏷️"
            color="purple" />

<<<<<<< HEAD
        <!-- Services supprimés -->
        <x-trash-table
            :items="$servicesTrash"
            title="Services supprimés"
            :columns="['Nom du Service', 'Supprimé']"
            :restoreRoute="'services.restore'"
            :forceDeleteRoute="'services.forceDelete'"
=======
        <x-trash-table
            :items="$servicesTrash"
            title="Services supprimes"
            :columns="['Nom du service', 'Supprime']"
            :restoreRoute="'services.restore'"
            :restoreMethod="'PATCH'"
            :forceDeleteRoute="'services.force-delete'"
>>>>>>> e9635ab
            :fields="['nom']"
            icon="🏢"
            color="orange" />

<<<<<<< HEAD
        <!-- Utilisateurs supprimés -->
        <x-trash-table
            :items="$usersTrash"
            title="Utilisateurs supprimés"
            :columns="['Utilisateur', 'Email', 'Supprimé']"
            :restoreRoute="'users.restore'"
=======
        <x-trash-table
            :items="$usersTrash"
            title="Utilisateurs supprimes"
            :columns="['Utilisateur', 'Email', 'Supprime']"
            :restoreRoute="'users.restore'"
            :restoreMethod="'PUT'"
>>>>>>> e9635ab
            :forceDeleteRoute="'users.forceDelete'"
            :fields="['name', 'email']"
            icon="👤"
            color="red" />
    </div>

<<<<<<< HEAD
    <!-- Message d'avertissement -->
    <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
    <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
        <div class="flex items-start gap-3">
            <div class="rounded-xl bg-amber-100 p-2 dark:bg-amber-900/30">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> e9635ab
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800 dark:text-amber-300">Attention</h3>
<<<<<<< HEAD
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                    La suppression définitive est irréversible. Les éléments supprimés ne pourront pas être récupérés.
=======
                <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                    La suppression definitive est irreversible. Un element force-delete ne pourra plus etre recupere.
>>>>>>> e9635ab
                </p>
            </div>
        </div>
    </div>
<<<<<<< HEAD
</x-app-layout>
=======
</x-app-layout>
>>>>>>> e9635ab
