<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Personnels</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Liste des personnels, étudiants et employés enregistrés dans le système.</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('personnels.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau personnel
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-xl">{{ session('error') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Identité</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Infos</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($personnels as $personnel)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">#{{ $personnel->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $personnel->prenom }} {{ $personnel->nom }}</div>
                                <div class="text-gray-500 text-sm">{{ $personnel->adresse ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $personnel->type_label === 'Employé' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $personnel->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div>{{ $personnel->email }}</div>
                                <div class="text-gray-500">{{ $personnel->telephone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($personnel->personnable)
                                @if($personnel->personnable_type === App\Models\Etudiant::class)
                                <div><span class="font-medium">École :</span> {{ $personnel->personnable->ecole ?? '-' }}</div>
                                @elseif($personnel->personnable_type === App\Models\Employe::class)
                                <div><span class="font-medium">Poste :</span> {{ $personnel->personnable->poste ?? '-' }}</div>
                                <div class="text-gray-500">Matricule : {{ $personnel->personnable->matricule ?? '-' }}</div>
                                @endif
                                @else
                                <div class="text-gray-500">Aucun détail</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if(!$personnel->user)
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700" onclick="openPasswordModal({{ $personnel->id }}, '{{ route('personnels.generate-account', $personnel) }}')">Générer compte</button>
                                @else
                                <span class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-lg">Compte actif</span>
                                @endif
                                <a href="{{ route('personnels.show', $personnel) }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200" title="Voir">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('personnels.edit', $personnel) }}" class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce personnel ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">Aucun personnel trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-gray-100 dark:border-gray-700">{{ $personnels->links() }}</div>
        </div>
    </div>

    <!-- Modal for Custom Password -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Générer un compte</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Entrez un mot de passe temporaire pour ce personnel. Si laissé vide, un mot de passe aléatoire sera généré.</p>
            
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
        function openPasswordModal(personnelId, actionUrl) {
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