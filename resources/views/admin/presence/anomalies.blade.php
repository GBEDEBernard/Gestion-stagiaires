<x-app-layout title="Anomalies de Présence - Supervision">

    {{-- ════════════════════════════════════════════════════════
         STYLES IDENTITÉ « MON STAGE » (COMPACT / ZERO ERREUR)
    ════════════════════════════════════════════════════════ --}}
    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }
        header { background-color: transparent !important; border-bottom: none !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; box-shadow: none !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-label { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #6e6862; }
        .dark .ks-label { color: #a89f93 !important; }

        .ks-btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; background: #1c3e96; color: #fff; border: 1px solid #1c3e96; border-radius: 6px; font-size: .85rem; font-weight: 600; padding: .4rem .9rem; min-height: 34px; transition: background-color .15s ease, transform .1s ease; }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; background: #fff; color: #241f1b; border: 1px solid #bfbab2; border-radius: 6px; font-size: .85rem; font-weight: 500; padding: .4rem .9rem; min-height: 34px; transition: all .15s ease; }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs { min-height: 28px !important; padding: .25rem .65rem !important; font-size: .74rem !important; font-weight: 600; border-radius: 5px !important; flex: none; white-space: nowrap; }

        .ks-sec { background: #fffefa; border: 1px solid #dcdad2; border-radius: 10px; box-shadow: 0 1px 2px rgba(36,31,27,.04); transition: border-color .15s ease, box-shadow .18s ease-out; }
        .dark .ks-sec { background: #131519 !important; border: 1px solid #222630 !important; border-color: #222630 !important; box-shadow: none; }
        .ks-sec-head { padding: .65rem .9rem; border-bottom: 1px solid #dcdad2; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .ks-sec-head { border-bottom: 1px solid #222630 !important; border-color: #222630 !important; }

        .ks-stat-card { border: 1px solid #dcdad2; border-radius: 10px; background-color: #fffefa; }
        .dark .ks-stat-card { background: #131519 !important; border: 1px solid #222630 !important; }

        .ks-stat-icon { width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .ks-stat-icon svg { width: 15px; height: 15px; stroke-width: 1.75; }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-stat-change { font-size: .68rem; font-weight: 600; padding: .15rem .45rem; border-radius: 4px; display: inline-flex; align-items: center; gap: .25rem; white-space: nowrap; }
        .ks-stat-change--positive { color: #15803d; background-color: #ecfdf5; }
        .dark .ks-stat-change--positive { color: #4ade80 !important; background-color: rgba(22,128,74,0.22) !important; }
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--neutral { color: #cbd5e1 !important; background-color: #262a30 !important; }
        .ks-stat-change--amber { color: #b45309; background-color: #fffbeb; }
        .dark .ks-stat-change--amber { color: #fbbf24 !important; background-color: rgba(180,83,9,0.22) !important; }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fef2f2; }
        .dark .ks-stat-change--negative { color: #f87171 !important; background-color: rgba(185,28,28,0.22) !important; }

        .an-pill { font-size: .72rem; font-weight: 500; padding: .2rem .55rem; border-radius: 999px; background: #faf9f6; color: #6e6862; border: 1px solid #dcdad2; display: inline-flex; align-items: center; gap: .25rem; }
        .dark .an-pill { background: #181c24; color: #94a3b8; border-color: #222630; }
        .an-pill b { font-weight: 700; color: #241f1b; }
        .dark .an-pill b { color: #f8fafc; }

        @media (max-width: 639px) {
            .an-desktop-table { display: none !important; }
            .an-mobile-list { display: block !important; }
        }
        @media (min-width: 640px) {
            .an-desktop-table { display: block !important; }
            .an-mobile-list { display: none !important; }
        }

        /* Dark mode overrides complets */
        .dark .text-slate-900, .dark .text-slate-800, .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-slate-600, .dark .text-slate-500, .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .border-slate-200, .dark .border-slate-300, .dark .border-gray-200 { border-color: #222630 !important; }
    </style>

    <div class="space-y-3 sm:space-y-3.5 overflow-x-hidden">
        {{-- ════════════════════════════════════════════════════════
             BANNER ANOMALIES (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-gradient-to-r from-[#1c3e96] via-[#1a3889] to-[#162f73] dark:from-[#131519] dark:via-[#161a22] dark:to-[#131519] shadow-sm">
            <div class="absolute inset-0 opacity-15 pointer-events-none"
                style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
            </div>
            <div class="relative px-3.5 sm:px-4 py-2.5 sm:py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="ks-stat-icon !w-6 !h-6 rounded bg-rose-500/20 text-rose-300 dark:bg-rose-500/30 dark:text-rose-400">
                                <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">Anomalies de Présence</h1>
                        </div>
                        <p class="text-[11px] sm:text-xs text-blue-200 dark:text-gray-400 mt-0.5">Incidents détectés et écarts de pointage à examiner</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <a href="{{ route('admin.presence.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Tableau de bord</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             BARRE DE PÉRIODE & FILTRES (COMPACTE STYLE MON STAGE)
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec p-2.5 sm:p-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                {{-- Onglets Période segmentés --}}
                <div class="inline-flex items-center p-0.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] self-start sm:self-auto">
                    @foreach([
                        ['all', 'Toutes'],
                        ['today', "Aujourd'hui"],
                        ['week', 'Semaine']
                    ] as [$val, $label])
                    <a href="?filter={{ $val }}"
                       class="px-3 py-1 text-xs font-semibold rounded-md transition-all {{ $filter === $val ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                {{-- Synthèse compacte --}}
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="badge ks-stat-change {{ $anomalies->count() > 0 ? 'ks-stat-change--negative' : 'ks-stat-change--positive' }} !text-xs !px-2.5 !py-1">
                        <span class="w-1.5 h-1.5 rounded-full {{ $anomalies->count() > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                        {{ $anomalies->count() }} anomalie{{ $anomalies->count() > 1 ? 's' : '' }}
                    </span>
                    <span class="text-xs font-medium text-[#6e6862] dark:text-[#a89f93]">
                        sur {{ $grouped->count() }} utilisateur{{ $grouped->count() > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             CONTENU : ÉTAT VIDE OU TABLEAU / CARTES PAR COLLABORATEUR
        ════════════════════════════════════════════════════════ --}}
        @if($grouped->isEmpty())
            <div class="ks-sec p-8 sm:p-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <h3 class="mt-3 text-sm sm:text-base font-semibold text-[#241f1b] dark:text-[#f8fafc]">Aucune anomalie ouverte</h3>
                <p class="mt-1 text-xs text-[#6e6862] dark:text-[#a89f93] max-w-sm mx-auto">Toutes les anomalies de présence ont été résolues ou aucun incident n'a été détecté pour cette période.</p>
                <a href="{{ route('admin.presence.index') }}" class="ks-btn-secondary ks-btn-xs mt-3 inline-flex">
                    Retour aux pointages
                </a>
            </div>
        @else
            {{-- ── SECTION PRINCIPALE DES ANOMALIES ── --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 !w-6 !h-6 rounded border border-rose-200 dark:border-rose-900">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Incidents groupés par collaborateur</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#faf9f6] dark:bg-[#181c24] text-[#6e6862] dark:text-[#a89f93] border border-[#dcdad2] dark:border-[#222630]">
                        {{ $grouped->count() }} dossier{{ $grouped->count() > 1 ? 's' : '' }}
                    </span>
                </div>

                {{-- VUE TABLETTE / DESKTOP (>= 640px) --}}
                <div class="an-desktop-table overflow-x-auto">
                    <table class="w-full text-left text-xs tracking-table">
                        <thead>
                            <tr class="border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]/80 text-[#6e6862] dark:text-[#a89f93] text-[10px] font-semibold uppercase tracking-wider">
                                <th class="px-3.5 py-2">Utilisateur</th>
                                <th class="px-3.5 py-2">Types d'anomalies</th>
                                <th class="px-3.5 py-2">Sévérité max</th>
                                <th class="px-3.5 py-2">Dernière détection</th>
                                <th class="px-3.5 py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @foreach($grouped as $i => $g)
                                @php
                                    $initial = strtoupper(substr($g['user'], 0, 2));
                                    $sevMax = $g['max_severity'];
                                    $sevClass = $sevMax >= 3 ? 'ks-stat-change--negative' : ($sevMax === 2 ? 'ks-stat-change--amber' : 'ks-stat-change--neutral');
                                    $sevDot = $sevMax >= 3 ? 'bg-rose-500' : ($sevMax === 2 ? 'bg-amber-500' : 'bg-slate-400');
                                    $sevLabel = $sevMax >= 3 ? 'Élevée' : ($sevMax === 2 ? 'Moyenne' : 'Faible');
                                @endphp
                                <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                    <td class="px-3.5 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-md bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] font-bold text-xs flex items-center justify-center flex-shrink-0 border border-[#1c3e96]/15 dark:border-[#60a5fa]/20">
                                                {{ $initial }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc] truncate">{{ $g['user'] }}</p>
                                                <p class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">{{ $g['types']->count() }} type{{ $g['types']->count() > 1 ? 's' : '' }} d'incident</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-2.5">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @foreach($g['types']->take(3) as $t)
                                                <span class="an-pill">
                                                    <span>{{ $t['label'] }}</span>
                                                    <b>×{{ $t['count'] }}</b>
                                                </span>
                                            @endforeach
                                            @if($g['types']->count() > 3)
                                                <span class="an-pill !font-bold">+{{ $g['types']->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-2.5">
                                        <span class="badge ks-stat-change {{ $sevClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $sevDot }}"></span>
                                            {{ $sevLabel }}
                                        </span>
                                    </td>
                                    <td class="px-3.5 py-2.5">
                                        <p class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc] ks-num">{{ \Carbon\Carbon::parse($g['last_detected'])->format('d/m/Y') }}</p>
                                        <p class="text-[10px] text-[#6e6862] dark:text-[#a89f93] ks-num">{{ \Carbon\Carbon::parse($g['last_detected'])->format('H:i') }}</p>
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ks-num bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                                {{ $g['total'] }}
                                            </span>
                                            <button type="button" class="ks-btn-secondary ks-btn-xs" onclick="openUserModal({{ $i }})">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                                <span>Détails</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- VUE MOBILE (< 640px) --}}
                <div class="an-mobile-list divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @foreach($grouped as $i => $g)
                        @php
                            $initial = strtoupper(substr($g['user'], 0, 2));
                            $sevMax = $g['max_severity'];
                            $sevClass = $sevMax >= 3 ? 'ks-stat-change--negative' : ($sevMax === 2 ? 'ks-stat-change--amber' : 'ks-stat-change--neutral');
                            $sevDot = $sevMax >= 3 ? 'bg-rose-500' : ($sevMax === 2 ? 'bg-amber-500' : 'bg-slate-400');
                            $sevLabel = $sevMax >= 3 ? 'Élevée' : ($sevMax === 2 ? 'Moyenne' : 'Faible');
                        @endphp
                        <div class="p-3 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-md bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] font-bold text-xs flex items-center justify-center flex-shrink-0 border border-[#1c3e96]/15 dark:border-[#60a5fa]/20">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc] truncate">{{ $g['user'] }}</p>
                                        <p class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">{{ $g['types']->count() }} type(s) d'incident</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <span class="badge ks-stat-change {{ $sevClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $sevDot }}"></span>
                                        {{ $sevLabel }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ks-num bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        {{ $g['total'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach($g['types']->take(3) as $t)
                                    <span class="an-pill">
                                        <span>{{ $t['label'] }}</span>
                                        <b>×{{ $t['count'] }}</b>
                                    </span>
                                @endforeach
                                @if($g['types']->count() > 3)
                                    <span class="an-pill !font-bold">+{{ $g['types']->count() - 3 }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] ks-num">
                                    Dernier : {{ \Carbon\Carbon::parse($g['last_detected'])->format('d/m/Y H:i') }}
                                </span>
                                <button type="button" class="ks-btn-secondary ks-btn-xs" onclick="openUserModal({{ $i }})">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                    <span>Détails</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════
         MODALE DÉTAIL & RÉSOLUTION (STYLE MON STAGE COMPACT)
    ════════════════════════════════════════════════════════ --}}
    <div id="userModalBackdrop" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm items-center justify-center p-3 sm:p-4">
        <div class="w-full max-w-2xl max-h-[88vh] flex flex-col rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden transition-all">
            {{-- En-tête de modale --}}
            <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between bg-[#faf9f6] dark:bg-[#181c24]">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div id="modalAvatar" class="w-8 h-8 rounded-md bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] font-bold text-xs flex items-center justify-center flex-shrink-0 border border-[#1c3e96]/15 dark:border-[#60a5fa]/20">
                        --
                    </div>
                    <div class="min-w-0">
                        <h2 id="modalUserName" class="text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] truncate">—</h2>
                        <p id="modalUserSub" class="text-[11px] text-[#6e6862] dark:text-[#a89f93] truncate">—</p>
                    </div>
                </div>
                <button type="button" onclick="closeUserModal()" class="w-7 h-7 rounded-md flex items-center justify-center text-[#6e6862] hover:text-[#241f1b] dark:text-[#a89f93] dark:hover:text-white hover:bg-[#ecebe3] dark:hover:bg-[#222630] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Corps scrollable de modale --}}
            <div class="p-3 sm:p-4 overflow-y-auto space-y-3 flex-1" id="modalBody">
                {{-- Injecté en JS --}}
            </div>

            {{-- Pied de modale --}}
            <div class="px-4 py-2.5 border-t border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] flex items-center justify-end">
                <button type="button" onclick="closeUserModal()" class="ks-btn-secondary ks-btn-xs">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const grouped = @json($grouped);
        const csrfToken = '{{ csrf_token() }}';

        function sevBadge(sev) {
            const map = {
                high: ['ks-stat-change--negative', 'bg-rose-500', 'Élevée'],
                medium: ['ks-stat-change--amber', 'bg-amber-500', 'Moyenne'],
                low: ['ks-stat-change--neutral', 'bg-slate-400', 'Faible']
            };
            const [cls, dot, label] = map[sev] || map.low;
            return `<span class="badge ks-stat-change ${cls}"><span class="w-1.5 h-1.5 rounded-full ${dot}"></span>${label}</span>`;
        }

        function openUserModal(index) {
            const g = grouped[index];
            if (!g) return;

            document.getElementById('modalAvatar').textContent = g.user.substring(0,2).toUpperCase();
            document.getElementById('modalUserName').textContent = g.user;
            document.getElementById('modalUserSub').textContent = `${g.total} anomalie(s) ouverte(s) · ${g.types.length} catégorie(s)`;

            const body = document.getElementById('modalBody');
            body.innerHTML = g.types.map(t => `
                <div class="rounded-lg border border-[#dcdad2] dark:border-[#222630] overflow-hidden bg-white dark:bg-[#131519]">
                    <div class="px-3 py-2 bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2 min-w-0">
                            ${sevBadge(t.severity)}
                            <div class="min-w-0">
                                <span class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">${t.label}</span>
                                ${t.count > 1 ? `<span class="text-[11px] text-[#6e6862] dark:text-[#a89f93] font-medium ml-1">(×${t.count})</span>` : ''}
                                <p class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">${t.description}</p>
                            </div>
                        </div>
                        <div>
                            ${t.count > 1 ? `<button type="button" class="ks-btn-secondary ks-btn-xs !text-[11px]" onclick="resolveBulk([${t.ids.join(',')}])">Tout résoudre (${t.count})</button>` : ''}
                        </div>
                    </div>
                    <div class="p-2 sm:p-2.5 space-y-1.5">
                        ${t.items.map(item => `
                            <div class="p-2 rounded-md bg-[#faf9f6] dark:bg-[#181c24]/60 border border-[#ecebe3] dark:border-[#222630] flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ks-num">${item.date}</p>
                                    ${item.observation ? `<p class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 truncate">"${item.observation}"</p>` : ''}
                                </div>
                                <button type="button" class="ks-btn-primary ks-btn-xs !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600" onclick="resolveOne(${item.id})">
                                    Résoudre
                                </button>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');

            const backdrop = document.getElementById('userModalBackdrop');
            backdrop.classList.remove('hidden');
            backdrop.classList.add('flex');
        }

        function closeUserModal() {
            const backdrop = document.getElementById('userModalBackdrop');
            backdrop.classList.add('hidden');
            backdrop.classList.remove('flex');
        }

        function resolveOne(id) {
            if (!confirm('Résoudre cette anomalie ?')) return;
            fetch(`{{ url('admin/presence') }}/${id}/resolve`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: new FormData(),
            }).then(() => window.location.reload());
        }

        function resolveBulk(ids) {
            if (!confirm(`Résoudre ces ${ids.length} anomalies identiques ?`)) return;
            const form = new FormData();
            ids.forEach(id => form.append('ids[]', id));
            fetch(`{{ route('admin.presence.anomalies.bulk-resolve') }}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: form,
            }).then(() => window.location.reload());
        }

        document.addEventListener('DOMContentLoaded', () => {
            const backdrop = document.getElementById('userModalBackdrop');
            backdrop.addEventListener('click', (e) => { if (e.target === backdrop) closeUserModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeUserModal(); });
        });
    </script>
    @endpush

</x-app-layout>