<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Jours Fériés</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez les jours fériés et activez/désactivez le pointage</p>
            </div>
            @can('holidays.create')
            <a href="{{ route('admin.holidays.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl hover:from-purple-600 hover:to-indigo-700 transition shadow-lg shadow-purple-600/20 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6" />
                </svg>
                Nouveau jour férié
            </a>
            @endcan
        </div>
    </div>

    <div class="bg-white ml-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actif</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notifié</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($holidays as $holiday)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors {{ $holiday->is_active ? 'bg-purple-50/50 dark:bg-purple-900/10' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold">
                                    {{ substr($holiday->label, 0, 1) }}
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $holiday->label }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                            {{ $holiday->description ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @can('holidays.toggle')
                            <form method="POST" action="{{ route('admin.holidays.toggle', $holiday) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 {{ $holiday->is_active ? 'bg-purple-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 {{ $holiday->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </form>
                            @else
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $holiday->is_active ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $holiday->is_active ? 'Oui' : 'Non' }}
                            </span>
                            @endcan
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($holiday->notified)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Notifié
                            </span>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @can('holidays.toggle')
                                @if($holiday->is_active)
                                <button type="button"
                                    onclick="openEmergencyModal({{ $holiday->id }}, '{{ $holiday->label }}', '{{ $holiday->date->format('Y-m-d') }}')"
                                    class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Appel d'urgence">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </button>
                                @endif
                                @endcan
                                @can('holidays.edit')
                                <a href="{{ route('admin.holidays.edit', $holiday) }}"
                                    class="p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                                @can('holidays.toggle')
                                @if($holiday->is_active && !$holiday->notified)
                                <a href="{{ route('admin.holidays.notify', $holiday) }}"
                                    class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition" title="Notifier les employés">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </a>
                                @endif
                                @endcan
                                @can('holidays.delete')
                                <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun jour férié enregistré</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Ajoutez un jour férié pour commencer.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($holidays->hasPages())
        <div class="p-5 border-t border-gray-100 dark:border-gray-700">
            {{ $holidays->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Appel d'Urgence --}}
    <div id="emergencyModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-black/60" onclick="closeEmergencyModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Appel d'urgence</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span id="emergencyHolidayLabel"></span> — <span id="emergencyHolidayDate"></span>
                        </p>
                    </div>
                    <button onclick="closeEmergencyModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="emergencyForm" method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <input type="hidden" name="holiday_id" id="emergencyHolidayId">

                    <div class="p-6 flex-1 overflow-y-auto">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rechercher des employés/étudiants
                            </label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" id="userSearch" placeholder="Rechercher par nom, email..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <div class="mb-4 flex gap-2">
                            <button type="button" onclick="filterUsers('all')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400" data-filter="all">Tous</button>
                            <button type="button" onclick="filterUsers('employe')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400" data-filter="employe">Employés</button>
                            <button type="button" onclick="filterUsers('etudiant')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400" data-filter="etudiant">Étudiants</button>
                        </div>

                        <div id="userList" class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-xl p-2">
                            <div class="text-center text-sm text-gray-400 py-8">Chargez les utilisateurs...</div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Message personnalisé <span class="text-gray-400 text-xs">(optionnel)</span>
                            </label>
                            <textarea name="message" rows="3" placeholder="Ex: Urgence maintenance atelier. Veuillez vous présenter au site principal."
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent transition"></textarea>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            <span id="selectedCount">0</span> personne(s) sélectionnée(s)
                        </p>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="closeEmergencyModal()"
                                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium text-sm">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition shadow-lg shadow-red-600/20 font-medium text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                Envoyer l'appel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let allUsers = [];
        let currentFilter = 'all';

        function openEmergencyModal(holidayId, label, date) {
            document.getElementById('emergencyHolidayId').value = holidayId;
            document.getElementById('emergencyHolidayLabel').textContent = label;
            document.getElementById('emergencyHolidayDate').textContent = new Date(date).toLocaleDateString('fr-FR', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            document.getElementById('emergencyForm').action = `/admin/holidays/${holidayId}/emergency-call`;
            document.getElementById('emergencyModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loadUsers();
        }

        function closeEmergencyModal() {
            document.getElementById('emergencyModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function loadUsers() {
            const list = document.getElementById('userList');
            list.innerHTML = '<div class="text-center text-sm text-gray-400 py-8">Chargement...</div>';
            fetch('{{ route("admin.holidays.users-list") }}')
                .then(r => r.json())
                .then(users => {
                    allUsers = users;
                    renderUsers();
                })
                .catch(() => {
                    list.innerHTML = '<div class="text-center text-sm text-red-500 py-8">Erreur de chargement.</div>';
                });
        }

        function renderUsers() {
            const list = document.getElementById('userList');
            const search = (document.getElementById('userSearch').value || '').toLowerCase();
            const filtered = allUsers.filter(u => {
                if (currentFilter !== 'all' && u.role_name !== currentFilter) return false;
                if (search && !u.name.toLowerCase().includes(search) && !u.email.toLowerCase().includes(search)) return false;
                return true;
            });

            if (filtered.length === 0) {
                list.innerHTML = '<div class="text-center text-sm text-gray-400 py-8">Aucun utilisateur trouvé.</div>';
                return;
            }

            list.innerHTML = filtered.map(u => `
                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                    <input type="checkbox" name="user_ids[]" value="${u.id}" onchange="updateCount()"
                        class="w-4 h-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${u.name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${u.email}</p>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full ${u.role_name === 'etudiant' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'}">${u.role}</span>
                </label>
            `).join('');
        }

        function updateCount() {
            const checked = document.querySelectorAll('#userList input[type="checkbox"]:checked').length;
            document.getElementById('selectedCount').textContent = checked;
        }

        function filterUsers(role) {
            currentFilter = role;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-purple-100', 'text-purple-700', 'dark:bg-purple-900/30', 'dark:text-purple-400');
                btn.classList.add('bg-gray-100', 'text-gray-600', 'dark:bg-gray-700', 'dark:text-gray-400');
            });
            const active = document.querySelector(`.filter-btn[data-filter="${role}"]`);
            if (active) {
                active.classList.remove('bg-gray-100', 'text-gray-600', 'dark:bg-gray-700', 'dark:text-gray-400');
                active.classList.add('bg-purple-100', 'text-purple-700', 'dark:bg-purple-900/30', 'dark:text-purple-400');
            }
            renderUsers();
        }

        document.getElementById('userSearch')?.addEventListener('input', renderUsers);
    </script>
    @endpush
</x-app-layout>
