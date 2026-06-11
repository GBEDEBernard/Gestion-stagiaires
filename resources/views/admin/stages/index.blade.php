<x-app-layout>
<style>
    :root {
        --brand:       #0f6fff;
        --brand-light: #e8f0fe;
        --brand-dark:  #0050d0;
        --surface:     #ffffff;
        --surface-alt: #f6f8fc;
        --border:      #e3e8f0;
        --text:        #0d1b2a;
        --muted:       #6b7a99;
        --radius-sm:   10px;
        --radius-md:   16px;
        --radius-lg:   24px;
        --shadow-card: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    }
    .dark {
        --brand-light: #0f2d5e;
        --surface:     #111827;
        --surface-alt: #1a2233;
        --border:      #263047;
        --text:        #f0f4ff;
        --muted:       #7a8aaa;
    }

    /* ── Wrapper ── */
    .si-wrap { max-width: 1280px; margin: 0 auto; padding: 2rem 1.25rem 4rem; }

    /* ── Header ── */
    .si-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        gap: 1rem; flex-wrap: wrap; margin-bottom: 1.75rem;
    }
    .si-eyebrow {
        font-size: .72rem; font-weight: 700; letter-spacing: .12em;
        text-transform: uppercase; color: var(--brand); margin-bottom: .3rem;
    }
    .si-header h1 { font-size: 1.75rem; font-weight: 800; color: var(--text); margin: 0 0 .25rem; }
    .si-header p  { font-size: .875rem; color: var(--muted); margin: 0; }
    .si-header-actions { display: flex; gap: .65rem; flex-wrap: wrap; }

    /* ── Buttons ── */
    .btn-ghost {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .6rem 1.1rem; font-size: .82rem; font-weight: 600;
        color: var(--muted); background: var(--surface-alt);
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        text-decoration: none; white-space: nowrap;
        transition: color .15s, border-color .15s, background .15s;
    }
    .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
    .btn-primary {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .65rem 1.25rem; font-size: .875rem; font-weight: 700;
        color: #fff; background: var(--brand); border: none;
        border-radius: var(--radius-sm); text-decoration: none; white-space: nowrap;
        transition: background .15s, transform .1s;
    }
    .btn-primary:hover { background: var(--brand-dark); }
    .btn-primary:active { transform: scale(.97); }

    /* ── Filters card ── */
    .si-filters {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
    }
    .si-filters-grid {
        display: grid; gap: .9rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px)  { .si-filters-grid { grid-template-columns: repeat(2,1fr); } }
    @media (min-width: 1024px) { .si-filters-grid { grid-template-columns: repeat(4,1fr); } }

    .fi-label {
        display: block; font-size: .73rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--muted); margin-bottom: .45rem;
    }
    .fi-input, .fi-select {
        width: 100%; padding: .62rem .9rem;
        font-size: .85rem; color: var(--text);
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        box-sizing: border-box; outline: none;
        transition: border-color .15s, box-shadow .15s;
        appearance: none; -webkit-appearance: none;
    }
    .fi-input:focus, .fi-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(15,111,255,.12);
        background: var(--surface);
    }
    .fi-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right .75rem center;
        padding-right: 2.25rem; cursor: pointer;
    }
    .fi-search-wrap { position: relative; }
    .fi-search-icon {
        position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }
    .fi-search-wrap .fi-input { padding-left: 2.3rem; }
    .si-count { font-size: .78rem; color: var(--muted); margin-top: .75rem; }

    /* ── Table card ── */
    .si-table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .si-table-scroll { overflow-x: auto; }
    table.si-table { width: 100%; border-collapse: collapse; }
    table.si-table thead tr {
        background: var(--surface-alt);
        border-bottom: 1px solid var(--border);
    }
    table.si-table th {
        padding: .9rem 1.25rem;
        font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .08em;
        color: var(--muted); text-align: left; white-space: nowrap;
    }
    table.si-table th.th-center { text-align: center; }
    table.si-table th.th-right  { text-align: right; }
    table.si-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .12s;
    }
    table.si-table tbody tr:last-child { border-bottom: none; }
    table.si-table tbody tr:hover { background: var(--surface-alt); }
    table.si-table td { padding: .9rem 1.25rem; vertical-align: middle; }

    /* ── Avatar ── */
    .si-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .88rem; color: #fff;
        flex-shrink: 0; overflow: hidden;
        border: 2px solid var(--border);
    }
    .si-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .si-avatar.grad-0 { background: linear-gradient(135deg,#3b82f6,#8b5cf6); }
    .si-avatar.grad-1 { background: linear-gradient(135deg,#10b981,#06b6d4); }
    .si-avatar.grad-2 { background: linear-gradient(135deg,#f59e0b,#ef4444); }
    .si-avatar.grad-3 { background: linear-gradient(135deg,#8b5cf6,#ec4899); }
    .si-avatar.grad-4 { background: linear-gradient(135deg,#0f6fff,#06b6d4); }

    /* ── Stagiaire cell ── */
    .si-person { display: flex; align-items: center; gap: .75rem; min-width: 160px; }
    .si-person-name { font-size: .875rem; font-weight: 600; color: var(--text); }
    .si-person-sub  { font-size: .78rem; color: var(--muted); margin-top: 1px; }

    /* ── Badges / pills ── */
    .pill {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .28rem .7rem; border-radius: 20px;
        font-size: .75rem; font-weight: 600; white-space: nowrap;
    }
    .pill-blue   { background: #dbeafe; color: #1d4ed8; }
    .pill-green  { background: #d1fae5; color: #065f46; }
    .pill-amber  { background: #fef3c7; color: #92400e; }
    .pill-gray   { background: var(--surface-alt); color: var(--muted); border: 1px solid var(--border); }
    .dark .pill-blue  { background: #1e3a5f; color: #93c5fd; }
    .dark .pill-green { background: #064e3b; color: #6ee7b7; }
    .dark .pill-amber { background: #451a03; color: #fcd34d; }
    .dark .pill-gray  { background: #1e2a3a; color: var(--muted); }
    .dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .dot-green  { background: #10b981; animation: pulse 2s infinite; }
    .dot-blue   { background: #3b82f6; }
    .dot-gray   { background: #9ca3af; }
    @keyframes pulse {
        0%,100% { opacity: 1; } 50% { opacity: .4; }
    }

    /* ── Période ── */
    .si-period { font-size: .82rem; }
    .si-period-start { color: var(--text); font-weight: 500; }
    .si-period-end   { color: var(--muted); margin-top: 1px; }
    .si-period-arrow { color: var(--muted); margin: 0 2px; }

    /* ── Organisation ── */
    .si-org-main { font-size: .85rem; font-weight: 600; color: var(--text); }
    .si-org-sub  { font-size: .78rem; color: var(--muted); margin-top: 2px; }

    /* ── Jours ── */
    .si-jours { font-size: .78rem; color: var(--muted); max-width: 140px; line-height: 1.4; }

    /* ── Action buttons ── */
    .si-actions { display: flex; align-items: center; justify-content: flex-end; gap: .4rem; }
    .act-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px;
        border: none; cursor: pointer; transition: background .15s;
        text-decoration: none;
    }
    .act-view   { background: var(--surface-alt); color: var(--muted); border: 1px solid var(--border); }
    .act-view:hover   { background: var(--border); color: var(--text); }
    .act-edit   { background: #dbeafe; color: #1d4ed8; }
    .act-edit:hover   { background: #bfdbfe; }
    .dark .act-edit   { background: #1e3a5f; color: #93c5fd; }
    .dark .act-edit:hover { background: #1e4080; }
    .act-delete { background: #fee2e2; color: #dc2626; }
    .act-delete:hover { background: #fecaca; }
    .dark .act-delete { background: #3b0e0e; color: #f87171; }
    .dark .act-delete:hover { background: #4c1414; }

    /* ── Empty state ── */
    .si-empty { padding: 4rem 1.5rem; text-align: center; }
    .si-empty-icon {
        width: 60px; height: 60px; background: var(--surface-alt);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; color: var(--muted);
    }
    .si-empty h3 { font-size: 1rem; font-weight: 600; color: var(--text); margin: 0 0 .35rem; }
    .si-empty p  { font-size: .875rem; color: var(--muted); margin: 0; }

    /* ── Pagination wrapper ── */
    .si-pagination { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }

    /* ── Responsive: hide less important cols on small ── */
    @media (max-width: 900px)  { .col-jours  { display: none; } }
    @media (max-width: 768px)  { .col-suivi  { display: none; } }
    @media (max-width: 640px)  { .col-org    { display: none; } }
</style>

<div class="si-wrap">

    {{-- ── Header ── --}}
    <div class="si-header">
        <div>
            <p class="si-eyebrow">GST · Gestion des stages</p>
            <h1>Stages</h1>
            <p>Pilotez les stages, leur lieu de présence et le responsable de suivi.</p>
        </div>
        <div class="si-header-actions">
            <a href="{{ route('stages.trash') }}" class="btn-ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Corbeille
            </a>
            <a href="{{ route('stages.create') }}" class="btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Nouveau stage
            </a>
        </div>
    </div>

    {{-- ── Filtres ── --}}
    <form id="filters-form" method="GET" action="{{ route('stages.index') }}" class="si-filters">
        <div class="si-filters-grid">
            <div>
                <label class="fi-label">Statut</label>
                <select name="statut" class="fi-select filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                    <option value="Termine"  {{ in_array(request('statut'), ['Termine','Terminé'], true) ? 'selected' : '' }}>Terminé</option>
                    <option value="A venir"  {{ in_array(request('statut'), ['A venir','À venir'], true) ? 'selected' : '' }}>À venir</option>
                </select>
            </div>
            <div>
                <label class="fi-label">Type de stage</label>
                <select name="typestage" class="fi-select filter-select">
                    <option value="">Tous les types</option>
                    @foreach($typestages as $type)
                    <option value="{{ $type->id }}" {{ request('typestage') == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="fi-label">Stagiaire</label>
                <div class="fi-search-wrap">
                    <svg class="fi-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="nom" value="{{ request('nom') }}" placeholder="Rechercher par nom…" class="fi-input search-input">
                </div>
            </div>
            <div>
                <label class="fi-label">École</label>
                <div class="fi-search-wrap">
                    <svg class="fi-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="ecole" value="{{ request('ecole') }}" placeholder="Rechercher par école…" class="fi-input search-input">
                </div>
            </div>
        </div>
        <p id="result-count" class="si-count">{{ $stages->total() }} stage(s) trouvé(s)</p>
    </form>

    {{-- ── Tableau ── --}}
    <div id="stages-table-container" class="si-table-card">
        <div class="si-table-scroll">
            <table class="si-table">
                <thead>
                    <tr>
                        <th>Stagiaire</th>
                        <th>Type</th>
                        <th class="col-org">Organisation</th>
                        <th class="col-suivi">Thème &amp; Suivi</th>
                        <th>Période</th>
                        <th class="col-jours">Jours</th>
                        <th class="th-center">Statut</th>
                        <th class="th-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stages as $stage)
                    @php
                        $status   = $stage->statut;
                        $etudiant = $stage->etudiant;
                        $personnel = $etudiant?->personnel;
                        $prenom   = $personnel?->prenom ?? '';
                        $nom      = $personnel?->nom     ?? '';
                        $initials = mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1)) ?: 'ST';
                        $gradClass = 'grad-' . ($stage->id % 5);

                        // Photo : depuis le user lié au personnel
                        $user = $personnel?->user ?? null;
                        $avatarPath = $user?->avatar ?? null;
                        $hasPhoto   = $avatarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath);
                    @endphp
                    <tr>
                        {{-- Stagiaire --}}
                        <td>
                            <div class="si-person">
                                <div class="si-avatar {{ $hasPhoto ? '' : $gradClass }}">
                                    @if($hasPhoto)
                                        <img src="{{ asset('storage/' . $avatarPath) }}" alt="{{ $nom }}">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <div>
                                    <p class="si-person-name">{{ $nom }} {{ $prenom }}</p>
                                    <p class="si-person-sub">{{ $etudiant?->ecole ?? '—' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="pill pill-blue">{{ $stage->typestage?->libelle ?? '—' }}</span>
                        </td>

                        {{-- Organisation --}}
                        <td class="col-org">
                            <p class="si-org-main">{{ $stage->domaine?->nom ?? 'Domaine non défini' }}</p>
                            <p class="si-org-sub">{{ $stage->site?->name ?? 'Site non défini' }}</p>
                        </td>

                        {{-- Thème & Suivi --}}
                        <td class="col-suivi">
                            <p class="si-org-main">{{ $stage->theme ?? '—' }}</p>
                            @php
                                $supPerso = $stage->supervisor?->personnel;
                                $supNom   = trim(($supPerso?->nom ?? '') . ' ' . ($supPerso?->prenom ?? ''));
                            @endphp
                            <p class="si-org-sub">{{ $supNom ?: 'Sans superviseur' }}</p>
                        </td>

                        {{-- Période --}}
                        <td>
                            <div class="si-period">
                                <div class="si-period-start">{{ $stage->date_debut?->format('d/m/Y') ?? '—' }}</div>
                                <div class="si-period-end">
                                    <span class="si-period-arrow">→</span>{{ $stage->date_fin?->format('d/m/Y') ?? '—' }}
                                </div>
                            </div>
                        </td>

                        {{-- Jours --}}
                        <td class="col-jours">
                            <span class="si-jours">
                                @if($stage->jours->count())
                                    {{ $stage->jours->pluck('jour')->implode(', ') }}
                                @else
                                    <span style="color:var(--muted)">—</span>
                                @endif
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td style="text-align:center">
                            @if($status === 'En cours')
                                <span class="pill pill-green"><span class="dot dot-green"></span>En cours</span>
                            @elseif(in_array($status, ['A venir','À venir'], true))
                                <span class="pill pill-blue"><span class="dot dot-blue"></span>À venir</span>
                            @else
                                <span class="pill pill-gray"><span class="dot dot-gray"></span>Terminé</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="si-actions">
                                <a href="{{ encrypted_route('stages.show', $stage) }}" class="act-btn act-view" title="Voir">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ encrypted_route('stages.edit', $stage) }}" class="act-btn act-edit" title="Modifier">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form action="{{ encrypted_route('stages.destroy', $stage) }}" method="POST" style="display:inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="act-btn act-delete" title="Supprimer">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="si-empty">
                                <div class="si-empty-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                </div>
                                <h3>Aucun stage trouvé</h3>
                                <p>Modifiez les filtres ou créez un nouveau stage.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="si-pagination">{{ $stages->links() }}</div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('filters-form');
    const container = document.getElementById('stages-table-container');
    if (!form || !container) return;

    let timer;

    function refresh() {
        const url = new URL(form.action);
        const data = new FormData(form);
        for (const [k, v] of data.entries()) {
            v ? url.searchParams.set(k, v) : url.searchParams.delete(k);
        }
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newCont = doc.getElementById('stages-table-container');
                if (newCont) container.innerHTML = newCont.innerHTML;
                const newCount = doc.getElementById('result-count');
                const oldCount = document.getElementById('result-count');
                if (newCount && oldCount) oldCount.innerText = newCount.innerText;
            })
            .catch(console.error);
    }

    form.querySelectorAll('.search-input').forEach(el =>
        el.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(refresh, 380); })
    );
    form.querySelectorAll('.filter-select').forEach(el =>
        el.addEventListener('change', refresh)
    );
    form.addEventListener('submit', e => { e.preventDefault(); refresh(); });
});
</script>
</x-app-layout>