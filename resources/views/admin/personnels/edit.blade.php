<x-app-layout>
<style>
    /* ── Token system (identique à create) ── */
    :root {
        --brand:        #0f6fff;
        --brand-light:  #e8f0fe;
        --brand-dark:   #0050d0;
        --surface:      #ffffff;
        --surface-alt:  #f6f8fc;
        --border:       #e3e8f0;
        --text:         #0d1b2a;
        --muted:        #6b7a99;
        --danger:       #dc2626;
        --danger-bg:    #fef2f2;
        --radius-sm:    10px;
        --radius-md:    16px;
        --radius-lg:    24px;
        --shadow-card:  0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    }
    .dark {
        --brand-light:  #0f2d5e;
        --surface:      #111827;
        --surface-alt:  #1a2233;
        --border:       #263047;
        --text:         #f0f4ff;
        --muted:        #7a8aaa;
        --danger-bg:    #3b0e0e;
    }

    .pc-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.25rem 4rem;
    }
    .pc-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }
    .pc-header-eyebrow {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--brand);
        margin-bottom: .35rem;
    }
    .pc-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
        margin: 0 0 .3rem;
    }
    .pc-header p {
        font-size: .875rem;
        color: var(--muted);
        margin: 0;
    }
    .pc-back-btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .6rem 1.1rem;
        font-size: .82rem;
        font-weight: 600;
        color: var(--muted);
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        text-decoration: none;
        white-space: nowrap;
        transition: color .15s, background .15s, border-color .15s;
    }
    .pc-back-btn:hover { color: var(--text); border-color: var(--muted); }
    .pc-alert {
        background: var(--danger-bg);
        border: 1px solid #fca5a5;
        border-radius: var(--radius-md);
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    .dark .pc-alert { border-color: #7f1d1d; }
    .pc-alert ul { margin: 0; padding: 0 0 0 1.1rem; }
    .pc-alert li { font-size: .83rem; color: var(--danger); line-height: 1.6; }
    .pc-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .pc-section {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--border);
    }
    .pc-section:last-child { border-bottom: none; }
    .pc-section-label {
        display: flex;
        align-items: center;
        gap: .6rem;
        margin-bottom: 1.25rem;
    }
    .pc-section-icon {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: var(--brand-light);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .pc-section-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .pc-section-sub {
        font-size: .78rem;
        color: var(--muted);
        margin: .1rem 0 0;
    }
    .pc-grid {
        display: grid;
        gap: 1.1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
        .pc-grid { grid-template-columns: repeat(2, 1fr); }
        .pc-grid .col-span-2 { grid-column: span 2; }
    }
    .pc-field label {
        display: block;
        font-size: .78rem;
        font-weight: 600;
        color: var(--muted);
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: .5rem;
    }
    .pc-field label .req {
        color: var(--brand);
        margin-left: 2px;
    }
    .pc-input,
    .pc-select,
    .pc-textarea {
        width: 100%;
        padding: .7rem 1rem;
        font-size: .88rem;
        color: var(--text);
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        box-sizing: border-box;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        appearance: none;
    }
    .pc-input:focus,
    .pc-select:focus,
    .pc-textarea:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(15,111,255,.12);
        background: var(--surface);
    }
    .pc-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .85rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }
    .pc-select:disabled {
        opacity: .5;
        cursor: not-allowed;
    }
    .pc-textarea { resize: vertical; min-height: 80px; }
    .pc-field-error {
        margin-top: .35rem;
        font-size: .77rem;
        color: var(--danger);
    }
    /* Badge type */
    .pc-type-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem 1rem;
        background: var(--brand-light);
        border-radius: 30px;
        color: var(--brand);
        font-size: .8rem;
        font-weight: 700;
        margin-top: .5rem;
    }
    .pc-sub {
        margin-top: 1.5rem;
        padding: 1.25rem 1.5rem;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
    }
    .pc-sub-title {
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--brand);
        margin: 0 0 1rem;
    }
    .pc-actions {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
        padding: 1.25rem 2rem;
        background: var(--surface-alt);
        border-top: 1px solid var(--border);
    }
    .pc-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .7rem 1.6rem;
        font-size: .875rem;
        font-weight: 700;
        color: #fff;
        background: var(--brand);
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background .15s, transform .1s;
    }
    .pc-btn-primary:hover { background: var(--brand-dark); }
    .pc-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .7rem 1.2rem;
        font-size: .875rem;
        font-weight: 600;
        color: var(--muted);
        background: transparent;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: color .15s, border-color .15s;
    }
    .pc-btn-ghost:hover { color: var(--text); border-color: var(--muted); }
    @media (max-width: 500px) {
        .pc-section { padding: 1.25rem 1rem; }
        .pc-actions { padding: 1rem; }
        .pc-header h1 { font-size: 1.4rem; }
    }
</style>

<div class="pc-wrapper">
    {{-- ── Page header ── --}}
    <div class="pc-header">
        <div>
            <p class="pc-header-eyebrow">GST · Gestion du personnel</p>
            <h1>Modifier le personnel</h1>
            <p>Mettez à jour les informations, les champs spécifiques s’adapteront au type.</p>
        </div>
        <a href="{{ route('personnels.index') }}" class="pc-back-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 5-7 7 7 7"/></svg>
            Retour à la liste
        </a>
    </div>

    {{-- ── Errors ── --}}
    @if($errors->any())
    <div class="pc-alert">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Main card ── --}}
    <div class="pc-card">
        <form action="{{ route('personnels.update', $personnel) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="{{ $type }}">

            {{-- §1 Informations personnelles --}}
            <div class="pc-section">
                <div class="pc-section-label">
                    <div class="pc-section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <p class="pc-section-title">Informations personnelles</p>
                        <p class="pc-section-sub">Identité, coordonnées et profil</p>
                    </div>
                </div>

                <div class="pc-grid">
                    <div class="pc-field">
                        <label>Prénom <span class="req">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom', $personnel->prenom) }}" required class="pc-input">
                        @error('prenom')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Nom <span class="req">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $personnel->nom) }}" required class="pc-input">
                        @error('nom')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $personnel->email) }}" required class="pc-input">
                        @error('email')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $personnel->telephone) }}" class="pc-input">
                        @error('telephone')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Genre</label>
                        <select name="genre" class="pc-select">
                            <option value="">— Sélectionner —</option>
                            <option value="Homme" {{ old('genre', $personnel->genre) == 'Homme' ? 'selected' : '' }}>Homme</option>
                            <option value="Femme" {{ old('genre', $personnel->genre) == 'Femme' ? 'selected' : '' }}>Femme</option>
                            <option value="Autre" {{ old('genre', $personnel->genre) == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('genre')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance', $personnel->date_naissance) }}" class="pc-input">
                        @error('date_naissance')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field col-span-2">
                        <label>Adresse</label>
                        <textarea name="adresse" rows="2" class="pc-textarea">{{ old('adresse', $personnel->adresse) }}</textarea>
                        @error('adresse')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- §2 Type de personnel (affichage statique) --}}
            <div class="pc-section">
                <div class="pc-section-label">
                    <div class="pc-section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    </div>
                    <div>
                        <p class="pc-section-title">Type de personnel</p>
                        <p class="pc-section-sub">Ce personnel est enregistré comme</p>
                    </div>
                </div>
                <div class="pc-type-badge">
                    @if($type === 'employe')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        Employé
                    @elseif($type === 'etudiant')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        Étudiant / Stagiaire
                    @else
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M5.5 20v-2a6.5 6.5 0 0 1 13 0v2"/></svg>
                        Inconnu
                    @endif
                </div>
            </div>

            {{-- Étudiant fields --}}
            <div id="etudiant-fields" class="pc-section {{ $type !== 'etudiant' ? 'hidden' : '' }}">
                <div class="pc-sub">
                    <p class="pc-sub-title">Informations étudiant</p>
                    <div class="pc-grid">
                        <div class="pc-field">
                            <label>École / Université</label>
                            <input type="text" name="ecole" value="{{ old('ecole', optional($personnel->personnable)->ecole) }}" class="pc-input">
                            @error('ecole')<p class="pc-field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Employé fields --}}
            @php
                $employeData = optional($personnel->personnable);
                $currentSiteId = old('site_id', $employeData->site_id);
                $currentDomaineId = old('domaine_id', $employeData->domaine_id);
            @endphp
            <div id="employe-fields" class="pc-section {{ $type !== 'employe' ? 'hidden' : '' }}">
                <div class="pc-sub">
                    <p class="pc-sub-title">Informations employé</p>
                    <div class="pc-grid">
                        <div class="pc-field">
                            <label>Site <span class="req">*</span></label>
                            <select name="site_id" id="site_id" class="pc-select">
                                <option value="">— Sélectionner un site —</option>
                                @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id', $currentSiteId) == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            </select>
                            @error('site_id')<p class="pc-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="pc-field">
                            <label>Domaine <span class="req">*</span></label>
                            <select name="domaine_id" id="domaine_id" class="pc-select">
                                <option value="">D'abord choisir un site</option>
                            </select>
                            @error('domaine_id')<p class="pc-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="pc-field">
                            <label>Poste</label>
                            <input type="text" name="poste" id="poste" value="{{ old('poste', $employeData->poste ?? 'Employé') }}" class="pc-input">
                            @error('poste')<p class="pc-field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Actions ── --}}
            <div class="pc-actions">
                <button type="submit" class="pc-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Mettre à jour
                </button>
                <a href="{{ route('personnels.index') }}" class="pc-btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
    const domainesParSite = @json($domainesParSite);
    const defaultSiteId    = @json(old('site_id', $currentSiteId ?? ''));
    const defaultDomaineId = @json(old('domaine_id', $currentDomaineId ?? ''));

    function updateDomaineOptions() {
        const siteSelect = document.getElementById('site_id');
        const domaineSelect = document.getElementById('domaine_id');
        if (!siteSelect || !domaineSelect) return;

        const siteId = siteSelect.value;
        const domaines = domainesParSite[siteId] || {};

        domaineSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = siteId ? 'Sélectionnez un domaine' : "D'abord choisir un site";
        domaineSelect.appendChild(placeholder);

        if (!siteId || Object.keys(domaines).length === 0) {
            domaineSelect.disabled = true;
            return;
        }

        domaineSelect.disabled = false;
        Object.entries(domaines).forEach(([id, nom]) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = nom;
            if (id == defaultDomaineId) option.selected = true;
            domaineSelect.appendChild(option);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const siteSelect = document.getElementById('site_id');
        if (siteSelect) {
            siteSelect.addEventListener('change', updateDomaineOptions);
        }
        updateDomaineOptions();
    });
</script>
</x-app-layout>