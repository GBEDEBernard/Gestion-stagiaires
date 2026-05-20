<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 dark:text-white">
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
                <a href="{{ route('employes.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 rounded-xl hover:bg-gray-200 transition">Corbeille</a>
            </div>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>@endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold">Matricule</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Domaine</th>
                            <th>Site</th>
                            <th>Poste</th>
                            <th>Compte</th>
                            <th class="text-right">Actions</th>
                        </tr>
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
                                <button type="button" class="text-blue-600 text-sm hover:underline" onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}')">Générer compte</button>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('employes.show', $emp) }}" class="p-2 bg-gray-100 rounded-lg">👁️</a>
                                <a href="{{ route('employes.edit', $emp) }}" class="p-2 bg-yellow-100 rounded-lg" title="Modifier" data-confirm-edit>✏️</a>
                                <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-100 rounded-lg">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500">Aucun employé trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t">{{ $employes->links() }}</div>
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
                    <input type="password" id="customPassword" name="custom_password" placeholder="Laisser vide pour générer aléatoirement" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
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

        // Close modal when pressing Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePasswordModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('passwordModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closePasswordModal();
            }
        });
    </script>
</x-app-layout>