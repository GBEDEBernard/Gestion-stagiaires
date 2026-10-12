<x-app-layout title="Détail du Résumé IA">

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            padding: .4rem .85rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 500;
            padding: .4rem .85rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        @keyframes ks-spin {
            to { transform: rotate(360deg); }
        }
        .ks-animate-spin { animation: ks-spin 1s linear infinite; }
    </style>

    <div class="w-full space-y-3 pb-6">

        {{-- ── NOTIFICATIONS FLASH ── --}}
        @if(session('success'))
            <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold ml-4">✕</button>
            </div>
        @endif

        {{-- ── BARRE DE NAVIGATION & ACTIONS ── --}}
        <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('summaries.index') }}" class="ks-btn-secondary ks-btn-xs !px-2" title="Retour aux résumés">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-bold text-[#241f1b] dark:text-white leading-tight">
                            Résumé {{ __($summary->period_type) }}
                        </h1>
                        @php
                            $badgeBg = match($summary->period_type) {
                                'weekly' => 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border-[#d8e0f5] dark:border-blue-900/50',
                                'monthly' => 'bg-[#f5f3ff] dark:bg-purple-950/40 text-[#6d28d9] dark:text-purple-300 border-[#e9d5ff] dark:border-purple-900/50',
                                'yearly' => 'bg-[#f0fdf4] dark:bg-emerald-950/40 text-[#15803d] dark:text-emerald-300 border-[#bbf7d0] dark:border-emerald-900/50',
                                default => 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border-[#d8e0f5] dark:border-blue-900/50',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider {{ $badgeBg }}">
                            {{ ucfirst(__($summary->period_type)) }}
                        </span>
                    </div>
                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">
                        Période couverte : {{ $summary->period_start->format('d/m/Y') }} → {{ $summary->period_end->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Regénérer --}}
                <form action="{{ route('summaries.generate') }}" method="POST" class="generate-form inline">
                    @csrf
                    <input type="hidden" name="period" value="{{ $summary->period_type }}">
                    <button type="submit" class="generate-btn ks-btn-primary ks-btn-xs cursor-pointer" title="Regénérer ce résumé avec les dernières données">
                        <span class="generate-text flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Regénérer
                        </span>
                        <span class="generate-spinner hidden">
                            <svg class="ks-animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                    </button>
                </form>

                {{-- Supprimer --}}
                <form action="{{ route('summaries.destroy', $summary) }}" method="POST"
                      onsubmit="return confirm('Confirmer la suppression définitive de ce résumé ?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="ks-btn-secondary ks-btn-xs !px-2.5 text-rose-600 dark:text-rose-400 hover:border-rose-400 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        {{-- ── CARTE PRINCIPALE ── --}}
        <div class="ks-sec overflow-hidden">

            {{-- Métadonnées compactes en bandeau --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-[#ecebe3] dark:divide-[#222630] bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#ecebe3] dark:border-[#222630] text-xs">
                <div class="p-2.5 sm:p-3">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Période</div>
                    <div class="font-bold text-[#241f1b] dark:text-white mt-0.5 text-xs truncate">
                        {{ $summary->period_start->format('d/m/Y') }} → {{ $summary->period_end->format('d/m/Y') }}
                    </div>
                </div>

                <div class="p-2.5 sm:p-3">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Modèle IA</div>
                    <div class="font-mono text-[#1c3e96] dark:text-blue-300 font-semibold mt-0.5 text-xs truncate">
                        {{ $summary->model_used ?? 'gpt-4o-mini' }}
                    </div>
                </div>

                <div class="p-2.5 sm:p-3">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Génération</div>
                    <div class="font-medium text-[#241f1b] dark:text-white mt-0.5 text-xs truncate">
                        {{ $summary->generated_at?->format('d/m/Y à H:i') ?? '—' }}
                    </div>
                </div>

                <div class="p-2.5 sm:p-3">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Données sources</div>
                    <div class="font-bold text-emerald-700 dark:text-emerald-400 mt-0.5 text-xs truncate">
                        @if(is_array($summary->raw_data) && count($summary->raw_data) > 0)
                            {{ count($summary->raw_data) }} rapports ({{ collect($summary->raw_data)->sum('hours_declared') }}h)
                        @else
                            Aucun rapport
                        @endif
                    </div>
                </div>
            </div>

            {{-- Corps du résumé --}}
            <div class="p-4 sm:p-6 space-y-3.5">
                @php
                    $lines = explode("\n", $summary->summary);
                @endphp

                <div class="space-y-2 text-xs sm:text-sm text-[#241f1b] dark:text-slate-200 leading-relaxed">
                    @foreach($lines as $line)
                        @php $trimmed = trim($line); @endphp

                        @if(preg_match('/^(?:###|\d+\.|\*\*)\s*(.+)/', $trimmed, $m) && (str_contains($trimmed, '**') || str_starts_with($trimmed, '###')))
                            {{-- Titre de section --}}
                            @php
                                $cleanTitle = trim($trimmed, "#* ");
                            @endphp
                            <div class="pt-3 pb-1 first:pt-0">
                                <h2 class="text-sm sm:text-base font-bold text-[#1c3e96] dark:text-blue-300 flex items-center gap-2 border-b border-[#ecebe3] dark:border-[#222630] pb-1.5">
                                    <span class="w-1.5 h-3.5 rounded bg-[#1c3e96] dark:bg-blue-400"></span>
                                    {{ $cleanTitle }}
                                </h2>
                            </div>
                        @elseif(preg_match('/^[*-]\s+(.+)/', $trimmed, $m))
                            {{-- Puce de liste --}}
                            <div class="flex items-start gap-2 pl-2 sm:pl-3 my-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1c3e96] dark:bg-blue-400 mt-1.5 shrink-0"></span>
                                <div class="text-xs sm:text-[13px] text-[#374151] dark:text-slate-300">
                                    {!! preg_replace('/\*\*(.+?)\*\*/', '<strong class="font-bold text-[#111827] dark:text-white">$1</strong>', e($m[1])) !!}
                                </div>
                            </div>
                        @elseif($trimmed !== '')
                            {{-- Paragraphe standard --}}
                            <p class="text-xs sm:text-[13px] text-[#374151] dark:text-slate-300">
                                {!! preg_replace('/\*\*(.+?)\*\*/', '<strong class="font-bold text-[#111827] dark:text-white">$1</strong>', e($trimmed)) !!}
                            </p>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Accordéon des rapports sources inclus --}}
            @if(is_array($summary->raw_data) && count($summary->raw_data) > 0)
                <div class="border-t border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6]/60 dark:bg-[#181c24]/60" x-data="{ openSources: false }">
                    <button type="button" @click="openSources = !openSources"
                            class="w-full px-4 py-2.5 flex items-center justify-between text-xs font-semibold text-[#6e6862] dark:text-slate-300 hover:text-[#1c3e96] dark:hover:text-white transition cursor-pointer">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#1c3e96] dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Voir les {{ count($summary->raw_data) }} rapports journaliers inclus dans cette synthèse
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="openSources ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openSources" x-collapse class="px-4 pb-4 space-y-2">
                        @foreach($summary->raw_data as $report)
                            <div class="p-3 rounded-lg bg-white dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] text-xs space-y-1">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-[#1c3e96] dark:text-blue-300">{{ $report['date'] ?? 'Date inconnue' }}</span>
                                        <span class="font-semibold text-[#241f1b] dark:text-white">{{ $report['title'] ?? 'Sans titre' }}</span>
                                    </div>
                                    <span class="text-[11px] font-mono text-[#6e6862] dark:text-slate-400">
                                        ⏱️ {{ $report['hours_declared'] ?? 0 }}h déclarées
                                    </span>
                                </div>

                                @if(!empty($report['task_title']))
                                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400">
                                        Tâche : <span class="font-medium text-[#241f1b] dark:text-slate-300">{{ $report['task_title'] }}</span>
                                    </p>
                                @endif

                                @if(!empty($report['summary']))
                                    <p class="text-xs text-[#4b5563] dark:text-slate-300">
                                        {{ Str::limit($report['summary'], 160) }}
                                    </p>
                                @endif

                                @if(!empty($report['blockers']))
                                    <div class="text-[11px] text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/30 p-1.5 rounded border border-rose-200 dark:border-rose-900/40">
                                        <span class="font-bold">Blocage :</span> {{ $report['blockers'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    <script>
        document.querySelectorAll('.generate-form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.generate-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.querySelector('.generate-text')?.classList.add('hidden');
                    btn.querySelector('.generate-spinner')?.classList.remove('hidden');
                }
            });
        });
    </script>
</x-app-layout>
