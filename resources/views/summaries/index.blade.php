<x-app-layout>
    <div class="summaries-pro min-h-screen" style="background: var(--sp-bg);">
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

            .summaries-pro {
                --sp-bg: #f6f8fa;
                --sp-surface: #ffffff;
                --sp-text: #1a1a2e;
                --sp-text-secondary: #4a4a6a;
                --sp-text-muted: #8a8aa8;
                --sp-text-dim: #b0b0c8;
                --sp-border: rgba(0, 0, 0, 0.06);
                --sp-border-strong: rgba(0, 0, 0, 0.08);
                --sp-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
                --sp-shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.08), 0 8px 32px rgba(0, 0, 0, 0.06);
                --sp-primary: #4f46e5;
                --sp-primary-hover: #4338ca;
                --sp-purple: #7c3aed;
                --sp-purple-hover: #6d28d9;
                --sp-emerald: #059669;
                --sp-emerald-hover: #047857;
                --sp-success: #10b981;
                --sp-danger: #ef4444;
                --sp-warning: #f59e0b;
                --sp-radius: 16px;
                --sp-radius-sm: 12px;
                --sp-font: 'Inter', -apple-system, system-ui, sans-serif;
            }

            .summaries-pro * {
                font-family: var(--sp-font);
            }

            /* Dark Mode */
            .summaries-pro.dark {
                --sp-bg: #0f0f1a;
                --sp-surface: #1a1a2e;
                --sp-text: #f1f1f6;
                --sp-text-secondary: #b0b0c8;
                --sp-text-muted: #6a6a8a;
                --sp-text-dim: #4a4a6a;
                --sp-border: rgba(255, 255, 255, 0.06);
                --sp-border-strong: rgba(255, 255, 255, 0.08);
                --sp-shadow: 0 1px 3px rgba(0, 0, 0, 0.4), 0 4px 16px rgba(0, 0, 0, 0.3);
                --sp-shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.5), 0 8px 32px rgba(0, 0, 0, 0.4);
                --sp-primary: #818cf8;
                --sp-primary-hover: #6366f1;
                --sp-purple: #a78bfa;
                --sp-purple-hover: #8b5cf6;
                --sp-emerald: #34d399;
                --sp-emerald-hover: #6ee7b7;
            }

            /* Animations */
            @keyframes sp-fade-in {
                from { opacity: 0; transform: translateY(16px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes sp-scale-in {
                from { opacity: 0; transform: scale(0.96); }
                to { opacity: 1; transform: scale(1); }
            }

            @keyframes sp-pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }

            .sp-appear { animation: sp-fade-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
            .sp-appear-2 { animation: sp-fade-in 0.5s 0.08s cubic-bezier(0.16, 1, 0.3, 1) both; }
            .sp-appear-3 { animation: sp-fade-in 0.5s 0.16s cubic-bezier(0.16, 1, 0.3, 1) both; }
            .sp-appear-4 { animation: sp-fade-in 0.5s 0.24s cubic-bezier(0.16, 1, 0.3, 1) both; }
            .sp-pulse { animation: sp-pulse 2s ease-in-out infinite; }

            /* Layout */
            .sp-container {
                max-width: 1280px;
                margin: 0 auto;
                padding: 24px 16px;
            }

            @media (min-width: 640px) {
                .sp-container { padding: 32px 24px; }
            }

            @media (min-width: 1024px) {
                .sp-container { padding: 40px 32px; }
            }

            /* Cards */
            .sp-card {
                background: var(--sp-surface);
                border: 1px solid var(--sp-border);
                border-radius: var(--sp-radius);
                box-shadow: var(--sp-shadow);
                transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            }

            .sp-card-hover:hover {
                border-color: var(--sp-primary);
                box-shadow: var(--sp-shadow-hover);
                transform: translateY(-2px);
            }

            .dark .sp-card-hover:hover {
                border-color: rgba(129, 140, 248, 0.3);
            }

            /* Buttons */
            .sp-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                border: none;
                border-radius: var(--sp-radius-sm);
                font-size: 0.875rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                white-space: nowrap;
                text-decoration: none;
                color: #fff;
            }

            .sp-btn svg {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
            }

            .sp-btn-primary {
                background: var(--sp-primary);
            }
            .sp-btn-primary:hover {
                background: var(--sp-primary-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            }

            .sp-btn-purple {
                background: var(--sp-purple);
            }
            .sp-btn-purple:hover {
                background: var(--sp-purple-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
            }

            .sp-btn-emerald {
                background: var(--sp-emerald);
            }
            .sp-btn-emerald:hover {
                background: var(--sp-emerald-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
            }

            .sp-btn-sm {
                padding: 8px 16px;
                font-size: 0.8125rem;
            }

            .sp-btn-sm svg {
                width: 16px;
                height: 16px;
            }

            /* Responsive button group */
            .sp-btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            @media (max-width: 640px) {
                .sp-btn-group .sp-btn {
                    flex: 1;
                    justify-content: center;
                    padding: 10px 14px;
                    font-size: 0.8rem;
                }
                .sp-btn-group .sp-btn svg {
                    width: 16px;
                    height: 16px;
                }
            }

            /* Badge */
            .sp-badge {
                display: inline-flex;
                align-items: center;
                padding: 2px 12px;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            .sp-badge-indigo {
                background: rgba(79, 70, 229, 0.1);
                color: var(--sp-primary);
            }

            .sp-badge-purple {
                background: rgba(124, 58, 237, 0.1);
                color: var(--sp-purple);
            }

            .sp-badge-emerald {
                background: rgba(5, 150, 105, 0.1);
                color: var(--sp-emerald);
            }

            .dark .sp-badge-indigo {
                background: rgba(129, 140, 248, 0.15);
            }
            .dark .sp-badge-purple {
                background: rgba(167, 139, 250, 0.15);
            }
            .dark .sp-badge-emerald {
                background: rgba(52, 211, 153, 0.15);
            }

            /* Empty state */
            .sp-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 64px 24px;
                text-align: center;
            }

            .sp-empty-icon {
                width: 80px;
                height: 80px;
                margin-bottom: 16px;
                color: var(--sp-text-dim);
            }

            .sp-empty h3 {
                font-size: 1.125rem;
                font-weight: 600;
                color: var(--sp-text);
                margin-bottom: 4px;
            }

            .sp-empty p {
                color: var(--sp-text-muted);
                font-size: 0.9375rem;
                max-width: 400px;
            }

            /* Summary item */
            .sp-summary-item {
                padding: 20px 24px;
            }

            @media (max-width: 640px) {
                .sp-summary-item {
                    padding: 16px 18px;
                }
            }

            .sp-summary-header {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-bottom: 12px;
            }

            @media (min-width: 640px) {
                .sp-summary-header {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }
            }

            .sp-summary-meta {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 8px 12px;
            }

            .sp-summary-date {
                font-size: 0.8125rem;
                color: var(--sp-text-muted);
            }

            .sp-summary-generated {
                font-size: 0.75rem;
                color: var(--sp-text-dim);
            }

            .sp-summary-content {
                color: var(--sp-text-secondary);
                font-size: 0.9375rem;
                line-height: 1.6;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
                overflow: hidden;
            }

            .sp-summary-content strong {
                color: var(--sp-text);
            }

            /* Header title */
            .sp-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--sp-text);
                letter-spacing: -0.02em;
            }

            @media (min-width: 640px) {
                .sp-title { font-size: 1.75rem; }
            }

            @media (min-width: 1024px) {
                .sp-title { font-size: 2rem; }
            }

            .sp-subtitle {
                font-size: 0.875rem;
                color: var(--sp-text-muted);
                margin-top: 2px;
            }

            /* Header */
            .sp-header {
                display: flex;
                flex-direction: column;
                gap: 16px;
                margin-bottom: 24px;
            }

            @media (min-width: 768px) {
                .sp-header {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }
            }

            .sp-header-left {
                display: flex;
                flex-direction: column;
            }

            .sp-header-right {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            /* Count badge */
            .sp-count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 24px;
                height: 24px;
                padding: 0 8px;
                border-radius: 9999px;
                background: rgba(79, 70, 229, 0.1);
                color: var(--sp-primary);
                font-size: 0.7rem;
                font-weight: 700;
            }

            .dark .sp-count {
                background: rgba(129, 140, 248, 0.15);
            }

            /* Scrollbar */
            .sp-scroll {
                scrollbar-width: thin;
                scrollbar-color: rgba(0, 0, 0, 0.06) transparent;
            }

            .sp-scroll::-webkit-scrollbar { width: 4px; }
            .sp-scroll::-webkit-scrollbar-track { background: transparent; }
            .sp-scroll::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.08); border-radius: 99px; }

            .dark .sp-scroll {
                scrollbar-color: rgba(255, 255, 255, 0.06) transparent;
            }
            .dark .sp-scroll::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.06);
            }

            /* Responsive grid */
            .sp-grid {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            /* Loading shimmer (optional) */
            .sp-shimmer {
                background: linear-gradient(90deg, 
                    var(--sp-border) 25%, 
                    var(--sp-surface) 50%, 
                    var(--sp-border) 75%
                );
                background-size: 200% 100%;
                animation: sp-shimmer 1.5s infinite;
            }

            @keyframes sp-shimmer {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
        </style>

        <script>
            // Auto dark mode
            (function() {
                const root = document.currentScript.parentElement;
                if (localStorage.getItem('theme') === 'dark' || document.documentElement.classList.contains('dark')) {
                    root.classList.add('dark');
                }
            })();
        </script>

        <div class="sp-container">

            {{-- HEADER --}}
            <header class="sp-header sp-appear">
                <div class="sp-header-left">
                    <h1 class="sp-title">
                        📊 Mes Résumés IA
                        @if(!$summaries->isEmpty())
                            <span class="sp-count ml-2">{{ $summaries->count() }}</span>
                        @endif
                    </h1>
                    <p class="sp-subtitle">Résumés automatiques de vos rapports d'activité</p>
                </div>

                <div class="sp-header-right sp-btn-group">
                    <form action="{{ route('summaries.generate') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="period" value="weekly">
                        <button type="submit" class="sp-btn sp-btn-primary sp-btn-sm">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Cette semaine
                        </button>
                    </form>

                    <form action="{{ route('summaries.generate') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="period" value="monthly">
                        <button type="submit" class="sp-btn sp-btn-purple sp-btn-sm">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Ce mois
                        </button>
                    </form>

                    <form action="{{ route('summaries.generate') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="period" value="yearly">
                        <button type="submit" class="sp-btn sp-btn-emerald sp-btn-sm">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Cette année
                        </button>
                    </form>
                </div>
            </header>

            {{-- CONTENT --}}
            <div class="sp-appear-2">
                @if($summaries->isEmpty())
                    {{-- Empty state --}}
                    <div class="sp-card sp-card-hover">
                        <div class="sp-empty">
                            <svg class="sp-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3>Aucun résumé pour le moment</h3>
                            <p>Génère un résumé IA de tes rapports pour faire le point sur ton activité.</p>
                            <div class="sp-btn-group mt-6">
                                <form action="{{ route('summaries.generate') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="period" value="weekly">
                                    <button type="submit" class="sp-btn sp-btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Générer un résumé
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Summary list --}}
                    <div class="sp-grid">
                        @foreach($summaries as $summary)
                            @php
                                $badgeClass = match($summary->period_type) {
                                    'weekly' => 'sp-badge-indigo',
                                    'monthly' => 'sp-badge-purple',
                                    'yearly' => 'sp-badge-emerald',
                                    default => 'sp-badge-indigo'
                                };
                                $badgeLabel = ucfirst(__($summary->period_type));
                            @endphp

                            <a href="{{ route('summaries.show', $summary) }}" 
                               class="sp-card sp-card-hover sp-summary-item {{ $loop->index % 3 === 0 ? 'sp-appear-2' : ($loop->index % 3 === 1 ? 'sp-appear-3' : 'sp-appear-4') }}">

                                <div class="sp-summary-header">
                                    <div class="sp-summary-meta">
                                        <span class="sp-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        <span class="sp-summary-date">
                                            <span class="hidden xs:inline">📅</span>
                                            {{ $summary->period_start->format('d/m/Y') }}
                                            <span class="sp-summary-date mx-1">→</span>
                                            {{ $summary->period_end->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <span class="sp-summary-generated">
                                        🕐 Généré le {{ $summary->generated_at?->format('d/m/Y à H:i') }}
                                    </span>
                                </div>

                                <div class="sp-summary-content">
                                    {!! Str::limit(strip_tags($summary->summary), 220) !!}
                                </div>

                                {{-- Mobile indicator --}}
                                <div class="mt-3 flex items-center gap-2 text-xs text-indigo-500 font-medium md:hidden">
                                    Voir le résumé complet
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Footer info --}}
                    <div class="mt-6 text-center text-xs text-gray-400 sp-appear-4">
                        <span class="inline-flex items-center gap-2">
                            <span class="sp-pulse inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ $summaries->count() }} résumé{{ $summaries->count() > 1 ? 's' : '' }} disponible{{ $summaries->count() > 1 ? 's' : '' }}
                        </span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>