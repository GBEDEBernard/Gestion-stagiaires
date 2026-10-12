<x-app-layout>
<div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]" x-data="logsPage()" x-init="init()">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#94a3b8]">Supervision technique</p>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">Journaux système</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-[#94a3b8] mt-0.5">Erreurs, avertissements, envois d'emails et activité applicative.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full border
                {{ $logFileExists ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' }}">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $logFileExists ? 'bg-emerald-400' : 'bg-rose-400' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $logFileExists ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                </span>
                {{ $logFileExists ? 'Surveillance active' : 'Fichier introuvable' }}
            </span>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-3.5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-[#94a3b8]">Total</p>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1" x-text="stats.total">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-rose-200/60 dark:border-rose-900/40 bg-rose-50/50 dark:bg-rose-950/20 p-3.5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Erreurs</p>
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1" x-text="stats.error">{{ $stats['error'] }}</p>
        </div>
        <div class="rounded-xl border border-amber-200/60 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/20 p-3.5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Avertissements</p>
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1" x-text="stats.warning">{{ $stats['warning'] }}</p>
        </div>
        <div class="rounded-xl border border-sky-200/60 dark:border-sky-900/40 bg-sky-50/50 dark:bg-sky-950/20 p-3.5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400">Infos</p>
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-bold text-sky-600 dark:text-sky-400 mt-1" x-text="stats.info">{{ $stats['info'] }}</p>
        </div>
    </div>

    {{-- ── Toolbar ── --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-2.5 flex flex-wrap items-center gap-2 shadow-xs">
        {{-- Filtre Durée --}}
        <div class="flex items-center gap-0.5 bg-slate-100 dark:bg-[#131519] rounded-lg p-1">
            <template x-for="opt in [['1','1m'],['5','5m'],['15','15m'],['60','1h'],['1440','24h']]" :key="opt[0]">
                <button @click="minutes = opt[0]; load()"
                    type="button"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition"
                    :class="minutes === opt[0] ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-[#94a3b8] hover:bg-slate-200 dark:hover:bg-[#1f232d]'"
                    x-text="opt[1]"></button>
            </template>
        </div>

        {{-- Filtre Niveau --}}
        <div class="flex items-center gap-0.5 bg-slate-100 dark:bg-[#131519] rounded-lg p-1">
            <template x-for="opt in [['all','Tous'],['error','Erreurs'],['warning','Avert.'],['info','Infos'],['debug','Debug']]" :key="opt[0]">
                <button @click="level = opt[0]; load()"
                    type="button"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition"
                    :class="level === opt[0] ? 'bg-slate-800 dark:bg-slate-700 text-white shadow-xs' : 'text-slate-600 dark:text-[#94a3b8] hover:bg-slate-200 dark:hover:bg-[#1f232d]'"
                    x-text="opt[1]"></button>
            </template>
        </div>

        {{-- Barre de recherche --}}
        <div class="relative flex-1 min-w-[160px]">
            <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
            <input type="text" x-model.debounce.400ms="search" @input="load()"
                placeholder="Rechercher dans les logs…"
                class="w-full text-xs rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 pl-8.5 py-1.5 placeholder:text-slate-400">
        </div>

        {{-- Auto-refresh toggle --}}
        <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 dark:text-[#94a3b8] cursor-pointer select-none px-1">
            <input type="checkbox" x-model="autoRefresh" @change="toggleAuto()"
                class="rounded border-slate-300 dark:border-[#222630] text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
            Auto
        </label>

        {{-- Bouton Actualiser --}}
        <button @click="load()" type="button" class="p-1.5 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] hover:bg-slate-50 dark:hover:bg-[#1f232d] text-slate-600 dark:text-[#94a3b8] transition" title="Actualiser">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>

        {{-- Bouton Vider tout --}}
        <button @click="openModal('clearAll')"
                type="button"
                class="p-1.5 rounded-lg border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 transition"
                title="Vider tous les logs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </div>

    {{-- ── Timeline des Logs ── --}}
    <div class="space-y-2.5" x-show="entries.length > 0">
        <template x-for="(log, i) in entries" :key="i">
            <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
                <div class="flex items-start gap-3 p-3.5">
                    {{-- Icone niveau --}}
                    <div class="shrink-0 w-7 h-7 rounded-lg flex items-center justify-center mt-0.5"
                        :class="{
                            'bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400': log.level === 'error',
                            'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400': log.level === 'warning',
                            'bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400': log.level === 'info',
                            'bg-slate-100 dark:bg-[#1f232d] text-slate-500 dark:text-[#94a3b8]': log.level === 'debug'
                        }">
                        <svg x-show="log.level === 'error'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <svg x-show="log.level === 'warning'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.7-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                        <svg x-show="log.level === 'info'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="log.level === 'debug'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 8l-4 4 4 4"/></svg>
                    </div>

                    {{-- Contenu du log --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded"
                                :class="{
                                    'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300': log.level === 'error',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300': log.level === 'warning',
                                    'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300': log.level === 'info',
                                    'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300': log.level === 'debug'
                                }" x-text="log.level"></span>
                            <span class="text-[11px] text-slate-400 font-mono" x-text="log.date"></span>
                            <span class="text-[10px] text-slate-300 dark:text-slate-600">•</span>
                            <span class="text-[11px] text-slate-400 dark:text-[#94a3b8]" x-text="relativeTime(log.date)"></span>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-[#f8fafc] mt-1.5 font-mono leading-relaxed break-words" x-text="log.message"></p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button x-show="log.context" @click="log._open = !log._open"
                            type="button"
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition">
                            <svg class="w-3.5 h-3.5 transition-transform" :class="log._open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            <span x-text="log._open ? 'Masquer' : 'Détails'"></span>
                        </button>
                        <button @click="openModal('deleteOne', log.id)"
                            type="button"
                            class="p-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 text-slate-400 hover:text-rose-500 transition"
                            title="Supprimer ce log">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Détails contextuels JSON --}}
                <div x-show="log._open" x-cloak x-collapse class="border-t border-slate-200 dark:border-[#222630] bg-slate-50/70 dark:bg-[#131519] relative">
                    <button @click="copy(JSON.stringify(log.context, null, 2), $event)"
                        type="button"
                        class="absolute top-2 right-2 text-[10px] font-semibold px-2 py-0.5 rounded-md bg-[#fffefa] dark:bg-[#161a22] border border-slate-200 dark:border-[#222630] text-slate-600 dark:text-[#94a3b8] hover:text-slate-900 shadow-xs">
                        Copier
                    </button>
                    <pre class="text-[11px] text-slate-700 dark:text-[#94a3b8] p-3.5 overflow-auto max-h-64 font-mono leading-relaxed" x-text="JSON.stringify(log.context, null, 2)"></pre>
                </div>
            </div>
        </template>
    </div>

    {{-- Empty state --}}
    <div x-show="entries.length === 0" x-cloak
        class="rounded-xl border border-dashed border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-10 text-center">
        <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-xs text-slate-400">Aucune entrée pour cette période ou ce filtre.</p>
    </div>

    {{-- ===== MODAL DE CONFIRMATION ===== --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/60 backdrop-blur-sm"
         @click.self="closeModal()">
        <div class="bg-[#fffefa] dark:bg-[#161a22] border border-slate-200 dark:border-[#222630] rounded-2xl shadow-2xl max-w-sm w-full p-5 transform transition-all"
             x-transition:enter.duration.200ms>
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]" x-text="modalTitle"></h3>
                    <p class="text-xs text-slate-500 dark:text-[#94a3b8] mt-1" x-text="modalMessage"></p>
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button @click="closeModal()"
                        type="button"
                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-[#f8fafc] hover:bg-slate-100 dark:hover:bg-[#1f232d] rounded-lg transition border border-slate-200 dark:border-[#222630]">
                    Annuler
                </button>
                <button @click="confirmAction()"
                        type="button"
                        class="px-3 py-1.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition shadow-xs">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>

</div>

@php
    $logsForJs = collect($entries)->map(function ($e) {
        $id = md5($e['date'] . $e['message']);
        return [
            'id'      => $id,
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
            modalOpen: false,
            modalAction: null,
            modalTargetId: null,
            modalTitle: '',
            modalMessage: '',
            modalConfirmText: '',

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
            },

            openModal(action, id = null) {
                this.modalAction = action;
                this.modalTargetId = id;
                if (action === 'deleteOne') {
                    this.modalTitle = 'Supprimer ce log';
                    this.modalMessage = 'Voulez-vous vraiment supprimer cette entrée ? Cette action est irréversible.';
                    this.modalConfirmText = 'Supprimer';
                } else if (action === 'clearAll') {
                    this.modalTitle = 'Vider tous les logs';
                    this.modalMessage = 'Voulez-vous vraiment vider toutes les entrées du journal ? Cette action est irréversible.';
                    this.modalConfirmText = 'Tout vider';
                }
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
                this.modalAction = null;
                this.modalTargetId = null;
            },

            confirmAction() {
                if (this.modalAction === 'deleteOne') {
                    const found = this.entries.find(e => e.id === this.modalTargetId);
                    if (found) {
                        fetch(`{{ route('admin.logs.delete') }}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ date: found.date, message: found.message })
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                this.entries = this.entries.filter(e => e.id !== this.modalTargetId);
                                if (this.stats[found.level] !== undefined && this.stats[found.level] > 0) {
                                    this.stats[found.level]--;
                                }
                                if (this.stats.total > 0) this.stats.total--;
                            }
                        })
                        .finally(() => this.closeModal());
                    }
                } else if (this.modalAction === 'clearAll') {
                    fetch(`{{ route('admin.logs.clear') }}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            this.entries = [];
                            this.stats = { total: 0, error: 0, warning: 0, info: 0 };
                        }
                    })
                    .finally(() => this.closeModal());
                }
            }
        };
    }
</script>
</x-app-layout>