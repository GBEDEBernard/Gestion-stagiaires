<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Personnels</h1>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Liste des personnels, étudiants et employés enregistrés dans le système.</p>
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
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-xl">{{ session('error') }}</div>
        @endif

        <form id="personnelsFilterForm" method="GET" action="{{ route('personnels.index') }}" class="mb-6 p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="grid gap-4 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Recherche</label>
                    <input id="personnelsSearch" type="search" name="search" value="{{ request('search') }}" placeholder="Nom, email, téléphone..." class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-sky-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type</label>
                    <select name="type" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-sky-500">
                        <option value="all" {{ request('type') === 'all' ? ' selected' : '' }}>Tous</option>
                        <option value="etudiant" {{ request('type') === 'etudiant' ? ' selected' : '' }}>Étudiant</option>
                        <option value="employe" {{ request('type') === 'employe' ? ' selected' : '' }}>Employé</option>
                        <option value="inconnu" {{ request('type') === 'inconnu' ? ' selected' : '' }}>Inconnu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Statut de compte</label>
                    <select name="account" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-sky-500">
                        <option value="all" {{ request('account') === 'all' ? ' selected' : '' }}>Tous</option>
                        <option value="with" {{ request('account') === 'with' ? ' selected' : '' }}>Avec compte</option>
                        <option value="without" {{ request('account') === 'without' ? ' selected' : '' }}>Sans compte</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">École</label>
                    <select name="school" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-sky-500">
                        <option value="">Toutes</option>
                        @foreach($schools as $school)
                        <option value="{{ $school }}" {{ request('school') === $school ? ' selected' : '' }}>{{ $school }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">Filtrer les personnels affichés</div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition">Appliquer</button>
                    <a href="{{ route('personnels.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Réinitialiser</a>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('personnelsFilterForm');
                if (!form) return;

                // submit when any select changes
                form.querySelectorAll('select').forEach(function(sel) {
                    sel.addEventListener('change', function() {
                        form.submit();
                    });
                });

                // debounce helper
                function debounce(fn, delay) {
                    var t;
                    return function() {
                        var args = arguments;
                        clearTimeout(t);
                        t = setTimeout(function() {
                            fn.apply(null, args);
                        }, delay);
                    }
                }

                var search = document.getElementById('personnelsSearch');
                if (search) {
                    // submit on Enter
                    search.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            form.submit();
                        }
                    });
                    // debounce submit on input
                    search.addEventListener('input', debounce(function() {
                        form.submit();
                    }, 600));
                }
            });
        </script>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">N°</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Identité</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Infos</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($personnels as $personnel)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $personnel->prenom }} {{ $personnel->nom }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $personnel->adresse ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($personnel->type_label === 'Employé') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                        @elseif($personnel->type_label === 'Étudiant') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                    {{ $personnel->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="text-gray-900 dark:text-white">{{ $personnel->email }}</div>
                                <div class="text-gray-500 dark:text-gray-400">{{ $personnel->telephone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($personnel->personnable)
                                @if($personnel->personnable_type === App\Models\Etudiant::class)
                                <div class="text-gray-900 dark:text-white"><span class="font-medium">École :</span> {{ $personnel->personnable->ecole ?? '-' }}</div>
                                @elseif($personnel->personnable_type === App\Models\Employe::class)
                                <div class="text-gray-900 dark:text-white"><span class="font-medium">Poste :</span> {{ $personnel->personnable->poste ?? '-' }}</div>
                                <div class="text-gray-500 dark:text-gray-400">Matricule : {{ $personnel->personnable->matricule ?? '-' }}</div>
                                @endif
                                @else
                                <div class="text-gray-500 dark:text-gray-400">Aucun détail</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                @if(!$personnel->user)
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-white bg-gradient-to-r from-sky-500 to-blue-600 rounded-lg hover:from-sky-600 hover:to-blue-700 shadow-sm transition" onclick="openPasswordModal({{ $personnel->id }}, '{{ route('personnels.generate-account', $personnel) }}')">
                                    Générer compte
                                </button>
                                @else
                                <form action="{{ route('personnels.generate-account', $personnel) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900/60 transition">
                                        Renvoyer email
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('personnels.show', $personnel) }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Voir">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('personnels.edit', $personnel) }}" class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800/50 transition" title="Modifier" data-confirm-edit>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" class="inline" data-confirm-delete>
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
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">Aucun personnel trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-gray-100 dark:border-gray-700">
                {{ $personnels->links() }}
            </div>
        </div>
    </div>

    <!-- Modal pour mot de passe personnalisé -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Générer un compte</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Entrez un mot de passe temporaire pour ce personnel. Si laissé vide, un mot de passe aléatoire sera généré.</p>

            <form id="passwordForm" method="POST" action="">
                @csrf
                <div class="mb-6">
                    <label for="customPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Mot de passe temporaire (optionnel)</label>
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