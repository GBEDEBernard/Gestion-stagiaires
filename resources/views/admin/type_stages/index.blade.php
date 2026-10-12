<x-app-layout title="Types de Stage">
    @php
        $totalTypes = \App\Models\Typestage::count();
    @endphp

    {{-- PLEINE LARGEUR w-full SANS RESTRICTION --}}
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Types de Stage
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Référentiel
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Gérez les natures et catégories de stage (Académique, Professionnel, Découverte...).
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('type_stages.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Nouveau Type</span>
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

        {{-- ── 2. CARTES KPIS ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Types enregistrés</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $totalTypes }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Convention</span>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Valide & Normalisée</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── 3. TABLEAU DES TYPES DE STAGE (PLEINE LARGEUR) ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            
            {{-- Vue Desktop (> 640px) --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#f6f5f0] dark:bg-[#131519] text-slate-600 dark:text-slate-300 font-semibold border-b border-[#dcdad2] dark:border-[#222630]">
                        <tr>
                            <th class="px-4 py-2.5 w-16 text-center">ID</th>
                            <th class="px-4 py-2.5">Libellé du type</th>
                            <th class="px-4 py-2.5">Code</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                        @forelse($type_stages as $typeStage)
                            <tr class="hover:bg-[#f6f5f0]/60 dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-[11px]">
                                    #{{ $typeStage->id }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-900 dark:text-[#f8fafc] text-sm">
                                        {{ $typeStage->libelle }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        {{ $typeStage->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ encrypted_route('type_stages.edit', $typeStage) }}"
                                           title="Modifier"
                                           aria-label="Modifier"
                                           data-confirm-edit
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-amber-600 dark:text-amber-400 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ encrypted_route('type_stages.destroy', $typeStage) }}" method="POST" data-confirm-delete class="inline-flex">
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
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <p class="text-xs font-medium">Aucun type de stage configuré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vue Mobile (< 640px - Écran 375px sans débordement) --}}
            <div class="block sm:hidden divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                @forelse($type_stages as $typeStage)
                    <div class="p-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="font-semibold text-xs text-slate-900 dark:text-[#f8fafc] truncate block">
                                {{ $typeStage->libelle }}
                            </span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    {{ $typeStage->code }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">
                                    ID #{{ $typeStage->id }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ encrypted_route('type_stages.edit', $typeStage) }}"
                               data-confirm-edit
                               class="px-2.5 py-1.5 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                Modifier
                            </a>
                            <form action="{{ encrypted_route('type_stages.destroy', $typeStage) }}" method="POST" data-confirm-delete class="inline">
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
                        Aucun type de stage configuré.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="p-3 sm:p-4 border-t border-[#dcdad2] dark:border-[#222630]">
                {{ $type_stages->links() }}
            </div>
        </div>

    </div>
</x-app-layout>