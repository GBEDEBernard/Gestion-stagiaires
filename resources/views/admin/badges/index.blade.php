<x-app-layout title="Gestion des Badges">
    @php
        $totalBadges = \App\Models\Badge::count();
        $dernierBadge = \App\Models\Badge::latest('id')->value('badge') ?? 'Aucun';
        $prochainBadge = \App\Models\Badge::getNextBadgeNumber();
    @endphp

    {{-- PLEINE LARGEUR w-full SANS RESTRICTION --}}
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Badges
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                Contrôle d'accès
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Gérez les identifiants et numéros de badge pour le pointage et la sécurité.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('badges.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Nouveau Badge</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Alertes Flash ── --}}
        @if(session('success'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-[10px] bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-medium">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="truncate">{{ session('success') }}</span>
                </div>
                <button type="button" @click="dismissed = true" class="p-1 rounded hover:bg-emerald-200/50 dark:hover:bg-emerald-800/50 text-emerald-700 dark:text-emerald-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-[10px] bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-medium">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="h-2 w-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="truncate">{{ session('error') }}</span>
                </div>
                <button type="button" @click="dismissed = true" class="p-1 rounded hover:bg-rose-200/50 dark:hover:bg-rose-800/50 text-rose-700 dark:text-rose-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- ── 2. CARTES KPIS (3 Indicateurs) ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            {{-- Total badges --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total badges</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $totalBadges }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                </div>
            </div>

            {{-- Dernier badge attribué --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dernier badge</span>
                    <p class="text-xl sm:text-2xl font-bold font-mono text-purple-600 dark:text-purple-400 mt-1">{{ $dernierBadge }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Prochain numéro automatique --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Prochain suggéré</span>
                    <p class="text-xl sm:text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">{{ $prochainBadge }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── 3. TABLEAU DES BADGES (PLEINE LARGEUR) ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            
            {{-- Vue Desktop (> 640px) --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#f6f5f0] dark:bg-[#131519] text-slate-600 dark:text-slate-300 font-semibold border-b border-[#dcdad2] dark:border-[#222630]">
                        <tr>
                            <th class="px-4 py-2.5 w-12 text-center">N°</th>
                            <th class="px-4 py-2.5">Numéro de Badge</th>
                            <th class="px-4 py-2.5">Date de création</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                        @forelse($badges as $badge)
                            <tr class="hover:bg-[#f6f5f0]/60 dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-[11px]">
                                    {{ $badges->firstItem() + $loop->index }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-purple-500/10 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-500/20 rounded-lg flex items-center justify-center font-bold text-xs font-mono shrink-0">
                                            {{ $badge->id }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-mono font-semibold text-slate-900 dark:text-[#f8fafc] text-sm">
                                                {{ $badge->badge }}
                                            </span>
                                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                                ID #{{ $badge->id }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-slate-800 dark:text-slate-200 font-medium">
                                        {{ $badge->created_at ? $badge->created_at->format('d/m/Y à H:i') : '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $badge->created_at ? $badge->created_at->diffForHumans() : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ encrypted_route('badges.edit', $badge) }}"
                                           title="Modifier"
                                           aria-label="Modifier"
                                           data-confirm-edit
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-amber-600 dark:text-amber-400 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ encrypted_route('badges.destroy', $badge) }}" method="POST" data-confirm-delete class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Supprimer"
                                                    aria-label="Supprimer"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                                        </div>
                                        <p class="text-xs font-medium">Aucun badge trouvé.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vue Mobile (< 640px - Écran 375px sans débordement) --}}
            <div class="block sm:hidden divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                @forelse($badges as $badge)
                    <div class="p-3.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 bg-purple-500/10 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-500/20 rounded-lg flex items-center justify-center font-bold text-xs font-mono shrink-0">
                                {{ $badge->id }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-xs text-slate-900 dark:text-[#f8fafc] truncate">
                                    {{ $badge->badge }}
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $badge->created_at ? $badge->created_at->format('d/m/Y') : '-' }}
                                    @if($badge->created_at)
                                        <span class="text-slate-400">({{ $badge->created_at->diffForHumans() }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ encrypted_route('badges.edit', $badge) }}"
                               data-confirm-edit
                               class="px-2.5 py-1.5 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                Modifier
                            </a>
                            <form action="{{ encrypted_route('badges.destroy', $badge) }}" method="POST" data-confirm-delete class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                        Aucun badge trouvé.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="p-3 sm:p-4 border-t border-[#dcdad2] dark:border-[#222630]">
                {{ $badges->links() }}
            </div>
        </div>

    </div>
</x-app-layout>