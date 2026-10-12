<x-app-layout title="Supervision des Résumés IA">

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6e6862;
        }
        .dark .ks-label { color: #a89f93; }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            padding: .4rem .85rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 500;
            padding: .4rem .85rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        .ks-stat-card {
            border: 1px solid #dcdad2;
            border-radius: 10px;
            background-color: #fffefa;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .dark .ks-stat-card {
            background: #131519 !important;
            border: 1px solid #222630 !important;
        }

        .ks-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: none;
        }
        .ks-stat-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

        .ks-stat-card--blue  .ks-stat-icon { background-color: #eef1fb; color: #1c3e96; }
        .ks-stat-card--purple .ks-stat-icon { background-color: #f5f3ff; color: #7c3aed; }
        .ks-stat-card--green .ks-stat-icon { background-color: #ecfdf5; color: #15803d; }
        .ks-stat-card--amber .ks-stat-icon { background-color: #fffbeb; color: #b45309; }

        .dark .ks-stat-card--blue  .ks-stat-icon { background-color: rgba(28, 62, 150, 0.25) !important; color: #93c5fd !important; }
        .dark .ks-stat-card--purple .ks-stat-icon { background-color: rgba(124, 58, 237, 0.25) !important; color: #c4b5fd !important; }
        .dark .ks-stat-card--green .ks-stat-icon { background-color: rgba(22, 101, 52, 0.25) !important; color: #86efac !important; }
        .dark .ks-stat-card--amber .ks-stat-icon { background-color: rgba(180, 83, 9, 0.25) !important; color: #fcd34d !important; }

        .ks-input {
            width: 100%;
            height: 32px;
            padding: 0 .6rem;
            font-size: .75rem;
            background: #faf9f6;
            border: 1px solid #dcdad2;
            border-radius: 6px;
            color: #241f1b;
            transition: all .15s ease;
        }
        .dark .ks-input {
            background: #181c24 !important;
            border-color: #2b3240 !important;
            color: #f1f5f9 !important;
        }
        .ks-input:focus {
            outline: none;
            border-color: #1c3e96;
            box-shadow: 0 0 0 1px #1c3e96;
        }
    </style>

    <div class="w-full space-y-3 pb-6">

        {{-- ── EN-TÊTE COMPACT ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white flex items-center gap-2">
                    Supervision des Résumés IA
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[#eef1fb] dark:bg-blue-950/50 text-[#1c3e96] dark:text-blue-300 border border-[#d8e0f5] dark:border-blue-900/50">
                        {{ $summaries->total() }}
                    </span>
                </h1>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Historique et contrôle des synthèses générées pour les stagiaires et employés.
                </p>
            </div>
        </div>

        {{-- ── FILTRES COMPACTS ── --}}
        <div class="ks-sec p-3">
            <form method="GET" action="{{ route('admin.summaries.index') }}" class="flex flex-wrap items-end gap-2.5">
                {{-- Filtre Période --}}
                <div class="w-full sm:w-44">
                    <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                        Période
                    </label>
                    <select name="period" class="ks-input">
                        <option value="">Toutes les périodes</option>
                        <option value="weekly" @selected(request('period') === 'weekly')>Hebdomadaire</option>
                        <option value="monthly" @selected(request('period') === 'monthly')>Mensuel</option>
                        <option value="yearly" @selected(request('period') === 'yearly')>Annuel</option>
                    </select>
                </div>

                {{-- Filtre Utilisateur --}}
                <div class="w-full sm:w-56">
                    <label class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                        Utilisateur
                    </label>
                    <select name="user_id" class="ks-input">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Boutons d'action filtres --}}
                <div class="flex items-center gap-1.5 pt-1">
                    <button type="submit" class="ks-btn-primary ks-btn-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrer
                    </button>

                    @if(request('period') || request('user_id'))
                        <a href="{{ route('admin.summaries.index') }}" class="ks-btn-secondary ks-btn-xs cursor-pointer" title="Effacer les filtres">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── TABLEAU COMPACT ── --}}
        <div class="ks-sec overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#6e6862] dark:text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="px-3.5 py-2.5">Utilisateur</th>
                            <th class="px-3 py-2.5">Période</th>
                            <th class="px-3 py-2.5">Intervalle</th>
                            <th class="px-3 py-2.5">Données</th>
                            <th class="px-3 py-2.5">Modèle IA</th>
                            <th class="px-3 py-2.5">Généré le</th>
                            <th class="px-3.5 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        @forelse($summaries as $summary)
                            @php
                                $badgeBg = match($summary->period_type) {
                                    'weekly' => 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border-[#d8e0f5] dark:border-blue-900/50',
                                    'monthly' => 'bg-[#f5f3ff] dark:bg-purple-950/40 text-[#6d28d9] dark:text-purple-300 border-[#e9d5ff] dark:border-purple-900/50',
                                    'yearly' => 'bg-[#f0fdf4] dark:bg-emerald-950/40 text-[#15803d] dark:text-emerald-300 border-[#bbf7d0] dark:border-emerald-900/50',
                                    default => 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border-[#d8e0f5] dark:border-blue-900/50',
                                };
                                $reportCount = is_array($summary->raw_data) ? count($summary->raw_data) : 0;
                            @endphp
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                {{-- Utilisateur --}}
                                <td class="px-3.5 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($summary->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-[#241f1b] dark:text-white leading-tight">
                                                {{ $summary->user->name ?? 'Utilisateur supprimé' }}
                                            </div>
                                            <div class="text-[10px] text-[#6e6862] dark:text-slate-400">
                                                {{ $summary->user->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Période --}}
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider {{ $badgeBg }}">
                                        {{ ucfirst(__($summary->period_type)) }}
                                    </span>
                                </td>

                                {{-- Dates --}}
                                <td class="px-3 py-2.5 font-mono text-[#241f1b] dark:text-slate-300 text-[11px] whitespace-nowrap">
                                    {{ $summary->period_start->format('d/m/Y') }} → {{ $summary->period_end->format('d/m/Y') }}
                                </td>

                                {{-- Données sources --}}
                                <td class="px-3 py-2.5 text-[11px] text-[#6e6862] dark:text-slate-400 whitespace-nowrap">
                                    @if($reportCount > 0)
                                        <span class="font-semibold text-emerald-700 dark:text-emerald-400">{{ $reportCount }} rapport(s)</span>
                                    @else
                                        <span class="italic text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Modèle IA --}}
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-mono bg-[#f1f0ed] dark:bg-[#1f242e] text-[#6e6862] dark:text-slate-400 border border-[#dcdad2] dark:border-[#2b3240]">
                                        {{ $summary->model_used ?? 'gpt-4o-mini' }}
                                    </span>
                                </td>

                                {{-- Date de génération --}}
                                <td class="px-3 py-2.5 font-mono text-[#6e6862] dark:text-slate-400 text-[11px] whitespace-nowrap">
                                    {{ $summary->generated_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-3.5 py-2.5 text-right whitespace-nowrap">
                                    <a href="{{ route('summaries.show', $summary) }}"
                                       class="ks-btn-secondary ks-btn-xs !h-7 !px-2.5 cursor-pointer" title="Voir le détail">
                                        <span>Consulter</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-xs text-[#6e6862] dark:text-slate-400">
                                    Aucun résumé IA ne correspond aux critères sélectionnés.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($summaries->hasPages())
                <div class="p-3 border-t border-[#ecebe3] dark:border-[#222630] flex justify-center bg-[#faf9f6]/40 dark:bg-[#181c24]/40">
                    {{ $summaries->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
