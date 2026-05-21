<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Corbeille — Employés</h1>
        <p class="text-sm text-slate-500">Employés supprimés (soft-deleted)</p>
    </div>

    <div class="space-y-6">
        <x-trash-table
            :items="$employes"
            title="Employés supprimés"
            :columns="['Employé', 'Email', 'Matricule', 'Supprimé']"
            :restoreRoute="'employes.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'employes.force-delete'"
            :fields="['personnel.full_name','personnel.email','matricule']"
            icon="👔"
            color="amber" />
    </div>

    <div class="mt-4">
        {{ $employes->links() }}
    </div>
</x-app-layout>