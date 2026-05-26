<x-app-layout>
<div class="mb-8 ml-4">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Accès & Sécurité</p>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Gestion des Rôles</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les rôles et leurs permissions associées</p>
        </div>
        <a href="{{ route('admin.roles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouveau rôle
        </a>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Stats ── --}}
    @php
        $roleColors = [
            'admin'      => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',    'border' => 'border-blue-200 dark:border-blue-800',    'text' => 'text-blue-700 dark:text-blue-300',    'dot' => 'bg-blue-500'],
            'superviseur'=> ['bg' => 'bg-purple-50 dark:bg-purple-900/20','border' => 'border-purple-200 dark:border-purple-800','text' => 'text-purple-700 dark:text-purple-300','dot' => 'bg-purple-500'],
            'employe'    => ['bg' => 'bg-amber-50 dark:bg-amber-900/20',  'border' => 'border-amber-200 dark:border-amber-800',  'text' => 'text-amber-700 dark:text-amber-300',  'dot' => 'bg-amber-500'],
            'etudiant'   => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20','border' => 'border-emerald-200 dark:border-emerald-800','text' => 'text-emerald-700 dark:text-emerald-300','dot' => 'bg-emerald-500'],
        ];
        $defaultColor = ['bg' => 'bg-gray-50 dark:bg-gray-800','border' => 'border-gray-200 dark:border-gray-700','text' => 'text-gray-700 dark:text-gray-300','dot' => 'bg-gray-400'];
        $mainRoles    = ['admin', 'superviseur', 'employe', 'etudiant'];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        <div class="col-span-2 sm:col-span-1 bg-indigo-600 rounded-2xl p-4 text-white shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-200 mb-1">Total rôles</p>
            <p class="text-3xl font-bold">{{ $roles->total() }}</p>
            <p class="text-xs text-indigo-200 mt-1">dans le système</p>
        </div>
        @foreach($mainRoles as $rName)
        @php
            $r     = $roles->firstWhere('name', $rName);
            $count = $r ? ($r->users_count ?? 0) : 0;
            $c     = $roleColors[$rName] ?? $defaultColor;
        @endphp
        <div class="rounded-2xl border {{ $c['border'] }} {{ $c['bg'] }} p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-semibold uppercase tracking-wider {{ $c['text'] }}">{{ ucfirst($rName) }}</p>
                <span class="w-2 h-2 rounded-full {{ $c['dot'] }}"></span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">utilisateur{{ $count > 1 ? 's' : '' }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Tableau ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-56">Rôle</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Permissions</th>
                        {{-- Largeur fixe pour éviter le décalage des avatars --}}
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-52">Utilisateurs</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 w-28">Créé le</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-400 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($roles as $role)
                    @php
                        $c          = $roleColors[$role->name] ?? $defaultColor;
                        $initials   = mb_strtoupper(mb_substr($role->name, 0, 2));
                        $permCount  = $role->permissions->count();
                        $usersCount = $role->users_count ?? 0;
                        $permGroups = $role->permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        {{-- Rôle --}}
                        <td class="px-5 py-4 w-56">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-bold {{ $c['bg'] }} {{ $c['text'] }} border {{ $c['border'] }}">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $role->name }}</p>
                                    <p class="text-xs truncate
                                        @if($role->name === 'admin') text-blue-500
                                        @elseif($role->name === 'superviseur') text-purple-500
                                        @elseif($role->name === 'employe') text-amber-500
                                        @elseif($role->name === 'etudiant') text-emerald-500
                                        @elseif($role->name === 'super_admin') text-red-500
                                        @else text-gray-400
                                        @endif">
                                        @if($role->name === 'admin') Administrateur système
                                        @elseif($role->name === 'superviseur') Responsable de suivi
                                        @elseif($role->name === 'employe') Personnel employé
                                        @elseif($role->name === 'etudiant') Stagiaire / Étudiant
                                        @elseif($role->name === 'super_admin') Accès complet
                                        @else Rôle personnalisé
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Permissions --}}
                        <td class="px-5 py-4">
                            {{-- Compteur + points --}}
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="flex -space-x-1">
                                    @foreach($role->permissions->take(5) as $perm)
                                    <div class="w-4 h-4 rounded-full border-2 border-white dark:border-gray-800 {{ $c['bg'] }} flex items-center justify-center" title="{{ $perm->name }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $c['dot'] }}"></span>
                                    </div>
                                    @endforeach
                                </div>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $permCount }} permission{{ $permCount > 1 ? 's' : '' }}
                                </span>
                            </div>
                            {{-- Groupes --}}
                            @if($permCount > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach($permGroups->keys()->take(4) as $group)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs {{ $c['bg'] }} {{ $c['text'] }}">{{ $group }}</span>
                                @endforeach
                                @if($permGroups->count() > 4)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">+{{ $permGroups->count() - 4 }}</span>
                                @endif
                            </div>
                            @endif
                        </td>

                        {{-- Utilisateurs — largeur fixe, structure rigide --}}
                        <td class="px-5 py-4 w-52">
                            <div class="flex items-center gap-3">
                                {{-- Avatars : bloc de largeur fixe pour que les avatars ne "bougent" pas --}}
                                <div class="flex shrink-0 -space-x-2" style="min-width: {{ min($role->users->count(), 4) * 20 + 8 }}px">
                                    @foreach($role->users->take(4) as $u)
                                    <div class="w-7 h-7 rounded-full text-black border-2  dark:border-white bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center dark:text-white text-xs font-semibold shrink-0"
                                         title="{{ $u->name }}">
                                        {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if($usersCount === 0)
                                    <div class="w-7 h-7 rounded-full border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center shrink-0">
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                    </div>
                                    @endif
                                </div>
                                {{-- Compteur texte : toujours aligné à gauche des avatars --}}
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $usersCount }} utilisateur{{ $usersCount > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="px-5 py-4 w-28 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $role->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4 w-24 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ encrypted_route('admin.roles.edit', $role) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                @if(!in_array($role->name, ['super_admin', 'admin']))
                                <form action="{{ encrypted_route('admin.roles.destroy', $role) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800/50 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @else
                                {{-- Rôle système protégé — cadenas --}}
                                <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800/60" title="Rôle système protégé">
                                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucun rôle trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $roles->links() }}
        </div>
    </div>

</div>
</x-app-layout>