<x-app-layout>
<style>
    /* ── Token system ── */
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

    /* ── Layout shell ── */
    .pc-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.25rem 4rem;
    }

    /* ── Page header ── */
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
    .pc-back-btn svg { flex-shrink: 0; }

    /* ── Alert errors ── */
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

    /* ── Card ── */
    .pc-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    /* ── Section divider inside card ── */
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
    .pc-section-icon svg { color: var(--brand); }
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

    /* ── Grid ── */
    .pc-grid {
        display: grid;
        gap: 1.1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
        .pc-grid { grid-template-columns: repeat(2, 1fr); }
        .pc-grid .col-span-2 { grid-column: span 2; }
    }

    /* ── Field ── */
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
        -webkit-appearance: none;
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

    /* ── Type selector pill tabs ── */
    .pc-type-tabs {
        display: inline-flex;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 4px;
        gap: 4px;
        margin-top: .75rem;
    }
    .pc-type-tab {
        position: relative;
        display: flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem 1.25rem;
        font-size: .85rem;
        font-weight: 600;
        color: var(--muted);
        background: transparent;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        transition: color .2s, background .2s, box-shadow .2s;
        white-space: nowrap;
        /* hide the real select */
    }
    .pc-type-tab:hover { color: var(--text); }
    .pc-type-tab.active {
        color: var(--brand);
        background: var(--surface);
        box-shadow: 0 1px 4px rgba(0,0,0,.1);
    }
    .dark .pc-type-tab.active { background: #1e2a3a; }

    /* ── Sub-section (specific fields) ── */
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

    /* ── Actions footer ── */
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
    .pc-btn-primary:active { transform: scale(.97); }
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

    /* ── Checkbox day pills ── */
    .day-grid { display: flex; flex-wrap: wrap; gap: .5rem; }
    .day-label {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .85rem;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        font-size: .82rem;
        font-weight: 600;
        color: var(--muted);
        transition: border-color .15s, color .15s, background .15s;
    }
    .day-label:has(input:checked) {
        border-color: var(--brand);
        color: var(--brand);
        background: var(--brand-light);
    }
    .day-label input { display: none; }

    /* ── Modal ── */
    .pc-modal-backdrop {
        position: fixed; inset: 0; z-index: 50;
        background: rgba(0,0,0,.55);
        backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
    }
    .pc-modal {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .pc-modal-header {
        position: sticky; top: 0; z-index: 10;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }
    .pc-modal-header-info { display: flex; align-items: center; gap: .75rem; }
    .pc-modal-icon {
        width: 40px; height: 40px;
        background: var(--brand-light);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .pc-modal-title { font-size: 1rem; font-weight: 700; color: var(--text); margin: 0; }
    .pc-modal-sub { font-size: .8rem; color: var(--muted); margin: 2px 0 0; }
    .pc-modal-sub span { color: var(--brand); font-weight: 600; }
    .pc-modal-close {
        display: flex; align-items: center; justify-content: center;
        width: 34px; height: 34px;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        color: var(--muted);
        flex-shrink: 0;
        transition: color .15s, background .15s;
    }
    .pc-modal-close:hover { color: var(--text); background: var(--border); }
    .pc-modal-body { padding: 1.5rem; display: grid; gap: 1rem; }
    .pc-modal-footer {
        display: flex; align-items: center; justify-content: flex-end; gap: .6rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        background: var(--surface-alt);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }
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
            <h1>Nouveau personnel</h1>
            <p>Renseignez les informations, choisissez le type, puis enregistrez.</p>
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
        <form action="{{ route('personnels.store') }}" method="POST">
        @csrf

        {{-- §1 Informations personnelles --}}
        <div class="pc-section">
            <div class="pc-section-label">
                <div class="pc-section-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <p class="pc-section-title">Informations personnelles</p>
                    <p class="pc-section-sub">Identité, coordonnées et profil</p>
                </div>
            </div>

            <div class="pc-grid">
                <div class="pc-field">
                    <label>Prénom <span class="req">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required class="pc-input" placeholder="ex : Jean">
                    @error('prenom')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pc-field">
                    <label>Nom <span class="req">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required class="pc-input" placeholder="ex : Dupont">
                    @error('nom')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pc-field">
                    <label>Email <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="pc-input" placeholder="jean.dupont@example.com">
                    @error('email')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pc-field">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" class="pc-input" placeholder="+229 97 00 00 00">
                    @error('telephone')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pc-field">
                    <label>Genre</label>
                    <select name="genre" class="pc-select">
                        <option value="">— Sélectionner —</option>
                        <option value="Homme"  {{ old('genre') == 'Homme'  ? 'selected' : '' }}>Homme</option>
                        <option value="Femme"  {{ old('genre') == 'Femme'  ? 'selected' : '' }}>Femme</option>
                        <option value="Autre"  {{ old('genre') == 'Autre'  ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('genre')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pc-field">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" class="pc-input">
                    @error('date_naissance')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pc-field col-span-2">
                    <label>Adresse</label>
                    <textarea name="adresse" rows="2" class="pc-textarea" placeholder="Quartier, ville…">{{ old('adresse') }}</textarea>
                    @error('adresse')<p class="pc-field-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- §2 Type de personnel --}}
        <div class="pc-section">
            <div class="pc-section-label">
                <div class="pc-section-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                </div>
                <div>
                    <p class="pc-section-title">Type de personnel</p>
                    <p class="pc-section-sub">Choisissez pour afficher les champs adaptés</p>
                </div>
            </div>

            {{-- Hidden real select --}}
            <select name="type" id="type-select" style="display:none">
                <option value="etudiant" {{ old('type','etudiant') === 'etudiant' ? 'selected' : '' }}>Étudiant</option>
                <option value="employe"  {{ old('type') === 'employe'  ? 'selected' : '' }}>Employé</option>
            </select>

            {{-- Visual tab switcher --}}
            <div class="pc-type-tabs" role="group" aria-label="Type de personnel">
                <button type="button" class="pc-type-tab {{ old('type','etudiant') === 'etudiant' ? 'active' : '' }}" data-value="etudiant">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    Étudiant / Stagiaire
                </button>
                <button type="button" class="pc-type-tab {{ old('type') === 'employe' ? 'active' : '' }}" data-value="employe">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    Employé
                </button>
            </div>
            @error('type')<p class="pc-field-error" style="margin-top:.5rem">{{ $message }}</p>@enderror

            {{-- Étudiant fields --}}
            <div id="etudiant-fields" class="pc-sub {{ old('type','etudiant') !== 'etudiant' ? 'hidden' : '' }}">
                <p class="pc-sub-title">Informations étudiant</p>
                <div class="pc-grid">
                    <div class="pc-field">
                        <label>École / Université <span class="req">*</span></label>
                        <input type="text" name="ecole" value="{{ old('ecole') }}" class="pc-input" placeholder="ex : UAC, EPAC…">
                        @error('ecole')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Employé fields --}}
            <div id="employe-fields" class="pc-sub {{ old('type') !== 'employe' ? 'hidden' : '' }}">
                <p class="pc-sub-title">Informations employé</p>
                <div class="pc-grid">
                    <div class="pc-field">
                        <label>Site <span class="req">*</span></label>
                        <select name="site_id" id="site_id" class="pc-select">
                            <option value="">— Sélectionner un site —</option>
                            @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                            @endforeach
                        </select>
                        @error('site_id')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Domaine <span class="req">*</span></label>
                        <select name="domaine_id" id="domaine_id" class="pc-select" disabled>
                            <option value="">D'abord choisir un site</option>
                        </select>
                        @error('domaine_id')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pc-field">
                        <label>Poste</label>
                        <input type="text" name="poste" id="poste" value="{{ old('poste', 'Employé') }}" class="pc-input">
                        @error('poste')<p class="pc-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="pc-actions">
            <button type="submit" class="pc-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('personnels.index') }}" class="pc-btn-ghost">Annuler</a>
        </div>

        </form>
    </div>{{-- /.pc-card --}}
</div>{{-- /.pc-wrapper --}}


{{-- ── Modal Stage ── --}}
@if(session('open_stage_modal'))
<div id="stageModal" class="pc-modal-backdrop">
    <div class="pc-modal">
        <div class="pc-modal-header">
            <div class="pc-modal-header-info">
                <div class="pc-modal-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--brand)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <p class="pc-modal-title">Créer le stage</p>
                    <p class="pc-modal-sub">Pour <span>{{ session('new_etudiant_nom') }}</span></p>
                </div>
            </div>
            <button type="button" class="pc-modal-close" onclick="document.getElementById('stageModal').remove()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form action="{{ route('stages.store') }}" method="POST">
        @csrf
        <input type="hidden" name="etudiant_id" value="{{ session('new_etudiant_id') }}">

        <div class="pc-modal-body">
            <div class="pc-grid">
                <div class="pc-field">
                    <label>Type de stage</label>
                    <select name="typestage_id" class="pc-select">
                        <option value="">— Sélectionner —</option>
                        @foreach($typestages as $type)
                        <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pc-field">
                    <label>Badge</label>
                    <select name="badge_id" class="pc-select">
                        <option value="">— Sélectionner —</option>
                        @foreach($badges as $badge)
                        <option value="{{ $badge->id }}">{{ $badge->badge }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pc-field">
                    <label>Service</label>
                    <select name="service_id" class="pc-select">
                        <option value="">— Sélectionner —</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pc-field">
                    <label>Site de présence</label>
                    <select name="site_id" class="pc-select">
                        <option value="">— Sélectionner —</option>
                        @foreach($stageSites as $site)
                        <option value="{{ $site->id }}">{{ $site->name }}{{ $site->city ? ' — '.$site->city : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pc-field">
                <label>Superviseur</label>
                <select name="supervisor_id" class="pc-select">
                    <option value="">— Sélectionner —</option>
                    @foreach($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->personnel->nom ?? $supervisor->name ?? '' }} {{ $supervisor->personnel->prenom ?? '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pc-field">
                <label>Thème du stage</label>
                <input type="text" name="theme" class="pc-input" placeholder="ex : Développement web, Data analyse…">
            </div>

            <div class="pc-grid">
                <div class="pc-field">
                    <label>Date de début <span class="req">*</span></label>
                    <input type="date" name="date_debut" required class="pc-input">
                </div>
                <div class="pc-field">
                    <label>Date de fin <span class="req">*</span></label>
                    <input type="date" name="date_fin" required class="pc-input">
                </div>
            </div>

            <div class="pc-field">
                <label>Jours de présence <span class="req">*</span></label>
                <div class="day-grid">
                    @foreach($jours as $jour)
                    <label class="day-label">
                        <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}">
                        {{ $jour->jour }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="pc-modal-footer">
            <button type="button" onclick="document.getElementById('stageModal').remove()" class="pc-btn-ghost">Ignorer</button>
            <button type="submit" class="pc-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Créer le stage
            </button>
        </div>
        </form>
    </div>
</div>
@endif


<script>
    /* ── Type tab switcher ── */
    const domainesParSite  = @json($domainesParSite);
    const defaultSiteId    = @json(old('site_id', ''));
    const defaultDomaineId = @json(old('domaine_id', ''));

    function setType(value) {
        const realSelect  = document.getElementById('type-select');
        const etudiantDiv = document.getElementById('etudiant-fields');
        const employeDiv  = document.getElementById('employe-fields');
        const isEtudiant  = value === 'etudiant';

        realSelect.value = value;

        etudiantDiv.classList.toggle('hidden', !isEtudiant);
        employeDiv.classList.toggle('hidden',  isEtudiant);

        etudiantDiv.querySelectorAll('input, select, textarea').forEach(f => f.disabled = !isEtudiant);
        employeDiv.querySelectorAll('input, select, textarea').forEach(f => f.disabled =  isEtudiant);

        const poste = document.getElementById('poste');
        if (poste && value === 'employe' && !poste.value) poste.value = 'Employé';

        // update tab visual
        document.querySelectorAll('.pc-type-tab').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.value === value);
        });
    }

    function updateDomaineOptions() {
        const siteSelect   = document.getElementById('site_id');
        const domaineSelect = document.getElementById('domaine_id');
        if (!siteSelect || !domaineSelect) return;

        const siteId  = siteSelect.value;
        const domaines = domainesParSite[siteId] || {};

        domaineSelect.innerHTML = '';
        const ph = document.createElement('option');
        ph.value = '';
        ph.textContent = siteId ? 'Sélectionnez un domaine' : "D'abord choisir un site";
        domaineSelect.appendChild(ph);

        if (!siteId || !Object.keys(domaines).length) {
            domaineSelect.disabled = true;
            return;
        }

        domaineSelect.disabled = false;
        Object.entries(domaines).forEach(([id, nom]) => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = nom;
            if (id == defaultDomaineId) opt.selected = true;
            domaineSelect.appendChild(opt);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Tab buttons
        document.querySelectorAll('.pc-type-tab').forEach(btn => {
            btn.addEventListener('click', () => setType(btn.dataset.value));
        });

        // Site → domaine
        const siteSelect = document.getElementById('site_id');
        if (siteSelect) siteSelect.addEventListener('change', updateDomaineOptions);

        // Init
        setType(document.getElementById('type-select').value || 'etudiant');
        updateDomaineOptions();

        // Pre-select site if old value
        if (defaultSiteId) {
            const s = document.getElementById('site_id');
            if (s) { s.value = defaultSiteId; updateDomaineOptions(); }
        }
    });
</script>
</x-app-layout>