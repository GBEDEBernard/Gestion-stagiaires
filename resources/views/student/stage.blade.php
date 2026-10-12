<x-app-layout>
    @php
        $user = auth()->user();
        $prenom = $user?->personnel?->prenom ?: ($user?->prenom ?: explode(' ', trim($user?->name ?? 'Stagiaire'))[0]);
        $initiales = strtoupper(substr(preg_replace('/\s+/', '', $user?->name ?? 'ST'), 0, 2));
        $heure = now()->hour;
        $salut = $heure < 18 ? 'Bonjour' : 'Bonsoir';
        $hasIn = (bool) $attendanceDay?->first_check_in_at;
        $hasOut = (bool) $attendanceDay?->last_check_out_at;
        $hasRep = in_array($todayReport?->status ?? null, ['submitted','reviewed','approved'], true);
        if (!$hasIn) { $nextTitre = 'Pointe ton arrivée pour démarrer ta journée'; $nextCta = 'Pointer mon arrivée'; $nextRoute = route('presence.pointage'); $nextSecond = null; }
        elseif (!$hasRep) { $nextTitre = 'Ta présence est ok — il reste ton rapport du jour'; $nextCta = 'Remplir mon rapport'; $nextRoute = route('tasks.index'); $nextSecond = ['Pointer mon départ', route('presence.pointage')]; }
        elseif (!$hasOut) { $nextTitre = 'Rapport envoyé — pense à pointer ton départ'; $nextCta = 'Pointer mon départ'; $nextRoute = route('presence.pointage'); $nextSecond = null; }
        else { $nextTitre = 'Journée terminée, à demain'; $nextCta = 'Voir mon historique'; $nextRoute = route('presence.historique'); $nextSecond = null; }
        $ringR = 26; $ringC = 2 * pi() * $ringR; $ringOff = $ringC * (1 - ($progressionStage ?? 0) / 100);
        $tasksOpen = $tasks->where('status', '!=', 'completed');
        $tasksDone = $tasks->where('status', 'completed');
    @endphp
    <style>
        /* Page en #fffffe (remplace le bg-gray-50 #f9fafb du layout, uniquement sur mon-stage) */
        body{background:#f1f0ed!important;}
        .dark body{background:#1f232d!important;}
        header{background-color:transparent!important;border-bottom:none!important;border-color:transparent!important;box-shadow:none!important;}
        .dark header{background-color:#131519!important;border-bottom:none!important;border-color:transparent!important;box-shadow:none!important;}
        .dark header button.rounded-lg, .dark header button.rounded-xl{background-color:#1f232d!important;}
        .stage-shell{overflow-x:hidden;width:100%;}
        .ks-shell{background:transparent;border:0;border-radius:0;overflow:visible;}
        .dark .ks-shell{background:transparent;border:0;}
        .ks-pad{padding:0 0 1.5rem;}
        :root{--ks-ink:#241f1b;--ks-blue:#1c3e96;--ks-blue-dark:#162f73;--ks-line:#bfbab2;--ks-muted:#6e6862;--ks-card:#fffefa;--ks-card-bd:#dcdad2;--ks-green:#15803d;--ks-green-bg:#dcfce7;--ks-red:#b91c1c;--ks-red-bg:#fee2e2;--ks-yellow:#b45309;--ks-yellow-bg:#fef3c7;}
        .ks-label{font-size:.68rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#6e6862;}
        .dark .ks-label{color:#a89f93;}
        .ks-hero{background:#faf9f6;border:1px solid #bfbab2;border-left:4px solid #1c3e96;border-radius:6px;padding:1rem 1.1rem;}
        .dark .ks-hero{background:#131519;border-color:#222630;border-left-color:#1c3e96;}
        .ks-btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;background:#1c3e96;color:#fff;border:1px solid #1c3e96;border-radius:6px;font-size:.9rem;font-weight:600;padding:.8rem 1.2rem;min-height:44px;transition:background-color .15s ease,transform .1s ease;}
        .ks-btn-primary:hover{background:#162f73;border-color:#162f73;}
        .ks-btn-primary:active{transform:scale(.98);}
        .ks-btn-primary:focus-visible,.ks-btn-secondary:focus-visible,a:focus-visible,button:focus-visible{outline:2px solid #1c3e96;outline-offset:2px;}
        .ks-btn-secondary{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;background:#fff;color:#241f1b;border:1px solid #bfbab2;border-radius:6px;font-size:.9rem;font-weight:500;padding:.8rem 1.2rem;min-height:44px;transition:all .15s ease;}
        .dark .ks-btn-secondary{background:#131519;color:#fff;border-color:#222630;}
        .ks-btn-secondary:hover{border-color:#1c3e96;color:#1c3e96;}
        /* Boutons compacts inline (timeline, lignes) — pas les gros CTA */
        .ks-btn-xs{min-height:30px!important;padding:.3rem .7rem!important;font-size:.74rem!important;font-weight:600;border-radius:5px!important;flex:none;white-space:nowrap;}
        /* Bordures #dcdad2, radius 12px */
        .ks-stat-card{border:1px solid #dcdad2;border-radius:10px;background-color:#fffefa;transition:transform .2s ease,box-shadow .2s ease;}
        .dark .ks-stat-card{background:#131519!important;border:1px solid #222630!important;border-color:#222630!important;}
        .ks-stat-card:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.06);}
        .ks-stat-icon{width:32px;height:32px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;flex:none;background-color:var(--ks-stat-icon-bg,#eef1fb);color:var(--ks-stat-icon-color,#1c3e96);}
        .ks-stat-icon svg{width:16px;height:16px;stroke-width:1.75;}
        .ks-stat-card--green{--ks-stat-accent:#16804a;--ks-stat-icon-bg:#ecfdf5;--ks-stat-icon-color:#15803d;}
        .ks-stat-card--blue{--ks-stat-accent:#1c3e96;--ks-stat-icon-bg:#eef1fb;--ks-stat-icon-color:#1c3e96;}
        .ks-stat-card--amber{--ks-stat-accent:#b45309;--ks-stat-icon-bg:#fffbeb;--ks-stat-icon-color:#b45309;}
        .ks-stat-card--red{--ks-stat-accent:#b91c1c;--ks-stat-icon-bg:#fff1f2;--ks-stat-icon-color:#b91c1c;}
        .ks-stat-card--indigo{--ks-stat-accent:#4338ca;--ks-stat-icon-bg:#eef2ff;--ks-stat-icon-color:#4338ca;}
        .ks-stat-card--violet{--ks-stat-accent:#7e22ce;--ks-stat-icon-bg:#faf5ff;--ks-stat-icon-color:#7e22ce;}
        .ks-stat-footer{border-top:0!important;}
        .ks-stat-change{border-radius:4px;font-size:.72rem;font-weight:600;display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .45rem;}
        .ks-stat-change--positive{color:#166534;background-color:#dcfce7;}
        .ks-stat-change--negative{color:#b91c1c;background-color:#fee2e2;}
        .ks-stat-change--neutral{color:#475569;background-color:#f1f5f9;}
        .ks-num{font-variant-numeric:tabular-nums;}
        .ks-sec{background:#fffefa;border:1px solid #dcdad2;border-radius:10px;box-shadow:0 1px 2px rgba(36,31,27,.05),0 8px 24px -16px rgba(36,31,27,.12);transition:border-color .15s ease,box-shadow .18s ease-out;}
        .dark .ks-sec{background:#131519!important;border:1px solid #222630!important;border-color:#222630!important;box-shadow:none;}
        .ks-sec-head{padding:.8rem 1rem;border-bottom:1px solid #dcdad2;display:flex;align-items:center;justify-content:space-between;gap:.5rem;}
        .dark .ks-sec-head{border-bottom:1px solid #222630!important;border-color:#222630!important;}
        .ks-row{display:flex;align-items:center;gap:.75rem;padding:.8rem 1rem;border-bottom:1px solid #ecebe3;min-height:56px;color:inherit;text-decoration:none;transition:background-color .12s ease,transform .12s ease;}
        .dark .ks-row{border-color:#222630!important;}
        .ks-row:last-child{border-bottom:0;}
        .ks-row:hover{background:#faf9f6;}
        .dark .ks-row:hover{background:#262a30;}
        .ks-row:active{transform:scale(.995);}
        .ks-hero h1{letter-spacing:-.02em;}
        /* C. Entrée premium : fade-up léger en cascade */
        @keyframes ks-rise{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
        .ks-rise{animation:ks-rise .45s cubic-bezier(.2,.8,.2,1) both;}
        @media(prefers-reduced-motion:reduce){.ks-rise{animation:none;}}
        .ks-track{background:#ecebe3;border-radius:999px;overflow:hidden;}
        .dark .ks-track{background:#262a30;}
        /* Ma journée : posée à même le main, pas de carte dans une carte */
        .ks-day{margin:0;padding:.2rem 0 0;}
        .ks-day-head{display:flex;align-items:baseline;justify-content:space-between;gap:.5rem;flex-wrap:wrap;padding:.1rem 0 .5rem;}
        .ks-tl{position:relative;list-style:none;margin:0;padding:0;}
        .ks-tl::before{content:"";position:absolute;left:15px;top:12px;bottom:12px;width:2px;background:#bfbab2;border-radius:2px;opacity:.55;}
        .dark .ks-tl::before{background:#222630!important;}
        .ks-step{position:relative;display:flex;gap:.7rem;padding:.55rem 0;align-items:flex-start;}
        .ks-bullet{position:relative;z-index:1;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex:none;background:#fffefa;border:1px solid #dcdad2;color:#6e6862;font-size:.85rem;}
        .dark .ks-bullet{background:#131519!important;border:1px solid #222630!important;}
        .ks-step.done .ks-bullet{background:#dcfce7;border-color:#15803d;color:#15803d;}
        .ks-step.now .ks-bullet{background:#1c3e96;border-color:#1c3e96;color:#fff;box-shadow:0 0 0 4px rgba(28,62,150,.14);}
        .ks-step.todo .ks-bullet{background:#fffefa;border-style:dashed;}
        .ks-step-body{min-width:0;flex:1;}
        @media(max-width:480px){
            .ks-tl::before{left:13px;}
            .ks-bullet{width:28px;height:28px;font-size:.75rem;}
            .ks-step{gap:.6rem;padding:.5rem 0;}
            .ks-step-body p{overflow-wrap:anywhere;}
        }
        details.ks-details summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.8rem 1rem;font-size:.82rem;font-weight:600;color:#57544f;min-height:44px;}
        details.ks-details summary::-webkit-details-marker{display:none;}
        details.ks-details summary:hover{color:#0f0e0d;}
        .ks-sticky{position:fixed;left:0;right:0;bottom:0;z-index:40;background:rgba(255,255,254,.92);backdrop-filter:blur(12px);border-top:1px solid #e3e0da;padding:.6rem .9rem calc(.6rem + env(safe-area-inset-bottom));display:flex;gap:.6rem;}
        .dark .ks-sticky{background:rgba(36,31,27,.92);border-color:#222630!important;}
        @media(min-width:1024px){.ks-sticky{display:none;}}
        .stage-shell{overflow-x:clip;} .stage-shell *{min-width:0;}
        @media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important;}}

        /* ——— Dark Hope UI / Kaisss : textes froids, lisibilité totale ——— */
        .dark .text-gray-400,.dark .text-gray-500,.dark .text-slate-400{color:#9aa0a6!important;}
        .dark h1,.dark h2,.dark h3{color:#f8fafc!important;}
        .dark .ks-label{color:#a89f93!important;}

        /* Rendre visible les classes Bootstrap utilisées dans les cartes */
        .dark .text-dark{color:#f8fafc!important;}
        .dark .text-secondary{color:#94a3b8!important;}
        .dark .text-muted{color:#94a3b8!important;}

        /* Rendre visible les textes de couleurs en dur du thème */
        .dark .text-\[\#241f1b\]{color:#f8fafc!important;}
        .dark .text-\[\#6e6862\]{color:#94a3b8!important;}
        .dark .text-\[\#1c3e96\]{color:#60a5fa!important;}
        .dark .text-\[\#57544f\]{color:#cbd5e1!important;}

        /* KPI cards en dark mode */
        .dark .ks-stat-card--green{--ks-stat-icon-bg:rgba(22,128,74,0.22);--ks-stat-icon-color:#4ade80;}
        .dark .ks-stat-card--blue{--ks-stat-icon-bg:rgba(28,62,150,0.25);--ks-stat-icon-color:#60a5fa;}
        .dark .ks-stat-card--amber{--ks-stat-icon-bg:rgba(180,83,9,0.22);--ks-stat-icon-color:#fbbf24;}
        .dark .ks-stat-card--red{--ks-stat-icon-bg:rgba(185,28,28,0.22);--ks-stat-icon-color:#f87171;}
        .dark .ks-stat-card--indigo{--ks-stat-icon-bg:rgba(67,56,202,0.25);--ks-stat-icon-color:#818cf8;}
        .dark .ks-stat-card--violet{--ks-stat-icon-bg:rgba(126,34,206,0.25);--ks-stat-icon-color:#c084fc;}

        .dark .ks-stat-change--positive{color:#4ade80!important;background-color:rgba(22,128,74,0.25)!important;}
        .dark .ks-stat-change--negative{color:#f87171!important;background-color:rgba(185,28,28,0.25)!important;}
        .dark .ks-stat-change--neutral{color:#cbd5e1!important;background-color:#262a30!important;}
        .dark .ks-eyebrow{background:#262a30!important;color:#d9d2c8!important;}

        .dark .ks-stat-card--violet .ks-num{color:#c084fc!important;}
        .dark .ks-stat-card--violet .text-uppercase{color:#c084fc!important;}
        .dark .ks-stat-card--violet .ks-stat-change{background:rgba(126,34,206,0.2)!important;color:#c8a2f0!important;}
        .dark .ks-stat-card--indigo .badge{background:rgba(218,107,64,0.2)!important;color:#fb923c!important;}
        .dark .ks-stat-card--indigo .ks-stat-icon{background-color:rgba(218,107,64,0.2)!important;color:#fb923c!important;}

        /* Cartes de tâches en dark mode */
        .dark .group.rounded-md{background-color:#15181f!important;border:1px solid #222630!important;border-color:#222630!important;}
        .dark .ks-track{background:#262a30!important;}
        .dark .bg-\[\#1c3e96\]{background-color:#3b82f6!important;}
        .dark .bg-\[\#eef1fb\]{background-color:rgba(59,130,246,0.2)!important;color:#93c5fd!important;}
        .dark .bg-\[\#faf9f6\]{background-color:#181c24!important;}

        /* ——— Remplacement strict de toutes les bordures claires en mode dark ——— */
        .dark .border,
        .dark .border-t,
        .dark .border-b,
        .dark [class*="border-[#dcdad2]"],
        .dark [class*="border-[#ecebe3]"],
        .dark [class*="border-[#bfbab2]"],
        .dark [class*="border-[#d8d4cc]"],
        .dark [class*="border-[#a7f3d0]"],
        .dark .border-\[\#ecebe3\],
        .dark .border-\[\#dcdad2\],
        .dark .border-\[\#bfbab2\],
        .dark .border-\[\#d8d4cc\],
        .dark .border-\[\#a7f3d0\]{
            border-color:#222630!important;
        }
        .dark [class*="border-dashed"]{
            border-color:#282f3d!important;
        }

        /* Cadre du stage en dark mode */
        .dark [aria-label="Cadre du stage"] p.font-semibold{color:#f8fafc!important;}
        .dark [aria-label="Cadre du stage"] .bg-\[\#ecfdf5\]{background-color:rgba(34,197,94,0.2)!important;color:#4ade80!important;}
        .dark [aria-label="Cadre du stage"] .bg-\[\#fef3c7\]{background-color:rgba(245,158,11,0.2)!important;color:#fbbf24!important;}
        .dark [aria-label="Cadre du stage"] .bg-\[\#f1f5f9\]{background-color:rgba(148,163,184,0.2)!important;color:#cbd5e1!important;}
        .dark [aria-label="Cadre du stage"] [x-text]{color:#f8fafc!important;}
        .dark [aria-label="Cadre du stage"] input{background-color:#15181f!important;border-color:#222630!important;color:#f8fafc!important;}

        /* Rapport final & parcours en dark mode */
        .dark [aria-label="Rapport final"] label{background-color:#15181f!important;border-color:#282f3d!important;color:#94a3b8!important;}
        .dark [aria-label="Mon parcours"] li span.font-semibold{color:#f8fafc!important;}
        .dark [aria-label="Mon parcours"] li span.text-gray-400{color:#94a3b8!important;}

        /* Scrollbars + sélection teintées primaire */
        *{scrollbar-width:thin;scrollbar-color:rgba(28,62,150,.45) transparent;}
        ::-webkit-scrollbar{width:10px;height:10px;}
        ::-webkit-scrollbar-track{background:transparent;}
        ::-webkit-scrollbar-thumb{background-color:rgba(28,62,150,.45);border-radius:10px;border:2px solid transparent;background-clip:content-box;}
        ::-webkit-scrollbar-thumb:hover{background-color:rgba(28,62,150,.75);}
        ::selection{background:rgba(28,62,150,.25);}
    </style>

    <div class="w-full stage-shell">
        <span class="sr-only">Mon stage - Tâches du stage</span>
        <div class="ks-shell"><div class="ks-pad space-y-4">

        @if(!$activeStage)
            <div class="ks-sec p-5 text-center">
                <p class="font-semibold text-gray-900 dark:text-white">Aucun stage actif</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Rattaché à aucun stage aujourd'hui — vois avec l'administration.</p>
            </div>
        @else
        @php
            $stMap = ['late'=>['En retard','#b45309'],'present'=>['Présent','#15803d'],'absent'=>['Absent','#b91c1c'],'incomplete'=>['Incomplet','#b45309'],'pending'=>['En attente','#1b38a7'],'completed'=>['Terminé','#15803d']];
            $stKey = $attendanceDay?->day_status; $stLib = $stMap[$stKey][0] ?? ($stKey ? ucfirst($stKey) : 'Non pointé'); $stCol = $stMap[$stKey][1] ?? '#57544f';
            if($etudiant->user_id ?? auth()->id()){
                if(\App\Models\AttendanceException::where('user_id', $etudiant->user_id ?? auth()->id())->whereDate('attendance_date', today())->exists()){ $stLib='Jour permissionné'; $stCol='#0e7490'; }
            }
            $repMap = ['submitted'=>'Soumis','reviewed'=>'Validé','approved'=>'Approuvé','draft'=>'Brouillon','pending'=>'En attente','rejected'=>'Rejeté'];
            $repLib = $repMap[$todayReport?->status ?? ''] ?? ($todayReport?->status ? ucfirst($todayReport->status) : 'En attente');
        @endphp
        @if(session('success'))
            <div role="status" class="rounded-md border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/40 dark:border-emerald-800/60 dark:text-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div x-data="{ editing:false, theme:@js($activeStage->theme ?? '') }">
            {{-- 1. Actions à droite --}}
            <div class="ks-rise flex flex-wrap items-center justify-between sm:justify-end gap-2 w-full" style="animation-delay:.03s">
                <a href="{{ $nextRoute }}" class="ks-btn-primary ks-btn-xs !text-[13px] !px-4 !py-2 shrink-0 whitespace-nowrap">{{ $nextCta }}</a>
                @if($nextSecond)<a href="{{ $nextSecond[1] }}" class="ks-btn-secondary ks-btn-xs !text-[13px] !px-4 !py-2 shrink-0 whitespace-nowrap">{{ $nextSecond[0] }}</a>@endif
            </div>

            {{-- 2. STRIP KPI façon MRR Kaisss : même bordure 1px #eaedf2 + graphe sparkline --}}
            @php
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
                $jp = $joursPresentSemaine ?? 0;
                $spPresence = $spark([max(0,$jp-2), max(0,$jp-1), max(0,$jp-0.5), $jp, $jp], '#16804a');
                $cr = $todayReport?->completion_rate ?? 0;
                $spRapport = $spark([0, $cr*0.3, $cr*0.6, $cr, $cr], '#1c3e96');
                $pg = $progressionStage ?? 0;
                $spStage = $spark([max(0,$pg-15), max(0,$pg-10), max(0,$pg-5), $pg, $pg], '#1c3e96');
                $accPresence = ($attendanceDay?->day_status === 'absent') ? 'red' : ($hasIn ? 'green' : 'amber');
                $chgPresence = ($attendanceDay?->day_status === 'absent') ? 'Absent' : (($joursPresentSemaine ?? 0) . '/' . ($joursTrackesSemaine ?? 0) . ' sem.');
                $typePresence = ($attendanceDay?->day_status === 'absent') ? 'negative' : ($hasIn ? 'positive' : 'neutral');
                $accRapport = in_array($todayReport?->status ?? null, ['reviewed','approved']) ? 'green' : (($todayReport?->status === 'rejected') ? 'red' : (($todayReport?->status === 'submitted') ? 'blue' : 'amber'));
                $typeRapport = in_array($todayReport?->status ?? null, ['reviewed','approved','submitted']) ? 'positive' : (($todayReport?->status === 'rejected') ? 'negative' : 'neutral');
            @endphp
            {{-- 2. STRIP KPI : 4 cartes — Site et Superviseur ont chacun la leur, plus rien de coincé --}}
            <section aria-label="État du jour" class="ks-rise mt-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3" style="animation-delay:.06s">
                <div class="ks-stat-card ks-stat-card--{{ $accPresence }} border shadow-none h-100">
                    <div class="p-3 flex flex-col justify-between h-100">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-secondary font-semibold truncate" style="font-size:.8125rem;">Présence</span>
                            <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg></span>
                        </div>
                        <div class="my-1 flex items-baseline gap-1"><span class="font-bold text-dark ks-num" style="font-size:1.45rem;line-height:1.15;white-space:nowrap;">{{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}</span><span class="font-semibold text-muted text-uppercase" style="font-size:.775rem;">→ {{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }} · {{ $stLib }}</span></div>
                        <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                            <span class="badge ks-stat-change ks-stat-change--{{ $typePresence }}">{{ $chgPresence }}</span>
                            <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true"><path d="{{ $spPresence['area'] }}" fill="{{ $spPresence['accent'] }}" opacity="0.12"/><path d="{{ $spPresence['line'] }}" fill="none" stroke="{{ $spPresence['accent'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
                <div class="ks-stat-card ks-stat-card--violet border shadow-none h-100">
                    <div class="p-3 flex flex-col justify-between h-100">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-secondary font-semibold truncate" style="font-size:.8125rem;">Rapport</span>
                            <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></span>
                        </div>
                        <div class="my-1 flex items-baseline gap-1"><span class="font-bold ks-num" style="font-size:1.45rem;line-height:1.15;color:#7e22ce;">{{ $todayReport?->completion_rate ?? 0 }}%</span><span class="font-semibold text-uppercase" style="font-size:.775rem;color:#7e22ce;">{{ $repLib }}</span></div>
                        <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                            <span class="badge ks-stat-change" style="background:#faf5ff;color:#7e22ce;">maj {{ $todayReport?->updated_at?->format('H:i') ?: '—' }}</span>
                            <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true"><path d="{{ $spRapport['area'] }}" fill="#7e22ce" opacity="0.12"/><path d="{{ $spRapport['line'] }}" fill="none" stroke="#7e22ce" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
                <div class="ks-stat-card ks-stat-card--blue border shadow-none h-100">
                    <div class="p-3 flex flex-col justify-between h-100">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-secondary font-semibold truncate" style="font-size:.8125rem;">Site d'affectation</span>
                            <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-5.5-7-11a7 7 0 0114 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                        </div>
                        <div class="my-1 min-w-0"><p class="font-bold text-dark truncate" style="font-size:1.1rem;line-height:1.2;">{{ $activeStage->site?->name ?: 'Site non défini' }}</p><p class="text-muted truncate" style="font-size:.775rem;">{{ $activeStage->site?->city ?: 'Ville non définie' }}</p></div>
                        <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                            <span class="badge ks-stat-change ks-stat-change--neutral">{{ $progressionStage }}% · reste {{ $joursRestants }}j</span>
                            <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true"><path d="{{ $spStage['area'] }}" fill="{{ $spStage['accent'] }}" opacity="0.12"/><path d="{{ $spStage['line'] }}" fill="none" stroke="{{ $spStage['accent'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
                <div class="ks-stat-card ks-stat-card--indigo border shadow-none h-100">
                    <div class="p-3 flex flex-col justify-between h-100">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-secondary font-semibold truncate" style="font-size:.8125rem;">Superviseur</span>
                            <span class="ks-stat-icon" style="background-color:#f8efe9;color:#da6b40;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                        </div>
                        <div class="my-1 min-w-0"><p class="font-bold text-dark truncate" style="font-size:1.1rem;line-height:1.2;">{{ $activeStage->supervisor?->name ?: 'Aucun superviseur' }}</p><p class="text-muted truncate" style="font-size:.775rem;">{{ $activeStage->supervisor?->email ?: 'Email non défini' }}</p></div>
                        <div class="ks-stat-footer flex items-center justify-between gap-2 mt-2 pt-1">
                            <span class="badge ks-stat-change" style="background:#f8efe9;color:#da6b40;">{{ $activeStage->domaine?->nom ?: 'Domaine —' }}</span>
                            <svg width="74" height="26" viewBox="0 0 100 36" style="overflow:visible;" aria-hidden="true"><path d="{{ $spStage['area'] }}" fill="#da6b40" opacity="0.12"/><path d="{{ $spStage['line'] }}" fill="none" stroke="#da6b40" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
            </section>

            <div class="mt-4 grid grid-cols-1 xl:grid-cols-4 gap-3 xl:items-start">
                {{-- 3. MES TÂCHES à la place de Ma journée --}}
                <section aria-label="Mes tâches" class="ks-sec ks-rise min-w-0 xl:col-span-3" style="animation-delay:.12s">
                    <div class="ks-sec-head"><h2 class="text-sm font-semibold text-[#241f1b] dark:text-white">Mes tâches · {{ $tasks->count() }}</h2><a href="{{ route('tasks.index') }}" class="text-xs font-medium text-[#1c3e96] hover:underline">Espace travail →</a></div>
                    @if($tasks->isEmpty())
                        <div class="p-6 text-center">
                            <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-md bg-[#eef1fb] text-[#1c3e96]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            <p class="mt-3 text-sm font-semibold text-[#241f1b] dark:text-white">Journée légère, aucune tâche assignée</p>
                            <p class="mx-auto mt-1 max-w-xs text-xs text-[#6e6862]">Décris ton avancement dans le rapport journalier, il servira de trace.</p>
                            <a href="{{ route('tasks.index') }}" class="ks-btn-secondary ks-btn-xs mx-auto mt-3">Ouvrir le rapport</a>
                        </div>
                    @else
                        @php
                            $ordre = ['in_progress' => 0, 'pending' => 1, 'changes_requested' => 2, 'blocked' => 3, 'awaiting_validation' => 4, 'completed' => 5];
                            $tasks6 = $tasks->sortBy(fn($t) => [($ordre[$t->status] ?? 9), -($t->updated_at?->timestamp ?? 0)])->take(6);
                        @endphp
                        <div class="p-3 sm:p-4 space-y-3">
                            @foreach($tasks6 as $task)
                            @php
                                $myReports = $task->dailyReports->where('user_id', $user->id)->take(2);
                                $statusMeta = match ($task->status) {
                                    'completed' => ['Terminée', 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 ring-emerald-600/20'],
                                    'awaiting_validation' => ['En attente validation', 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 ring-purple-600/20'],
                                    'blocked' => ['Bloquée', 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 ring-rose-600/20'],
                                    'in_progress' => ['En cours', 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 ring-blue-600/20'],
                                    'changes_requested' => ['Corrections demandées', 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 ring-amber-600/20'],
                                    default => ['À faire', 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-200 ring-slate-600/20'],
                                };
                                $priorityMeta = match ($task->priority) {
                                    'urgent' => ['Urgente', 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 ring-rose-600/20'],
                                    'high' => ['Haute', 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 ring-amber-600/20'],
                                    'low' => ['Basse', 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-gray-300 ring-slate-400/20'],
                                    default => ['Normale', 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-indigo-500/20'],
                                };
                                $pp = min(100, max(0, $task->last_progress_percent ?? $task->base_progress_percent ?? 0));
                            @endphp
                            <div class="group rounded-md border border-[#dcdad2] dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm min-w-0">
                                <a href="{{ encrypted_route('tasks.show', $task) }}" class="block min-w-0">
                                    <div class="flex w-full flex-col gap-3 md:flex-row md:items-start md:justify-between min-w-0">
                                        <div class="flex-1 min-w-0 md:pr-4">
                                            <h3 class="text-[15px] font-semibold text-[#241f1b] dark:text-white group-hover:text-[#1c3e96] transition-colors leading-snug break-words">{{ $task->title }}</h3>
                                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $priorityMeta[1] }}">{{ $priorityMeta[0] }}</span>
                                                @if ($task->owner_id === $user->id)
                                                <span class="inline-flex items-center rounded-full bg-[#eef1fb] dark:bg-[#1c3e96]/20 px-2.5 py-0.5 text-[11px] font-medium text-[#1c3e96] dark:text-[#60a5fa] ring-1 ring-inset ring-[#1c3e96]/20 dark:ring-[#60a5fa]/30">Créée par toi</span>
                                                @else
                                                <span class="inline-flex items-center rounded-full bg-sky-50 dark:bg-sky-500/20 px-2.5 py-0.5 text-[11px] font-medium text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-500/20 dark:ring-sky-400/30">Assignée à toi</span>
                                                @endif
                                            </div>
                                            <p class="mt-2 text-[13px] text-[#6e6862] dark:text-gray-400 leading-relaxed break-words">{{ $task->description ?: 'Aucune description fournie.' }}</p>
                                            <div class="mt-2.5 flex max-w-xs items-center gap-2">
                                                <div class="ks-track h-1.5 w-full"><div class="h-1.5 rounded-full {{ $task->status === 'completed' ? 'bg-[#15803d]' : 'bg-[#1c3e96]' }}" style="width:{{ $pp }}%"></div></div>
                                                <span class="text-xs font-semibold tabular-nums text-[#241f1b] dark:text-gray-200">{{ $pp }}%</span>
                                            </div>
                                            @if ($myReports->isNotEmpty())
                                            <div class="mt-3">
                                                <p class="ks-label !text-[10px]">Tes rapports déposés</p>
                                                <div class="mt-1.5 space-y-1.5">
                                                    @foreach ($myReports as $report)
                                                    <div class="flex items-center gap-2 text-xs text-[#6e6862] dark:text-gray-300">
                                                        <svg class="h-3.5 w-3.5 shrink-0 text-[#15803d]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $report->report_date->isoFormat('DD MMM') }}</span><span>·</span><span class="font-semibold tabular-nums text-slate-800 dark:text-slate-200">{{ $report->task_progress_percent ?? 0 }}%</span><span>·</span><span class="truncate">{{ $report->summary ?: 'Rapport déposé' }}</span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-1 gap-x-4 gap-y-1 text-xs text-[#6e6862] shrink-0 md:text-right md:min-w-[170px]">
                                            <div>Priorité : <span class="font-semibold text-[#241f1b] dark:text-gray-200">{{ ['high'=>'Haute','low'=>'Basse','medium'=>'Normale','urgent'=>'Urgente'][$task->priority] ?? ucfirst($task->priority ?? 'Normale') }}</span></div>
                                            <div>Démarré le : <span class="font-semibold text-[#241f1b] dark:text-gray-200 tabular-nums">{{ $task->started_at?->format('d/m/Y') ?: ($task->created_at?->format('d/m/Y') ?: '--') }}</span></div>
                                            <div class="col-span-2 md:col-span-1">date de fin : <span class="font-semibold text-[#241f1b] dark:text-gray-200 tabular-nums">{{ $task->due_date?->format('d/m/Y') ?: 'Non définie' }}</span></div>
                                            <div class="col-span-2 md:col-span-1 mt-1 inline-flex items-center gap-1 font-semibold text-[#1c3e96] dark:text-blue-400 md:justify-end">Ouvrir l'espace de travail <span aria-hidden="true">→</span></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                            @if($tasks->count() > 6)
                            <div class="px-4 pb-4"><a href="{{ route('tasks.index') }}" class="ks-btn-secondary ks-btn-xs w-full">Voir les {{ $tasks->count() - 6 }} autres dans l'espace travail →</a></div>
                            @endif
                        </div>
                    @endif
                </section>

                {{-- 4. CADRE : grille 2x2 mini-tuiles + jauge, plus de simple liste --}}
                <aside aria-label="Cadre du stage" class="ks-sec ks-rise p-4 xl:col-span-1 min-w-0 xl:sticky xl:top-[84px] flex flex-col" style="animation-delay:.18s">
                    <div class="flex items-center justify-between gap-2">
                        <p class="ks-label">Cadre du stage</p>
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#eef1fb] px-2 py-0.5 text-[10px] font-semibold text-[#1c3e96] dark:bg-indigo-950/60 dark:text-indigo-300">{{ $joursRestants }}j restants</span>
                    </div>
                    {{-- Thème déplacé ici : plus en haut de page --}}
                    <div class="mt-2 rounded-md border border-[#ecebe3] dark:border-gray-700 bg-[#faf9f6] dark:bg-gray-900/40 p-2.5">
                        <p class="ks-label !text-[10px]">Thème</p>
                        <p x-show="!editing" class="truncate text-xs font-semibold text-slate-800 dark:text-white" x-text="theme || 'Stage sans thème'"></p>
                        <button x-show="!editing" @click="editing=true" class="mt-0.5 text-[11px] font-medium text-[#1c3e96] dark:text-blue-400 hover:underline">Modifier le thème</button>
                        <form x-show="editing" x-cloak method="POST" action="{{ route('student.theme.update') }}" class="mt-1.5 flex gap-1.5">
                            @csrf @method('PUT')
                            <input name="theme" x-model="theme" aria-label="Thème du stage" class="min-w-0 flex-1 rounded-md border border-[#d8d4cc] dark:border-gray-700 dark:bg-gray-800 dark:text-white px-2.5 py-1.5 text-xs focus:border-[#1c3e96] focus:outline-none" placeholder="Ex : Développement web…">
                            <button class="ks-btn-primary ks-btn-xs">OK</button>
                            <button type="button" @click="editing=false" aria-label="Annuler" class="ks-btn-secondary ks-btn-xs">✕</button>
                        </form>
                    </div>
                    <div class="mt-2.5 space-y-2">
                        <div class="rounded-md border border-[#ecebe3] dark:border-gray-700 bg-[#faf9f6] dark:bg-gray-900/40 p-2.5 flex items-center gap-3">
                            <span class="flex h-7 w-7 flex-none items-center justify-center rounded-md bg-[#eef1fb] text-[#1c3e96] dark:bg-indigo-950/60 dark:text-indigo-400"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                            <div class="min-w-0 flex-1">
                                <p class="ks-label !text-[10px]">Période</p>
                                <p class="truncate text-xs font-semibold tabular-nums text-slate-800 dark:text-white">{{ $activeStage->date_debut?->format('d/m/y') }} → {{ $activeStage->date_fin?->format('d/m/y') }}</p>
                            </div>
                        </div>
                        <div class="rounded-md border border-[#ecebe3] dark:border-gray-700 bg-[#faf9f6] dark:bg-gray-900/40 p-2.5 flex items-center gap-3">
                            <span class="flex h-7 w-7 flex-none items-center justify-center rounded-md bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/60 dark:text-emerald-400"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg></span>
                            <div class="min-w-0 flex-1">
                                <p class="ks-label !text-[10px]">Domaine</p>
                                <p class="truncate text-xs font-semibold text-slate-800 dark:text-white">{{ $activeStage->domaine?->nom ?: '—' }}</p>
                            </div>
                        </div>
                        <div class="rounded-md border border-[#ecebe3] dark:border-gray-700 bg-[#faf9f6] dark:bg-gray-900/40 p-2.5 flex items-center gap-3">
                            <span class="flex h-7 w-7 flex-none items-center justify-center rounded-md bg-[#fef3c7] text-[#b45309] dark:bg-amber-950/60 dark:text-amber-400"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10v10H7zM7 3v4m10-4v4"/></svg></span>
                            <div class="min-w-0 flex-1">
                                <p class="ks-label !text-[10px]">Type</p>
                                <p class="truncate text-xs font-semibold text-slate-800 dark:text-white">{{ $activeStage->typestage?->libelle ?: '—' }}</p>
                            </div>
                        </div>
                        <div class="rounded-md border border-[#ecebe3] dark:border-gray-700 bg-[#faf9f6] dark:bg-gray-900/40 p-2.5 flex items-center gap-3">
                            <span class="flex h-7 w-7 flex-none items-center justify-center rounded-md bg-[#f1f5f9] text-[#475569] dark:bg-slate-800 dark:text-slate-300"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-5.5-7-11a7 7 0 0114 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                            <div class="min-w-0 flex-1">
                                <p class="ks-label !text-[10px]">Présence</p>
                                <p class="truncate text-xs font-semibold text-slate-800 dark:text-white">{{ ['geolocation_only'=>'Géolocalisée','qr_code'=>'QR Code','manual'=>'Manuelle'][$activeStage->presence_mode] ?? 'Géolocalisée' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-[#ecebe3] dark:border-gray-700">
                        @php $segColor = $presenceSemaine >= 80 ? '#15803d' : ($presenceSemaine >= 50 ? '#b45309' : '#b91c1c'); $segTotal = max(1, (int) ($joursTrackesSemaine ?: 5)); $segDone = min($segTotal, (int) $joursPresentSemaine); @endphp
                        <div class="flex items-center justify-between gap-2">
                            <span class="ks-label !text-[10px]">Semaine</span>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold tabular-nums" style="background:{{ $segColor }}14;color:{{ $segColor }}">{{ $joursPresentSemaine }}/{{ $joursTrackesSemaine }} · {{ $presenceSemaine }}%</span>
                        </div>
                        <div class="mt-2 flex gap-1" role="img" aria-label="Présence {{ $joursPresentSemaine }} sur {{ $joursTrackesSemaine }}">
                            @for($i = 0; $i < $segTotal; $i++)
                                <span class="h-1.5 flex-1 rounded-full {{ $i < $segDone ? '' : 'dark:!bg-gray-800' }}" style="background:{{ $i < $segDone ? $segColor : '#ecebe3' }}"></span>
                            @endfor
                        </div>
                    </div>
                </aside>
            </div>

            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                {{-- 6. DOCUMENT : dropzone horizontale --}}
                <section aria-label="Rapport final" class="ks-sec p-4">
                    <p class="ks-label">Rapport final · PDF</p>
                    @if($activeStage->final_report_path)
                    <div class="mt-2 flex items-center gap-2 rounded-md border border-[#a7f3d0] dark:border-emerald-800 bg-[#dcfce7]/50 dark:bg-emerald-950/40 p-2.5 text-xs"><span class="flex h-8 w-8 flex-none items-center justify-center rounded-md bg-[#15803d]/10 dark:bg-emerald-900/60 text-[#15803d] dark:text-emerald-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></span><span class="min-w-0 flex-1 truncate font-semibold text-[#15803d] dark:text-emerald-300">{{ $activeStage->final_report_name }}</span><a href="{{ $activeStage->final_report_url }}" target="_blank" class="ks-btn-primary ks-btn-xs flex-none">Voir</a></div>
                    @else
                    <div class="mt-2 flex items-center gap-2.5 rounded-md border border-dashed border-[#bfbab2] dark:border-gray-700 bg-[#faf9f6] dark:bg-gray-900/40 p-3">
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-md bg-[#fef3c7] dark:bg-amber-950/60 text-[#b45309] dark:text-amber-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9m0 0l-3 3m3-3l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                        <p class="text-xs text-[#6e6862] dark:text-gray-300">Pas encore déposé — ton PDF final arrivera ici.</p>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('student.final_report.upload') }}" enctype="multipart/form-data" class="mt-2 flex gap-2">
                        @csrf
                        <label class="min-w-0 flex-1 cursor-pointer truncate rounded-md border border-dashed border-[#d8d4cc] dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-xs text-gray-500 dark:text-gray-300 hover:border-[#da6b40]"><span>Choisir PDF…</span><input type="file" name="final_report" accept=".pdf,application/pdf" class="sr-only" onchange="this.parentElement.querySelector('span').textContent=this.files[0]?this.files[0].name:'Choisir PDF…'"></label>
                        <button class="ks-btn-primary ks-btn-xs flex-none">{{ $activeStage->final_report_path ? 'Remplacer' : 'Déposer' }}</button>
                    </form>
                    @error('final_report')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </section>
                {{-- 7. PARCOURS : une ligne par stage --}}
                <section aria-label="Mon parcours" class="ks-sec p-4">
                    <p class="ks-label">Parcours · {{ $chaines->count() }} stage(s)</p>
                    <ul class="mt-2 space-y-2">
                    @forelse($chaines as $c)
                        <li class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 rounded-full flex-none" style="background:{{ $c['en_cours'] ? '#da6b40' : '#a7f3d0' }}"></span>
                            <span class="min-w-0 flex-1 truncate font-semibold text-slate-800 dark:text-white">{{ $c['racine']->theme ?: 'Stage' }} <span class="font-normal text-gray-400">{{ $c['debut']?->format('m/y') }}→{{ $c['fin']?->format('m/y') }} · {{ $c['mois'] }} mois · {{ $c['taches_terminees'] }}/{{ $c['taches'] }} · {{ $c['attestations']->count() }} att.</span></span>
                            <span class="flex-none text-[10px] font-semibold uppercase" style="color:{{ $c['en_cours'] ? '#da6b40' : '#15803d' }}">{{ $c['en_cours'] ? 'En cours' : 'Fait' }}</span>
                        </li>
                    @empty
                        <li class="text-xs text-gray-400">Aucun stage enregistré.</li>
                    @endforelse
                    </ul>
                </section>
            </div>
        </div>
        @endif

        {{-- 8. STICKY MOBILE : pointer / rapport toujours à portée de pouce --}}
        @if(isset($activeStage) && $activeStage)
        <div class="ks-sticky lg:hidden" role="toolbar" aria-label="Actions rapides">
            <a href="{{ route('presence.pointage') }}" class="ks-btn-primary flex-1">Pointer</a>
            <a href="{{ route('tasks.index') }}" class="ks-btn-secondary flex-1">Rapport</a>
        </div>
        @endif

        </div></div>
    </div>
</x-app-layout>
