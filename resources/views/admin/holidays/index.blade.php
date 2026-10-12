<x-app-layout title="Jours Fériés">

{{-- ═══════════════════════════════════════════════
     HOLIDAYS INDEX — Jours Fériés
     Design System: Mon Stage
     ═══════════════════════════════════════════════ --}}

<div class="w-full space-y-3 sm:space-y-4 pb-6 text-slate-900 dark:text-slate-100">

    {{-- ── 1. En-tête compact Mon Stage ────────────── --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-[#1c3e96]/15 dark:bg-[#1c3e96]/30 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-sm sm:text-base font-semibold text-[#111827] dark:text-[#f8fafc] truncate">Jours Fériés</h1>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd]">
                            Calendrier
                        </span>
                    </div>
                    <p class="text-xs text-[#64748b] dark:text-[#94a3b8] truncate">
                        Gérez les jours fériés, la suspension automatique du pointage et les appels d'urgence
                    </p>
                </div>
            </div>

            @can('holidays.create')
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.holidays.create') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#1c3e96] hover:bg-[#162f73] text-white shadow-sm transition">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nouveau jour férié</span>
                </a>
            </div>
            @endcan
        </div>
    </div>

    {{-- ── 2. Cartes KPIs Mon Stage ────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between transition-colors">
            <div>
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Total Jours Fériés</p>
                <p class="text-lg font-bold text-[#111827] dark:text-[#f8fafc] mt-0.5">{{ $holidays->total() }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between transition-colors">
            <div>
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Jours Fériés Actifs</p>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $holidays->where('is_active', true)->count() }}
                </p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between transition-colors">
            <div>
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Effectif Concerné</p>
                <p class="text-lg font-bold text-[#1c3e96] dark:text-[#93c5fd] mt-0.5">
                    {{ $activeUsersCount }} <span class="text-xs font-normal text-[#64748b] dark:text-[#94a3b8]">actifs</span>
                </p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ── 3. Carte principale & Tableau Mon Stage ── --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden transition-colors shadow-sm">

        {{-- ── Version Desktop : tableau ────────── --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#f8f7f4] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] text-[#64748b] dark:text-[#94a3b8] font-semibold uppercase tracking-wider text-[11px]">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Libellé</th>
                        <th class="px-4 py-3 hidden lg:table-cell">Description</th>
                        <th class="px-4 py-3 text-center">Statut Pointage</th>
                        <th class="px-4 py-3 text-center">Notification</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]/60 text-[#111827] dark:text-[#f8fafc]">
                    @forelse($holidays as $holiday)
                    <tr class="transition-colors hover:bg-[#f1efe9]/40 dark:hover:bg-[#181c24]/40 {{ $holiday->is_active ? 'bg-[#1c3e96]/[0.02] dark:bg-[#1c3e96]/[0.05]' : '' }}">

                        {{-- Date --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-medium text-[#111827] dark:text-[#f8fafc]">
                                {{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </span>
                        </td>

                        {{-- Libellé --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 shrink-0 rounded-lg bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd] flex items-center justify-center text-xs font-bold select-none">
                                    {{ mb_strtoupper(mb_substr($holiday->label, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-[#111827] dark:text-[#f8fafc]">{{ $holiday->label }}</span>
                            </div>
                        </td>

                        {{-- Description --}}
                        <td class="px-4 py-3 text-[#64748b] dark:text-[#94a3b8] max-w-xs truncate hidden lg:table-cell">
                            {{ $holiday->description ?? '—' }}
                        </td>

                        {{-- Toggle actif --}}
                        <td class="px-4 py-3 text-center">
                            @can('holidays.toggle')
                            <form method="POST" action="{{ route('admin.holidays.toggle', $holiday) }}" class="inline-flex justify-center">
                                @csrf
                                <button type="submit"
                                        title="{{ $holiday->is_active ? 'Désactiver (autoriser le pointage)' : 'Activer (bloquer le pointage)' }}"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#1c3e96] focus:ring-offset-2 dark:focus:ring-offset-[#131519] {{ $holiday->is_active ? 'bg-[#1c3e96]' : 'bg-[#cbd5e1] dark:bg-[#2b303c]' }}">
                                    <span class="sr-only">{{ $holiday->is_active ? 'Désactiver' : 'Activer' }}</span>
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out {{ $holiday->is_active ? 'translate-x-6' : 'translate-x-1' }} flex items-center justify-center text-[9px] font-bold {{ $holiday->is_active ? 'text-[#1c3e96]' : 'text-slate-400' }}">
                                        {{ $holiday->is_active ? '✓' : '✕' }}
                                    </span>
                                </button>
                            </form>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $holiday->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                {{ $holiday->is_active ? 'Bloqué' : 'Normal' }}
                            </span>
                            @endcan
                        </td>

                        {{-- Notifié --}}
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($holiday->notified)
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Notifié
                            </span>
                            @else
                            <span class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Non notifié</span>
                            @endif

                            @if(($holiday->exemptions_count ?? 0) > 0)
                            <div class="mt-0.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 px-1.5 py-0.5 rounded-full">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    {{ $holiday->exemptions_count }} appelé(s)
                                </span>
                            </div>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1">
                                @can('holidays.toggle')
                                @if($holiday->is_active)
                                <button type="button"
                                        onclick="openEmergencyModal({{ $holiday->id }}, '{{ addslashes($holiday->label) }}', '{{ $holiday->date->format('Y-m-d') }}')"
                                        title="Appel d'urgence"
                                        class="p-1.5 rounded-md text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 hover:bg-red-500/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </button>
                                @endif
                                @endcan

                                @can('holidays.edit')
                                <a href="{{ route('admin.holidays.edit', $holiday) }}"
                                   title="Modifier"
                                   class="p-1.5 rounded-md text-amber-600 dark:text-amber-400 bg-amber-500/10 dark:bg-amber-500/20 hover:bg-amber-500/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('holidays.toggle')
                                @if($holiday->is_active)
                                <form method="POST" action="{{ route('admin.holidays.notify.post', $holiday) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            title="{{ $holiday->notified ? 'Renvoyer la notification' : 'Notifier tous les utilisateurs actifs' }}"
                                            class="p-1.5 rounded-md text-[#1c3e96] dark:text-[#93c5fd] bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 hover:bg-[#1c3e96]/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan

                                @can('holidays.delete')
                                <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Supprimer"
                                            class="p-1.5 rounded-md text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 hover:bg-red-500/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8">
                            @include('admin.holidays._empty')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Version Mobile : cartes ──────────── --}}
        <div class="md:hidden divide-y divide-[#dcdad2]/60 dark:divide-[#222630]/60">
            @forelse($holidays as $holiday)
            <div class="p-3 space-y-2.5 transition-colors {{ $holiday->is_active ? 'bg-[#1c3e96]/[0.02] dark:bg-[#1c3e96]/[0.05]' : '' }}">

                {{-- Ligne 1 : icône + libellé + date + toggle --}}
                <div class="flex items-start justify-between gap-2.5">
                    <div class="flex items-start gap-2.5 min-w-0 flex-1">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd] flex items-center justify-center text-xs font-bold select-none mt-0.5">
                            {{ mb_strtoupper(mb_substr($holiday->label, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-[#111827] dark:text-[#f8fafc] truncate">{{ $holiday->label }}</p>
                            <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] mt-0.5">
                                {{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </p>
                            @if($holiday->description)
                            <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] mt-1 line-clamp-2">{{ $holiday->description }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Toggle Switch --}}
                    <div class="shrink-0 flex flex-col items-end gap-1">
                        @can('holidays.toggle')
                        <form method="POST" action="{{ route('admin.holidays.toggle', $holiday) }}">
                            @csrf
                            <button type="submit"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 focus:outline-none {{ $holiday->is_active ? 'bg-[#1c3e96]' : 'bg-[#cbd5e1] dark:bg-[#2b303c]' }}">
                                <span class="sr-only">Toggle</span>
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out {{ $holiday->is_active ? 'translate-x-4' : 'translate-x-0.5' }} flex items-center justify-center text-[7px] font-bold {{ $holiday->is_active ? 'text-[#1c3e96]' : 'text-slate-400' }}">
                                    {{ $holiday->is_active ? '✓' : '✕' }}
                                </span>
                            </button>
                        </form>
                        @endcan

                        @if($holiday->notified)
                        <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-emerald-600 dark:text-emerald-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Notifié
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Badges exemptions éventuels --}}
                @if(($holiday->exemptions_count ?? 0) > 0)
                <div class="pt-1">
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        {{ $holiday->exemptions_count }} personne(s) appelée(s) en urgence
                    </span>
                </div>
                @endif

                {{-- Ligne 2 : Actions mobiles --}}
                <div class="flex items-center gap-1.5 pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]/60">
                    @can('holidays.toggle')
                    @if($holiday->is_active)
                    <button type="button"
                            onclick="openEmergencyModal({{ $holiday->id }}, '{{ addslashes($holiday->label) }}', '{{ $holiday->date->format('Y-m-d') }}')"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 hover:bg-red-500/20 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Urgence
                    </button>
                    @endif
                    @endcan

                    @can('holidays.edit')
                    <a href="{{ route('admin.holidays.edit', $holiday) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-500/10 dark:bg-amber-500/20 hover:bg-amber-500/20 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    @endcan

                    @can('holidays.toggle')
                    @if($holiday->is_active)
                    <form method="POST" action="{{ route('admin.holidays.notify.post', $holiday) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-[#1c3e96] dark:text-[#93c5fd] bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 hover:bg-[#1c3e96]/20 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $holiday->notified ? 'Renvoyer' : 'Notifier' }}
                        </button>
                    </form>
                    @endif
                    @endcan

                    @can('holidays.delete')
                    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" data-confirm-delete>
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-1.5 rounded-md text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 hover:bg-red-500/20 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @empty
            <div class="p-6">
                @include('admin.holidays._empty')
            </div>
            @endforelse
        </div>

        {{-- ── Pagination ───────────────────────── --}}
        @if($holidays->hasPages())
        <div class="px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#f8f7f4] dark:bg-[#181c24]">
            {{ $holidays->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL — Appel d'Urgence Mon Stage
     ═══════════════════════════════════════════════ --}}
<div id="emergencyModal"
     class="fixed inset-0 z-50 hidden"
     aria-modal="true" role="dialog" aria-labelledby="emergencyModalTitle">

    {{-- Overlay --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeEmergencyModal()"></div>

    {{-- Panneau modal --}}
    <div class="fixed inset-0 z-10 flex items-center justify-center p-3 sm:p-4 overflow-y-auto pointer-events-none">
        <div class="w-full sm:max-w-xl bg-[#fffefa] dark:bg-[#161a22] rounded-[10px] border border-[#dcdad2] dark:border-[#222630] shadow-2xl flex flex-col max-h-[92dvh] sm:max-h-[85vh] pointer-events-auto transition-colors">

            {{-- En-tête modal --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] shrink-0 bg-[#f8f7f4] dark:bg-[#181c24]">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-red-500/10 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 id="emergencyModalTitle" class="text-xs sm:text-sm font-semibold text-[#111827] dark:text-[#f8fafc] truncate">
                            Appel d'urgence
                        </h2>
                        <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] truncate">
                            <span id="emergencyHolidayLabel" class="font-medium text-[#111827] dark:text-[#f8fafc]"></span>
                            <span class="mx-1">·</span>
                            <span id="emergencyHolidayDate"></span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeEmergencyModal()"
                        class="p-1 rounded-md text-[#64748b] hover:text-[#111827] dark:hover:text-[#f8fafc] hover:bg-[#dcdad2]/40 dark:hover:bg-[#222630] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Corps du modal --}}
            <form id="emergencyForm" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="holiday_id" id="emergencyHolidayId">

                <div class="flex-1 overflow-y-auto p-4 space-y-3.5">

                    {{-- Recherche & Filtres --}}
                    <div class="space-y-2">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[#64748b] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="userSearch" placeholder="Rechercher par nom ou email…"
                                   class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] placeholder-[#94a3b8] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                        </div>

                        {{-- Filtres segmentés --}}
                        <div class="flex items-center gap-1 p-0.5 rounded-lg bg-[#f8f7f4] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] w-fit">
                            <button type="button" onclick="filterUsers('all')" data-filter="all"
                                    class="filter-btn px-2.5 py-1 text-[11px] font-medium rounded-md transition-colors bg-[#1c3e96] text-white">
                                Tous
                            </button>
                            <button type="button" onclick="filterUsers('employe')" data-filter="employe"
                                    class="filter-btn px-2.5 py-1 text-[11px] font-medium rounded-md transition-colors text-[#64748b] dark:text-[#94a3b8] hover:text-[#111827] dark:hover:text-[#f8fafc]">
                                Employés
                            </button>
                            <button type="button" onclick="filterUsers('etudiant')" data-filter="etudiant"
                                    class="filter-btn px-2.5 py-1 text-[11px] font-medium rounded-md transition-colors text-[#64748b] dark:text-[#94a3b8] hover:text-[#111827] dark:hover:text-[#f8fafc]">
                                Stagiaires
                            </button>
                        </div>
                    </div>

                    {{-- Liste utilisateurs --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748b] dark:text-[#94a3b8] uppercase mb-1">
                            Sélectionner les personnes à autoriser
                        </label>
                        <div id="userList"
                             class="space-y-1 max-h-48 overflow-y-auto rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] p-1.5 scrollbar-thin">
                            <div class="text-center text-xs text-[#64748b] dark:text-[#94a3b8] py-6">Chargement…</div>
                        </div>
                    </div>

                    {{-- Message personnalisé --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748b] dark:text-[#94a3b8] uppercase mb-1">
                            Message d'instruction <span class="text-[10px] font-normal lowercase">(optionnel)</span>
                        </label>
                        <textarea name="message" rows="2"
                                  placeholder="Ex : Présence requise pour maintenance urgente sur le site principal."
                                  class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] placeholder-[#94a3b8] resize-none focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition"></textarea>
                    </div>

                    {{-- Personnes déjà appelées --}}
                    <div id="alreadyCalledSection" class="hidden pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]/60">
                        <div class="flex items-center gap-1.5 mb-2">
                            <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="text-xs font-semibold text-[#111827] dark:text-[#f8fafc]">
                                Déjà appelés en urgence
                            </span>
                            <span id="alreadyCalledCount" class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-red-500/10 dark:bg-red-500/20 text-[10px] font-bold text-red-600 dark:text-red-400"></span>
                        </div>
                        <div id="alreadyCalledList" class="space-y-1.5 max-h-36 overflow-y-auto"></div>
                    </div>
                </div>

                {{-- Pied de modal --}}
                <div class="px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#f8f7f4] dark:bg-[#181c24] flex items-center justify-between gap-3 shrink-0">
                    <p class="text-xs text-[#64748b] dark:text-[#94a3b8] shrink-0">
                        <span id="selectedCount" class="font-bold text-[#111827] dark:text-[#f8fafc]">0</span> sélectionné(s)
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeEmergencyModal()"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border border-[#dcdad2] dark:border-[#222630] text-[#64748b] dark:text-[#94a3b8] hover:bg-[#dcdad2]/30 dark:hover:bg-[#222630] transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white shadow-sm transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Envoyer l'appel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════════ --}}
@push('scripts')
<script>
    let allUsers          = [];
    let currentFilter     = 'all';
    let currentExemptions = [];

    /* ── Exemptions par jour férié ── */
    const holidayExemptions = @json($exemptionsByHoliday);

    function openEmergencyModal(holidayId, label, date) {
        document.getElementById('emergencyHolidayId').value    = holidayId;
        document.getElementById('emergencyHolidayLabel').textContent = label;
        document.getElementById('emergencyHolidayDate').textContent  = new Date(date).toLocaleDateString('fr-FR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        document.getElementById('emergencyForm').action = `/admin/holidays/${holidayId}/emergency-call`;
        document.getElementById('emergencyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        currentExemptions = holidayExemptions[holidayId] || [];
        renderAlreadyCalled();
        loadUsers();
    }

    function closeEmergencyModal() {
        document.getElementById('emergencyModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeEmergencyModal();
    });

    function renderAlreadyCalled() {
        const section = document.getElementById('alreadyCalledSection');
        const list    = document.getElementById('alreadyCalledList');
        const count   = document.getElementById('alreadyCalledCount');

        count.textContent = currentExemptions.length;

        if (!currentExemptions.length) {
            section.classList.add('hidden');
            return;
        }

        section.classList.remove('hidden');
        list.innerHTML = currentExemptions.map(e => `
            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-red-500/10 dark:bg-red-500/15 border border-red-200/60 dark:border-red-800/40">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-[#111827] dark:text-[#f8fafc] truncate">${esc(e.name)}</p>
                    <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] truncate">
                        ${esc(e.email)}${e.message ? ' — ' + esc(e.message) : ''}
                    </p>
                </div>
                <form method="POST" action="/admin/holidays/exemptions/${e.id}" onsubmit="return confirm('Révoquer cette autorisation d\\'urgence ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Révoquer l'appel"
                            class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-medium text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Retirer
                    </button>
                </form>
            </div>
        `).join('');
    }

    function esc(v) {
        const div = document.createElement('div');
        div.textContent = v ?? '';
        return div.innerHTML;
    }

    function loadUsers() {
        const list = document.getElementById('userList');
        list.innerHTML = '<div class="text-center text-xs text-[#64748b] dark:text-[#94a3b8] py-6">Chargement…</div>';

        fetch('{{ route("admin.holidays.users-list") }}')
            .then(r => r.json())
            .then(users => { allUsers = users; renderUsers(); })
            .catch(() => {
                list.innerHTML = '<div class="text-center text-xs text-red-500 py-6">Erreur de chargement des utilisateurs.</div>';
            });
    }

    function renderUsers() {
        const list   = document.getElementById('userList');
        const search = (document.getElementById('userSearch').value || '').toLowerCase().trim();

        const filtered = allUsers.filter(u => {
            if (currentFilter !== 'all' && u.role_name !== currentFilter) return false;
            if (search && !u.name.toLowerCase().includes(search) && !u.email.toLowerCase().includes(search)) return false;
            return true;
        });

        if (!filtered.length) {
            list.innerHTML = '<div class="text-center text-xs text-[#64748b] dark:text-[#94a3b8] py-6">Aucun utilisateur trouvé.</div>';
            return;
        }

        list.innerHTML = filtered.map(u => {
            const isStudent = u.role_name === 'etudiant';
            const badgeCls  = isStudent
                ? 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400'
                : 'bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400';

            return `
            <label class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg cursor-pointer hover:bg-[#f1efe9]/60 dark:hover:bg-[#181c24] transition-colors">
                <input type="checkbox" name="user_ids[]" value="${u.id}" onchange="updateCount()"
                       class="w-3.5 h-3.5 rounded text-[#1c3e96] border-[#dcdad2] dark:border-[#222630] focus:ring-[#1c3e96] shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-[#111827] dark:text-[#f8fafc] truncate">${esc(u.name)}</p>
                    <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] truncate">${esc(u.email)}</p>
                </div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full shrink-0 ${badgeCls}">${esc(u.role)}</span>
            </label>`;
        }).join('');
    }

    function updateCount() {
        const n = document.querySelectorAll('#userList input[type="checkbox"]:checked').length;
        document.getElementById('selectedCount').textContent = n;
    }

    function filterUsers(role) {
        currentFilter = role;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            const active = btn.dataset.filter === role;
            if (active) {
                btn.className = 'filter-btn px-2.5 py-1 text-[11px] font-medium rounded-md transition-colors bg-[#1c3e96] text-white';
            } else {
                btn.className = 'filter-btn px-2.5 py-1 text-[11px] font-medium rounded-md transition-colors text-[#64748b] dark:text-[#94a3b8] hover:text-[#111827] dark:hover:text-[#f8fafc]';
            }
        });
        renderUsers();
    }

    document.getElementById('userSearch')?.addEventListener('input', renderUsers);
</script>
@endpush

</x-app-layout>