<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Employés</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gestion des employés et génération de leurs comptes</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('employes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 transition shadow-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvel employé
                </a>
                <a href="{{ route('employes.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Corbeille
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-xl border border-green-200 dark:border-green-800">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matricule</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom complet</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Domaine</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Poste</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Compte</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($employes as $emp)
                        @php $p = $emp->personnel; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $emp->matricule }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $p->prenom }} {{ $p->nom }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $p->email }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $emp->domaine->nom }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $emp->site->name }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $emp->poste ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($p->user)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    Compte actif
                                </span>
                                @else
                                <button type="button" class="text-sky-600 dark:text-sky-400 text-sm font-medium hover:underline" onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}')">Générer compte</button>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('employes.show', $emp) }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Voir">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('employes.edit', $emp) }}" class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800/50 transition" title="Modifier" data-confirm-edit>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/50 transition" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">Aucun employé trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-gray-100 dark:border-gray-700">
                {{ $employes->links() }}
            </div>
        </div>
    </div>

    <!-- Modal for Custom Password -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Générer un compte</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Entrez un mot de passe temporaire pour cet employé. Si laissé vide, un mot de passe aléatoire sera généré.</p>

            <form id="passwordForm" method="POST" action="">
                @csrf
                <div class="mb-6">
                    <label for="customPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mot de passe temporaire (optionnel)</label>
                    <input type="password" id="customPassword" name="custom_password" placeholder="Laisser vide pour générer aléatoirement" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePasswordModal()" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition">Générer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(empId, actionUrl) {
            document.getElementById('passwordModal').classList.remove('hidden');
            document.getElementById('passwordForm').action = actionUrl;
            document.getElementById('customPassword').value = '';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePasswordModal();
            }
        });

        document.getElementById('passwordModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closePasswordModal();
            }
        });
    </script>
</x-app-layout>