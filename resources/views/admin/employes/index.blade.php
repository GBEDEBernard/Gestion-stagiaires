<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Employés</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gestion des employés et génération de leurs comptes</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('employes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 transition shadow-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Nouvel employé
                </a>
                <a href="{{ route('employes.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 rounded-xl hover:bg-gray-200 transition">Corbeille</a>
            </div>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>@endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr><th class="px-6 py-4 text-left text-xs font-semibold">Matricule</th><th>Nom complet</th><th>Email</th><th>Domaine</th><th>Site</th><th>Poste</th><th>Compte</th><th class="text-right">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($employes as $emp)
                            @php $p = $emp->personnel; @endphp
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">{{ $emp->matricule }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $p->prenom }} {{ $p->nom }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->email }}</td>
                                <td class="px-6 py-4">{{ $emp->domaine->nom }}</td>
                                <td class="px-6 py-4">{{ $emp->site->name }}</td>
                                <td class="px-6 py-4">{{ $emp->poste ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($p->user)
                                        <span class="text-green-600 text-sm font-medium">✓ Actif</span>
                                    @else
                                        <form action="{{ route('employes.generate-account', $emp) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-blue-600 text-sm hover:underline">Générer compte</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('employes.show', $emp) }}" class="p-2 bg-gray-100 rounded-lg">👁️</a>
                                    <a href="{{ route('employes.edit', $emp) }}" class="p-2 bg-yellow-100 rounded-lg">✏️</a>
                                    <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-100 rounded-lg">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-16 text-center text-gray-500">Aucun employé trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t">{{ $employes->links() }}</div>
        </div>
    </div>
</x-app-layout>