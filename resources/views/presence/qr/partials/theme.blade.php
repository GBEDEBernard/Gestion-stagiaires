{{--
    Thème partagé des écrans de pointage QR.
    CSS écrit à la main plutôt que Tailwind CDN : ces pages sont ouvertes
    debout devant une porte, souvent en données mobiles. Aucune requête
    réseau supplémentaire, aucune police distante, rendu immédiat.
--}}
<style>
    :root {
        --bg:              #f5f4ee;
        --surface:         #ffffff;
        --surface-sunken:  #faf9f5;
        --text:            #1a1a18;
        --text-secondary:  #6b6961;
        --text-tertiary:   #94918a;
        --border:          #e7e5dc;
        --border-strong:   #d7d4c9;
        --accent:          #c15f3c;
        --accent-soft:     #f8efe9;
        --success:         #2f7a55;
        --success-soft:    #ecf4ef;
        --info:            #3f6b9e;
        --info-soft:       #eef3f9;
        --danger:          #b3402e;
        --danger-soft:     #fbeeeb;
        --radius:          12px;
        --radius-sm:       8px;
        --shadow-sm:       0 1px 2px rgba(30, 28, 24, .04);
        --shadow:          0 1px 3px rgba(30, 28, 24, .05), 0 10px 28px -12px rgba(30, 28, 24, .12);
    }

    *, *::before, *::after { box-sizing: border-box; }

    html, body {
        margin: 0;
        padding: 0;
        min-height: 100%;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI",
                     "Helvetica Neue", Arial, sans-serif;
        font-size: 15px;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        min-height: 100vh;
        min-height: 100dvh;
    }

    /* ---------- Carte principale (modal) ---------- */

    .card {
        width: 100%;
        max-width: 400px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 28px 24px 24px;
        animation: card-in 340ms cubic-bezier(.2, .8, .2, 1) both;
    }

    @keyframes card-in {
        from { opacity: 0; transform: translateY(8px) scale(.985); }
        to   { opacity: 1; transform: none; }
    }

    /* ---------- Pastille d'icône ---------- */

    .badge {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        animation: badge-in 380ms 60ms cubic-bezier(.2, .8, .2, 1) both;
    }
    .badge svg { width: 22px; height: 22px; stroke-width: 1.75; }

    .badge--success { background: var(--success-soft); color: var(--success); }
    .badge--info    { background: var(--info-soft);    color: var(--info); }
    .badge--danger  { background: var(--danger-soft);  color: var(--danger); }
    .badge--accent  { background: var(--accent-soft);  color: var(--accent); }

    @keyframes badge-in {
        from { opacity: 0; transform: scale(.8); }
        to   { opacity: 1; transform: none; }
    }

    /* Tracé progressif du symbole (coche, croix...) */
    .draw path, .draw polyline, .draw line {
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: draw 520ms 200ms cubic-bezier(.3, .7, .2, 1) forwards;
    }
    @keyframes draw { to { stroke-dashoffset: 0; } }

    /* ---------- Typographie ---------- */

    .title {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 600;
        letter-spacing: -.015em;
        line-height: 1.3;
    }

    .subtitle {
        margin: 0 0 20px;
        font-size: 14px;
        color: var(--text-secondary);
    }

    .eyebrow {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 20px;
        font-size: 12px;
        font-weight: 500;
        color: var(--text-tertiary);
    }
    .eyebrow svg { width: 13px; height: 13px; stroke-width: 1.75; flex: none; }

    /* ---------- Tableau de détails ---------- */

    .details {
        background: var(--surface-sunken);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 4px 14px;
        margin-bottom: 18px;
    }

    .row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 16px;
        padding: 10px 0;
        font-size: 13px;
        border-bottom: 1px solid var(--border);
    }
    .row:last-child { border-bottom: 0; }

    .row dt { color: var(--text-secondary); flex: none; }
    .row dd {
        margin: 0;
        font-weight: 500;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    /* ---------- Bloc d'information / erreur ---------- */

    .notice {
        display: flex;
        gap: 10px;
        padding: 13px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface-sunken);
        margin-bottom: 18px;
        text-align: left;
    }
    .notice svg { width: 16px; height: 16px; stroke-width: 1.75; flex: none; margin-top: 1px; }
    .notice-body { min-width: 0; }
    .notice-title {
        margin: 0 0 3px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: -.005em;
    }
    .notice-text {
        margin: 0;
        font-size: 13px;
        line-height: 1.5;
        color: var(--text-secondary);
        overflow-wrap: anywhere;
    }

    .notice--danger { background: var(--danger-soft); border-color: #f0d9d4; }
    .notice--danger svg, .notice--danger .notice-title { color: var(--danger); }
    .notice--danger .notice-text { color: #8a4437; }

    .notice--accent { background: var(--accent-soft); border-color: #efdfd6; }
    .notice--accent svg, .notice--accent .notice-title { color: var(--accent); }
    .notice--accent .notice-text { color: #8a5340; }

    /* ---------- Boutons ---------- */

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 10px 16px;
        font: inherit;
        font-size: 14px;
        font-weight: 500;
        border-radius: var(--radius-sm);
        border: 1px solid transparent;
        cursor: pointer;
        transition: background-color 120ms ease, border-color 120ms ease;
        -webkit-tap-highlight-color: transparent;
    }
    .btn svg { width: 15px; height: 15px; stroke-width: 1.75; }

    .btn--primary { background: var(--text); color: #fff; }
    .btn--primary:hover { background: #333330; }

    .btn--secondary {
        background: var(--surface);
        color: var(--text);
        border-color: var(--border-strong);
    }
    .btn--secondary:hover { background: var(--surface-sunken); }

    /* ---------- Pied de carte ---------- */

    .footnote {
        margin: 0;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        font-size: 12px;
        line-height: 1.5;
        color: var(--text-tertiary);
    }

    .stack > * + * { margin-top: 8px; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
        .draw path, .draw polyline, .draw line { stroke-dashoffset: 0; }
    }
</style>
