<x-app-layout>
    <!-- En-tête -->
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Utilisateurs</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez les utilisateurs et leurs permissions</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition shadow-lg shadow-green-600/20 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Nouvel Utilisateur
            </a>
        </div>

        {{-- Messages flash --}}
        @if (session('generated_account'))
         <div class="max-w-3xl rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 space-y-2 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-200">
            <p class="font-semibold">Compte créé avec mot de passe temporaire</p>
            <p class="text-sm">Le mot de passe a été défini par l'admin. Un mail unique d'activation a été préparé.</p>
            <div class="grid gap-2 text-sm sm:grid-cols-3">
                <div><span class="font-medium">Nom:</span> {{ session('generated_account.name') }}</div>
                <div><span class="font-medium">Email:</span> {{ session('generated_account.email') }}</div>
                <div><span class="font-medium">Mot de passe:</span> {{ session('generated_account.password') }}</div>
            </div>
            <p class="text-sm">
                {{ session('generated_account.account_email_sent') ? 'Mail unique d\'activation envoyé automatiquement.' : 'Le mail n\'a pas pu être envoyé.' }}
            </p>
        </div>
        @endif

        @if (session('updated_account'))
        <div class="mt-4 max-w-3xl rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-blue-800 text-sm dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200">
            @if (session('updated_account.temporary_password_reset'))
                Le compte <span class="font-semibold">{{ session('updated_account.email') }}</span> a reçu un nouveau mot de passe temporaire.
                {{ session('updated_account.account_email_sent') ? 'Le mail d\'onboarding a été renvoyé.' : 'Le mail n\'a pas pu être envoyé.' }}
            @else
                L'email du compte a été mis à jour vers <span class="font-semibold">{{ session('updated_account.email') }}</span>.
                {{ session('updated_account.verification_email_sent') ? 'Un nouveau mail de vérification a été envoyé.' : 'Le mail n\'a pas pu être envoyé.' }}
            @endif
        </div>
        @endif
    </div>

    <!-- Formulaire de filtres -->
    <div class="mb-6 ml-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <!-- Recherche -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou email..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500">
                </div>

                <!-- Statut du compte -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Statut</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <!-- Vérification email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Email vérifié</label>
                    <select name="verified" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="">Tous</option>
                        <option value="verified" {{ request('verified') == 'verified' ? 'selected' : '' }}>Vérifié</option>
                        <option value="not_verified" {{ request('verified') == 'not_verified' ? 'selected' : '' }}>Non vérifié</option>
                    </select>
                </div>

                <!-- Type de mot de passe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Mot de passe</label>
                    <select name="password_status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="">Tous</option>
                        <option value="temporary" {{ request('password_status') == 'temporary' ? 'selected' : '' }}>Temporaire</option>
                        <option value="permanent" {{ request('password_status') == 'permanent' ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                </div>

                <!-- Rôle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Rôle</label>
                    <select name="role" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="">Tous</option>
                        @foreach($roles as $roleName)
                            <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>{{ ucfirst($roleName) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition shadow-sm">Appliquer</button>
                    <a href="{{ route('admin.users.index') }}" class="flex-1 inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau moderne -->
    <div class="bg-white ml-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">N°</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rôles</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Créé le</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div> 
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status === 'actif')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs font-medium">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                Inactif
                            </span>
                            @endif
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $user->hasVerifiedEmail() ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                    {{ $user->hasVerifiedEmail() ? 'Email vérifié' : 'Email à vérifier' }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $user->must_change_password ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' }}">
                                    {{ $user->must_change_password ? 'Mot de passe temporaire' : 'Mot de passe personnalisé' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">
                                    {{ $role->name }}
                                </span>
                                @empty
                                <span class="text-gray-400">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @forelse($user->getAllPermissions()->take(3) as $permission)
                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded text-xs" title="{{ $permission->name }}">
                                    {{ \Illuminate\Support\Str::limit($permission->name, 12) }}
                                </span>
                                @empty
                                <span class="text-gray-400">-</span>
                                @endforelse
                                @if($user->getAllPermissions()->count() > 3)
                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded text-xs">
                                    +{{ $user->getAllPermissions()->count() - 3 }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Bouton Activer / Désactiver -->
                                <button type="button"
                                    data-toggle-url="{{ encrypted_route('admin.users.toggle-status', $user) }}"
                                    data-user-name="{{ $user->name }}"
                                    data-current-status="{{ $user->status }}"
                                    class="open-toggle-modal p-2 {{ $user->status === 'actif' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 hover:bg-orange-200 dark:hover:bg-orange-900/50' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-900/50' }} rounded-lg transition"
                                    title="{{ $user->status === 'actif' ? 'Désactiver le compte' : 'Réactiver le compte' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($user->status === 'actif')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V7a4 4 0 00-8 0v3m8 0H5m0 0v6a2 2 0 002 2h8a2 2 0 002-2v-6H5z" />
                                        @endif
                                    </svg>
                                </button>

                                <!-- Bouton Modifier -->
                                <a href="{{ encrypted_route('admin.users.edit', $user) }}"
                                    class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                <!-- Bouton Supprimer -->
                                <form action="{{ encrypted_route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun utilisateur trouvé</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Commencez par créer un nouvel utilisateur</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 border-t border-gray-100 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal de confirmation pour toggle status -->
    <div id="toggleStatusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Confirmation</h2>
            </div>
            <p id="modalMessage" class="text-gray-600 dark:text-gray-300 mb-6"></p>
            <div class="flex gap-3">
                <button type="button" id="modalCancelBtn" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Annuler</button>
                <button type="button" id="modalConfirmBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition">Confirmer</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('toggleStatusModal');
            const modalMessage = document.getElementById('modalMessage');
            const cancelBtn = document.getElementById('modalCancelBtn');
            const confirmBtn = document.getElementById('modalConfirmBtn');
            let currentFormAction = null;

            document.querySelectorAll('.open-toggle-modal').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const userName = this.getAttribute('data-user-name');
                    const currentStatus = this.getAttribute('data-current-status');
                    const actionUrl = this.getAttribute('data-toggle-url');

                    const newStatusText = currentStatus === 'actif' ? 'désactiver' : 'réactiver';
                    modalMessage.innerText = `Voulez-vous vraiment ${newStatusText} le compte de ${userName} ?`;
                    currentFormAction = actionUrl;
                    modal.classList.remove('hidden');
                });
            });

            cancelBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
                currentFormAction = null;
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    currentFormAction = null;
                }
            });

            confirmBtn.addEventListener('click', function () {
                if (currentFormAction) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = currentFormAction;
                    form.style.display = 'none';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>