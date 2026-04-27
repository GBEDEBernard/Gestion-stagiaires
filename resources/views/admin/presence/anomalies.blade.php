<x-app-layout title="Anomalies de Présence - Admin">

    <x-slot name="header">
        <div class="an-header">
            <div>
                <div class="an-header-badge">Supervision</div>
                <h1 class="an-header-title">Anomalies de Présence</h1>
                <p class="an-header-sub">Incidents détectés à reviewer</p>
            </div>
            <a href="{{ route('admin.presence.index') }}" class="an-btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Tableau de bord
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
        .an-wrap *{ box-sizing:border-box; }
        .an-wrap{ font-family:var(--font); color:var(--text); }

        /* header */
        .an-header{ display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem; }
        .an-header-badge{
            display:inline-block; font-size:.7rem; font-weight:600; letter-spacing:.12em; text-transform:uppercase;
            color:var(--rose); background:rgba(244,63,94,.12); border:1px solid rgba(244,63,94,.25);
            border-radius:999px; padding:.25rem .75rem; margin-bottom:.5rem;
        }
        .an-header-title{ font-size:1.75rem; font-weight:700; color:#fff; margin:0; }
        .an-header-sub{ font-size:.88rem; color:var(--muted); margin:.2rem 0 0; }
        .an-btn-back{
            display:flex; align-items:center; gap:.5rem; padding:.55rem 1.1rem;
            background:var(--bg2); border:1px solid var(--border); color:var(--text);
            font-size:.83rem; font-weight:500; border-radius:.65rem; text-decoration:none; transition:all .2s;
        }
        .an-btn-back:hover{ border-color:var(--border-hi); background:var(--bg3); }

        /* page */
        .an-page{ max-width:1100px; margin:0 auto; padding:1.5rem 1.5rem 3rem; }

        /* empty state */
        .an-empty{
            background:var(--bg2); border:1px solid var(--border); border-radius:1.25rem;
            padding:5rem 2rem; text-align:center;
        }
        .an-empty-icon{ font-size:3rem; margin-bottom:1rem; }
        .an-empty-title{ font-size:1.1rem; font-weight:600; color:#fff; margin-bottom:.5rem; }
        .an-empty-sub{ font-size:.85rem; color:var(--muted); }

        /* summary bar */
        .an-summary{
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;
            background:var(--bg2); border:1px solid var(--border); border-radius:1rem;
            padding:1.1rem 1.5rem; margin-bottom:1.25rem;
        }
        .an-summary-count{ display:flex; align-items:baseline; gap:.4rem; }
        .an-summary-num{ font-size:1.8rem; font-weight:700; color:var(--rose); font-family:var(--mono); }
        .an-summary-label{ font-size:.85rem; color:var(--muted); }
        .an-summary-filters{ display:flex; gap:.5rem; flex-wrap:wrap; }
        .an-filter-chip{
            padding:.3rem .85rem; border-radius:999px; font-size:.76rem; font-weight:600;
            background:rgba(244,63,94,.1); color:var(--rose); border:1px solid rgba(244,63,94,.2);
            text-decoration:none; transition:all .2s; cursor:pointer;
        }
        .an-filter-chip.active, .an-filter-chip:hover{
            background:rgba(244,63,94,.2); border-color:rgba(244,63,94,.4);
        }

        /* table card */
        .an-card{ background:var(--bg2); border:1px solid var(--border); border-radius:1rem; overflow:hidden; }

        table.an-table{ width:100%; border-collapse:collapse; }
        table.an-table thead tr{ background:rgba(255,255,255,.025); }
        table.an-table th{
            padding:.7rem 1.2rem; text-align:left; font-size:.71rem; font-weight:600;
            letter-spacing:.09em; text-transform:uppercase; color:var(--muted);
            border-bottom:1px solid var(--border);
        }
        table.an-table td{
            padding:.95rem 1.2rem; font-size:.86rem; border-bottom:1px solid rgba(255,255,255,.04);
            vertical-align:middle;
        }
        table.an-table tbody tr:last-child td{ border-bottom:none; }
        table.an-table tbody tr:hover td{ background:rgba(255,255,255,.02); }

        /* user cell */
        .an-user{ display:flex; align-items:center; gap:.75rem; }
        .an-avatar{
            width:34px; height:34px; border-radius:.5rem; display:flex; align-items:center; justify-content:center;
            font-size:.8rem; font-weight:700; flex-shrink:0;
            background:linear-gradient(135deg,rgba(244,63,94,.3),rgba(244,63,94,.1));
            color:var(--rose); border:1px solid rgba(244,63,94,.2);
        }
        .an-user-name{ font-weight:500; color:#fff; font-size:.88rem; }
        .an-user-event{ font-size:.74rem; color:var(--muted); margin-top:.1rem; }

        /* tags */
        .an-tag{
            display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .65rem;
            border-radius:999px; font-size:.73rem; font-weight:600;
        }
        .tag-rose  { background:rgba(244,63,94,.12); color:var(--rose); border:1px solid rgba(244,63,94,.2); }
        .tag-amber { background:rgba(245,158,11,.12); color:var(--amber); border:1px solid rgba(245,158,11,.2); }
        .tag-blue  { background:rgba(59,130,246,.12); color:var(--blue); border:1px solid rgba(59,130,246,.2); }
        .tag-gray  { background:rgba(107,114,128,.12); color:var(--muted); border:1px solid rgba(107,114,128,.2); }

        /* date */
        .an-date{ font-size:.8rem; font-family:var(--mono); color:var(--muted); }

        /* resolve btn */
        .an-resolve-btn{
            display:inline-flex; align-items:center; gap:.4rem; padding:.4rem 1rem;
            background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); color:var(--emerald);
            font-size:.8rem; font-weight:600; border-radius:.5rem; cursor:pointer;
            transition:all .2s; white-space:nowrap;
        }
        .an-resolve-btn:hover{
            background:var(--emerald); color:#fff; border-color:var(--emerald);
            transform:translateY(-1px); box-shadow:0 4px 12px rgba(16,185,129,.3);
        }
    </style>

    <div class="an-wrap">
    <div class="an-page">

        @if($anomalies->isEmpty())
            <div class="an-empty">
                <div class="an-empty-icon">✅</div>
                <div class="an-empty-title">Aucune anomalie ouverte</div>
                <div class="an-empty-sub">Toutes les anomalies de présence ont été résolues.</div>
            </div>

        @else

            {{-- summary --}}
            <div class="an-summary">
                <div class="an-summary-count">
                    <span class="an-summary-num">{{ $anomalies->count() }}</span>
                    <span class="an-summary-label">anomalie{{ $anomalies->count()>1?'s':'' }} ouverte{{ $anomalies->count()>1?'s':'' }}</span>
                </div>
                <div class="an-summary-filters">
                    <span class="an-filter-chip active">Toutes</span>
                    <span class="an-filter-chip">Aujourd'hui</span>
                    <span class="an-filter-chip">Semaine</span>
                </div>
            </div>

            {{-- table --}}
            <div class="an-card">
                <table class="an-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Événement</th>
                            <th>Détecté le</th>
                            <th>Type</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anomalies as $anomaly)
                            @php
                                $name = $anomaly->attendanceEvent->stage?->etudiant?->nom
                                     ?? $anomaly->user?->name
                                     ?? 'Inconnu';
                                $initial = strtoupper(substr($name, 0, 2));
                                $eventLabel = $anomaly->attendanceEvent?->event_type === 'check_in' ? 'Entree' : 'Sortie';
                            @endphp
                            <tr>
                                <td>
                                    <div class="an-user">
                                        <div class="an-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="an-user-name">{{ $name }}</div>
                                            <div class="an-user-event">{{ $eventLabel }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:500;color:#fff;font-size:.85rem;">{{ $eventLabel }}</div>
                                    <div class="an-date">{{ $anomaly->attendanceEvent?->occurred_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="an-date" style="color:var(--text);">{{ $anomaly->detected_at->format('d/m/Y') }}</div>
                                    <div class="an-date">{{ $anomaly->detected_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <span class="an-tag tag-rose">{{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.presence.anomalies.resolve', $anomaly->id) }}"
                                          method="POST" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                onclick="return confirm('Résoudre cette anomalie ?')"
                                                class="an-resolve-btn">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                            Résoudre
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif

    </div>
    </div>

</x-app-layout>
