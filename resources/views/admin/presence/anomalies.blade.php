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
            --bg: #ffffff;
            --bg2: #f8fafc;
            --bg3: #f1f5f9;
            --border: rgba(0,0,0,0.08);
            --border-hi: rgba(0,0,0,0.14);
            --text: #0f172a;
            --muted: #64748b;
            --emerald: #10b981;
            --emerald-d: #059669;
            --amber: #f59e0b;
            --rose: #f43f5e;
            --blue: #3b82f6;
            --purple: #8b5cf6;
            --font: 'DM Sans', sans-serif;
            --mono: 'DM Mono', monospace;
        }
        .dark {
            --bg: #0f1117; --bg2: #161b27; --bg3: #1c2333;
            --border: rgba(255,255,255,0.07); --border-hi: rgba(255,255,255,0.14);
            --text: #e8eaf0; --muted: #8b93a3;
        }

        .an-wrap * { box-sizing: border-box; }
        .an-wrap { font-family: var(--font); color: var(--text); background: var(--bg); min-height: 100vh; }

        .an-header { display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem; padding:1rem 1.5rem 0; }
        .an-header-badge {
            display:inline-block; font-size:.7rem; font-weight:600; letter-spacing:.12em; text-transform:uppercase;
            color:var(--rose); background:rgba(244,63,94,.1); border:1px solid rgba(244,63,94,.2);
            border-radius:999px; padding:.25rem .75rem; margin-bottom:.5rem;
        }
        .an-header-title { font-size:1.75rem; font-weight:700; color:var(--text); margin:0; }
        .an-header-sub { font-size:.88rem; color:var(--muted); margin:.2rem 0 0; }
        .an-btn-back {
            display:flex; align-items:center; gap:.5rem; padding:.55rem 1.1rem;
            background:var(--bg2); border:1px solid var(--border); color:var(--text);
            font-size:.83rem; font-weight:500; border-radius:.65rem; text-decoration:none; transition:all .2s;
        }
        .an-btn-back:hover { border-color:var(--border-hi); background:var(--bg3); }

        .an-page { max-width:1200px; margin:0 auto; padding:1.5rem 1.5rem 3rem; }

        .an-empty {
            background:var(--bg2); border:1px solid var(--border); border-radius:1.25rem;
            padding:5rem 2rem; text-align:center;
        }
        .an-empty-icon { font-size:3rem; margin-bottom:1rem; }
        .an-empty-title { font-size:1.1rem; font-weight:600; color:var(--text); margin-bottom:.5rem; }
        .an-empty-sub { font-size:.85rem; color:var(--muted); }

        /* SUMMARY BAR */
        .an-summary {
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;
            background:var(--bg2); border:1px solid var(--border); border-radius:1rem;
            padding:1.1rem 1.5rem; margin-bottom:1.25rem;
        }
        .an-summary-count { display:flex; align-items:baseline; gap:.5rem; }
        .an-summary-num { font-size:1.9rem; font-weight:700; color:var(--rose); font-family:var(--mono); }
        .an-summary-label { font-size:.85rem; color:var(--muted); }
        .an-summary-filters { display:flex; gap:.5rem; flex-wrap:wrap; }
        .an-filter-chip {
            padding:.4rem 1rem; border-radius:999px; font-size:.8rem; font-weight:600;
            background:var(--bg3); color:var(--muted); border:1px solid var(--border);
            text-decoration:none; transition:all .2s; cursor:pointer;
        }
        .an-filter-chip:hover { border-color:var(--border-hi); color:var(--text); }
        .an-filter-chip.active {
            background:rgba(244,63,94,.1); color:var(--rose); border-color:rgba(244,63,94,.3);
        }

        /* USER GROUP TABLE */
        .an-card { background:var(--bg2); border:1px solid var(--border); border-radius:1rem; overflow:hidden; }
        table.an-table { width:100%; border-collapse:collapse; }
        table.an-table thead tr { background:var(--bg3); }
        table.an-table th {
            padding:.75rem 1.2rem; text-align:left; font-size:.71rem; font-weight:600;
            letter-spacing:.09em; text-transform:uppercase; color:var(--muted);
            border-bottom:1px solid var(--border);
        }
        table.an-table td { padding:1rem 1.2rem; font-size:.86rem; border-bottom:1px solid var(--border); vertical-align:middle; }
        table.an-table tbody tr:last-child td { border-bottom:none; }
        table.an-table tbody tr { transition:background .15s; }
        table.an-table tbody tr:hover td { background:var(--bg3); }

        .an-user { display:flex; align-items:center; gap:.75rem; }
        .an-avatar {
            width:38px; height:38px; border-radius:.65rem; display:flex; align-items:center; justify-content:center;
            font-size:.82rem; font-weight:700; flex-shrink:0;
            background:linear-gradient(135deg, rgba(139,92,246,.18), rgba(139,92,246,.06));
            color:var(--purple); border:1px solid rgba(139,92,246,.18);
        }
        .an-user-name { font-weight:600; color:var(--text); font-size:.9rem; }
        .an-user-sub { font-size:.75rem; color:var(--muted); margin-top:.1rem; }

        .an-count-badge {
            display:inline-flex; align-items:center; justify-content:center; min-width:26px; height:26px;
            padding:0 .5rem; border-radius:999px; font-size:.8rem; font-weight:700; font-family:var(--mono);
            background:rgba(244,63,94,.1); color:var(--rose); border:1px solid rgba(244,63,94,.2);
        }
        .an-count-badge.low-sev { background:rgba(107,114,128,.1); color:var(--muted); border-color:var(--border); }
        .an-count-badge.med-sev { background:rgba(245,158,11,.1); color:var(--amber); border-color:rgba(245,158,11,.2); }

        .an-type-pills { display:flex; flex-wrap:wrap; gap:.35rem; }
        .an-type-pill {
            font-size:.73rem; font-weight:500; padding:.2rem .6rem; border-radius:999px;
            background:var(--bg3); color:var(--muted); border:1px solid var(--border);
        }
        .an-type-pill b { color:var(--text); font-weight:700; }

        .an-sev-badge {
            display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .65rem;
            border-radius:999px; font-size:.72rem; font-weight:600;
        }
        .sev-high  { background:rgba(244,63,94,.1); color:var(--rose); }
        .sev-medium{ background:rgba(245,158,11,.1); color:var(--amber); }
        .sev-low   { background:rgba(107,114,128,.1); color:var(--muted); }
        .an-sev-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:currentColor; }

        .an-date { font-size:.8rem; font-family:var(--mono); color:var(--muted); }

        .an-detail-btn {
            display:inline-flex; align-items:center; gap:.4rem; padding:.45rem 1.1rem;
            background:var(--bg); border:1px solid var(--border-hi); color:var(--text);
            font-size:.8rem; font-weight:600; border-radius:.6rem; cursor:pointer; transition:all .2s;
        }
        .an-detail-btn:hover { background:var(--purple); border-color:var(--purple); color:#fff; transform:translateY(-1px); }

        /* MODAL */
        .an-modal-backdrop {
            display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
            backdrop-filter:blur(3px); z-index:999; align-items:center; justify-content:center; padding:1rem;
        }
        .an-modal-backdrop.active { display:flex; }
        .an-modal {
            background:var(--bg); border:1px solid var(--border-hi); border-radius:1.25rem;
            max-width:680px; width:100%; max-height:88vh; overflow-y:auto;
            box-shadow:0 24px 70px rgba(0,0,0,.25);
        }
        .an-modal-head {
            display:flex; align-items:center; justify-content:space-between;
            padding:1.25rem 1.5rem; border-bottom:1px solid var(--border);
            position:sticky; top:0; background:var(--bg); z-index:2;
        }
        .an-modal-head-info { display:flex; align-items:center; gap:.85rem; }
        .an-modal-head h2 { margin:0; font-size:1.05rem; font-weight:700; color:var(--text); }
        .an-modal-head-sub { font-size:.78rem; color:var(--muted); margin-top:.1rem; }
        .an-modal-close { background:none; border:none; color:var(--muted); font-size:1.5rem; cursor:pointer; padding:0; line-height:1; }
        .an-modal-close:hover { color:var(--text); }
        .an-modal-body { padding:1.25rem 1.5rem 1.5rem; }

        .an-type-group {
            border:1px solid var(--border); border-radius:.85rem; margin-bottom:.9rem; overflow:hidden;
        }
        .an-type-group-head {
            display:flex; align-items:center; justify-content:space-between; gap:.75rem;
            padding:.85rem 1.1rem; background:var(--bg2);
        }
        .an-type-group-title { display:flex; align-items:center; gap:.6rem; }
        .an-type-group-name { font-weight:600; font-size:.88rem; color:var(--text); }
        .an-type-group-desc { font-size:.78rem; color:var(--muted); margin-top:.15rem; }
        .an-type-group-actions { display:flex; align-items:center; gap:.5rem; }

        .an-btn-mini {
            padding:.35rem .8rem; border-radius:.5rem; border:1px solid var(--border-hi);
            background:var(--bg); color:var(--text); font-size:.75rem; font-weight:600; cursor:pointer; transition:all .2s;
        }
        .an-btn-mini:hover { background:var(--emerald); border-color:var(--emerald); color:#fff; }

        .an-type-group-items { padding:.5rem 1.1rem 1rem; display:flex; flex-direction:column; gap:.5rem; }
        .an-occurrence {
            display:flex; align-items:center; justify-content:space-between; gap:.75rem;
            padding:.6rem .85rem; background:var(--bg2); border-radius:.6rem; font-size:.8rem;
        }
        .an-occurrence-obs { color:var(--amber); font-size:.75rem; margin-top:.15rem; }

        .an-btn-resolve-one {
            padding:.3rem .7rem; border-radius:.45rem; border:1px solid rgba(16,185,129,.3);
            background:rgba(16,185,129,.08); color:var(--emerald); font-size:.72rem; font-weight:600; cursor:pointer;
        }
        .an-btn-resolve-one:hover { background:var(--emerald); color:#fff; }

        .an-empty-inline { text-align:center; padding:3rem 1rem; color:var(--muted); }
    </style>

    <div class="an-wrap">
    <div class="an-page">

        @if($grouped->isEmpty())
            <div class="an-empty">
                <div class="an-empty-icon">✅</div>
                <div class="an-empty-title">Aucune anomalie ouverte</div>
                <div class="an-empty-sub">Toutes les anomalies de présence ont été résolues.</div>
            </div>
        @else
            <div class="an-summary">
                <div class="an-summary-count">
                    <span class="an-summary-num">{{ $anomalies->count() }}</span>
                    <span class="an-summary-label">anomalie{{ $anomalies->count()>1?'s':'' }} · {{ $grouped->count() }} utilisateur{{ $grouped->count()>1?'s':'' }}</span>
                </div>
                <div class="an-summary-filters">
                    <a href="?filter=all" class="an-filter-chip {{ $filter==='all'?'active':'' }}">Toutes</a>
                    <a href="?filter=today" class="an-filter-chip {{ $filter==='today'?'active':'' }}">Aujourd'hui</a>
                    <a href="?filter=week" class="an-filter-chip {{ $filter==='week'?'active':'' }}">Semaine</a>
                </div>
            </div>

            <div class="an-card">
                <table class="an-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Types d'anomalies</th>
                            <th>Sévérité max</th>
                            <th>Dernière détection</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grouped as $i => $g)
                            @php
                                $initial = strtoupper(substr($g['user'], 0, 2));
                                $sevMax = $g['max_severity'];
                                $sevClass = $sevMax >= 3 ? 'sev-high' : ($sevMax === 2 ? 'sev-medium' : 'sev-low');
                                $sevLabel = $sevMax >= 3 ? 'Élevée' : ($sevMax === 2 ? 'Moyenne' : 'Faible');
                                $badgeClass = $sevMax >= 3 ? '' : ($sevMax === 2 ? 'med-sev' : 'low-sev');
                            @endphp
                            <tr>
                                <td>
                                    <div class="an-user">
                                        <div class="an-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="an-user-name">{{ $g['user'] }}</div>
                                            <div class="an-user-sub">{{ $g['types']->count() }} type(s) d'incident</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="an-type-pills">
                                        @foreach($g['types']->take(3) as $t)
                                            <span class="an-type-pill">{{ $t['label'] }} <b>×{{ $t['count'] }}</b></span>
                                        @endforeach
                                        @if($g['types']->count() > 3)
                                            <span class="an-type-pill">+{{ $g['types']->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="an-sev-badge {{ $sevClass }}">
                                        <span class="an-sev-dot"></span>{{ $sevLabel }}
                                    </span>
                                </td>
                                <td>
                                    <div class="an-date" style="color:var(--text);">{{ \Carbon\Carbon::parse($g['last_detected'])->format('d/m/Y') }}</div>
                                    <div class="an-date">{{ \Carbon\Carbon::parse($g['last_detected'])->format('H:i') }}</div>
                                </td>
                                <td style="text-align:right;">
                                    <span class="an-count-badge {{ $badgeClass }}" style="margin-right:.6rem;">{{ $g['total'] }}</span>
                                    <button type="button" class="an-detail-btn" onclick="openUserModal({{ $i }})">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                        Détails
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

    {{-- MODAL — détail utilisateur groupé par type --}}
    <div id="userModalBackdrop" class="an-modal-backdrop">
        <div class="an-modal">
            <div class="an-modal-head">
                <div class="an-modal-head-info">
                    <div class="an-avatar" id="modalAvatar" style="width:42px;height:42px;">--</div>
                    <div>
                        <h2 id="modalUserName">—</h2>
                        <div class="an-modal-head-sub" id="modalUserSub">—</div>
                    </div>
                </div>
                <button type="button" class="an-modal-close" onclick="closeUserModal()">&times;</button>
            </div>
            <div class="an-modal-body" id="modalBody">
                {{-- injecté en JS --}}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const grouped = @json($grouped);
        const csrfToken = '{{ csrf_token() }}';

        function sevBadge(sev) {
            const map = { high: ['sev-high','Élevée'], medium: ['sev-medium','Moyenne'], low: ['sev-low','Faible'] };
            const [cls, label] = map[sev] || map.low;
            return `<span class="an-sev-badge ${cls}"><span class="an-sev-dot"></span>${label}</span>`;
        }

        function openUserModal(index) {
            const g = grouped[index];
            if (!g) return;

            document.getElementById('modalAvatar').textContent = g.user.substring(0,2).toUpperCase();
            document.getElementById('modalUserName').textContent = g.user;
            document.getElementById('modalUserSub').textContent = `${g.total} anomalie(s) ouverte(s)`;

            const body = document.getElementById('modalBody');
            body.innerHTML = g.types.map(t => `
                <div class="an-type-group">
                    <div class="an-type-group-head">
                        <div class="an-type-group-title">
                            ${sevBadge(t.severity)}
                            <div>
                                <div class="an-type-group-name">${t.label} ${t.count > 1 ? `<span style="color:var(--muted);font-weight:500;">(×${t.count})</span>` : ''}</div>
                                <div class="an-type-group-desc">${t.description}</div>
                            </div>
                        </div>
                        <div class="an-type-group-actions">
                            ${t.count > 1 ? `<button type="button" class="an-btn-mini" onclick="resolveBulk([${t.ids.join(',')}])">Tout résoudre</button>` : ''}
                        </div>
                    </div>
                    <div class="an-type-group-items">
                        ${t.items.map(item => `
                            <div class="an-occurrence">
                                <div>
                                    <div class="an-date" style="color:var(--text);">${item.date}</div>
                                    ${item.observation ? `<div class="an-occurrence-obs">"${item.observation}"</div>` : ''}
                                </div>
                                <button type="button" class="an-btn-resolve-one" onclick="resolveOne(${item.id})">Résoudre</button>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');

            document.getElementById('userModalBackdrop').classList.add('active');
        }

        function closeUserModal() {
            document.getElementById('userModalBackdrop').classList.remove('active');
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