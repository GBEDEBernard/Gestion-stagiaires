<x-app-layout>
<div class="mb-8 ml-4">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Accès & Sécurité</p>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Utilisateurs</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les comptes et leurs permissions</p>
        </div>
        <a href="{{ route('admin.users.create', ['default_role' => 'admin']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Nouvel utilisateur
        </a>
    </div>

    {{-- ── Flash ── --}}
    <div id="flash-messages">
        @if(session('success'))
        <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('generated_account'))
        <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-800 dark:text-emerald-200">
            <p class="font-semibold mb-2">✅ Compte créé avec succès</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <div><span class="font-medium">Nom :</span> {{ session('generated_account.name') }}</div>
                <div class="break-all"><span class="font-medium">Email :</span> {{ session('generated_account.email') }}</div>
                <div><span class="font-medium">Mot de passe :</span> {{ session('generated_account.password') }}</div>
            </div>
            <p class="text-xs mt-2 text-emerald-600 dark:text-emerald-400">
                {{ session('generated_account.account_email_sent') ? '📧 Email d\'activation envoyé automatiquement.' : '⚠ Email non envoyé.' }}
            </p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
        @endif
    </div>

 {{-- ── Filtres (recherche AJAX automatique) ── --}}
<form id="filters-form" method="GET" action="{{ route('admin.users.index') }}"
      class="mb-6 p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        {{-- Recherche --}}
        <div class="sm:col-span-3 lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Recherche</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom ou email..."
                       class="search-input w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
        </div>

        {{-- Statut --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Statut</label>
            <select name="status" class="filter-select w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500">
                <option value="">Tous</option>
                <option value="actif"    {{ request('status') == 'actif'    ? 'selected' : '' }}>Actif</option>
                <option value="inactif"  {{ request('status') == 'inactif'  ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>

        {{-- Vérification email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Email</label>
            <select name="verified" class="filter-select w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500">
                <option value="">Tous</option>
                <option value="verified"     {{ request('verified') == 'verified'     ? 'selected' : '' }}>Vérifié</option>
                <option value="not_verified" {{ request('verified') == 'not_verified' ? 'selected' : '' }}>Non vérifié</option>
            </select>
        </div>

        {{-- Rôle --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Rôle</label>
            <select name="role" class="filter-select w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500">
                <option value="">Tous</option>
                @foreach($roles as $roleName)
                    <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>{{ ucfirst($roleName) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-3 flex items-center justify-between">
        <p id="result-count" class="text-xs text-gray-400">{{ $users->total() }} utilisateur(s) trouvé(s)</p>
    </div>
</form>

    {{-- ── Tableau responsive ── --}}
    <div id="users-table-container" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-10">N°</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Utilisateur</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-48">Statut</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-36">Rôle</th>
                        <th class="hidden xl:table-cell px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-48">Permissions</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-24">Créé le</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-400 w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    @php
                        $avatarPath = $user->avatar ?? null;
                        $hasAvatar  = $avatarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath);
                        $colors     = ['from-blue-500 to-cyan-600','from-purple-500 to-pink-600','from-amber-500 to-orange-600','from-emerald-500 to-teal-600','from-rose-500 to-red-600'];
                        $color      = $colors[$user->id % count($colors)];
                        $allPerms   = $user->getAllPermissions();
                        $permCount  = $allPerms->count();
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3.5 text-xs text-gray-400 w-10">{{ $loop->iteration }}</td>
                        <td class="px-1 py-3.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="shrink-0 w-9 h-9 rounded-full overflow-hidden {{ $hasAvatar ? '' : "bg-gradient-to-br $color" }} flex items-center justify-center text-white text-sm font-bold">
                                    @if($hasAvatar)
                                        <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate max-w-[160px] lg:max-w-xs">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[160px] lg:max-w-xs">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 w-48">
                            <div class="flex flex-col gap-1">
                                @if($user->status === 'actif')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 w-fit">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Actif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 w-fit">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>Inactif
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium w-fit {{ $user->hasVerifiedEmail() ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                    {{ $user->hasVerifiedEmail() ? '✓ Email vérifié' : '⚠ À vérifier' }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium w-fit {{ $user->must_change_password ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    {{ $user->must_change_password ? 'Temporaire' : 'Permanent' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 w-36">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ $role->name }}</span>
                                @empty <span class="text-xs text-gray-400">—</span> @endforelse
                            </div>
                        </td>
                        <td class="hidden xl:table-cell px-4 py-3.5 w-48">
                            <div class="flex flex-wrap gap-1">
                                @foreach($allPerms->take(2) as $permission)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 max-w-[90px] truncate" title="{{ $permission->name }}">{{ $permission->name }}</span>
                                @endforeach
                                @if($permCount > 2)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">+{{ $permCount - 2 }}</span>
                                @elseif($permCount === 0)
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 w-24 text-xs text-gray-400 whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3.5 w-28 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <button type="button"
                                        data-toggle-url="{{ encrypted_route('admin.users.toggle-status', $user) }}"
                                        data-user-name="{{ $user->name }}"
                                        data-current-status="{{ $user->status }}"
                                        class="open-toggle-modal inline-flex items-center justify-center w-8 h-8 rounded-lg transition {{ $user->status === 'actif' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-200' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200' }}"
                                        title="{{ $user->status === 'actif' ? 'Désactiver' : 'Réactiver' }}">
                                    @if($user->status === 'actif')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @endif
                                </button>

                                <a href="{{ encrypted_route('admin.users.show', $user) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <a href="{{ encrypted_route('admin.users.edit', $user) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <form action="{{ encrypted_route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucun utilisateur trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>
</div>

{{-- ── Modal confirmation toggle statut ── --}}
<div id="toggleStatusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Confirmation</h2>
        </div>
        <div class="px-6 py-5">
            <p id="modalMessage" class="text-sm text-gray-600 dark:text-gray-300 mb-5"></p>
            <div class="flex gap-3">
                <button type="button" id="modalCancelBtn"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">Annuler</button>
                <button type="button" id="modalConfirmBtn"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal toggle status
    const modal = document.getElementById('toggleStatusModal');
    const message = document.getElementById('modalMessage');
    const cancelBtn = document.getElementById('modalCancelBtn');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    let actionUrl = null;

    function attachToggleEvents() {
        document.querySelectorAll('.open-toggle-modal').forEach(btn => {
            btn.removeEventListener('click', toggleHandler);
            btn.addEventListener('click', toggleHandler);
        });
    }

    function toggleHandler(e) {
        const name = this.dataset.userName;
        const status = this.dataset.currentStatus;
        actionUrl = this.dataset.toggleUrl;
        message.textContent = `Voulez-vous ${status === 'actif' ? 'désactiver' : 'réactiver'} le compte de ${name} ?`;
        modal.classList.remove('hidden');
    }

    [cancelBtn, modal].forEach(el => {
        el.addEventListener('click', function (e) {
            if (e.target === el) { modal.classList.add('hidden'); actionUrl = null; }
        });
    });

    confirmBtn.addEventListener('click', function () {
        if (!actionUrl) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH">`;
        document.body.appendChild(form);
        form.submit();
    });

    // AJAX recherche dynamique
    const filtersForm = document.getElementById('filters-form');
    const searchInput = filtersForm.querySelector('.search-input');
    const filterSelects = filtersForm.querySelectorAll('.filter-select');
    const container = document.getElementById('users-table-container');
    let debounceTimer;

    function updateUsersTable() {
        const url = new URL(filtersForm.action);
        const formData = new FormData(filtersForm);
        for (let [key, value] of formData.entries()) {
            if (value && value !== '') url.searchParams.set(key, value);
            else url.searchParams.delete(key);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('users-table-container');
                if (newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                    const newCount = doc.getElementById('result-count');
                    const oldCount = document.getElementById('result-count');
                    if (newCount && oldCount) oldCount.innerText = newCount.innerText;
                } else {
                    const newTable = doc.querySelector('#users-table-container');
                    if (newTable) container.innerHTML = newTable.innerHTML;
                }
                attachToggleEvents();
            })
            .catch(err => console.error('Erreur AJAX:', err));
    }

    // Événements
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => updateUsersTable(), 400);
    });
    filterSelects.forEach(select => select.addEventListener('change', () => updateUsersTable()));

    // Initialisation
    attachToggleEvents();
});
</script>
</x-app-layout>