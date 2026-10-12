<x-app-layout title="Historique de présence">
    @php
        $chartData = $userStats['chart_data'] ?? [];
        $hasChartData = !empty($chartData['labels']);

        // Fonction Sparkline SVG (identique à mon-stage)
        $spark = function(array $raw, string $accent) {
            $data = array_values(array_map('floatval', $raw));
            if (count($data) < 2) { $data = array_pad($data, 5, $data[0] ?? 0); }
            $max = max($data); $min = min($data); $range = ($max - $min) > 0 ? ($max - $min) : 1;
            $n = count($data); $pts = [];
            foreach ($data as $i => $v) {
                $x = round(($i / ($n - 1)) * 100, 2);
                $y = round(30 - (($v - $min) / $range) * 24 + 3, 2);
                $pts[] = "$x,$y";
            }
            $line = 'M' . implode(' L', $pts);
            return ['line' => $line, 'area' => $line . ' L100,36 L0,36 Z', 'accent' => $accent];
        };

        $chartPresent = $userStats['chart_data']['present'] ?? [];
        $chartLate = $userStats['chart_data']['late_minutes'] ?? [];
        $chartHours = $userStats['chart_data']['worked_hours'] ?? [];
        $chartAbs = $userStats['chart_data']['absences'] ?? [];

        $spPresence = $spark(!empty($chartPresent) ? $chartPresent : [1, 1, 1, 1, 1], '#16804a');
        $spRetard = $spark(!empty($chartLate) ? $chartLate : [0, 0, 0, 0, 0], '#b45309');
        $spHeures = $spark(!empty($chartHours) ? $chartHours : [7, 7, 7, 7, 7], '#1c3e96');
        $spAbsence = $spark(!empty($chartAbs) ? $chartAbs : [0, 0, 0, 0, 0], '#b91c1c');

        $totalDays = $userStats['total_days'] ?? 0;
        $presentDays = $userStats['present_days'] ?? 0;
        $presentRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
        $absentDays = max(0, $totalDays - $presentDays);
        $totalLateMin = $userStats['total_late_minutes'] ?? 0;
        $totalWorkedHours = $userStats['total_worked_hours'] ?? 0;
        $avgDailyHours = $userStats['avg_daily_hours'] ?? 0;
        $openAnomalies = $userStats['open_anomalies'] ?? 0;

        // Préparation des enregistrements du tableau
        $coveredDates = $attendanceDays->map(fn($d) => $d->attendance_date->toDateString())->flip();
        $excusedDays = ($exceptions ?? collect())
            ->reject(fn($e) => isset($coveredDates[$e->attendance_date->toDateString()]))
            ->map(fn($e) => (object)[
                'type' => $e->isAutomatic() ? 'permission' : 'excused',
                'record' => $e,
            ]);
        $allRecords = $attendanceDays
            ->map(fn($d) => (object)['type' => 'attendance', 'record' => $d])
            ->concat($excusedDays)
            ->sortByDesc(fn($r) => $r->record->attendance_date);
    @endphp

    <style>
        /* Harmonisation de la page avec mon-stage */
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }
        header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header button.rounded-lg, .dark header button.rounded-xl { background-color: #1f232d !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
            --ks-green: #15803d;
            --ks-green-bg: #dcfce7;
            --ks-red: #b91c1c;
            --ks-red-bg: #fee2e2;
            --ks-yellow: #b45309;
            --ks-yellow-bg: #fef3c7;
        }

        .ks-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6e6862;
        }
        .dark .ks-label { color: #a89f93; }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 600;
            padding: .45rem .9rem;
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
            font-size: .85rem;
            font-weight: 500;
            padding: .45rem .9rem;
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

        /* Cartes KPI compactes */
        .ks-stat-card {
            border: 1px solid #dcdad2;
            border-radius: 10px;
            background-color: #fffefa;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .dark .ks-stat-card {
            background: #131519 !important;
            border: 1px solid #222630 !important;
        }
        .ks-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
        }

        .ks-stat-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: none;
            background-color: var(--ks-stat-icon-bg, #eef1fb);
            color: var(--ks-stat-icon-color, #1c3e96);
        }
        .ks-stat-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

        .ks-stat-card--green { --ks-stat-accent: #16804a; --ks-stat-icon-bg: #ecfdf5; --ks-stat-icon-color: #15803d; }
        .ks-stat-card--blue { --ks-stat-accent: #1c3e96; --ks-stat-icon-bg: #eef1fb; --ks-stat-icon-color: #1c3e96; }
        .ks-stat-card--amber { --ks-stat-accent: #b45309; --ks-stat-icon-bg: #fffbeb; --ks-stat-icon-color: #b45309; }
        .ks-stat-card--red { --ks-stat-accent: #b91c1c; --ks-stat-icon-bg: #fff1f2; --ks-stat-icon-color: #b91c1c; }

        .dark .ks-stat-card--green { --ks-stat-icon-bg: rgba(22,128,74,0.22); --ks-stat-icon-color: #4ade80; }
        .dark .ks-stat-card--blue { --ks-stat-icon-bg: rgba(28,62,150,0.25); --ks-stat-icon-color: #60a5fa; }
        .dark .ks-stat-card--amber { --ks-stat-icon-bg: rgba(180,83,9,0.22); --ks-stat-icon-color: #fbbf24; }
        .dark .ks-stat-card--red { --ks-stat-icon-bg: rgba(185,28,28,0.22); --ks-stat-icon-color: #f87171; }

        .ks-stat-change {
            border-radius: 4px;
            font-size: .72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .45rem;
        }
        .ks-stat-change--positive { color: #166534; background-color: #dcfce7; }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fee2e2; }
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--positive { color: #4ade80 !important; background-color: rgba(22,128,74,0.25) !important; }
        .dark .ks-stat-change--negative { color: #f87171 !important; background-color: rgba(185,28,28,0.25) !important; }
        .dark .ks-stat-change--neutral { color: #cbd5e1 !important; background-color: #262a30 !important; }

        .ks-num { font-variant-numeric: tabular-nums; }

        /* Conteneurs et cartes de section */
        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
        }
        .dark .ks-sec {
            background: #131519 !important;
            border: 1px solid #222630 !important;
            box-shadow: none;
        }
        .ks-sec-head {
            padding: .65rem .85rem;
            border-bottom: 1px solid #dcdad2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .dark .ks-sec-head {
            border-bottom: 1px solid #222630 !important;
        }

        @keyframes ks-rise { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        .ks-rise { animation: ks-rise .35s cubic-bezier(.2,.8,.2,1) both; }

        /* Scrollbars fines */
        * { scrollbar-width: thin; scrollbar-color: rgba(28,62,150,.35) transparent; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: rgba(28,62,150,.35); border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background-color: rgba(28,62,150,.6); }
    </style>

    <div class="w-full space-y-3 pb-6"
         x-data="{
             obsModal: false, obsText: '', obsDate: '',
             corrModal: false, corrDate: '', corrLabel: '', corrReason: '',
             timeModal: false, timeDayId: null, timeLabel: '', timeCurrent: '', timeValue: '', timeReason: '',
             outModal: false, outDayId: null, outLabel: '', outCurrent: '', outClaimed: '', outClaimReason: '', outValue: '', outReason: ''
         }"
         @open-correction.window="corrModal = true; corrDate = $event.detail.date; corrLabel = $event.detail.label; corrReason = ''"
         @open-time-correction.window="timeModal = true; timeDayId = $event.detail.id; timeLabel = $event.detail.label; timeCurrent = $event.detail.current; timeValue = ''; timeReason = ''"
         @open-departure-correction.window="outModal = true; outDayId = $event.detail.id; outLabel = $event.detail.label; outCurrent = $event.detail.current; outClaimed = $event.detail.claimed; outClaimReason = $event.detail.claimReason; outValue = $event.detail.claimed; outReason = ''">

        {{-- 1. EN-TÊTE COMPACT --}}
        <div class="ks-rise flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4" style="animation-delay:.02s">
            <div class="min-w-0">
                <span class="ks-label">Présence · Historique</span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-white mt-0.5 truncate">
                    @isset($user)
                        Pointages de {{ $user->name }}
                    @else
                        Mon historique de présence
                    @endisset
                </h1>
            </div>
            <div class="flex items-center flex-wrap gap-2">
                <a href="{{ route('presence.pointage') }}" class="ks-btn-primary ks-btn-xs !text-[13px] !px-3.5 !py-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        {{-- 2. BARRE DE FILTRES ET ONGLETS DE PÉRIODE COMPACTE --}}
        <div class="ks-rise flex flex-wrap items-center justify-between gap-2.5 bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-xl p-2 shadow-sm" style="animation-delay:.04s">
            {{-- Onglets de périodes rapides --}}
            <div class="flex items-center gap-1 bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630] p-1 rounded-lg overflow-x-auto max-w-full">
                @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                <a href="?period={{ $k }}"
                   class="px-2.5 py-1 text-xs rounded-md transition-all font-medium shrink-0 whitespace-nowrap {{ request('period', 'month') === $k ? 'bg-[#1c3e96] text-white shadow-sm font-semibold' : 'text-[#6e6862] dark:text-slate-400 hover:text-[#241f1b] dark:hover:text-white hover:bg-white/60 dark:hover:bg-white/5' }}">
                    {{ $lbl }}
                </a>
                @endforeach
            </div>

            {{-- Formulaire de dates personnalisées inline --}}
            <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="period" value="custom" />
                <div class="flex items-center gap-1.5 text-xs text-[#6e6862] dark:text-slate-400 shrink-0">
                    <span class="font-medium">Du</span>
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom ?? today()->startOfMonth()->format('Y-m-d')) }}"
                           class="h-7 px-2 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] text-[#241f1b] dark:text-slate-200 focus:outline-none focus:border-[#1c3e96]" />
                    <span class="font-medium">Au</span>
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo ?? today()->endOfMonth()->format('Y-m-d')) }}"
                           class="h-7 px-2 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] text-[#241f1b] dark:text-slate-200 focus:outline-none focus:border-[#1c3e96]" />
                </div>
                <button type="submit" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-2.5 shrink-0 whitespace-nowrap">
                    Filtrer
                </button>
                @if(request('period') === 'custom' || request('date_from') || request('date_to'))
                <a href="{{ url()->current() }}" class="ks-btn-secondary ks-btn-xs !text-xs !py-1 !px-2.5 shrink-0 whitespace-nowrap">
                    Réinitialiser
                </a>
                @endif
            </form>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-[#ecfdf5] dark:bg-emerald-950/40 dark:border-emerald-800/60 dark:text-emerald-200 px-3.5 py-2 text-xs text-[#15803d]">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="rounded-lg border border-rose-200 bg-[#fff1f2] dark:bg-rose-950/40 dark:border-rose-800/60 dark:text-rose-200 px-3.5 py-2 text-xs text-[#b91c1c]">
            ✗ {{ $errors->first() }}
        </div>
        @endif

        {{-- 3. STRIP KPI FAÇON MON-STAGE --}}
        <section aria-label="Statistiques de présence" class="ks-rise grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3" style="animation-delay:.06s">
            {{-- 1. Taux de présence --}}
            <div class="ks-stat-card ks-stat-card--green border shadow-none h-full">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[13px]">Taux de Présence</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M8.5 12.5l2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                    <div class="my-1 flex items-baseline gap-1.5">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-2xl leading-none">{{ $presentRate }}%</span>
                        <span class="font-medium text-[#6e6862] dark:text-slate-400 text-xs">({{ $presentDays }}/{{ $totalDays }} j)</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--{{ $presentRate >= 80 ? 'positive' : ($presentRate >= 50 ? 'neutral' : 'negative') }}">
                            {{ $presentDays }} présents
                        </span>
                        <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true">
                            <path d="{{ $spPresence['area'] }}" fill="{{ $spPresence['accent'] }}" opacity="0.12"/>
                            <path d="{{ $spPresence['line'] }}" fill="none" stroke="{{ $spPresence['accent'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 2. Retards Cumulés --}}
            <div class="ks-stat-card ks-stat-card--amber border shadow-none h-full">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[13px]">Retards Cumulés</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>
                    <div class="my-1 flex items-baseline gap-1.5">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-2xl leading-none">{{ round($totalLateMin / 60, 1) }}h</span>
                        <span class="font-medium text-[#6e6862] dark:text-slate-400 text-xs">({{ $userStats['late_days'] ?? 0 }} retards)</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--{{ $totalLateMin > 0 ? 'negative' : 'positive' }}">
                            {{ $totalLateMin > 0 ? formatMinutes($totalLateMin) : '0 min' }}
                        </span>
                        <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true">
                            <path d="{{ $spRetard['area'] }}" fill="{{ $spRetard['accent'] }}" opacity="0.12"/>
                            <path d="{{ $spRetard['line'] }}" fill="none" stroke="{{ $spRetard['accent'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 3. Heures Travaillées --}}
            <div class="ks-stat-card ks-stat-card--blue border shadow-none h-full">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[13px]">Heures Travaillées</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="my-1 flex items-baseline gap-1.5">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-2xl leading-none">{{ $totalWorkedHours }}h</span>
                        <span class="font-medium text-[#6e6862] dark:text-slate-400 text-xs">moy. {{ $avgDailyHours }}h/j</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--neutral">
                            {{ $avgDailyHours }}h/jour
                        </span>
                        <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true">
                            <path d="{{ $spHeures['area'] }}" fill="{{ $spHeures['accent'] }}" opacity="0.12"/>
                            <path d="{{ $spHeures['line'] }}" fill="none" stroke="{{ $spHeures['accent'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 4. Absences & Anomalies --}}
            <div class="ks-stat-card ks-stat-card--red border shadow-none h-full">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[#6e6862] dark:text-slate-400 font-semibold truncate text-[13px]">Absences & Anomalies</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 7.5h.008v.008H12v-.008z" />
                            </svg>
                        </span>
                    </div>
                    <div class="my-1 flex items-baseline gap-1.5">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-2xl leading-none">{{ $absentDays }}</span>
                        <span class="font-medium text-[#6e6862] dark:text-slate-400 text-xs">jour(s) absent(s)</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--{{ $openAnomalies > 0 ? 'negative' : 'neutral' }}">
                            {{ $openAnomalies }} anomalie(s)
                        </span>
                        <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true">
                            <path d="{{ $spAbsence['area'] }}" fill="{{ $spAbsence['accent'] }}" opacity="0.12"/>
                            <path d="{{ $spAbsence['line'] }}" fill="none" stroke="{{ $spAbsence['accent'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. PANEL DE CORRECTION DES ABSENCES (ADMIN) --}}
        @isset($user)
        <div class="ks-sec ks-rise p-3 space-y-2.5" style="animation-delay:.08s">
            <div class="flex items-center justify-between pb-2 border-b border-[#ecebe3] dark:border-[#222630]">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-6 !h-6 !bg-amber-500/10 !text-amber-600 dark:!text-amber-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                    </span>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-[#241f1b] dark:text-white">Corriger les absences administratives</h3>
                </div>
                <span class="text-[11px] text-[#6e6862] dark:text-slate-400">Cliquez sur un jour pour régulariser ou annuler</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                {{-- Absences détectées --}}
                <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-[#241f1b] dark:text-white">Absences détectées</span>
                        <span class="badge ks-stat-change ks-stat-change--negative">{{ count($absenceDates ?? []) }}</span>
                    </div>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        @forelse($absenceDates ?? [] as $absence)
                        <div class="flex items-center justify-between p-1.5 rounded bg-white dark:bg-[#131519] border border-[#ecebe3] dark:border-[#222630]">
                            <div>
                                <div class="font-medium text-[#241f1b] dark:text-slate-200">{{ $absence['label'] }}</div>
                                <div class="text-[11px] text-[#6e6862] dark:text-slate-400">Aucun pointage</div>
                            </div>
                            <button type="button" class="ks-btn-primary ks-btn-xs !py-0.5 !px-2 !text-[11px]" onclick="openCorrection('{{ $absence['date'] }}', '{{ $absence['label'] }}')">
                                Corriger
                            </button>
                        </div>
                        @empty
                        <p class="text-[11px] text-[#6e6862] dark:text-slate-400 italic py-2 text-center">Aucune absence détectée sur cette période.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Jours corrigés --}}
                <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-[#241f1b] dark:text-white">Jours déjà corrigés</span>
                        <span class="badge ks-stat-change ks-stat-change--positive">{{ $exceptions->count() }}</span>
                    </div>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        @forelse($exceptions as $exception)
                        <div class="flex items-center justify-between p-1.5 rounded bg-white dark:bg-[#131519] border border-[#ecebe3] dark:border-[#222630]">
                            <div class="min-w-0 pr-2">
                                <div class="font-medium text-[#241f1b] dark:text-slate-200 truncate">{{ \Carbon\Carbon::parse($exception->attendance_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</div>
                                <div class="text-[11px] text-[#6e6862] dark:text-slate-400 truncate">{{ $exception->reason ?: 'Corrigé' }}</div>
                            </div>
                            <form method="POST" action="{{ route('attendance.tracking.user.exception.destroy', [$user, $exception]) }}" onsubmit="return confirm('Annuler cette correction ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ks-btn-secondary ks-btn-xs !py-0.5 !px-2 !text-[11px] text-rose-600 hover:text-rose-700">
                                    Annuler
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="text-[11px] text-[#6e6862] dark:text-slate-400 italic py-2 text-center">Aucune correction enregistrée.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endisset

        {{-- 5. GRAPHIQUES COMPACTS CÔTE À CÔTE --}}
        @if($hasChartData)
        <div class="ks-rise grid grid-cols-1 lg:grid-cols-2 gap-3.5" style="animation-delay:.09s">
            {{-- Graphique 1 : Évolution --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#241f1b] dark:text-white">Évolution · Présence & Ponctualité</span>
                    <div class="flex items-center gap-2 text-[10px] text-[#6e6862] dark:text-slate-400">
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Présent</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span>À l'heure</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Retard</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Absent</span>
                    </div>
                </div>
                <div class="p-2.5 h-[210px]">
                    <canvas id="chartPresence" class="w-full h-full"></canvas>
                </div>
            </div>

            {{-- Graphique 2 : Heures & Retards --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#241f1b] dark:text-white">Volume travaillé & Retards (min)</span>
                    <div class="flex items-center gap-2 text-[10px] text-[#6e6862] dark:text-slate-400">
                        <span class="inline-flex items-center gap-1"><span class="w-2.5 h-2 rounded-sm bg-blue-500"></span>Heures (h)</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2.5 h-2 rounded-sm bg-amber-500"></span>Retard (min)</span>
                    </div>
                </div>
                <div class="p-2.5 h-[210px]">
                    <canvas id="chartOverview" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
        @endif

        {{-- 6. TABLEAU DES POINTAGES RÉCENTS --}}
        <div class="ks-sec ks-rise overflow-hidden" style="animation-delay:.11s">
            <div class="ks-sec-head">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#241f1b] dark:text-white">Historique des pointages</span>
                    <span class="text-[11px] text-[#6e6862] dark:text-slate-400">· {{ $period === 'custom' ? (request('date_from').' → '.request('date_to')) : ucfirst($period ?? 'mois') }}</span>
                </div>
                <span class="badge ks-stat-change ks-stat-change--neutral font-medium">
                    {{ $attendanceDays->count() }} enregistrements
                </span>
            </div>

            {{-- Version Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] text-[11px] font-semibold text-[#6e6862] dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3.5">Date</th>
                            <th class="py-2.5 px-3">Arrivée</th>
                            <th class="py-2.5 px-3">Départ</th>
                            <th class="py-2.5 px-3">Heures</th>
                            <th class="py-2.5 px-3">Retard</th>
                            <th class="py-2.5 px-3">Statut</th>
                            <th class="py-2.5 px-3.5">Observation / Rapport</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        @forelse($allRecords as $item)
                            @if($item->type === 'attendance')
                                @php $day = $item->record; @endphp
                                <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                    <td class="py-2.5 px-3.5">
                                        <div class="font-semibold text-[#241f1b] dark:text-white">{{ $day->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                        <div class="text-[11px] text-[#6e6862] dark:text-slate-400 capitalize">{{ $day->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono font-medium text-[#241f1b] dark:text-slate-200">
                                        {{ $day->first_check_in_at?->format('H:i') ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono font-medium text-[#241f1b] dark:text-slate-200">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</span>
                                            @if($day->departure_status === 'auto_closed')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40" title="Départ non pointé, clôturé à l'heure prévue">non pointé</span>
                                            @elseif($day->departure_status === 'claimed')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40" title="{{ $day->claimed_check_out_reason }}">
                                                    déclaré · {{ $day->claimed_check_out_at?->format('H:i') }}
                                                </span>
                                            @elseif($day->departure_status === 'corrected' && $day->correctionDepart)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40" title="{{ $day->correctionDepart->reason }}">
                                                    rétabli {{ $day->correctionDepart->original_check_out_at?->format('H:i') }}
                                                </span>
                                            @endif

                                            @isset($user)
                                                @if(in_array($day->departure_status, ['auto_closed', 'claimed'], true))
                                                    <button type="button" class="ks-btn-primary ks-btn-xs !py-0.5 !px-1.5 !text-[10px]"
                                                        onclick="openDepartureCorrection({{ $day->id }}, '{{ $day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}', '{{ $day->last_check_out_at?->format('H:i') }}', '{{ $day->claimed_check_out_at?->format('H:i') }}', @js($day->claimed_check_out_reason))">
                                                        Rétablir
                                                    </button>
                                                @elseif($day->departure_status === 'corrected' && $day->correctionDepart)
                                                    <form method="POST" action="{{ route('attendance.tracking.user.correction.destroy', [$user, $day->correctionDepart]) }}" class="inline" onsubmit="return confirm('Annuler cette correction ?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="ks-btn-secondary ks-btn-xs !py-0.5 !px-1.5 !text-[10px] text-rose-600">Annuler</button>
                                                    </form>
                                                @endif
                                            @endisset
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono font-semibold text-[#1c3e96] dark:text-indigo-400">
                                        {{ $day->worked_minutes > 0 ? round($day->worked_minutes / 60, 1).'h' : '—' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($day->late_minutes > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#fffbeb] text-[#b45309] border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40">
                                                {{ formatMinutes($day->late_minutes) }}
                                            </span>
                                        @else
                                            <span class="text-[#6e6862] dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($day->first_check_in_at)
                                                @if($day->arrival_status === 'late')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#fffbeb] text-[#b45309] border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40">
                                                        En retard
                                                    </span>
                                                    @isset($user)
                                                    <button type="button" class="ks-btn-primary ks-btn-xs !py-0.5 !px-1.5 !text-[10px]"
                                                        onclick="openTimeCorrection({{ $day->id }}, '{{ $day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}', '{{ $day->first_check_in_at->format('H:i') }}')">
                                                        Corriger
                                                    </button>
                                                    @endisset
                                                @elseif($day->correction)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#ecfdf5] text-[#15803d] border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40">
                                                        À l'heure
                                                    </span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40" title="{{ $day->correction->reason }}">
                                                        corrigé {{ $day->correction->original_check_in_at?->format('H:i') }}
                                                    </span>
                                                    @isset($user)
                                                    <form method="POST" action="{{ route('attendance.tracking.user.correction.destroy', [$user, $day->correction]) }}" class="inline" onsubmit="return confirm('Annuler cette correction ?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="ks-btn-secondary ks-btn-xs !py-0.5 !px-1.5 !text-[10px] text-rose-600">Annuler</button>
                                                    </form>
                                                    @endisset
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#ecfdf5] text-[#15803d] border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40">
                                                        À l'heure
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#fff1f2] text-[#b91c1c] border border-rose-200/60 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/40">
                                                    Absent
                                                </span>
                                                @isset($user)
                                                <button type="button" class="ks-btn-primary ks-btn-xs !py-0.5 !px-1.5 !text-[10px]" onclick="openCorrection('{{ $day->attendance_date->format('Y-m-d') }}', '{{ $day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}')">
                                                    Corriger
                                                </button>
                                                @endisset
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3.5">
                                        @if($day->arrival_status === 'late')
                                            @php $obs = $day->late_observation; @endphp
                                            @if($obs)
                                                <button type="button"
                                                        @click="obsText = {{ json_encode($obs) }}; obsDate = '{{ $day->attendance_date?->format('d/m/Y') ?? '--' }}'; obsModal = true"
                                                        class="text-left max-w-[200px] truncate text-amber-700 dark:text-amber-300 hover:underline cursor-pointer block text-xs">
                                                    {{ Str::limit($obs, 50) }}
                                                </button>
                                            @else
                                                <span class="text-[#6e6862] dark:text-slate-400 text-[11px] italic">Sans observation</span>
                                            @endif
                                        @else
                                            @php $report = $day->dailyReports->first(); @endphp
                                            @if($report)
                                                @if($report->status === 'approved')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Rapport approuvé</span>
                                                @elseif($report->status === 'submitted')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">Rapport soumis</span>
                                                @elseif($report->status === 'draft')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Brouillon</span>
                                                @else
                                                    <span class="text-[11px] text-[#6e6862] dark:text-slate-400 capitalize">{{ $report->status }}</span>
                                                @endif
                                            @else
                                                <span class="text-[#6e6862] dark:text-slate-400">—</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @else
                                @php $exception = $item->record; @endphp
                                <tr class="bg-purple-50/40 dark:bg-purple-950/20 hover:bg-purple-50/70 transition-colors">
                                    <td class="py-2.5 px-3.5">
                                        <div class="font-semibold text-[#241f1b] dark:text-white">{{ $exception->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                        <div class="text-[11px] text-[#6e6862] dark:text-slate-400 capitalize">{{ $exception->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-[#6e6862] dark:text-slate-400">—</td>
                                    <td class="py-2.5 px-3 font-mono text-[#6e6862] dark:text-slate-400">—</td>
                                    <td class="py-2.5 px-3 font-mono text-[#6e6862] dark:text-slate-400">—</td>
                                    <td class="py-2.5 px-3 font-mono text-[#6e6862] dark:text-slate-400">—</td>
                                    <td class="py-2.5 px-3">
                                        @if($item->type === 'permission')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#faf5ff] text-[#7e22ce] border border-purple-200/60 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/40">
                                                Journée permissionnée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#ecfeff] text-[#0891b2] border border-cyan-200/60 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800/40">
                                                Journée excusée
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3.5 text-xs text-[#6e6862] dark:text-slate-400">
                                        {{ $exception->reason ?: ($item->type === 'permission' ? 'Permission approuvée' : 'Correction administrative') }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-[#6e6862] dark:text-slate-400 italic">
                                    Aucun pointage trouvé pour cette période.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Version Mobile (Cartes compactes) --}}
            <div class="block md:hidden p-2.5 space-y-2">
                @forelse($allRecords as $item)
                    @if($item->type === 'attendance')
                        @php $day = $item->record; @endphp
                        <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630] text-xs space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[#241f1b] dark:text-white">{{ $day->attendance_date->locale('fr')->isoFormat('dddd D MMM YYYY') }}</span>
                                @if($day->first_check_in_at)
                                    @if($day->arrival_status === 'late')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#fffbeb] text-[#b45309] border border-amber-200/60">En retard</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#ecfdf5] text-[#15803d] border border-emerald-200/60">À l'heure</span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#fff1f2] text-[#b91c1c] border border-rose-200/60">Absent</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-3 gap-1 py-1 text-[11px] text-[#6e6862] dark:text-slate-400 border-y border-[#ecebe3] dark:border-[#222630]">
                                <div>Arrivée : <strong class="text-[#241f1b] dark:text-slate-200 font-mono">{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</strong></div>
                                <div>Départ : <strong class="text-[#241f1b] dark:text-slate-200 font-mono">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</strong></div>
                                <div>Total : <strong class="text-[#1c3e96] dark:text-indigo-400 font-mono">{{ $day->worked_minutes > 0 ? round($day->worked_minutes/60,1).'h' : '—' }}</strong></div>
                            </div>
                            @if($day->arrival_status === 'late' && $day->late_observation)
                                <div class="text-[11px] text-amber-700 dark:text-amber-400 flex items-center justify-between">
                                    <span class="truncate">Obs : {{ Str::limit($day->late_observation, 40) }}</span>
                                    <button type="button" @click="obsText = {{ json_encode($day->late_observation) }}; obsDate = '{{ $day->attendance_date?->format('d/m/Y') }}'; obsModal = true" class="underline text-[10px] ml-1">Voir</button>
                                </div>
                            @endif
                        </div>
                    @else
                        @php $exception = $item->record; @endphp
                        <div class="p-2.5 rounded-lg bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200/60 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[#241f1b] dark:text-white">{{ $exception->attendance_date->locale('fr')->isoFormat('dddd D MMM YYYY') }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-700">{{ $item->type === 'permission' ? 'Permission' : 'Excusé' }}</span>
                            </div>
                            <p class="text-[11px] text-[#6e6862] dark:text-slate-400">{{ $exception->reason ?: 'Journée régularisée' }}</p>
                        </div>
                    @endif
                @empty
                    <p class="text-center text-xs text-[#6e6862] dark:text-slate-400 italic py-4">Aucun pointage trouvé.</p>
                @endforelse
            </div>
        </div>

        {{-- ==================== MODALES HARMONISÉES ==================== --}}
        {{-- Modal Observation --}}
        <div x-show="obsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-[1px]" @click.self="obsModal = false">
            <div class="w-full max-w-md rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 shadow-2xl space-y-3">
                <div class="flex items-center justify-between border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
                    <div>
                        <span class="ks-label">Observation du retard</span>
                        <div class="text-xs text-[#6e6862] dark:text-slate-400" x-text="'Date : ' + obsDate"></div>
                    </div>
                    <button @click="obsModal = false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white p-1">✕</button>
                </div>
                <div class="py-2">
                    <p class="text-xs leading-relaxed text-[#241f1b] dark:text-slate-200 bg-[#faf9f6] dark:bg-[#181c24] p-3 rounded-lg border border-[#ecebe3] dark:border-[#222630]" x-text="obsText"></p>
                </div>
                <div class="flex justify-end pt-1">
                    <button @click="obsModal = false" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-3">Fermer</button>
                </div>
            </div>
        </div>

        {{-- Modales Administrateur --}}
        @isset($user)
        {{-- Modal Correction Absence --}}
        <div x-show="corrModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-[1px]" @click.self="corrModal = false">
            <div class="w-full max-w-md rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 shadow-2xl space-y-3">
                <div class="flex items-center justify-between border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
                    <div>
                        <span class="ks-label">Régulariser une absence</span>
                        <div class="text-xs text-[#6e6862] dark:text-slate-400" x-text="corrLabel ? 'Jour : ' + corrLabel : ''"></div>
                    </div>
                    <button @click="corrModal = false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white p-1">✕</button>
                </div>
                <form method="POST" action="{{ route('attendance.tracking.user.exception.store', $user) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="attendance_date" x-model="corrDate" />
                    <div>
                        <label class="block text-xs font-medium text-[#6e6862] dark:text-slate-400 mb-1">Motif de la régularisation (optionnel)</label>
                        <textarea name="reason" x-model="corrReason" rows="2" placeholder="Ex : Ordre de mission, problème de matériel..."
                                  class="w-full text-xs p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-white focus:outline-none focus:border-[#1c3e96]"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="corrModal = false" class="ks-btn-secondary ks-btn-xs !text-xs !py-1 !px-3">Annuler</button>
                        <button type="submit" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-3">✓ Confirmer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Heure Arrivée --}}
        <div x-show="timeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-[1px]" @click.self="timeModal = false">
            <div class="w-full max-w-md rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 shadow-2xl space-y-3">
                <div class="flex items-center justify-between border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
                    <div>
                        <span class="ks-label">Rétablir l'heure d'arrivée</span>
                        <div class="text-xs text-[#6e6862] dark:text-slate-400" x-text="timeLabel ? 'Jour : ' + timeLabel : ''"></div>
                    </div>
                    <button @click="timeModal = false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white p-1">✕</button>
                </div>
                <form method="POST" :action="timeDayId ? '{{ url('admin/attendance-tracking/user/' . ($user->id ?? 0) . '/days') }}/' + timeDayId + '/correction' : '#'" class="space-y-3">
                    @csrf
                    <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630] text-xs">
                        <span class="text-[#6e6862] dark:text-slate-400">Heure enregistrée :</span>
                        <strong class="font-mono text-[#241f1b] dark:text-white ml-1" x-text="timeCurrent"></strong>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#6e6862] dark:text-slate-400 mb-1">Heure réelle d'arrivée</label>
                        <input type="time" name="time" x-model="timeValue" required
                               class="w-full h-8 px-2 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-white focus:outline-none focus:border-[#1c3e96]" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#6e6862] dark:text-slate-400 mb-1">Motif obligatoire</label>
                        <textarea name="reason" x-model="timeReason" rows="2" required minlength="5" placeholder="Ex : Lecteur QR en panne..."
                                  class="w-full text-xs p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-white focus:outline-none focus:border-[#1c3e96]"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="timeModal = false" class="ks-btn-secondary ks-btn-xs !text-xs !py-1 !px-3">Annuler</button>
                        <button type="submit" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-3">✓ Rétablir l'heure</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Heure Départ --}}
        <div x-show="outModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-[1px]" @click.self="outModal = false">
            <div class="w-full max-w-md rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 shadow-2xl space-y-3">
                <div class="flex items-center justify-between border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
                    <div>
                        <span class="ks-label">Rétablir l'heure de départ</span>
                        <div class="text-xs text-[#6e6862] dark:text-slate-400" x-text="outLabel ? 'Jour : ' + outLabel : ''"></div>
                    </div>
                    <button @click="outModal = false" class="text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white p-1">✕</button>
                </div>
                <form method="POST" :action="outDayId ? '{{ url('admin/attendance-tracking/user/' . ($user->id ?? 0) . '/days') }}/' + outDayId + '/correction-depart' : '#'" class="space-y-3">
                    @csrf
                    <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630] text-xs space-y-1">
                        <div>
                            <span class="text-[#6e6862] dark:text-slate-400">Clôturé d'office à :</span>
                            <strong class="font-mono text-[#241f1b] dark:text-white ml-1" x-text="outCurrent"></strong>
                        </div>
                        <div x-show="outClaimed" class="text-amber-700 dark:text-amber-300">
                            <span>Déclaré par l'intéressé :</span>
                            <strong class="font-mono ml-1" x-text="outClaimed"></strong>
                            <div class="italic text-[11px] mt-0.5" x-text="outClaimReason"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#6e6862] dark:text-slate-400 mb-1">Heure réelle de départ</label>
                        <input type="time" name="time" x-model="outValue" required
                               class="w-full h-8 px-2 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-white focus:outline-none focus:border-[#1c3e96]" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#6e6862] dark:text-slate-400 mb-1">Motif obligatoire</label>
                        <textarea name="reason" x-model="outReason" rows="2" required minlength="5" placeholder="Ex : Départ confirmé par le tuteur..."
                                  class="w-full text-xs p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#181c24] text-[#241f1b] dark:text-white focus:outline-none focus:border-[#1c3e96]"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="outModal = false" class="ks-btn-secondary ks-btn-xs !text-xs !py-1 !px-3">Annuler</button>
                        <button type="submit" class="ks-btn-primary ks-btn-xs !text-xs !py-1 !px-3">✓ Rétablir</button>
                    </div>
                </form>
            </div>
        </div>
        @endisset

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        @isset($user)
        function openCorrection(date, label) {
            window.dispatchEvent(new CustomEvent('open-correction', { detail: { date, label } }));
        }
        function openTimeCorrection(id, label, current) {
            window.dispatchEvent(new CustomEvent('open-time-correction', { detail: { id, label, current } }));
        }
        function openDepartureCorrection(id, label, current, claimed, claimReason) {
            window.dispatchEvent(new CustomEvent('open-departure-correction', {
                detail: { id, label, current: current || '--:--', claimed: claimed || '', claimReason: claimReason || '' }
            }));
        }
        @endisset

        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.classList.contains('dark');
            const fontColor = isDark ? '#94a3b8' : '#6e6862';
            const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';

            Chart.defaults.font.family = "Figtree, system-ui, sans-serif";
            Chart.defaults.color = fontColor;

            @php
            $chartDataSafe = $userStats['chart_data'] ?? [
                'labels' => [],
                'dates' => [],
                'present' => [],
                'on_time' => [],
                'late_days' => [],
                'absences' => [],
                'late_minutes' => [],
                'worked_hours' => [],
            ];
            @endphp

            const cd = @json($chartDataSafe);
            const labels = cd.labels ?? [];
            const dates = cd.dates ?? [];
            const present = cd.present ?? [];
            const onTime = cd.on_time ?? [];
            const lateDays = cd.late_days ?? [];
            const absences = cd.absences ?? [];
            const lateMinutes = cd.late_minutes ?? [];
            const workedHours = cd.worked_hours ?? [];
            const workedMinutes = workedHours.map(v => Math.round(v * 60));

            const fmtMin = (m) => {
                if (!m || m <= 0) return '0min';
                const h = Math.floor(m / 60), mins = m % 60;
                if (h === 0) return mins + 'min';
                if (mins === 0) return h + 'h';
                return h + 'h ' + mins + 'min';
            };

            if (!labels.length) return;

            const tooltipStyle = {
                backgroundColor: isDark ? '#15181f' : '#241f1b',
                borderColor: isDark ? '#222630' : '#dcdad2',
                borderWidth: 1,
                titleColor: '#fff',
                bodyColor: '#e2e8f0',
                padding: 10,
                cornerRadius: 8,
            };

            // GRAPHIQUE 1 : Évolution Présence & Ponctualité
            const ctx1 = document.getElementById('chartPresence');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Présent',
                                data: present,
                                borderColor: '#15803d',
                                backgroundColor: 'rgba(22,128,74,0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: present.map(v => v ? 4 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#15803d',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1.5,
                                yAxisID: 'yBin',
                            },
                            {
                                label: "À l'heure",
                                data: onTime,
                                borderColor: '#1c3e96',
                                backgroundColor: 'rgba(28,62,150,0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 1.8,
                                pointRadius: onTime.map(v => v ? 3.5 : 0),
                                pointHoverRadius: 5.5,
                                pointBackgroundColor: '#1c3e96',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1.5,
                                yAxisID: 'yBin',
                            },
                            {
                                label: 'En retard',
                                data: lateDays,
                                borderColor: '#b45309',
                                backgroundColor: 'transparent',
                                tension: 0.35,
                                borderWidth: 1.8,
                                pointRadius: lateDays.map(v => v ? 4 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#b45309',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1.5,
                                yAxisID: 'yBin',
                            },
                            {
                                label: 'Absent',
                                data: absences,
                                borderColor: '#b91c1c',
                                backgroundColor: 'transparent',
                                tension: 0.35,
                                borderWidth: 1.8,
                                pointRadius: absences.map(v => v ? 4 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#b91c1c',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1.5,
                                yAxisID: 'yBin',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        onClick: (evt, items, chart) => {
                            if (typeof openCorrection !== 'function' || !items.length) return;
                            const ds = chart.data.datasets[items[0].datasetIndex];
                            if (ds.label !== 'Absent' || ds.data[items[0].index] !== 1) return;
                            const date = dates[items[0].index];
                            if (date) openCorrection(date, labels[items[0].index]);
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label(ctx) {
                                        const v = ctx.parsed.y;
                                        const lbl = ctx.dataset.label;
                                        return `${lbl}: ${v === 1 ? 'Oui' : 'Non'}`;
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: { color: gridColor },
                                ticks: { font: { size: 10 }, color: fontColor, maxRotation: 45, maxTicksLimit: 12 },
                            },
                            yBin: {
                                type: 'linear',
                                min: -0.1,
                                max: 1.3,
                                ticks: {
                                    stepSize: 1,
                                    callback: v => v === 0 ? '0' : v === 1 ? '1' : '',
                                    color: fontColor,
                                    font: { size: 10 },
                                },
                                grid: { color: gridColor },
                            },
                        },
                    },
                });
            }

            // GRAPHIQUE 2 : Barres Volume & Retards
            const ctx2 = document.getElementById('chartOverview');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Heures travaillées',
                                data: workedHours,
                                backgroundColor: 'rgba(28,62,150,0.75)',
                                borderColor: '#1c3e96',
                                borderWidth: 1,
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.7,
                                yAxisID: 'yHours',
                            },
                            {
                                label: 'Retard (min)',
                                data: lateMinutes,
                                backgroundColor: 'rgba(180,83,9,0.7)',
                                borderColor: '#b45309',
                                borderWidth: 1,
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.7,
                                yAxisID: 'yMin',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label(ctx) {
                                        const v = ctx.parsed.y;
                                        return ctx.dataset.label === 'Heures travaillées' ? `Heures: ${v}h` : `Retard: ${fmtMin(v)}`;
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 }, color: fontColor, maxRotation: 45, maxTicksLimit: 12 },
                            },
                            yHours: {
                                type: 'linear',
                                position: 'left',
                                min: 0,
                                ticks: { callback: v => v + 'h', color: '#1c3e96', font: { size: 10 } },
                                grid: { color: gridColor },
                            },
                            yMin: {
                                type: 'linear',
                                position: 'right',
                                min: 0,
                                ticks: { callback: v => v + 'm', color: '#b45309', font: { size: 10 } },
                                grid: { drawOnChartArea: false },
                            },
                        },
                    },
                });
            }
        });
    </script>
    @endpush
</x-app-layout>