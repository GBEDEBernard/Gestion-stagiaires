<x-app-layout title="Gestion des Sites">
    @php
        $totalSites = \App\Models\Site::count();
        $sitesActifs = \App\Models\Site::where('is_active', true)->count();
        $totalGeofences = \App\Models\SiteGeofence::count();
    @endphp

    {{-- PLEINE LARGEUR w-full SANS RESTRICTION --}}
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">

        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Sites & Lieux de Présence
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Géolocalisation
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Gérez les lieux de présence, adresses et périmètres GPS autorisés (Geofencing).
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('sites.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Nouveau Site</span>
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
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total sites</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $totalSites }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sites actifs</span>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $sitesActifs }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Périmètres GPS</span>
                    <p class="text-xl sm:text-2xl font-bold text-cyan-600 dark:text-cyan-400 mt-1">{{ $totalGeofences }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── 3. TABLEAU DES SITES (PLEINE LARGEUR) ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            
            {{-- Vue Desktop (> 768px) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#f6f5f0] dark:bg-[#131519] text-slate-600 dark:text-slate-300 font-semibold border-b border-[#dcdad2] dark:border-[#222630]">
                        <tr>
                            <th class="px-4 py-2.5 w-16">Code</th>
                            <th class="px-4 py-2.5">Nom du Site</th>
                            <th class="px-4 py-2.5">Ville</th>
                            <th class="px-4 py-2.5">Zone Geofence</th>
                            <th class="px-4 py-2.5">État</th>
                            <th class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                        @forelse($sites as $site)
                            @php
                                $primaryGeofence = $site->geofences->firstWhere('is_primary', true) ?? $site->geofences->first();
                            @endphp
                            <tr class="hover:bg-[#f6f5f0]/60 dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-3 font-mono font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                                    {{ $site->code }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 dark:text-[#f8fafc] text-sm">
                                            {{ $site->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                            {{ $site->address ?: 'Adresse non renseignée' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                    {{ $site->city ?: '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($primaryGeofence)
                                        <div class="text-slate-800 dark:text-slate-200 font-medium">
                                            {{ $primaryGeofence->name }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                            Rayon : {{ $primaryGeofence->radius_meters }}m · Précision : {{ $primaryGeofence->allowed_accuracy_meters }}m
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Aucune zone</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $site->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $site->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('sites.qr-poster', $site) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-800 transition"
                                           title="Imprimer l'Affiche QR de Porte">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                        </a>

                                        <a href="{{ encrypted_route('sites.edit', $site) }}"
                                           title="Modifier"
                                           aria-label="Modifier"
                                           data-confirm-edit
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-amber-600 dark:text-amber-400 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ encrypted_route('sites.destroy', $site) }}" method="POST" data-confirm-delete class="inline-flex">
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
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        </div>
                                        <p class="text-xs font-medium">Aucun site n'est encore configuré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vue Mobile (< 768px - Écran 375px sans débordement) --}}
            <div class="block md:hidden divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
                @forelse($sites as $site)
                    @php
                        $primaryGeofence = $site->geofences->firstWhere('is_primary', true) ?? $site->geofences->first();
                    @endphp
                    <div class="p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-semibold text-xs text-slate-900 dark:text-[#f8fafc] truncate block">
                                    {{ $site->name }}
                                </span>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                    {{ $site->address ?: 'Adresse non renseignée' }}
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $site->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }} shrink-0">
                                {{ $site->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- Geofence mobile --}}
                        <div class="flex justify-between items-center text-[11px] text-slate-600 dark:text-slate-300 bg-[#fcfbf9] dark:bg-[#181c24] border border-[#dcdad2]/60 dark:border-[#222630] rounded-lg p-2">
                            <span class="text-slate-400">Code : <strong class="font-mono text-slate-700 dark:text-slate-200">{{ $site->code }}</strong></span>
                            @if($primaryGeofence)
                                <span>Zone : {{ $primaryGeofence->radius_meters }}m</span>
                            @else
                                <span class="text-slate-400 italic">Sans zone</span>
                            @endif
                        </div>

                        {{-- Actions Mobile --}}
                        <div class="flex items-center justify-end gap-1.5 pt-1">
                            <a href="{{ route('sites.qr-poster', $site) }}" target="_blank"
                               class="px-2 py-1 rounded bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-[11px] font-medium text-indigo-600 dark:text-indigo-400">
                                Affiche QR
                            </a>
                            <a href="{{ encrypted_route('sites.edit', $site) }}"
                               data-confirm-edit
                               class="px-2 py-1 rounded bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                Modifier
                            </a>
                            <form action="{{ encrypted_route('sites.destroy', $site) }}" method="POST" data-confirm-delete class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 rounded bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                        Aucun site n'est encore configuré.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="p-3 sm:p-4 border-t border-[#dcdad2] dark:border-[#222630]">
                {{ $sites->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
