<x-app-layout title="Antécédents {{ $user->name }}">
<<<<<<< HEAD
<x-slot name="header">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ $user->name }}</h1>
            <p class="mt-1 text-xl text-slate-600">
                @if($userStats['is_etudiant'])
                    {{ $user->etudiant->nom }} {{ $user->etudiant->prenom }} - Stagiaire
                @else
                    Employé - {{ $user->domaine?->nom ?? 'Non assigné' }}
                @endif
            </p>
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    {{-- Période tabs --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-1">
        <nav class="-mb-px flex space-x-8">
            @foreach(['month' => 'Mois', 'week' => 'Semaine', 'year' => 'Année'] as $periodKey => $label)
                <a href="?period={{ $periodKey }}" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === $periodKey ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Cards utilisateur --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stats-card title="Jours Pointés" value="{{ $userStats['total_days'] }}" icon="calendar" color="slate">
            <x-slot:subtitle>{{ $userStats['present_days'] }} présents</x-slot:subtitle>
        </x-stats-card>

        <x-stats-card title="Taux Présence" value="{{ round(($userStats['present_days'] / max(1, $userStats['total_days'])) * 100, 1) }}%" icon="check-circle" color="emerald">
            <x-slot:subtitle>+{{ $userStats['late_days'] ?? 0 }} retards</x-slot:subtitle>
        </x-stats-card>

        <x-stats-card title="Heures Totales" value="{{ $userStats['total_worked_hours'] }}h" icon="clock" color="blue">
            <x-slot:subtitle>Moy. {{ $userStats['avg_daily_hours'] }}h/jour</x-slot:subtitle>
        </x-stats-card>

        <x-stats-card title="Retards Cumulés" value="{{ $userStats['total_late_minutes'] }}min" icon="exclamation" color="amber">
            <x-slot:subtitle>{{ $userStats['open_anomalies'] }} anomalies ouvertes</x-slot:subtitle>
        </x-stats-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Graph heures travaillées --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">⏱️ Heures Travaillées</h3>
            <canvas id="workedHoursChart" height="300"></canvas>
        </div>

        {{-- Graph retards --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">⚠️ Retards Journaliers</h3>
            <canvas id="lateChart" height="300"></canvas>
        </div>
    </div>

    {{-- Anomalies récentes --}}
    @if($anomalies->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    🚨 {{ $anomalies->count() }} Anomalie{{ $anomalies->count() > 1 ? 's' : '' }} Ouverte{{ $anomalies->count() > 1 ? 's' : '' }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Sévérité</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Action</th>
=======

    <x-slot name="header">
        <div class="us-header">
            <div class="us-header-left">
                <div class="us-header-badge">
                    @if($userStats['is_etudiant']) Stagiaire @else Employé @endif
                </div>
                <h1 class="us-header-title">{{ $user->name }}</h1>
                <p class="us-header-sub">
                    @if($userStats['is_etudiant'])
                        {{ $user->etudiant->nom }} {{ $user->etudiant->prenom }}
                    @else
                        {{ $user->domaine?->nom ?? 'Domaine non assigné' }}
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.presence.index') }}" class="us-btn-back">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Retour
            </a>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
        :root {
            --bg:      #0f1117;
            --bg2:     #161b27;
            --bg3:     #1c2333;
            --border:  rgba(255,255,255,0.07);
            --border-hi:rgba(255,255,255,0.14);
            --text:    #e8eaf0;
            --muted:   #6b7280;
            --emerald: #10b981;
            --amber:   #f59e0b;
            --rose:    #f43f5e;
            --blue:    #3b82f6;
            --font:    'DM Sans', sans-serif;
            --mono:    'DM Mono', monospace;
        }
        .us-wrap*{ box-sizing:border-box; }
        .us-wrap{ font-family:var(--font); color:var(--text); }

        /* header */
        .us-header{ display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem; }
        .us-header-badge{
            display:inline-block; font-size:.7rem; font-weight:600; letter-spacing:.12em; text-transform:uppercase;
            color:var(--blue); background:rgba(59,130,246,.12); border:1px solid rgba(59,130,246,.25);
            border-radius:999px; padding:.25rem .75rem; margin-bottom:.5rem;
        }
        .us-header-title{ font-size:1.75rem; font-weight:700; color:#fff; margin:0; }
        .us-header-sub{ font-size:.88rem; color:var(--muted); margin:.25rem 0 0; }
        .us-btn-back{
            display:flex; align-items:center; gap:.5rem; padding:.5rem 1.1rem;
            background:var(--bg2); border:1px solid var(--border); color:var(--text);
            font-size:.82rem; font-weight:500; border-radius:.65rem; text-decoration:none; transition:all .2s;
        }
        .us-btn-back:hover{ border-color:var(--border-hi); }

        /* page */
        .us-page{ max-width:1300px; margin:0 auto; padding:1.5rem 1.5rem 3rem; }

        /* tabs */
        .us-tabs{
            display:flex; gap:.25rem; background:var(--bg2); border:1px solid var(--border);
            border-radius:.75rem; padding:.3rem; width:fit-content;
        }
        .us-tab{
            padding:.4rem 1rem; border-radius:.5rem; font-size:.82rem; font-weight:500;
            color:var(--muted); text-decoration:none; transition:all .2s;
        }
        .us-tab:hover{ color:var(--text); background:var(--bg3); }
        .us-tab.active{ background:var(--bg3); color:#fff; font-weight:600; }

        /* kpis */
        .us-kpis{ display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
        @media(max-width:900px){ .us-kpis{ grid-template-columns:repeat(2,1fr); } }

        .us-kpi{
            background:var(--bg2); border:1px solid var(--border); border-radius:1rem;
            padding:1.3rem 1.4rem; position:relative; overflow:hidden; transition:border-color .2s;
        }
        .us-kpi:hover{ border-color:var(--border-hi); }
        .us-kpi-accent{ position:absolute; top:0; left:0; right:0; height:2px; border-radius:1rem 1rem 0 0; }
        .us-kpi-top{ display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:.65rem; }
        .us-kpi-icon{
            width:34px; height:34px; border-radius:.55rem; display:flex; align-items:center;
            justify-content:center; font-size:.9rem;
        }
        .us-kpi-value{ font-size:1.9rem; font-weight:700; color:#fff; line-height:1; margin-bottom:.25rem; font-family:var(--mono); }
        .us-kpi-label{ font-size:.8rem; color:var(--muted); }
        .us-kpi-sub{ font-size:.74rem; color:var(--muted); margin-top:.45rem; padding-top:.45rem; border-top:1px solid var(--border); }

        .k-slate .us-kpi-accent{ background:var(--muted); }
        .k-slate .us-kpi-icon{ background:rgba(107,114,128,.1); color:var(--muted); }
        .k-emerald .us-kpi-accent{ background:var(--emerald); }
        .k-emerald .us-kpi-icon{ background:rgba(16,185,129,.1); color:var(--emerald); }
        .k-blue .us-kpi-accent{ background:var(--blue); }
        .k-blue .us-kpi-icon{ background:rgba(59,130,246,.1); color:var(--blue); }
        .k-amber .us-kpi-accent{ background:var(--amber); }
        .k-amber .us-kpi-icon{ background:rgba(245,158,11,.1); color:var(--amber); }

        /* charts row */
        .us-charts{ display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        @media(max-width:800px){ .us-charts{ grid-template-columns:1fr; } }
        .us-card{
            background:var(--bg2); border:1px solid var(--border); border-radius:1rem;
            padding:1.5rem; transition:border-color .2s;
        }
        .us-card:hover{ border-color:var(--border-hi); }
        .us-card-title{ font-size:.9rem; font-weight:600; color:#fff; margin-bottom:1rem; }
        .us-chart-wrap{ position:relative; height:230px; }

        /* anomalies table */
        .us-an-card{ background:var(--bg2); border:1px solid var(--border); border-radius:1rem; overflow:hidden; }
        .us-an-head{
            display:flex; justify-content:space-between; align-items:center;
            padding:1.1rem 1.4rem; border-bottom:1px solid var(--border);
        }
        .us-an-head-title{ font-size:.9rem; font-weight:600; color:#fff; }
        table.us-an-table{ width:100%; border-collapse:collapse; }
        table.us-an-table th{
            padding:.6rem 1.2rem; text-align:left; font-size:.71rem; font-weight:600;
            letter-spacing:.08em; text-transform:uppercase; color:var(--muted);
            background:rgba(255,255,255,.025); border-bottom:1px solid var(--border);
        }
        table.us-an-table td{
            padding:.85rem 1.2rem; font-size:.84rem;
            border-bottom:1px solid rgba(255,255,255,.04); vertical-align:middle;
        }
        table.us-an-table tbody tr:last-child td{ border-bottom:none; }
        table.us-an-table tbody tr:hover td{ background:rgba(255,255,255,.02); }

        .us-tag{
            display:inline-flex; align-items:center; padding:.2rem .65rem;
            border-radius:999px; font-size:.73rem; font-weight:600;
        }
        .sev-low    { background:rgba(16,185,129,.12); color:var(--emerald); border:1px solid rgba(16,185,129,.2); }
        .sev-medium { background:rgba(245,158,11,.12); color:var(--amber); border:1px solid rgba(245,158,11,.2); }
        .sev-high   { background:rgba(244,63,94,.12); color:var(--rose); border:1px solid rgba(244,63,94,.2); }
        .type-tag   { background:rgba(99,102,241,.12); color:#a5b4fc; border:1px solid rgba(99,102,241,.2); }

        .mt-6{ margin-top:1.5rem; }
        .mt-4{ margin-top:1rem; }
        .sec-title{
            font-size:.95rem; font-weight:600; color:#fff; margin-bottom:.85rem;
            display:flex; align-items:center; gap:.5rem;
        }
        .sec-title::before{
            content:''; display:inline-block; width:3px; height:15px;
            background:var(--emerald); border-radius:99px;
        }
    </style>

    <div class="us-wrap">
    <div class="us-page">

        {{-- period tabs --}}
        <div class="us-tabs">
            @foreach(['week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                <a href="?period={{ $k }}" class="us-tab {{ request('period')===$k?'active':'' }}">{{ $lbl }}</a>
            @endforeach
        </div>

        {{-- KPIs --}}
        <div class="us-kpis mt-6">
            <div class="us-kpi k-slate">
                <div class="us-kpi-accent"></div>
                <div class="us-kpi-top">
                    <div class="us-kpi-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <div class="us-kpi-value">{{ $userStats['total_days'] }}</div>
                <div class="us-kpi-label">Jours Pointés</div>
                <div class="us-kpi-sub">{{ $userStats['present_days'] }} présents</div>
            </div>

            <div class="us-kpi k-emerald">
                <div class="us-kpi-accent"></div>
                <div class="us-kpi-top">
                    <div class="us-kpi-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </div>
                <div class="us-kpi-value">{{ round(($userStats['present_days']/max(1,$userStats['total_days']))*100,1) }}%</div>
                <div class="us-kpi-label">Taux Présence</div>
                <div class="us-kpi-sub">+{{ $userStats['late_days']??0 }} retards</div>
            </div>

            <div class="us-kpi k-blue">
                <div class="us-kpi-accent"></div>
                <div class="us-kpi-top">
                    <div class="us-kpi-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div class="us-kpi-value">{{ $userStats['total_worked_hours'] }}h</div>
                <div class="us-kpi-label">Heures Totales</div>
                <div class="us-kpi-sub">Moy. {{ $userStats['avg_daily_hours'] }}h/jour</div>
            </div>

            <div class="us-kpi k-amber">
                <div class="us-kpi-accent"></div>
                <div class="us-kpi-top">
                    <div class="us-kpi-icon">⚠️</div>
                </div>
                <div class="us-kpi-value">{{ $userStats['total_late_minutes'] }}<span style="font-size:1rem;font-weight:500;color:var(--muted);">min</span></div>
                <div class="us-kpi-label">Retards Cumulés</div>
                <div class="us-kpi-sub">{{ $userStats['open_anomalies'] }} anomalies ouvertes</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="us-charts mt-6">
            <div class="us-card">
                <div class="us-card-title">⏱️ Heures Travaillées</div>
                <div class="us-chart-wrap"><canvas id="workedChart"></canvas></div>
            </div>
            <div class="us-card">
                <div class="us-card-title">⚠️ Retards Journaliers (min)</div>
                <div class="us-chart-wrap"><canvas id="lateChart"></canvas></div>
            </div>
        </div>

        {{-- Anomalies --}}
        @if($anomalies->count()>0)
        <div class="mt-6">
            <div class="sec-title">
                🚨 {{ $anomalies->count() }} Anomalie{{ $anomalies->count()>1?'s':'' }} Ouverte{{ $anomalies->count()>1?'s':'' }}
            </div>
            <div class="us-an-card">
                <table class="us-an-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Sévérité</th>
                            <th style="text-align:right;">Action</th>
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anomalies as $anomaly)
<<<<<<< HEAD
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                    {{ $anomaly->detected_at->format('d/M H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-800 text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php $severityColor = ['low' => 'green', 'medium' => 'amber', 'high' => 'rose'][$anomaly->severity] @endphp
                                    <span class="px-2 py-1 bg-{{ $severityColor }}-100 text-{{ $severityColor }}-800 text-xs font-bold rounded-full">
                                        {{ ucfirst($anomaly->severity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold">Résoudre</a>
                                </td>
                            </tr>
=======
                        <tr>
                            <td style="font-family:var(--mono);font-size:.8rem;color:var(--muted);">
                                {{ $anomaly->detected_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <span class="us-tag type-tag">{{ ucfirst(str_replace('_',' ',$anomaly->anomaly_type)) }}</span>
                            </td>
                            <td>
                                @php $sc=['low'=>'sev-low','medium'=>'sev-medium','high'=>'sev-high'][$anomaly->severity??'low']??'sev-low' @endphp
                                <span class="us-tag {{ $sc }}">{{ ucfirst($anomaly->severity??'low') }}</span>
                            </td>
                            <td style="text-align:right;">
                                <a href="#" style="font-size:.82rem;font-weight:600;color:var(--emerald);text-decoration:none;">Résoudre →</a>
                            </td>
                        </tr>
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
<<<<<<< HEAD
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Heures chart
    const workedCtx = document.getElementById('workedHoursChart')?.getContext('2d');
    if (workedCtx) {
        new Chart(workedCtx, {
            type: 'line',
            data: {
                labels: @json($userStats['chart_data']['labels']),
                datasets: [{
                    label: 'Heures',
                    data: @json($userStats['chart_data']['worked_hours']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // Retards chart
    const lateCtx = document.getElementById('lateChart')?.getContext('2d');
    if (lateCtx) {
        new Chart(lateCtx, {
            type: 'bar',
            data: {
                labels: @json($userStats['chart_data']['labels']),
                datasets: [{ 
                    label: 'Min Retard',
                    data: @json($userStats['chart_data']['late_minutes']),
                    backgroundColor: 'rgb(251, 191, 36)'
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }
});
</script>
@endpush>

=======
        @endif

    </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.color = '#6b7280';

        const labels = @json($userStats['chart_data']['labels']);
        const worked = @json($userStats['chart_data']['worked_hours']);
        const late   = @json($userStats['chart_data']['late_minutes']);

        const grid = { color:'rgba(255,255,255,0.05)', drawBorder:false };
        const commonOpts = {
            responsive:true, maintainAspectRatio:false,
            interaction:{ mode:'index', intersect:false },
            plugins:{
                legend:{ display:false },
                tooltip:{
                    backgroundColor:'#1c2333', borderColor:'rgba(255,255,255,.1)', borderWidth:1,
                    titleColor:'#e8eaf0', bodyColor:'#9ca3af', padding:10, cornerRadius:8,
                }
            },
            scales:{
                x:{ grid, ticks:{ font:{size:10} }, border:{display:false} },
                y:{ grid, ticks:{ font:{size:10} }, border:{display:false}, beginAtZero:true }
            }
        };

        /* heures travaillées */
        const ctxW = document.getElementById('workedChart').getContext('2d');
        const gradW = ctxW.createLinearGradient(0,0,0,230);
        gradW.addColorStop(0,'rgba(59,130,246,.3)');
        gradW.addColorStop(1,'rgba(59,130,246,0)');
        new Chart(ctxW, {
            type:'line',
            data:{
                labels,
                datasets:[{
                    data:worked, borderColor:'#3b82f6', borderWidth:2.5,
                    backgroundColor:gradW, fill:true, tension:0.45,
                    pointBackgroundColor:'#3b82f6', pointRadius:3, pointHoverRadius:6,
                }]
            },
            options:{ ...commonOpts, plugins:{...commonOpts.plugins, tooltip:{...commonOpts.plugins.tooltip, callbacks:{ label:ctx=>`${ctx.parsed.y}h` }}} }
        });

        /* retards */
        const ctxL = document.getElementById('lateChart').getContext('2d');
        new Chart(ctxL, {
            type:'bar',
            data:{
                labels,
                datasets:[{
                    data:late, backgroundColor:'rgba(245,158,11,.65)', hoverBackgroundColor:'#f59e0b',
                    borderRadius:5, borderSkipped:false,
                }]
            },
            options:{ ...commonOpts, plugins:{...commonOpts.plugins, tooltip:{...commonOpts.plugins.tooltip, callbacks:{ label:ctx=>`${ctx.parsed.y} min` }}} }
        });
    });
    </script>
    @endpush

</x-app-layout>
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
