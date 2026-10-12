<x-app-layout title="Corrections de pointage - Supervision">

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

        .ks-stat-icon { width: 32px; height: 32px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .ks-stat-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-stat-change { font-size: .68rem; font-weight: 600; padding: .15rem .45rem; border-radius: 4px; display: inline-flex; align-items: center; gap: .25rem; white-space: nowrap; }
        .ks-stat-change--positive { color: #15803d; background-color: #ecfdf5; }
        .dark .ks-stat-change--positive { color: #4ade80 !important; background-color: rgba(22,128,74,0.22) !important; }
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--neutral { color: #cbd5e1 !important; background-color: #262a30 !important; }
        .ks-stat-change--blue { color: #1c3e96; background-color: #eef1fb; }
        .dark .ks-stat-change--blue { color: #93c5fd !important; background-color: rgba(28,62,150,0.25) !important; }
        .ks-stat-change--amber { color: #b45309; background-color: #fffbeb; }
        .dark .ks-stat-change--amber { color: #fbbf24 !important; background-color: rgba(180,83,9,0.22) !important; }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fef2f2; }
        .dark .ks-stat-change--negative { color: #f87171 !important; background-color: rgba(185,28,28,0.22) !important; }

        .cd-pill { font-size: .72rem; font-weight: 500; padding: .2rem .55rem; border-radius: 999px; background: #faf9f6; color: #6e6862; border: 1px solid #dcdad2; display: inline-flex; align-items: center; gap: .25rem; }
        .dark .cd-pill { background: #181c24; color: #94a3b8; border-color: #222630; }
        .cd-pill b { font-weight: 700; color: #241f1b; }
        .dark .cd-pill b { color: #f8fafc; }

        /* Dark mode overrides complets */
        .dark .text-slate-900, .dark .text-slate-800, .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-slate-600, .dark .text-slate-500, .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .border-slate-200, .dark .border-slate-300, .dark .border-gray-200 { border-color: #222630 !important; }
    </style>

    <div class="space-y-3 sm:space-y-3.5 overflow-x-hidden"
         x-data="{
             outModal: false, outDayId: null, outUserId: null, outUserName: '', outLabel: '',
             outCurrent: '', outClaimed: '', outClaimReason: '', outValue: '', outReason: ''
         }"
         @open-departure-correction.window="outModal = true; outDayId = $event.detail.id; outUserId = $event.detail.userId; outUserName = $event.detail.userName; outLabel = $event.detail.label; outCurrent = $event.detail.current; outClaimed = $event.detail.claimed; outClaimReason = $event.detail.claimReason; outValue = $event.detail.claimed; outReason = ''">

        {{-- ════════════════════════════════════════════════════════
             1. BANNIÈRE CORRECTIONS DÉPARTS (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-gradient-to-r from-[#1c3e96] via-[#1a3889] to-[#162f73] dark:from-[#131519] dark:via-[#161a22] dark:to-[#131519] shadow-sm">
            <div class="absolute inset-0 opacity-15 pointer-events-none"
                style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
            </div>
            <div class="relative px-3.5 sm:px-4 py-2.5 sm:py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="ks-stat-icon !w-6 !h-6 rounded bg-amber-500/20 text-amber-200 dark:bg-amber-500/30 dark:text-amber-300">
                                <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">Corrections des Départs</h1>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $pendingCorrections->count() > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-400/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' }}">
                                <span class="w-1.5 h-1.5 {{ $pendingCorrections->count() > 0 ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' }} rounded-full"></span>
                                {{ $pendingCorrections->count() }} en attente
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-blue-100/80 dark:text-gray-400 mt-0.5">Régularisation des départs non émargés et clôturés d'office à l'heure prévue</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <a href="{{ route('admin.presence.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Supervision</span>
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <span>Tableau de bord</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             2. CARTES STATS KPIS (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            {{-- KPI 1: Départs en attente --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Départs à Régulariser</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold {{ $pendingCorrections->count() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-[#241f1b] dark:text-[#f8fafc]' }}">
                            {{ $pendingCorrections->count() }}
                        </span>
                    </div>
                    <span class="text-[11px] font-medium {{ $pendingCorrections->count() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-0.5 block">
                        {{ $pendingCorrections->count() > 0 ? 'Validation manuelle requise' : 'Aucune correction en attente' }}
                    </span>
                </div>
                <span class="ks-stat-icon {{ $pendingCorrections->count() > 0 ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 2: Déclarations stagiaires --}}
            @php
                $claimedCount = $pendingCorrections->where('departure_status', 'claimed')->count();
            @endphp
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Déclarations Usagers</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-[#1c3e96] dark:text-blue-400">
                            {{ $claimedCount }}
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        Heure et justificatif fournis
                    </span>
                </div>
                <span class="ks-stat-icon bg-blue-50 text-[#1c3e96] dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 3: Clôturés d'office --}}
            @php
                $autoClosedCount = $pendingCorrections->where('departure_status', 'auto_closed')->count();
            @endphp
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Clôturés d'Office</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-[#241f1b] dark:text-[#f8fafc]">
                            {{ $autoClosedCount }}
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        Clôture automatique fin prévue
                    </span>
                </div>
                <span class="ks-stat-icon bg-slate-50 text-slate-600 dark:bg-slate-900 dark:text-slate-400 border border-slate-200 dark:border-slate-800">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             3. FILE DES RÉGULARISATIONS (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec overflow-hidden">
            <div class="ks-sec-head flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-5 !h-5 bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                        <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                    <h2 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">File des Régularisations</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="cd-pill">
                        <b>{{ $pendingCorrections->count() }}</b> dossier{{ $pendingCorrections->count() > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            <div class="p-2.5 sm:p-3.5 space-y-2.5">
                @forelse($pendingCorrections as $day)
                @php
                    $person        = $day->etudiant?->user ?? $day->user;
                    $personName    = $person?->name ?? ($day->etudiant?->user?->name ?? 'Utilisateur inconnu');
                    $personRole    = $day->etudiant ? 'Stagiaire' : 'Employé';
                    $hasClaim      = $day->departure_status === 'claimed';
                    $missedSince   = $day->attendance_date->diffForHumans();
                    $autoTime      = $day->last_check_out_at?->format('H:i') ?? '--:--';
                    $claimedTime   = $day->claimed_check_out_at?->format('H:i');
                    $userId        = $person?->id;
                @endphp

                <article class="rounded-[8px] border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] p-3 sm:p-3.5 hover:border-[#1c3e96]/40 dark:hover:border-blue-500/40 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        {{-- Identité --}}
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/40 text-[#1c3e96] dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($personName, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <h3 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] truncate">{{ $personName }}</h3>
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded {{ $day->etudiant ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                        {{ $personRole }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">
                                    <span>{{ $day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                                    <span>•</span>
                                    <span class="text-[10px]">{{ $missedSince }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Heures & Actions --}}
                        <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3 sm:gap-4 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#ecebe3] dark:border-[#222630]">
                            {{-- Clôturé d'office --}}
                            <div class="text-left sm:text-right">
                                <span class="ks-label block text-[9px]">Clôturé d'office</span>
                                <span class="ks-num text-xs sm:text-sm font-bold text-amber-600 dark:text-amber-400">{{ $autoTime }}</span>
                            </div>

                            {{-- Déclaré --}}
                            <div class="text-left sm:text-right">
                                <span class="ks-label block text-[9px]">Déclaré</span>
                                @if($hasClaim)
                                <span class="ks-num text-xs sm:text-sm font-bold text-[#1c3e96] dark:text-blue-400">{{ $claimedTime }}</span>
                                @else
                                <span class="text-xs text-[#a89f93]">—</span>
                                @endif
                            </div>

                            {{-- Bouton Action --}}
                            <div>
                                <button type="button"
                                    onclick="openDepartureCorrection(
                                        {{ $day->id }},
                                        {{ $userId ?? 0 }},
                                        @js($personName),
                                        @js($day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY')),
                                        @js($autoTime),
                                        @js($claimedTime),
                                        @js($day->claimed_check_out_reason)
                                    )"
                                    class="ks-btn-primary ks-btn-xs w-full sm:w-auto">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Rétablir l'heure</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Note de réclamation éventuelle --}}
                    @if($hasClaim && $day->claimed_check_out_reason)
                    <div class="mt-2.5 p-2 rounded-md bg-[#faf9f6] dark:bg-[#181c24] border-l-2 border-[#1c3e96] dark:border-blue-500 text-xs text-[#6e6862] dark:text-[#a89f93]">
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $personName }} a déclaré :</span>
                        <span class="italic">« {{ $day->claimed_check_out_reason }} »</span>
                    </div>
                    @endif
                </article>
                @empty
                <div class="text-center py-10 px-4">
                    <span class="ks-stat-icon !w-10 !h-10 mx-auto mb-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/40">
                        <svg class="!w-5 !h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <p class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Aucun départ à régler</p>
                    <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">Toutes les journées clôturées d'office ont été traitées.</p>
                </div>
                @endforelse
            </div>

            {{-- Rappel réglementaire compact --}}
            <div class="p-2.5 sm:p-3 bg-[#faf9f6] dark:bg-[#181c24] border-t border-[#dcdad2] dark:border-[#222630] flex items-center gap-2 text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Le motif de correction est obligatoire : le volume horaire réel est pris en compte dans le bilan de stage et les attestations.</span>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             4. MODALE DE RÉGULARISATION (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div x-show="outModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-sm"
             style="display:none;"
             @click.self="outModal = false">
            <div class="w-full max-w-lg rounded-[12px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
                 role="dialog" aria-modal="true" aria-labelledby="correction-title">
                
                {{-- En-tête Modale --}}
                <div class="p-3.5 sm:p-4 border-b border-[#dcdad2] dark:border-[#222630] flex items-start justify-between gap-3 bg-[#faf9f6] dark:bg-[#181c24]">
                    <div>
                        <span class="ks-label">Rétablir l'heure de départ</span>
                        <h3 class="text-sm sm:text-base font-bold text-[#241f1b] dark:text-[#f8fafc] mt-0.5" x-text="outUserName" id="correction-title"></h3>
                        <p class="text-xs text-[#6e6862] dark:text-[#a89f93]" x-text="outLabel"></p>
                    </div>
                    <button type="button" @click="outModal = false"
                            class="p-1 rounded-md text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition"
                            aria-label="Fermer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Formulaire --}}
                <form method="POST" :action="outUserId && outDayId ? '{{ url('admin/attendance-tracking/user') }}/' + outUserId + '/days/' + outDayId + '/correction-depart' : '#'">
                    @csrf
                    <div class="p-3.5 sm:p-4 space-y-3">
                        <p class="text-xs text-[#6e6862] dark:text-[#a89f93]">
                            Le départ n'a pas été pointé : la journée a été clôturée d'office à l'heure de fin prévue. Définissez ici l'heure réelle après vérification du justificatif.
                        </p>

                        {{-- Comparaison heure clôturée vs déclarée --}}
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                            <div>
                                <span class="ks-label block text-[10px]">Clôturé d'office à</span>
                                <span class="ks-num font-bold text-amber-600 dark:text-amber-400 text-sm" x-text="outCurrent"></span>
                            </div>
                            <div>
                                <span class="ks-label block text-[10px]">Déclaré par l'intéressé</span>
                                <span class="ks-num font-bold text-[#1c3e96] dark:text-blue-400 text-sm" x-text="outClaimed || 'Non déclaré'"></span>
                            </div>
                        </div>

                        <div x-show="outClaimReason" x-cloak class="p-2 rounded bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30 text-xs text-blue-900 dark:text-blue-200">
                            <span class="font-semibold">Justificatif fourni :</span>
                            <span x-text="outClaimReason"></span>
                        </div>

                        {{-- Saisie heure --}}
                        <div>
                            <label class="ks-label block mb-1">Heure réelle de départ <span class="text-rose-500">*</span></label>
                            <input type="time" name="time" x-model="outValue" required
                                   class="w-full px-3 py-2 text-xs sm:text-sm rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition font-mono">
                        </div>

                        {{-- Saisie motif --}}
                        <div>
                            <label class="ks-label block mb-1">Motif de la régularisation <span class="text-rose-500">*</span></label>
                            <textarea name="reason" x-model="outReason" rows="3" required minlength="5"
                                      placeholder="Ex. départ à 18h30 validé par le responsable de site / fiche de pointage manuel"
                                      class="w-full px-3 py-2 text-xs sm:text-sm rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition"></textarea>
                            <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                                Minimum 5 caractères. Ce motif est archivé dans le dossier d'assiduité.
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#dcdad2] dark:border-[#222630]">
                            <button type="button" class="ks-btn-secondary ks-btn-xs" @click="outModal = false">
                                Annuler
                            </button>
                            <button type="submit" class="ks-btn-primary ks-btn-xs">
                                Valider la correction
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function openDepartureCorrection(id, userId, userName, label, current, claimed, claimReason) {
            window.dispatchEvent(new CustomEvent('open-departure-correction', {
                detail: { id, userId, userName, label, current: current || '--:--', claimed: claimed || '', claimReason: claimReason || '' }
            }));
        }
    </script>
    @endpush

</x-app-layout>