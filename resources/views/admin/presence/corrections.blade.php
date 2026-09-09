<x-app-layout title="Corrections de pointage - Admin">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap');

        .cd-scope {
            --paper: #f5f3ee;
            --paper-strong: #f0ece5;
            --paper-raised: rgba(255, 255, 255, 0.86);
            --ink: #181c22;
            --ink-soft: #5f6773;
            --ink-faint: #9aa0a9;
            --line: rgba(143, 136, 123, 0.22);
            --copper: #aa5b2d;
            --copper-strong: #8f491d;
            --copper-soft: rgba(170, 91, 45, 0.12);
            --teal: #1c5f68;
            --teal-soft: rgba(28, 95, 104, 0.08);
            --green: #2a7d5f;
            --green-soft: rgba(42, 125, 95, 0.12);
            --shadow-soft: 0 14px 34px rgba(24, 28, 34, 0.08);
            --shadow-card: 0 18px 40px rgba(24, 28, 34, 0.08);
            font-family: 'Public Sans', ui-sans-serif, system-ui, sans-serif;
            color: var(--ink);
            letter-spacing: -0.01em;
        }

        .cd-scope .mono {
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-feature-settings: "tnum" 1;
        }

        .cd-dark .cd-scope {
            --paper: #121720;
            --paper-strong: #171d29;
            --paper-raised: rgba(30, 35, 44, 0.82);
            --ink: #eef1f4;
            --ink-soft: #a3acba;
            --ink-faint: #768193;
            --line: rgba(142, 155, 173, 0.16);
            --copper-soft: rgba(170, 91, 45, 0.18);
            --teal-soft: rgba(28, 95, 104, 0.18);
            --green-soft: rgba(42, 125, 95, 0.18);
            --shadow-soft: 0 18px 40px rgba(5, 7, 11, 0.35);
            --shadow-card: 0 22px 50px rgba(5, 7, 11, 0.35);
        }

        .cd-scope * {
            box-sizing: border-box;
        }

        .cd-shell {
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 18px 44px;
        }

        .cd-header {
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            padding: 24px 22px 20px;
            margin-bottom: 24px;
            border: 1px solid var(--line);
            border-radius: 26px;
            background:
                linear-gradient(135deg, rgba(255,255,255,0.58), rgba(255,255,255,0.14)),
                linear-gradient(130deg, rgba(170,91,45,0.05), rgba(28,95,104,0.05), rgba(42,125,95,0.04));
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .cd-header::after {
            content: "";
            position: absolute;
            inset: auto -10% -45% 48%;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(170,91,45,0.12), rgba(170,91,45,0));
            pointer-events: none;
        }

        .cd-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        .cd-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--copper), var(--teal));
            box-shadow: 0 0 0 5px rgba(170,91,45,0.08);
        }

        .cd-title {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.8rem);
            font-weight: 800;
            line-height: 1.06;
            letter-spacing: -0.06em;
        }

        .cd-sub {
            margin: 12px 0 0;
            max-width: 58ch;
            font-size: 14px;
            line-height: 1.7;
            color: var(--ink-soft);
        }

        .cd-header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .cd-count-card {
            min-width: 110px;
            padding: 12px 14px 10px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255,255,255,0.42);
            text-align: center;
            box-shadow: var(--shadow-soft);
        }

        .cd-count {
            font-size: clamp(2rem, 3vw, 2.7rem);
            line-height: 1;
            font-weight: 700;
            color: var(--copper);
            letter-spacing: -0.06em;
        }

        .cd-count-label {
            margin-top: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        .cd-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 4px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .cd-link:hover {
            transform: translateY(-1px);
        }

        .cd-link::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 1px;
            background: linear-gradient(90deg, var(--copper), var(--teal));
        }

        .cd-panel {
            border: 1px solid var(--line);
            border-radius: 26px;
            background: rgba(255,255,255,0.28);
            box-shadow: var(--shadow-card);
            backdrop-filter: blur(8px);
            overflow: hidden;
        }

        .cd-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 22px 12px;
            border-bottom: 1px solid var(--line);
            background: rgba(255,255,255,0.1);
        }

        .cd-toolbar-left {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .cd-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255,255,255,0.36);
            font-size: 12px;
            color: var(--ink-soft);
            font-weight: 600;
        }

        .cd-pill::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 0 4px rgba(42, 125, 95, 0.12);
        }

        .cd-list {
            padding: 22px;
        }

        .cd-row {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) auto auto auto;
            align-items: center;
            gap: 18px;
            padding: 18px 18px 18px 14px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: rgba(255,255,255,0.36);
            box-shadow: 0 8px 18px rgba(24,28,34,0.02);
            animation: fadeInUp 0.45s ease both;
            animation-delay: var(--delay, 0ms);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .cd-row + .cd-row {
            margin-top: 14px;
        }

        .cd-row:hover {
            transform: translateY(-1px);
            border-color: rgba(170,91,45,0.28);
            box-shadow: 0 16px 28px rgba(24,28,34,0.06);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 900px) {
            .cd-row {
                grid-template-columns: 1fr;
                align-items: flex-start;
            }

            .cd-time-block {
                text-align: left;
            }

            .cd-actions {
                width: 100%;
            }

            .cd-btn {
                width: 100%;
                justify-content: center;
            }
        }

        .cd-identity {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .cd-avatar {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--paper-strong), rgba(255,255,255,0.7));
            border: 1px solid var(--line);
            font-weight: 700;
            font-size: 14px;
            color: var(--ink);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
            flex-shrink: 0;
        }

        .cd-identity-copy {
            min-width: 0;
        }

        .cd-person-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            color: var(--ink-soft);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .cd-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--ink-faint);
        }

        .cd-name {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.03em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cd-meta {
            margin: 4px 0 0;
            font-size: 12.5px;
            color: var(--ink-soft);
            line-height: 1.5;
        }

        .cd-time-block {
            min-width: 124px;
            text-align: left;
        }

        .cd-time-label {
            font-size: 11px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--ink-soft);
            font-weight: 700;
        }

        .cd-time-value {
            display: inline-block;
            margin-top: 4px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.04em;
            color: var(--ink);
        }

        .cd-time-value.is-copper { color: var(--copper); }
        .cd-time-value.is-teal { color: var(--teal); }

        .cd-actions {
            display: flex;
            justify-content: flex-end;
        }

        .cd-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--ink), #2a313d);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            box-shadow: 0 10px 18px rgba(24,28,34,0.15);
        }

        .cd-btn:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, var(--copper), var(--copper-strong));
            box-shadow: 0 14px 22px rgba(170,91,45,0.22);
        }

        .cd-btn svg {
            width: 15px;
            height: 15px;
        }

        .cd-note {
            grid-column: 1 / -1;
            margin: 4px 0 0 58px;
            padding: 12px 14px 12px 16px;
            border-left: 3px solid var(--teal);
            border-radius: 0 12px 12px 0;
            background: rgba(28,95,104,0.04);
            font-size: 13px;
            line-height: 1.6;
            color: var(--ink-soft);
        }

        .cd-note strong {
            color: var(--ink);
            font-weight: 700;
        }

        .cd-callout {
            margin: 0;
            padding: 16px 18px;
            border-left: 4px solid var(--copper);
            background: linear-gradient(135deg, var(--copper-soft), rgba(255,255,255,0.18));
            color: var(--ink);
            font-size: 13.5px;
            line-height: 1.7;
        }

        .cd-empty {
            padding: 64px 20px 52px;
            text-align: center;
        }

        .cd-empty .cd-mark {
            width: 54px;
            height: 54px;
            margin: 0 auto 18px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--green-soft), rgba(255,255,255,0.5));
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(42,125,95,0.2);
            box-shadow: 0 15px 28px rgba(42,125,95,0.08);
        }

        .cd-empty .cd-mark svg {
            width: 24px;
            height: 24px;
            color: var(--green);
        }

        .cd-empty-title {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
        }

        .cd-empty .cd-meta {
            margin-top: 6px;
        }

        .cd-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(18, 23, 32, 0.58);
            backdrop-filter: blur(8px);
        }

        .cd-modal-card {
            width: min(100%, 620px);
            border-radius: 26px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255,255,255,0.82), rgba(255,255,255,0.72));
            box-shadow: 0 24px 60px rgba(18,23,32,0.25);
            overflow: hidden;
            animation: modalIn 0.22s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .cd-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 24px 24px 16px;
            border-bottom: 1px solid var(--line);
        }

        .cd-modal-body {
            padding: 22px 24px 24px;
        }

        .cd-close-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255,255,255,0.4);
            color: var(--ink-soft);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .cd-close-btn:hover {
            color: var(--ink);
            border-color: rgba(170,91,45,0.32);
            transform: rotate(90deg);
        }

        .cd-summary-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            margin-top: 16px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.35);
        }

        .cd-summary-box strong {
            color: var(--ink);
        }

        .cd-claim-box {
            margin-top: 16px;
            padding: 14px 16px;
            border-left: 4px solid var(--teal);
            border-radius: 12px;
            background: var(--teal-soft);
        }

        .cd-claim-box .cd-meta {
            margin-top: 4px;
        }

        .cd-field-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
        }

        .cd-input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255,255,255,0.44);
            color: var(--ink);
            padding: 12px 14px;
            font-size: 14px;
            font-family: 'IBM Plex Mono', monospace;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .cd-input:focus {
            outline: none;
            border-color: rgba(170,91,45,0.5);
            box-shadow: 0 0 0 4px rgba(170,91,45,0.08);
        }

        textarea.cd-input {
            font-family: 'Public Sans', sans-serif;
            resize: vertical;
            min-height: 110px;
        }

        .cd-input-row {
            margin-top: 18px;
        }

        .cd-actions-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
        }

        .cd-btn-ghost {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255,255,255,0.28);
            color: var(--ink-soft);
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .cd-btn-ghost:hover {
            border-color: rgba(170,91,45,0.28);
            color: var(--ink);
        }

        .cd-btn-primary {
            border: none;
            border-radius: 12px;
            padding: 11px 18px;
            background: linear-gradient(135deg, var(--copper), var(--copper-strong));
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 12px 22px rgba(170,91,45,0.22);
            transition: transform 0.18s ease, filter 0.18s ease;
        }

        .cd-btn-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.04);
        }
    </style>

    <div class="cd-scope">
        <div class="cd-shell">
            <header class="cd-header">
                <div>
                    <p class="cd-kicker">Suivi &amp; présence</p>
                    <h1 class="cd-title">Corrections de pointage</h1>
                    <p class="cd-sub">
                        Un départ non pointé reste ouvert tant qu'il n'a pas été rétabli à la main.
                        Vérifiez chaque personne, posez l'heure réelle, puis passez à la suivante.
                    </p>
                </div>

                <div class="cd-header-right">
                    <div class="cd-count-card">
                        <div class="cd-count mono">{{ str_pad($pendingCorrections->count(), 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="cd-count-label">Départ{{ $pendingCorrections->count() > 1 ? 's' : '' }} en attente</div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="cd-link">Tableau de bord</a>
                </div>
            </header>

            <div x-data="{
                outModal: false, outDayId: null, outUserId: null, outUserName: '', outLabel: '',
                outCurrent: '', outClaimed: '', outClaimReason: '', outValue: '', outReason: ''
            }" @open-departure-correction.window="outModal = true; outDayId = $event.detail.id; outUserId = $event.detail.userId; outUserName = $event.detail.userName; outLabel = $event.detail.label; outCurrent = $event.detail.current; outClaimed = $event.detail.claimed; outClaimReason = $event.detail.claimReason; outValue = $event.detail.claimed; outReason = ''">

                <main class="cd-panel">
                    <div class="cd-toolbar">
                        <div class="cd-toolbar-left">
                            <span class="cd-pill">File de corrections</span>
                            <span class="cd-pill">Validation manuelle</span>
                        </div>
                    </div>

                    <div class="cd-list">
                        @forelse($pendingCorrections as $day)
                        @php
                            $person        = $day->etudiant?->user ?? $day->user;
                            $personName    = $person?->name ?? ($day->etudiant?->user?->name ?? 'Utilisateur inconnu');
                            $personRole    = $day->etudiant ? 'Stagiaire' : 'Employé';
                            $hasClaim      = $day->departure_status === 'claimed';
                            $missedSince   = $day->attendance_date->diffForHumans();
                            $autoTime      = $day->last_check_out_at?->format('H:i') ?? '--:--';
                            $claimedTime   = $day->claimed_check_out_at?->format('H:i');
                            $userId        = $person?->id;
                        @endphp

                        <article class="cd-row" style="--delay: {{ $loop->index * 70 }}ms;">
                            <div class="cd-identity">
                                <div class="cd-avatar">{{ strtoupper(substr($personName, 0, 1)) }}</div>
                                <div class="cd-identity-copy">
                                    <div class="cd-person-meta">
                                        <span>{{ $personRole }}</span>
                                        <span class="cd-dot"></span>
                                        <span>{{ $missedSince }}</span>
                                    </div>
                                    <h2 class="cd-name">{{ $personName }}</h2>
                                    <p class="cd-meta">{{ $day->attendance_date->locale('fr')->isoFormat('dddd D MMMM') }}</p>
                                </div>
                            </div>

                            <div class="cd-time-block">
                                <div class="cd-time-label">Clôturé d'office</div>
                                <div class="cd-time-value mono is-copper">{{ $autoTime }}</div>
                            </div>

                            @if($hasClaim)
                                <div class="cd-time-block">
                                    <div class="cd-time-label">Déclaré</div>
                                    <div class="cd-time-value mono is-teal">{{ $claimedTime }}</div>
                                </div>
                            @else
                                <div class="cd-time-block">
                                    <div class="cd-time-label">Déclaré</div>
                                    <div class="cd-time-value mono" style="color: var(--ink-faint);">—</div>
                                </div>
                            @endif

                            <div class="cd-actions">
                                <button type="button"
                                    onclick="openDepartureCorrection(
                                        {{ $day->id }},
                                        {{ $userId ?? 0 }},
                                        @js($personName),
                                        @js($day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY')),
                                        @js($autoTime),
                                        @js($claimedTime),
                                        @js($day->claimed_check_out_reason)
                                    )"
                                    class="cd-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                    </svg>
                                    Rétablir l'heure
                                </button>
                            </div>

                            @if($hasClaim && $day->claimed_check_out_reason)
                                <div class="cd-note">
                                    <strong>{{ $personName }} a déclaré :</strong> {{ $day->claimed_check_out_reason }}
                                </div>
                            @endif
                        </article>
                        @empty
                        <div class="cd-empty">
                            <div class="cd-mark">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="cd-empty-title">Aucun départ à régler</p>
                            <p class="cd-meta">Toutes les journées clôturées d'office ont été traitées.</p>
                        </div>
                        @endforelse
                    </div>

                    <p class="cd-callout">
                        Le motif de correction est obligatoire : le volume horaire entre dans la note de stage.
                        Ne rétablissez l'heure qu'après avoir vu la personne ou son justificatif.
                    </p>
                </main>

                <div x-show="outModal" x-cloak class="cd-modal-backdrop" style="display:none;" @click.self="outModal = false">
                    <div class="cd-modal-card" role="dialog" aria-modal="true" aria-labelledby="correction-title">
                        <div class="cd-modal-header">
                            <div>
                                <p class="cd-kicker" style="margin-bottom: 6px;">Rétablir l'heure de départ</p>
                                <p class="cd-name" x-text="outUserName" id="correction-title"></p>
                                <p class="cd-meta" x-text="outLabel"></p>
                            </div>
                            <button type="button" class="cd-close-btn" @click="outModal = false" aria-label="Fermer">✕</button>
                        </div>

                        <form method="POST" :action="outUserId && outDayId ? '{{ url('admin/attendance-tracking/user') }}/' + outUserId + '/days/' + outDayId + '/correction-depart' : '#'">
                            @csrf

                            <div class="cd-modal-body">
                                <p class="cd-sub" style="margin-top: 0;">
                                    Le départ n'a pas été pointé : la journée a été clôturée à l'heure de fin prévue,
                                    jamais au-delà. Posez ici l'heure réelle, après avoir vu la personne.
                                </p>

                                <div class="cd-summary-box">
                                    <span class="cd-time-label">Clôturé d'office à</span>
                                    <strong class="mono cd-time-value is-copper" x-text="outCurrent"></strong>
                                </div>

                                <div class="cd-claim-box" x-show="outClaimed" x-cloak>
                                    <p class="cd-meta">
                                        Déclaré par l'intéressé : <strong class="mono" style="color: var(--ink);" x-text="outClaimed"></strong>
                                    </p>
                                    <p class="cd-meta" x-text="outClaimReason"></p>
                                </div>
                                <p class="cd-meta" style="margin-top: 14px;" x-show="!outClaimed" x-cloak>Rien n'a été déclaré pour cette journée.</p>

                                <div class="cd-input-row">
                                    <label class="cd-field-label">Heure réelle de départ</label>
                                    <input type="time" name="time" x-model="outValue" required class="cd-input">
                                </div>

                                <div class="cd-input-row">
                                    <label class="cd-field-label">Motif</label>
                                    <textarea name="reason" x-model="outReason" rows="3" required minlength="5"
                                        placeholder="Ex. départ à 18h30 confirmé par le responsable de site"
                                        class="cd-input"></textarea>
                                    <p class="cd-meta" style="margin-top: 8px;">
                                        Obligatoire : le volume horaire entre dans la note de stage.
                                    </p>
                                </div>

                                <div class="cd-actions-row">
                                    <button type="button" class="cd-btn-ghost" @click="outModal = false">Fermer</button>
                                    <button type="submit" class="cd-btn-primary">Rétablir l'heure</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openDepartureCorrection(id, userId, userName, label, current, claimed, claimReason) {
            window.dispatchEvent(new CustomEvent('open-departure-correction', {
                detail: { id, userId, userName, label, current: current || '--:--', claimed: claimed || '', claimReason: claimReason || '' }
            }));
        }
    </script>
    @endpush

</x-app-layout>