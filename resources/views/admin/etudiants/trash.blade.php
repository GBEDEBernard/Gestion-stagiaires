<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Corbeille — Stagiaires</h1>
        <p class="text-sm text-slate-500">Stagiaires supprimés (soft-deleted)</p>
    </div>

    <div class="space-y-6">
        <x-trash-table
            :items="$etudiants"
            title="Stagiaires supprimés"
            :columns="['Stagiaire', 'Email', 'Supprimé']"
            :restoreRoute="'etudiants.restore'"
            :restoreMethod="'PUT'"
            :forceDeleteRoute="'etudiants.forceDelete'"
            :fields="['personnel.full_name','personnel.email']"
            icon="👨‍🎓"
            color="blue" />
    </div>

    <div class="mt-4">
        {{ $etudiants->links() }}
    </div>
</x-app-layout>