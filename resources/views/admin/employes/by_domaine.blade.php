<x-app-layout>
    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }
        header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.05), 0 8px 24px -16px rgba(36,31,27,.12);
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            padding: .45rem .85rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover {
            background: #162f73;
            border-color: #162f73;
        }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #dcdad2;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            padding: .45rem .85rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary {
            background: #131519;
            color: #f8fafc;
            border-color: #222630;
        }
        .ks-btn-secondary:hover {
            border-color: #1c3e96;
            color: #1c3e96;
        }
    </style>

    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        {{-- ── En-tête Compact ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('employes.index') }}" class="ks-btn-secondary !p-1.5" title="Retour à la liste complète">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#1c3e96] dark:bg-[#60a5fa]"></span>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                            Secteur · {{ $domaine->nom }}
                        </p>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc] mt-0.5">
                        Collaborateurs du domaine {{ $domaine->nom }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.create', ['role' => 'employe', 'domaine_id' => $domaine->id]) }}" class="ks-btn-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Nouvel Employé</span>
                </a>
            </div>
        </div>

        {{-- ── Tableau Compact des Collaborateurs du Domaine ── --}}
        <div class="ks-sec overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] text-[10.5px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                            <th class="px-3.5 py-2.5 w-12 text-center">ID</th>
                            <th class="px-3.5 py-2.5">Collaborateur</th>
                            <th class="px-3.5 py-2.5">Email</th>
                            <th class="px-3.5 py-2.5">Rôles</th>
                            <th class="px-3.5 py-2.5">Statut</th>
                            <th class="px-3.5 py-2.5">Créé le</th>
                            <th class="px-3.5 py-2.5 text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                        @forelse($users as $u)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24]/60 transition-colors">
                                <td class="px-3.5 py-2 text-center font-mono text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                                    #{{ $u->id }}
                                </td>
                                <td class="px-3.5 py-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-r from-violet-600/20 to-blue-600/20 border border-[#1c3e96]/30 text-[#1c3e96] dark:text-[#60a5fa] font-bold text-[10.5px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <span class="font-bold text-[#241f1b] dark:text-[#f8fafc] truncate block">
                                            {{ $u->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3.5 py-2 text-[#6e6862] dark:text-[#a89f93] font-mono text-[11px]">
                                    {{ $u->email }}
                                </td>
                                <td class="px-3.5 py-2">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($u->roles as $role)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-[#eef1fb] text-[#1c3e96] dark:bg-[#1c3e96]/20 dark:text-[#60a5fa]">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-3.5 py-2">
                                    @php
                                        $isActif = in_array(strtolower($u->status ?? ''), ['actif', 'active'], true);
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $isActif ? 'bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/20 dark:text-[#4ade80]' : 'bg-[#fee2e2] text-[#b91c1c] dark:bg-[#b91c1c]/20 dark:text-[#f87171]' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isActif ? 'bg-[#15803d]' : 'bg-[#b91c1c]' }}"></span>
                                        {{ $isActif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-2 text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                                    {{ $u->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-3.5 py-2 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ encrypted_route('admin.users.edit', $u) }}" class="p-1 rounded text-[#6e6862] hover:text-[#b45309] dark:text-[#a89f93] dark:hover:text-[#fbbf24] transition" title="Modifier">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <form action="{{ encrypted_route('admin.users.destroy', $u) }}" method="POST" class="inline" data-confirm-delete>
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-[#6e6862] hover:text-[#b91c1c] dark:text-[#a89f93] dark:hover:text-[#f87171] transition" title="Supprimer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-[#6e6862] dark:text-[#a89f93]">
                                    <p class="text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">Aucun employé dans ce domaine</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-2.5 border-t border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>