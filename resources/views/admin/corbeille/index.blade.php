<x-app-layout>
    <div class="mb-8">
        <div class="overflow-hidden rounded-3xl bg-slate-900/95 p-6 shadow-2xl ring-1 ring-slate-800">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Corbeille</p>
                    <h1 class="mt-3 text-3xl font-semibold text-white">Éléments supprimés</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">
                        Retrouve ici tous les étudiants, le personnel, les stages et les services supprimés.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-slate-700 bg-slate-950/80 px-4 py-4">
                        <p class="text-xs uppercase text-slate-500">Total supprimés</p>
                        <p class="mt-2 text-3xl font-bold text-white">
                            {{ $stagesTrash->count() + $etudiantsTrash->count() + $badgesTrash->count() + $servicesTrash->count() + $usersTrash->count() }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-700 bg-slate-950/80 px-4 py-4">
                        <p class="text-xs uppercase text-slate-500">Étudiants</p>
                        <p class="mt-2 text-3xl font-bold text-cyan-400">{{ $etudiantsTrash->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-700 bg-slate-950/80 px-4 py-4">
                        <p class="text-xs uppercase text-slate-500">Personnel</p>
                        <p class="mt-2 text-3xl font-bold text-amber-400">{{ $usersTrash->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <x-trash-table
            :items="$etudiantsTrash"
            title="Étudiants supprimés"
            :columns="['Étudiant', 'Email', 'Supprimé']"
            :restoreRoute="'etudiants.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'etudiants.forceDelete'"
            :fields="['personnel.full_name', 'personnel.email']"
            icon="👨‍🎓"
            color="blue" />

        <x-trash-table
            :items="$usersTrash"
            title="Personnel supprimé"
            :columns="['Nom', 'Email', 'Supprimé']"
            :restoreRoute="'users.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'users.forceDelete'"
            :fields="['name','email']"
            icon="👤"
            color="red" />

        <x-trash-table
            :items="$stagesTrash"
            title="Stages supprimés"
            :columns="['Étudiant', 'Fin de période', 'Supprimé']"
            :restoreRoute="'stages.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'stages.forceDelete'"
            :periodColumn="['date_debut', 'date_fin']"
            :relationColumn="'etudiant.personnel.full_name'"
            icon="📋"
            color="purple" />

        <x-trash-table
            :items="$servicesTrash"
            title="Services supprimés"
            :columns="['Service', 'Supprimé']"
            :restoreRoute="'services.restore'"
            :restoreMethod="'PATCH'"
            :forceDeleteRoute="'services.force-delete'"
            :fields="['nom']"
            icon="🏢"
            color="orange" />

        <x-trash-table
            :items="$badgesTrash"
            title="Badges supprimés"
            :columns="['Badge', 'Supprimé']"
            :restoreRoute="'badges.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'badges.force-delete'"
            :fields="['badge']"
            icon="🏷️"
            color="violet" />
    </div>

    <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
        <div class="flex items-start gap-3">
            <div class="rounded-xl bg-amber-100 p-2 dark:bg-amber-900/30">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800 dark:text-amber-300">Attention</h3>
                <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                    La suppression définitive est irréversible. Un élément force-delete ne pourra plus être récupéré.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>