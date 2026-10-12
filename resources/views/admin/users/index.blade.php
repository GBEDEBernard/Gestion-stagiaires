<x-app-layout>
<div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Accès & Sécurité</p>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">Utilisateurs</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-[#94a3b8] mt-0.5">Gérez les comptes d'accès, statuts et rôles de la plateforme.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.create', ['default_role' => 'admin']) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition shadow-xs shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Nouvel utilisateur</span>
            </a>
        </div>
    </div>

    {{-- ── Flash ── --}}
    <div id="flash-messages">
        @if(session('success'))
        <div class="p-3 flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs font-semibold shadow-xs">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('generated_account'))
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-200 shadow-xs">
            <p class="font-bold mb-1.5">Compte créé avec succès</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <div><span class="font-medium text-slate-500">Nom :</span> {{ session('generated_account.name') }}</div>
                <div class="break-all"><span class="font-medium text-slate-500">Email :</span> {{ session('generated_account.email') }}</div>
                <div><span class="font-medium text-slate-500">Mot de passe :</span> {{ session('generated_account.password') }}</div>
            </div>
            <p class="text-[11px] mt-2 text-emerald-600 dark:text-emerald-400">
                {{ session('generated_account.account_email_sent') ? 'Email d\'activation envoyé automatiquement.' : 'Email non envoyé.' }}
            </p>
        </div>
        @endif

        @if(session('error'))
        <div class="p-3 flex items-center gap-2 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-semibold shadow-xs">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
        @endif
    </div>

    {{-- ── Filtres (recherche AJAX automatique) ── --}}
    <form id="filters-form" method="GET" action="{{ route('admin.users.index') }}"
          class="p-3.5 bg-[#fffefa] dark:bg-[#161a22] rounded-xl border border-slate-200/80 dark:border-[#222630] shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
            {{-- Recherche --}}
            <div class="sm:col-span-3 lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-[#94a3b8] mb-1">Recherche</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom ou email…"
                           class="search-input w-full pl-8.5 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 placeholder:text-slate-400">
                </div>
            </div>

            {{-- Statut --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-[#94a3b8] mb-1">Statut</label>
                <select name="status" class="filter-select w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] focus:ring-1 focus:ring-emerald-500">
                    <option value="">Tous les statuts</option>
                    <option value="actif"    {{ request('status') == 'actif'    ? 'selected' : '' }}>Actif</option>
                    <option value="inactif"  {{ request('status') == 'inactif'  ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>

            {{-- Vérification email --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-[#94a3b8] mb-1">Email</label>
                <select name="verified" class="filter-select w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] focus:ring-1 focus:ring-emerald-500">
                    <option value="">Tous</option>
                    <option value="verified"     {{ request('verified') == 'verified'     ? 'selected' : '' }}>Vérifié</option>
                    <option value="not_verified" {{ request('verified') == 'not_verified' ? 'selected' : '' }}>Non vérifié</option>
                </select>
            </div>

            {{-- Rôle --}}
            <div class="sm:col-span-3 lg:col-span-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-100 dark:border-[#222630]">
                <div class="flex items-center gap-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-[#94a3b8]">Rôle :</label>
                    <select name="role" class="filter-select px-2.5 py-1 text-xs rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc]">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $roleName)
                            <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>{{ ucfirst($roleName) }}</option>
                        @endforeach
                    </select>
                </div>
                <p id="result-count" class="text-[11px] text-slate-400">{{ $users->total() }} utilisateur(s) trouvé(s)</p>
            </div>
        </div>
    </form>

    {{-- ── Tableau responsive ── --}}
    <div id="users-table-container" class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
        
        {{-- Version Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/70 dark:bg-[#131519] border-b border-slate-200 dark:border-[#222630] text-slate-500 dark:text-[#94a3b8] uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 w-10">N°</th>
                        <th class="px-4 py-3">Utilisateur</th>
                        <th class="px-4 py-3 w-48">Statut</th>
                        <th class="px-4 py-3 w-36">Rôle</th>
                        <th class="hidden xl:table-cell px-4 py-3 w-48">Permissions</th>
                        <th class="px-4 py-3 w-24">Créé le</th>
                        <th class="px-4 py-3 text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#222630]">
                    @forelse($users as $user)
                    @php
                        $avatarPath = $user->avatar ?? null;
                        $hasAvatar  = $avatarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath);
                        $colors     = ['from-blue-500 to-cyan-600','from-purple-500 to-pink-600','from-amber-500 to-orange-600','from-emerald-500 to-teal-600','from-rose-500 to-red-600'];
                        $color      = $colors[$user->id % count($colors)];
                        $allPerms   = $user->getAllPermissions();
                        $permCount  = $allPerms->count();
                    @endphp
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/40 transition">
                        <td class="px-4 py-3 text-slate-400 w-10">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="shrink-0 w-8 h-8 rounded-full overflow-hidden {{ $hasAvatar ? '' : "bg-gradient-to-br $color" }} flex items-center justify-center text-white text-xs font-bold border border-white dark:border-[#222630]">
                                    @if($hasAvatar)
                                        <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate max-w-[160px] lg:max-w-xs">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate max-w-[160px] lg:max-w-xs">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 w-48">
                            <div class="flex flex-col gap-1">
                                @if($user->status === 'actif')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 w-fit">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Actif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 w-fit">
                                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>Inactif
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium w-fit {{ $user->hasVerifiedEmail() ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300' }}">
                                    {{ $user->hasVerifiedEmail() ? '✓ Vérifié' : '⚠ Non vérifié' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 w-36">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900/40">{{ $role->name }}</span>
                                @empty <span class="text-slate-400">—</span> @endforelse
                            </div>
                        </td>
                        <td class="hidden xl:table-cell px-4 py-3 w-48">
                            <div class="flex flex-wrap gap-1">
                                @foreach($allPerms->take(2) as $permission)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-[#131519] text-slate-500 dark:text-[#94a3b8] max-w-[90px] truncate" title="{{ $permission->name }}">{{ $permission->name }}</span>
                                @endforeach
                                @if($permCount > 2)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-[#131519] text-slate-500 dark:text-[#94a3b8]">+{{ $permCount - 2 }}</span>
                                @elseif($permCount === 0)
                                <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 w-24 text-[11px] text-slate-400 whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 w-28 text-right">
                            <div class="inline-flex items-center justify-end gap-1">
                                <button type="button"
                                        data-toggle-url="{{ encrypted_route('admin.users.toggle-status', $user) }}"
                                        data-user-name="{{ $user->name }}"
                                        data-current-status="{{ $user->status }}"
                                        class="open-toggle-modal inline-flex items-center justify-center w-7 h-7 rounded-lg transition border {{ $user->status === 'actif' ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/40 hover:bg-amber-100' : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/40 hover:bg-emerald-100' }}"
                                        title="{{ $user->status === 'actif' ? 'Désactiver' : 'Réactiver' }}">
                                    @if($user->status === 'actif')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @endif
                                </button>

                                <a href="{{ encrypted_route('admin.users.show', $user) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] text-slate-600 dark:text-[#94a3b8] hover:bg-slate-200 transition" title="Voir">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <a href="{{ encrypted_route('admin.users.edit', $user) }}"
                                   class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/40 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition" title="Modifier">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <form action="{{ encrypted_route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition" title="Supprimer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-xs text-slate-400">
                            Aucun utilisateur trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Version Mobile 375px --}}
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-[#222630]">
            @forelse($users as $user)
            @php
                $avatarPath = $user->avatar ?? null;
                $hasAvatar  = $avatarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath);
                $colors     = ['from-blue-500 to-cyan-600','from-purple-500 to-pink-600','from-amber-500 to-orange-600','from-emerald-500 to-teal-600','from-rose-500 to-red-600'];
                $color      = $colors[$user->id % count($colors)];
            @endphp
            <div class="p-3 space-y-2 text-xs">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="shrink-0 w-8 h-8 rounded-full overflow-hidden {{ $hasAvatar ? '' : "bg-gradient-to-br $color" }} flex items-center justify-center text-white text-xs font-bold">
                            @if($hasAvatar)
                                <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">{{ $user->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ encrypted_route('admin.users.edit', $user) }}"
                           class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ encrypted_route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm-delete>
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-100 dark:border-[#222630]">
                    <div class="flex items-center gap-1.5">
                        @if($user->status === 'actif')
                        <span class="text-emerald-600 font-bold">Actif</span>
                        @else
                        <span class="text-rose-600 font-bold">Inactif</span>
                        @endif
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500">{{ $user->roles->first()?->name ?? 'Sans rôle' }}</span>
                    </div>
                    <span class="text-slate-400">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-xs text-slate-400">
                Aucun utilisateur trouvé.
            </div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-slate-200 dark:border-[#222630] bg-slate-50/50 dark:bg-[#131519]">
            {{ $users->links() }}
        </div>
    </div>
</div>

{{-- ── Modal confirmation toggle statut ── --}}
<div id="toggleStatusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-[#fffefa] dark:bg-[#161a22] border border-slate-200 dark:border-[#222630] rounded-2xl shadow-2xl w-full max-w-sm p-4">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">Confirmation du statut</h2>
        </div>
        <p id="modalMessage" class="text-xs text-slate-600 dark:text-[#94a3b8] mb-4"></p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modalCancelBtn"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-[#f8fafc] bg-slate-100 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] rounded-lg hover:bg-slate-200 transition">Annuler</button>
            <button type="button" id="modalConfirmBtn"
                    class="px-3 py-1.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition shadow-xs">Confirmer</button>
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

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => updateUsersTable(), 400);
    });
    filterSelects.forEach(select => select.addEventListener('change', () => updateUsersTable()));

    attachToggleEvents();
});
</script>
</x-app-layout>