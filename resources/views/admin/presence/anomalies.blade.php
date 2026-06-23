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
            --purple:  #8b5cf6;
            --blue:    #3b82f6;
            --font:    'DM Sans', sans-serif;
            --mono:    'DM Mono', monospace;
        }
        .an-wrap *{ box-sizing:border-box; }
        .an-wrap{ font-family:var(--font); color:var(--text); }

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

        .an-page{ max-width:1100px; margin:0 auto; padding:1.5rem 1.5rem 3rem; }

        .an-empty{
            background:var(--bg2); border:1px solid var(--border); border-radius:1.25rem;
            padding:5rem 2rem; text-align:center;
        }
        .an-empty-icon{ font-size:3rem; margin-bottom:1rem; }
        .an-empty-title{ font-size:1.1rem; font-weight:600; color:#fff; margin-bottom:.5rem; }
        .an-empty-sub{ font-size:.85rem; color:var(--muted); }

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

        .an-user{ display:flex; align-items:center; gap:.75rem; }
        .an-avatar{
            width:34px; height:34px; border-radius:.5rem; display:flex; align-items:center; justify-content:center;
            font-size:.8rem; font-weight:700; flex-shrink:0;
            background:linear-gradient(135deg,rgba(244,63,94,.3),rgba(244,63,94,.1));
            color:var(--rose); border:1px solid rgba(244,63,94,.2);
        }
        .an-user-name{ font-weight:500; color:#fff; font-size:.88rem; }
        .an-user-event{ font-size:.74rem; color:var(--muted); margin-top:.1rem; }

        .an-tag{
            display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .65rem;
            border-radius:999px; font-size:.73rem; font-weight:600;
        }
        .tag-rose  { background:rgba(244,63,94,.12); color:var(--rose); border:1px solid rgba(244,63,94,.2); }
        .tag-amber { background:rgba(245,158,11,.12); color:var(--amber); border:1px solid rgba(245,158,11,.2); }
        .tag-blue  { background:rgba(59,130,246,.12); color:var(--blue); border:1px solid rgba(59,130,246,.2); }
        .tag-gray  { background:rgba(107,114,128,.12); color:var(--muted); border:1px solid rgba(107,114,128,.2); }
        .tag-purple{ background:rgba(139,92,246,.12); color:var(--purple); border:1px solid rgba(139,92,246,.2); }

        .an-date{ font-size:.8rem; font-family:var(--mono); color:var(--muted); }

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

        .an-desc-cell{
            max-width:280px; font-size:.8rem; color:var(--muted); line-height:1.4;
        }

        .an-sev-dot{
            display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:6px;
        }

        /* modal */
        .an-modal-backdrop{
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.65);
            backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center; padding:1rem;
        }
        .an-modal-backdrop.active{ display:flex; }
        .an-modal{
            background:var(--bg2); border:1px solid var(--border-hi); border-radius:1.25rem;
            max-width:640px; width:100%; max-height:90vh; overflow-y:auto;
            box-shadow:0 20px 60px rgba(0,0,0,.5);
        }
        .an-modal-head{
            display:flex; align-items:center; justify-content:space-between;
            padding:1.25rem 1.5rem; border-bottom:1px solid var(--border);
        }
        .an-modal-head h2{ margin:0; font-size:1.05rem; font-weight:600; color:#fff; }
        .an-modal-close{
            background:none; border:none; color:var(--muted); font-size:1.4rem; cursor:pointer; padding:0; line-height:1;
        }
        .an-modal-close:hover{ color:var(--text); }
        .an-modal-body{ padding:1.5rem; }
        .an-modal-footer{
            display:flex; justify-content:flex-end; gap:.75rem;
            padding:1rem 1.5rem; border-top:1px solid var(--border);
        }

        .an-info-grid{ display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.25rem; }
        .an-info-item{}
        .an-info-label{ font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin-bottom:.2rem; }
        .an-info-value{ font-size:.88rem; color:#fff; font-weight:500; }

        .an-section{ margin-bottom:1.25rem; }
        .an-section-title{
            font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin-bottom:.5rem;
            display:flex; align-items:center; gap:.5rem;
        }
        .an-section-body{
            background:rgba(0,0,0,.2); border:1px solid var(--border); border-radius:.6rem;
            padding:.85rem 1rem; font-size:.86rem; line-height:1.6; color:var(--text); white-space:pre-line;
        }

        .an-observation{
            background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2); border-radius:.6rem;
            padding:.75rem 1rem; font-size:.84rem; color:var(--amber); margin-bottom:1.25rem;
        }
        .an-observation strong{ color:#fff; }

        .an-resolve-note{
            width:100%; background:rgba(0,0,0,.25); border:1px solid var(--border); border-radius:.6rem;
            padding:.75rem 1rem; color:var(--text); font-size:.86rem; font-family:var(--font); resize:vertical; min-height:70px;
        }
        .an-resolve-note:focus{ outline:none; border-color:var(--blue); }

        .an-btn-cancel{
            padding:.5rem 1.1rem; border-radius:.5rem; border:1px solid var(--border);
            background:transparent; color:var(--muted); font-size:.84rem; font-weight:500; cursor:pointer;
        }
        .an-btn-cancel:hover{ border-color:var(--border-hi); color:var(--text); }

        .an-btn-confirm{
            display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1.25rem;
            background:var(--emerald); border:none; border-radius:.5rem; color:#fff;
            font-size:.84rem; font-weight:600; cursor:pointer; transition:all .2s;
        }
        .an-btn-confirm:hover{ box-shadow:0 4px 16px rgba(16,185,129,.35); transform:translateY(-1px); }

        .an-severity-badge{
            display:inline-flex; align-items:center; gap:.3rem; padding:.15rem .55rem;
            border-radius:999px; font-size:.72rem; font-weight:600;
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

            <div class="an-card">
                <table class="an-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Problème</th>
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
                                $severityColors = ['low' => 'tag-gray', 'medium' => 'tag-amber', 'high' => 'tag-rose'];
                                $severityColor = $severityColors[$anomaly->severity] ?? 'tag-amber';
                                $payloadMessage = $anomaly->payload['message_observation'] ?? null;
                            @endphp
                            <tr>
                                <td>
                                    <div class="an-user">
                                        <div class="an-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="an-user-name">{{ $name }}</div>
                                            <div class="an-user-event">{{ $anomaly->attendanceEvent->type }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:500;color:#fff;font-size:.85rem;margin-bottom:2px;">{{ $anomaly->type_label }}</div>
                                    <div class="an-desc-cell">{{ $anomaly->type_description }}</div>
                                    @if($payloadMessage)
                                        <div style="font-size:.75rem;color:var(--amber);margin-top:3px;">✏️ "{{ $payloadMessage }}"</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="an-date" style="color:var(--text);">{{ $anomaly->detected_at->format('d/m/Y') }}</div>
                                    <div class="an-date">{{ $anomaly->detected_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="an-tag {{ $severityColor }}" style="margin-bottom:4px;">
                                        <span class="an-sev-dot" style="background:currentColor;"></span>
                                        {{ ucfirst($anomaly->severity) }}
                                    </div>
                                    <div style="font-size:.73rem;color:var(--muted);">{{ $anomaly->type_label }}</div>
                                </td>
                                <td style="text-align:right;">
                                    <button type="button"
                                            class="an-resolve-btn"
                                            onclick="openAnomalyModal({{ $anomaly->id }})">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                        Résoudre
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif

    </div>
    </div>

    {{-- Modal de résolution --}}
    <div id="anomalyModalBackdrop" class="an-modal-backdrop">
        <div class="an-modal">
            <div class="an-modal-head">
                <h2>Résoudre l'anomalie</h2>
                <button type="button" class="an-modal-close" onclick="closeAnomalyModal()">&times;</button>
            </div>
            <div class="an-modal-body">
                <div class="an-info-grid">
                    <div class="an-info-item">
                        <div class="an-info-label">Utilisateur</div>
                        <div class="an-info-value" id="modalUser">—</div>
                    </div>
                    <div class="an-info-item">
                        <div class="an-info-label">Type</div>
                        <div class="an-info-value" id="modalType">—</div>
                    </div>
                    <div class="an-info-item">
                        <div class="an-info-label">Sévérité</div>
                        <div class="an-info-value" id="modalSeverity">—</div>
                    </div>
                    <div class="an-info-item">
                        <div class="an-info-label">Détecté le</div>
                        <div class="an-info-value" id="modalDate">—</div>
                    </div>
                </div>

                <div class="an-section">
                    <div class="an-section-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Quel est le problème ?
                    </div>
                    <div class="an-section-body" id="modalDescription">—</div>
                </div>

                <div class="an-section">
                    <div class="an-section-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Comment résoudre ?
                    </div>
                    <div class="an-section-body" id="modalSolution">—</div>
                </div>

                <div id="modalObservation" class="an-observation" style="display:none;">
                    <strong>Observation de l'utilisateur :</strong><br>
                    <span id="modalObsText"></span>
                </div>

                <form id="modalResolveForm" method="POST">
                    @csrf
                    @method('POST')
                    <div class="an-section">
                        <div class="an-section-title">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Note de résolution (optionnelle)
                        </div>
                        <textarea name="resolution_note" class="an-resolve-note" placeholder="Ajouter une note sur la résolution..." maxlength="1000"></textarea>
                    </div>

                    <div class="an-modal-footer">
                        <button type="button" class="an-btn-cancel" onclick="closeAnomalyModal()">Annuler</button>
                        <button type="submit" class="an-btn-confirm">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Confirmer la résolution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const anomalies = @json($anomaliesJson);

        function openAnomalyModal(id) {
            const a = anomalies.find(x => x.id === id);
            if (!a) return;
            document.getElementById('modalUser').textContent = a.user;
            document.getElementById('modalType').textContent = a.type;
            document.getElementById('modalSeverity').textContent = a.severity;
            document.getElementById('modalDate').textContent = a.date;
            document.getElementById('modalDescription').textContent = a.description;
            document.getElementById('modalSolution').textContent = a.solution;
            const obs = document.getElementById('modalObservation');
            const obsText = document.getElementById('modalObsText');
            if (a.observation) {
                obs.style.display = 'block';
                obsText.textContent = a.observation;
            } else {
                obs.style.display = 'none';
            }
            document.getElementById('modalResolveForm').action = '{{ url('admin/presence') }}/' + id + '/resolve';
            document.getElementById('anomalyModalBackdrop').classList.add('active');
        }

        function closeAnomalyModal() {
            document.getElementById('anomalyModalBackdrop').classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const backdrop = document.getElementById('anomalyModalBackdrop');
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) closeAnomalyModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAnomalyModal();
            });
        });
    </script>
    @endpush

</x-app-layout>
