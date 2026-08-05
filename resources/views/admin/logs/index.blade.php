<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    Journaux système
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Erreurs, envois d'emails et activité applicative en temps quasi réel
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
                {{ $logFileExists ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $logFileExists ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                {{ $logFileExists ? 'Fichier de logs actif' : 'Aucun fichier trouvé' }}
            </span>
        </div>
    </x-slot>

    <div class="py-8" x-data="logsPage()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="stats.total">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-900/40 p-4">
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium">Erreurs</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400" x-text="stats.error">{{ $stats['error'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-amber-200 dark:border-amber-900/40 p-4">
                    <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Avertissements</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400" x-text="stats.warning">{{ $stats['warning'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-sky-200 dark:border-sky-900/40 p-4">
                    <p class="text-xs text-sky-600 dark:text-sky-400 font-medium">Infos</p>
                    <p class="text-2xl font-bold text-sky-600 dark:text-sky-400" x-text="stats.info">{{ $stats['info'] }}</p>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex flex-wrap items-center gap-3">
                <select x-model="minutes" @change="load()"
                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 focus:ring-teal-500 focus:border-teal-500">
                    <option value="1">Dernière minute</option>
                    <option value="5">5 dernières minutes</option>
                    <option value="15">15 dernières minutes</option>
                    <option value="60" selected>Dernière heure</option>
                    <option value="1440">Dernières 24h</option>
                </select>

                <select x-model="level" @change="load()"
                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 focus:ring-teal-500 focus:border-teal-500">
                    <option value="all">Tous les niveaux</option>
                    <option value="error">Erreurs</option>
                    <option value="warning">Avertissements</option>
                    <option value="info">Infos</option>
                </select>

                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" x-model.debounce.400ms="search" @input="load()"
                        placeholder="Rechercher (message, route, ID...)"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 focus:ring-teal-500 focus:border-teal-500 pl-9">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer select-none">
                    <input type="checkbox" x-model="autoRefresh" @change="toggleAuto()"
                        class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    Auto-refresh
                </label>

                <button @click="load()" class="ml-auto text-sm px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition">
                    Actualiser
                </button>
            </div>

            {{-- Liste des logs --}}
            <div class="space-y-2" x-show="entries.length > 0">
                <template x-for="(log, i) in entries" :key="i">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border-l-4 shadow-sm p-3"
                        :class="{
                            'border-l-red-500': log.level === 'error',
                            'border-l-amber-500': log.level === 'warning',
                            'border-l-sky-500': log.level === 'info',
                            'border-l-gray-400': !['error','warning','info'].includes(log.level)
                        }">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                                    :class="{
                                        'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400': log.level === 'error',
                                        'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400': log.level === 'warning',
                                        'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400': log.level === 'info'
                                    }" x-text="log.level"></span>
                                <span class="text-xs text-gray-400 dark:text-gray-500" x-text="log.date"></span>
                            </div>
                            <button x-show="log.context" @click="log._open = !log._open" class="text-xs text-teal-600 hover:underline">
                                <span x-text="log._open ? 'Masquer détails' : 'Voir détails'"></span>
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-200 mt-1.5 font-mono leading-relaxed" x-text="log.message"></p>
                        <pre x-show="log._open" x-cloak
                            class="mt-2 text-xs bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300 p-3 rounded-md overflow-auto max-h-64"
                            x-text="JSON.stringify(log.context, null, 2)"></pre>
                    </div>
                </template>
            </div>

            {{-- Empty state --}}
            <div x-show="entries.length === 0" x-cloak
                class="bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">Aucune entrée pour cette période / ce filtre 👍</p>
            </div>
        </div>
    </div>

  @php
    $logsForJs = collect($entries)->map(function ($e) {
        return [
            'date'    => $e['date'] instanceof \Carbon\Carbon
                            ? $e['date']->format('Y-m-d H:i:s')
                            : (string) $e['date'],
            'level'   => $e['level'],
            'message' => $e['message'],
            'context' => $e['context'],
        ];
    })->values();
@endphp

<script>
    function logsPage() {
        return {
            entries: @json($logsForJs),
            stats: @json($stats),
            minutes: 60,
            level: 'all',
            search: '',
            autoRefresh: true,
            timer: null,

            init() {
                this.toggleAuto();
            },
            load() {
                fetch(`{{ route('admin.logs.index') }}?minutes=${this.minutes}&level=${this.level}&search=${encodeURIComponent(this.search)}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    this.entries = data.entries.map(e => ({ ...e, _open: false }));
                    this.stats = data.stats;
                });
            },
            toggleAuto() {
                clearInterval(this.timer);
                if (this.autoRefresh) {
                    this.timer = setInterval(() => this.load(), 15000);
                }
            }
        }
    }
</script>
</x-app-layout>