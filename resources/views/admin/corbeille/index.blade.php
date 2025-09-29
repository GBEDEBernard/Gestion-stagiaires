<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-6">
            <h1 class="text-4xl font-bold text-gray-900 mb-10 flex items-center gap-3">
                🗑️ Corbeille
            </h1>

            <!-- Stages supprimés -->
            <x-trash-table 
                :items="$stagesTrash" 
                title="Stages supprimés"
                :columns="['Étudiant', 'Période']"
                :restoreRoute="'stages.restore'"
                :forceDeleteRoute="'stages.forceDelete'"
                :periodColumn="['date_debut','date_fin']"
                :relationColumn="'etudiant.nom'"
            />

            <!-- Étudiants supprimés -->
            <x-trash-table 
                :items="$etudiantsTrash" 
                title="Étudiants supprimés"
                :columns="['Nom', 'Email']"
                :restoreRoute="'etudiants.restore'"
                :forceDeleteRoute="'etudiants.forceDelete'"
                :fields="['nom','email']"
            />

            <!-- Badges supprimés -->
            <x-trash-table 
                :items="$badgesTrash" 
                title="Badges supprimés"
                :columns="['Numéro de Badge']"
                :restoreRoute="'badges.restore'"
                :forceDeleteRoute="'badges.forceDelete'"
                :fields="['badge']"
            />

            <!-- Services supprimés -->
            <x-trash-table 
                :items="$servicesTrash" 
                title="Services supprimés"
                :columns="['Nom']"
                :restoreRoute="'services.restore'"
                :forceDeleteRoute="'services.forceDelete'"
                :fields="['nom']"
            />

            <!-- Utilisateurs supprimés -->
            <x-trash-table 
                :items="$usersTrash" 
                title="Utilisateurs supprimés"
                :columns="['Nom','Email']"
                :restoreRoute="'users.restore'"
                :forceDeleteRoute="'users.forceDelete'"
                :fields="['name','email']"
            />

        </div>
    </div>
</x-app-layout>
