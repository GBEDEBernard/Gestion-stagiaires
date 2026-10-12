<x-app-layout>
@php $labelService = app(\App\Services\PermissionLabelService::class); @endphp
<div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Accès & Sécurité</p>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">Gestion des Rôles</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-[#94a3b8] mt-0.5">Gérez les rôles du personnel et leurs permissions associées.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shadow-xs shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Nouveau rôle</span>
            </a>
        </div>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="p-3 flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs font-semibold shadow-xs">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-3 flex items-center gap-2 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-semibold shadow-xs">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Stats ── --}}
    @php
        $palette = [
            ['bg' => 'bg-blue-50/60 dark:bg-blue-950/30',    'border' => 'border-blue-200 dark:border-blue-900/50',    'text' => 'text-blue-700 dark:text-blue-300',    'dot' => 'bg-blue-500'],
            ['bg' => 'bg-purple-50/60 dark:bg-purple-950/30','border' => 'border-purple-200 dark:border-purple-900/50','text' => 'text-purple-700 dark:text-purple-300','dot' => 'bg-purple-500'],
            ['bg' => 'bg-amber-50/60 dark:bg-amber-950/30',  'border' => 'border-amber-200 dark:border-amber-900/50',  'text' => 'text-amber-700 dark:text-amber-300',  'dot' => 'bg-amber-500'],
            ['bg' => 'bg-emerald-50/60 dark:bg-emerald-950/30','border' => 'border-emerald-200 dark:border-emerald-900/50','text' => 'text-emerald-700 dark:text-emerald-300','dot' => 'bg-emerald-500'],
            ['bg' => 'bg-rose-50/60 dark:bg-rose-950/30',    'border' => 'border-rose-200 dark:border-rose-900/50',    'text' => 'text-rose-700 dark:text-rose-300',    'dot' => 'bg-rose-500'],
            ['bg' => 'bg-cyan-50/60 dark:bg-cyan-950/30',    'border' => 'border-cyan-200 dark:border-cyan-900/50',    'text' => 'text-cyan-700 dark:text-cyan-300',    'dot' => 'bg-cyan-500'],
            ['bg' => 'bg-orange-50/60 dark:bg-orange-950/30','border' => 'border-orange-200 dark:border-orange-900/50','text' => 'text-orange-700 dark:text-orange-300','dot' => 'bg-orange-500'],
            ['bg' => 'bg-teal-50/60 dark:bg-teal-950/30',    'border' => 'border-teal-200 dark:border-teal-900/50',    'text' => 'text-teal-700 dark:text-teal-300',    'dot' => 'bg-teal-500'],
        ];
        $roleColorMap = [];
        foreach ($allRoles as $i => $r) {
            $roleColorMap[$r->name] = $palette[$i % count($palette)];
        }
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="col-span-2 sm:col-span-1 rounded-xl bg-indigo-600 p-3.5 text-white shadow-xs">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-200 mb-0.5">Total rôles</p>
            <p class="text-2xl font-bold">{{ $allRoles->count() }}</p>
            <p class="text-[11px] text-indigo-200 mt-0.5">dans le système</p>
        </div>
        @foreach($allRoles as $r)
        @php $c = $roleColorMap[$r->name]; @endphp
        <div class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} p-3.5 shadow-xs">
            <div class="flex items-center justify-between mb-0.5">
                <p class="text-[11px] font-semibold uppercase tracking-wider {{ $c['text'] }} truncate">{{ $r->name }}</p>
                <span class="w-1.5 h-1.5 rounded-full {{ $c['dot'] }}"></span>
            </div>
            <p class="text-xl font-bold text-slate-900 dark:text-[#f8fafc]">{{ $r->users_count ?? 0 }}</p>
            <p class="text-[11px] text-slate-500 dark:text-[#94a3b8] mt-0.5">utilisateur{{ ($r->users_count ?? 0) > 1 ? 's' : '' }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Tableau / Cartes ── --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
        
        {{-- Version Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/70 dark:bg-[#131519] border-b border-slate-200 dark:border-[#222630] text-slate-500 dark:text-[#94a3b8] uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 w-56">Rôle</th>
                        <th class="px-4 py-3">Permissions</th>
                        <th class="px-4 py-3 w-52">Utilisateurs</th>
                        <th class="px-4 py-3 w-28">Créé le</th>
                        <th class="px-4 py-3 text-right w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#222630]">
                    @forelse($roles as $role)
                    @php
                        $c          = $roleColorMap[$role->name] ?? ['bg' => 'bg-slate-50 dark:bg-[#131519]','border' => 'border-slate-200 dark:border-[#222630]','text' => 'text-slate-700 dark:text-slate-300','dot' => 'bg-slate-400'];
                        $initials   = mb_strtoupper(mb_substr($role->name, 0, 2));
                        $permCount  = $role->permissions->count();
                        $usersCount = $role->users_count ?? 0;
                        $permGroups = $role->permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                    @endphp
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/40 transition">

                        {{-- Rôle --}}
                        <td class="px-4 py-3 w-56">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $c['bg'] }} {{ $c['text'] }} border {{ $c['border'] }}">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">{{ $role->name }}</p>
                                    <p class="text-[11px] truncate text-slate-400 dark:text-[#94a3b8]">
                                        @if($role->name === 'admin') Administrateur système
                                        @elseif($role->name === 'superviseur') Responsable de suivi
                                        @elseif($role->name === 'employe') Personnel employé
                                        @elseif($role->name === 'etudiant') Stagiaire
                                        @elseif($role->name === 'super_admin') Accès complet
                                        @else Rôle personnalisé
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Permissions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-[11px] font-semibold text-slate-600 dark:text-[#94a3b8] whitespace-nowrap">
                                    {{ $permCount }} permission{{ $permCount > 1 ? 's' : '' }}
                                </span>
                            </div>
                            @if($permCount > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach($permGroups->keys()->take(4) as $group)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium {{ $c['bg'] }} {{ $c['text'] }}">{{ $labelService->getGroupLabel($group) }}</span>
                                @endforeach
                                @if($permGroups->count() > 4)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 dark:bg-[#131519] text-slate-500 dark:text-[#94a3b8] border border-slate-200 dark:border-[#222630]">+{{ $permGroups->count() - 4 }}</span>
                                @endif
                            </div>
                            @endif
                        </td>

                        {{-- Utilisateurs --}}
                        <td class="px-4 py-3 w-52">
                            <div class="flex items-center gap-2">
                                <div class="flex shrink-0 -space-x-1.5">
                                    @foreach($role->users->take(4) as $u)
                                    <div class="w-6 h-6 rounded-full border border-white dark:border-[#161a22] bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-200 text-[10px] font-bold shrink-0"
                                         title="{{ $u->name }}">
                                        {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if($usersCount === 0)
                                    <div class="w-6 h-6 rounded-full border border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center shrink-0">
                                        <span class="text-slate-400 text-[10px]">—</span>
                                    </div>
                                    @endif
                                </div>
                                <span class="text-[11px] font-medium text-slate-600 dark:text-[#94a3b8] whitespace-nowrap">
                                    {{ $usersCount }} utilisateur{{ $usersCount > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-3 w-28 text-[11px] text-slate-400 dark:text-[#94a3b8] whitespace-nowrap">
                            {{ $role->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 w-24 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ encrypted_route('admin.roles.edit', $role) }}"
                                   class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition border border-indigo-200 dark:border-indigo-900/40" title="Modifier">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                @if(!in_array($role->name, ['super_admin', 'admin']))
                                <form action="{{ encrypted_route('admin.roles.destroy', $role) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition border border-rose-200 dark:border-rose-900/40" title="Supprimer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @else
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 dark:bg-[#131519] border border-slate-200 dark:border-[#222630]" title="Rôle système protégé">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <p class="text-xs text-slate-400">Aucun rôle trouvé.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Version Mobile 375px --}}
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-[#222630]">
            @forelse($roles as $role)
            @php
                $c          = $roleColorMap[$role->name] ?? ['bg' => 'bg-slate-50 dark:bg-[#131519]','border' => 'border-slate-200 dark:border-[#222630]','text' => 'text-slate-700 dark:text-slate-300','dot' => 'bg-slate-400'];
                $initials   = mb_strtoupper(mb_substr($role->name, 0, 2));
                $permCount  = $role->permissions->count();
                $usersCount = $role->users_count ?? 0;
            @endphp
            <div class="p-3 space-y-2 text-xs">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $c['bg'] }} {{ $c['text'] }} border {{ $c['border'] }}">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">{{ $role->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ $usersCount }} utilisateur{{ $usersCount > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ encrypted_route('admin.roles.edit', $role) }}"
                           class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @if(!in_array($role->name, ['super_admin', 'admin']))
                        <form action="{{ encrypted_route('admin.roles.destroy', $role) }}" method="POST" class="inline" data-confirm-delete>
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-100 dark:border-[#222630]">
                    <span>{{ $permCount }} permissions</span>
                    <span>Créé le {{ $role->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-xs text-slate-400">
                Aucun rôle trouvé.
            </div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-slate-200 dark:border-[#222630] bg-slate-50/50 dark:bg-[#131519]">
            {{ $roles->links() }}
        </div>
    </div>

</div>
</x-app-layout>