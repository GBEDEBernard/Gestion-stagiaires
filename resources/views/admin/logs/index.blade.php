<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    Journaux système
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Erreurs, envois d'emails et activité applicative
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full
                {{ $logFileExists ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $logFileExists ? 'bg-emerald-400' : 'bg-red-400' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $logFileExists ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                </span>
                {{ $logFileExists ? 'Surveillance active' : 'Fichier introuvable' }}
            </span>
        </div>
    </x-slot>

    <div class="py-8" x-data="logsPage()" x-init="init()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total</p>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2" x-text="stats.total">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-100 dark:border-red-900/30 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-red-500 uppercase tracking-wide">Erreurs</p>
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p class="text-3xl font-bold text-red-500 mt-2" x-text="stats.error">{{ $stats['error'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-amber-100 dark:border-amber-900/30 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-amber-500 uppercase tracking-wide">Avertissements</p>
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-3xl font-bold text-amber-500 mt-2" x-text="stats.warning">{{ $stats['warning'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-sky-100 dark:border-sky-900/30 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-sky-500 uppercase tracking-wide">Infos</p>
                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-3xl font-bold text-sky-500 mt-2" x-text="stats.info">{{ $stats['info'] }}</p>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="sticky top-0 z-10 bg-white/90 dark:bg-gray-800/90 backdrop-blur border border-gray-100 dark:border-gray-700/60 rounded-2xl p-3 shadow-sm flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900/50 rounded-xl p-1">
                    <template x-for="opt in [['1','1m'],['5','5m'],['15','15m'],['60','1h'],['1440','24h']]" :key="opt[0]">
                        <button @click="minutes = opt[0]; load()"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition"
                            :class="minutes === opt[0] ? 'bg-teal-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            x-text="opt[1]"></button>
                    </template>
                </div>

                <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900/50 rounded-xl p-1">
                    <template x-for="opt in [['all','Tous'],['error','Erreurs'],['warning','Avert.'],['info','Infos'],['debug','Debug']]" :key="opt[0]">
                        <button @click="level = opt[0]; load()"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition"
                            :class="level === opt[0] ? 'bg-gray-800 dark:bg-gray-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            x-text="opt[1]"></button>
                    </template>
                </div>

                <div class="relative flex-1 min-w-[180px]">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" x-model.debounce.400ms="search" @input="load()"
                        placeholder="Rechercher..."
                        class="w-full text-sm rounded-xl border-0 bg-gray-50 dark:bg-gray-900/50 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 pl-9 py-2 placeholder:text-gray-400">
                </div>

                <label class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 cursor-pointer select-none px-2">
                    <input type="checkbox" x-model="autoRefresh" @change="toggleAuto()"
                        class="rounded border-gray-300 text-teal-600 focus:ring-teal-500 w-3.5 h-3.5">
                    Auto
                </label>

                <button @click="load()" class="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Actualiser">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>

            {{-- Timeline --}}
            <div class="relative space-y-3" x-show="entries.length > 0">
                <template x-for="(log, i) in entries" :key="i">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="flex items-start gap-3 p-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center mt-0.5"
                                :class="{
                                    'bg-red-50 dark:bg-red-900/30': log.level === 'error',
                                    'bg-amber-50 dark:bg-amber-900/30': log.level === 'warning',
                                    'bg-sky-50 dark:bg-sky-900/30': log.level === 'info',
                                    'bg-gray-100 dark:bg-gray-700': log.level === 'debug'
                                }">
                                <svg x-show="log.level === 'error'" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <svg x-show="log.level === 'warning'" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.7-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                                <svg x-show="log.level === 'info'" class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg x-show="log.level === 'debug'" class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 8l-4 4 4 4"/></svg>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md"
                                        :class="{
                                            'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400': log.level === 'error',
                                            'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400': log.level === 'warning',
                                            'bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-400': log.level === 'info',
                                            'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400': log.level === 'debug'
                                        }" x-text="log.level"></span>
                                    <span class="text-xs text-gray-400 font-mono" x-text="log.date"></span>
                                    <span class="text-[11px] text-gray-300">•</span>
                                    <span class="text-[11px] text-gray-400" x-text="relativeTime(log.date)"></span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 mt-2 font-mono leading-relaxed break-words" x-text="log.message"></p>
                            </div>

                            <button x-show="log.context" @click="log._open = !log._open"
                                class="flex-shrink-0 text-xs font-medium text-teal-600 hover:text-teal-700 dark:text-teal-400 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-teal-50 dark:hover:bg-teal-900/20 transition">
                                <svg class="w-3.5 h-3.5 transition-transform" :class="log._open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <span x-text="log._open ? 'Masquer' : 'Détails'"></span>
                            </button>
                        </div>

                        {{-- Contexte / contenu brut --}}
                        <div x-show="log._open" x-cloak x-collapse class="border-t border-gray-100 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-900/40 relative">
                            <button @click="copy(JSON.stringify(log.context, null, 2), $event)"
                                class="absolute top-2 right-2 text-[11px] px-2 py-1 rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-gray-700 shadow-sm">
                                Copier
                            </button>
                            <pre class="text-xs text-gray-600 dark:text-gray-300 p-4 overflow-auto max-h-72 font-mono leading-relaxed" x-text="JSON.stringify(log.context, null, 2)"></pre>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Empty state --}}
            <div x-show="entries.length === 0" x-cloak
                class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-14 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-gray-400 text-sm">Aucune entrée pour cette période / ce filtre</p>
            </div>
        </div>
    </div>

    @php
        $logsForJs = collect($entries)->map(function ($e) {
            return [
                'date'    => $e['date'] instanceof \Carbon\Carbon ? $e['date']->format('Y-m-d H:i:s') : (string) $e['date'],
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
                minutes: '60',
                level: 'all',
                search: '',
                autoRefresh: true,
                timer: null,

                init() { this.toggleAuto(); },

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
                    if (this.autoRefresh) this.timer = setInterval(() => this.load(), 15000);
                },
                relativeTime(dateStr) {
                    const diff = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T'))) / 1000);
                    if (diff < 60) return `il y a ${diff}s`;
                    if (diff < 3600) return `il y a ${Math.floor(diff / 60)} min`;
                    if (diff < 86400) return `il y a ${Math.floor(diff / 3600)} h`;
                    return `il y a ${Math.floor(diff / 86400)} j`;
                },
                copy(text, evt) {
                    navigator.clipboard.writeText(text);
                    const btn = evt.target;
                    const original = btn.textContent;
                    btn.textContent = 'Copié !';
                    setTimeout(() => btn.textContent = original, 1200);
                }
            }
        }
    </script>
</x-app-layout>