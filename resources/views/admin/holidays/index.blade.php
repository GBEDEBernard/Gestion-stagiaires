<x-app-layout>

{{-- ═══════════════════════════════════════════════
     HOLIDAYS INDEX — Jours Fériés
     Stack: Tailwind CSS, Alpine.js v3, Blade
     Dark mode: class strategy (.dark)
     ═══════════════════════════════════════════════ --}}

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">

    {{-- ── En-tête ──────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                Jours Fériés
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Gérez les jours fériés et activez / désactivez le pointage
            </p>
        </div>

        @can('holidays.create')
        <a href="{{ route('admin.holidays.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                  bg-gradient-to-r from-purple-500 to-indigo-600
                  hover:from-purple-600 hover:to-indigo-700
                  text-white text-sm font-semibold
                  shadow-lg shadow-purple-600/20
                  transition-all duration-200 self-start sm:self-auto whitespace-nowrap">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau jour férié
        </a>
        @endcan
    </div>

    {{-- ── Carte principale ─────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 dark:ring-white/5 overflow-hidden">

        {{-- ── Version desktop : tableau ────────── --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Description</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actif</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notifié</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($holidays as $holiday)
                    <tr class="group transition-colors duration-150
                               hover:bg-gray-50 dark:hover:bg-gray-700/30
                               {{ $holiday->is_active ? 'bg-purple-50/40 dark:bg-purple-900/10' : '' }}">

                        {{-- Date --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </span>
                        </td>

                        {{-- Libellé --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 shrink-0 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600
                                            flex items-center justify-center text-white text-sm font-bold select-none">
                                    {{ mb_strtoupper(mb_substr($holiday->label, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $holiday->label }}</span>
                            </div>
                        </td>

                        {{-- Description --}}
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate hidden lg:table-cell">
                            {{ $holiday->description ?? '—' }}
                        </td>

                        {{-- Toggle actif --}}
                        <td class="px-6 py-4 text-center">
                            @can('holidays.toggle')
                            <form method="POST" action="{{ route('admin.holidays.toggle', $holiday) }}" class="inline-flex justify-center">
                                @csrf
                                <button type="submit" 
                                        title="{{ $holiday->is_active ? 'Désactiver' : 'Activer' }}"
                                        class="relative group/toggle inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 
                                               focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2
                                               {{ $holiday->is_active 
                                                  ? 'bg-gradient-to-r from-purple-500 to-indigo-600 shadow-md shadow-purple-500/30' 
                                                  : 'bg-gray-300 dark:bg-gray-600' }}
                                               hover:scale-105 active:scale-95">
                                    
                                    {{-- Effet de glow --}}
                                    <span class="absolute inset-0 rounded-full opacity-0 group-hover/toggle:opacity-100 transition-opacity duration-300
                                                 {{ $holiday->is_active ? 'bg-purple-400/20 blur-md' : 'bg-gray-400/20 blur-md' }}">
                                    </span>
                                    
                                    <span class="sr-only">{{ $holiday->is_active ? 'Désactiver' : 'Activer' }}</span>
                                    
                                    {{-- Cercle mobile --}}
                                    <span class="relative inline-block h-5 w-5 transform rounded-full bg-white shadow-lg 
                                                 transition-all duration-300 ease-in-out
                                                 {{ $holiday->is_active ? 'translate-x-6' : 'translate-x-1' }}
                                                 group-hover/toggle:scale-110">
                                        
                                        {{-- Icône à l'intérieur du cercle --}}
                                        <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold
                                                     {{ $holiday->is_active ? 'text-purple-600' : 'text-gray-400' }}">
                                            {{ $holiday->is_active ? '✓' : '✕' }}
                                        </span>
                                    </span>
                                    
                                    {{-- Indicateur de statut - version améliorée avec animation --}}
                                    <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] font-semibold 
                                                 transition-all duration-300 opacity-0 group-hover/toggle:opacity-100
                                                 {{ $holiday->is_active ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500' }}">
                                        {{ $holiday->is_active ? 'ACTIF' : 'INACTIF' }}
                                    </span>
                                </button>
                            </form>
                            @else
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full
                                         {{ $holiday->is_active
                                            ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                                            : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $holiday->is_active ? 'Oui' : 'Non' }}
                            </span>
                            @endcan
                        </td>

                        {{-- Notifié --}}
                        <td class="px-6 py-4 text-center">
                            @if($holiday->notified)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Notifié
                            </span>
                            @else
                            <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Non notifié</span>
                            @endif

                            @if(($holiday->exemptions_count ?? 0) > 0)
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 dark:text-red-400">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    {{ $holiday->exemptions_count }} appelé(s)
                                </span>
                            </div>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @can('holidays.toggle')
                                @if($holiday->is_active)
                                <button type="button"
                                        onclick="openEmergencyModal({{ $holiday->id }}, '{{ $holiday->label }}', '{{ $holiday->date->format('Y-m-d') }}')"
                                        title="Appel d'urgence"
                                        class="p-2 rounded-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </button>
                                @endif
                                @endcan

                                @can('holidays.edit')
                                <a href="{{ route('admin.holidays.edit', $holiday) }}"
                                   title="Modifier"
                                   class="p-2 rounded-lg text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('holidays.toggle')
                                @if($holiday->is_active)
                                <form method="POST" action="{{ route('admin.holidays.notify.post', $holiday) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            title="{{ $holiday->notified ? 'Renvoyer la notification' : 'Publier la notification à tous les utilisateurs actifs' }}"
                                            class="p-2 rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan

                                @can('holidays.delete')
                                <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Supprimer"
                                            class="p-2 rounded-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <td colspan="6" class="px-6 py-20 text-center">
                            @include('admin.holidays._empty')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Version mobile : cartes ──────────── --}}
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700/60">
            @forelse($holidays as $holiday)
            <div class="p-4 {{ $holiday->is_active ? 'bg-purple-50/40 dark:bg-purple-900/10' : '' }}">

                {{-- Ligne 1 : icône + libellé + badges --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600
                                flex items-center justify-center text-white font-bold text-sm select-none">
                        {{ mb_strtoupper(mb_substr($holiday->label, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $holiday->label }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </p>
                        @if($holiday->description)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 line-clamp-2">{{ $holiday->description }}</p>
                        @endif
                    </div>

                    {{-- Status badges --}}
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        @can('holidays.toggle')
                        <form method="POST" action="{{ route('admin.holidays.toggle', $holiday) }}">
                            @csrf
                            <button type="submit"
                                    class="relative group/toggle inline-flex h-6 w-11 items-center rounded-full transition-all duration-300
                                           {{ $holiday->is_active 
                                              ? 'bg-gradient-to-r from-purple-500 to-indigo-600 shadow-md shadow-purple-500/30' 
                                              : 'bg-gray-300 dark:bg-gray-600' }}
                                           hover:scale-105 active:scale-95">
                                
                                <span class="sr-only">Toggle</span>
                                
                                {{-- Effet de glow --}}
                                <span class="absolute inset-0 rounded-full opacity-0 group-hover/toggle:opacity-100 transition-opacity duration-300
                                             {{ $holiday->is_active ? 'bg-purple-400/20 blur-md' : 'bg-gray-400/20 blur-md' }}">
                                </span>
                                
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out
                                             {{ $holiday->is_active ? 'translate-x-5' : 'translate-x-0.5' }}
                                             group-hover/toggle:scale-110">
                                    
                                    {{-- Icône à l'intérieur du cercle --}}
                                    <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold
                                                 {{ $holiday->is_active ? 'text-purple-600' : 'text-gray-400' }}">
                                        {{ $holiday->is_active ? '✓' : '✕' }}
                                    </span>
                                </span>
                            </button>
                        </form>
                        @endcan

                        @if($holiday->notified)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Notifié
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Ligne 2 : actions --}}
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60">
                    @can('holidays.toggle')
                    @if($holiday->is_active)
                    <button type="button"
                            onclick="openEmergencyModal({{ $holiday->id }}, '{{ $holiday->label }}', '{{ $holiday->date->format('Y-m-d') }}')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                                   text-xs font-medium text-red-600 dark:text-red-400
                                   bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Urgence
                    </button>
                    @endif
                    @endcan

                    @can('holidays.edit')
                    <a href="{{ route('admin.holidays.edit', $holiday) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                              text-xs font-medium text-amber-600 dark:text-amber-400
                              bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                                       text-xs font-medium text-blue-600 dark:text-blue-400
                                       bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            {{ $holiday->notified ? 'Renvoyer' : 'Publier' }}
                        </button>
                    </form>
                    @endif
                    @endcan

                    @can('holidays.delete')
                    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" data-confirm-delete>
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center p-2 rounded-lg
                                       text-red-600 dark:text-red-400
                                       bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @empty
            <div class="px-6 py-20 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 mb-4">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-700 dark:text-gray-200 font-semibold">Aucun jour férié enregistré</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Ajoutez un jour férié pour commencer.</p>
            </div>
            @endforelse
        </div>

        {{-- ── Pagination ───────────────────────── --}}
        @if($holidays->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $holidays->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL — Appel d'Urgence
     ═══════════════════════════════════════════════ --}}
<div id="emergencyModal"
     class="fixed inset-0 z-50 hidden"
     aria-modal="true" role="dialog" aria-labelledby="emergencyModalTitle">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEmergencyModal()"></div>

    {{-- Panneau --}}
    <div class="relative min-h-screen flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="relative w-full sm:max-w-2xl
                    bg-white dark:bg-gray-800
                    rounded-t-2xl sm:rounded-2xl
                    shadow-2xl
                    ring-1 ring-gray-900/10 dark:ring-white/10
                    flex flex-col max-h-[92dvh] sm:max-h-[90vh]">

            {{-- En-tête modal --}}
            <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div>
                    <h2 id="emergencyModalTitle" class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-100 dark:bg-red-900/30">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </span>
                        Appel d'urgence
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 ml-9">
                        <span id="emergencyHolidayLabel" class="font-medium text-gray-700 dark:text-gray-300"></span>
                        <span class="mx-1">·</span>
                        <span id="emergencyHolidayDate"></span>
                    </p>
                </div>
                <button onclick="closeEmergencyModal()"
                        class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Corps modal --}}
            <form id="emergencyForm" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="holiday_id" id="emergencyHolidayId">

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

                    {{-- Recherche --}}
                    <div>
                        <label for="userSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Rechercher des personnes
                        </label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="userSearch" placeholder="Nom ou adresse e-mail…"
                                   class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl
                                          border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700/60
                                          text-gray-900 dark:text-white
                                          placeholder-gray-400 dark:placeholder-gray-500
                                          focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                          transition">
                        </div>
                    </div>

                    {{-- Filtres --}}
                    <div class="flex gap-2">
                        <button type="button" onclick="filterUsers('all')" data-filter="all"
                                class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                       bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                            Tous
                        </button>
                        <button type="button" onclick="filterUsers('employe')" data-filter="employe"
                                class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                       bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                            Employés
                        </button>
                        <button type="button" onclick="filterUsers('etudiant')" data-filter="etudiant"
                                class="filter-btn px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                       bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                            Stagiaires
                        </button>
                    </div>

                    {{-- Liste utilisateurs --}}
                    <div id="userList"
                         class="space-y-1 max-h-56 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-600 p-2
                                scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
                        <div class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">Chargement…</div>
                    </div>

                    {{-- Message personnalisé --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Message
                            <span class="ml-1 text-xs font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <textarea name="message" rows="3"
                                  placeholder="Ex : Urgence maintenance atelier. Veuillez vous présenter au site principal."
                                  class="w-full px-4 py-2.5 text-sm rounded-xl resize-none
                                         border border-gray-300 dark:border-gray-600
                                         bg-white dark:bg-gray-700/60
                                         text-gray-900 dark:text-white
                                         placeholder-gray-400 dark:placeholder-gray-500
                                         focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                         transition"></textarea>
                    </div>

                    {{-- Personnes déjà appelées --}}
                    <div id="alreadyCalledSection" class="hidden">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                Déjà appelés en urgence
                            </span>
                            <span id="alreadyCalledCount" class="inline-flex items-center justify-center min-w-6 h-6 px-1.5 rounded-full bg-red-100 dark:bg-red-900/30 text-xs font-bold text-red-700 dark:text-red-400"></span>
                        </div>
                        <div id="alreadyCalledList" class="space-y-1.5"></div>
                    </div>
                </div>

                {{-- Pied modal --}}
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3 shrink-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 shrink-0">
                        <span id="selectedCount" class="font-semibold text-gray-700 dark:text-gray-300">0</span> sélectionné(s)
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeEmergencyModal()"
                                class="px-4 py-2 text-sm font-medium rounded-xl
                                       bg-gray-100 dark:bg-gray-700
                                       text-gray-700 dark:text-gray-300
                                       hover:bg-gray-200 dark:hover:bg-gray-600
                                       transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl
                                       bg-gradient-to-r from-red-500 to-red-600
                                       hover:from-red-600 hover:to-red-700
                                       text-white shadow-lg shadow-red-600/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    let allUsers    = [];
    let currentFilter = 'all';
    let currentExemptions = [];

    /* ── Exemptions par jour férié (sérialisées depuis le serveur) ── */
    const holidayExemptions = @json($exemptionsByHoliday);

    /* ── Ouvrir / fermer ──────────────────────── */
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

    /* Fermer avec Escape */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeEmergencyModal();
    });

    /* ── Rendu des personnes déjà appelées ────── */
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
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-red-50/60 dark:bg-red-900/15 border border-red-100 dark:border-red-800/50">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${esc(e.name)}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        ${esc(e.email)}${e.message ? ' — ' + esc(e.message) : ''}
                    </p>
                </div>
                <form method="POST" action="/admin/holidays/exemptions/${e.id}" onsubmit="return confirm('Révoquer cette autorisation d\\'urgence ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Révoquer l'appel"
                            class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium
                                   text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30
                                   hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    /* ── Chargement des utilisateurs ─────────── */
    function loadUsers() {
        const list = document.getElementById('userList');
        list.innerHTML = '<div class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">Chargement…</div>';

        fetch('{{ route("admin.holidays.users-list") }}')
            .then(r => r.json())
            .then(users => { allUsers = users; renderUsers(); })
            .catch(() => {
                list.innerHTML = '<div class="text-center text-sm text-red-500 dark:text-red-400 py-8">Erreur de chargement.</div>';
            });
    }

    /* ── Rendu de la liste ────────────────────── */
    function renderUsers() {
        const list   = document.getElementById('userList');
        const search = (document.getElementById('userSearch').value || '').toLowerCase().trim();

        const filtered = allUsers.filter(u => {
            if (currentFilter !== 'all' && u.role_name !== currentFilter) return false;
            if (search && !u.name.toLowerCase().includes(search) && !u.email.toLowerCase().includes(search)) return false;
            return true;
        });

        if (!filtered.length) {
            list.innerHTML = '<div class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">Aucun utilisateur trouvé.</div>';
            return;
        }

        list.innerHTML = filtered.map(u => {
            const isStudent = u.role_name === 'etudiant';
            const badgeCls  = isStudent
                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';

            return `
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer
                          hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors
                          border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                <input type="checkbox" name="user_ids[]" value="${u.id}" onchange="updateCount()"
                       class="w-4 h-4 rounded text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500 shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${u.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${u.email}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0 ${badgeCls}">${u.role}</span>
            </label>`;
        }).join('');
    }

    /* ── Compteur --*/
    function updateCount() {
        const n = document.querySelectorAll('#userList input[type="checkbox"]:checked').length;
        document.getElementById('selectedCount').textContent = n;
    }

    /* ── Filtres ──────────────────────────────── */
    function filterUsers(role) {
        currentFilter = role;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            const active = btn.dataset.filter === role;
            btn.classList.toggle('bg-purple-100',           active);
            btn.classList.toggle('text-purple-700',         active);
            btn.classList.toggle('dark:bg-purple-900/30',   active);
            btn.classList.toggle('dark:text-purple-400',    active);
            btn.classList.toggle('bg-gray-100',             !active);
            btn.classList.toggle('text-gray-600',           !active);
            btn.classList.toggle('dark:bg-gray-700',        !active);
            btn.classList.toggle('dark:text-gray-400',      !active);
        });
        renderUsers();
    }

    document.getElementById('userSearch')?.addEventListener('input', renderUsers);
</script>
@endpush

</x-app-layout>