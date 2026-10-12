<x-app-layout title="Suivi des tâches">
    @php
        $user = auth()->user();
        $canCreate = $user->can('tasks.create');
        $canAssign = $user->can('tasks.assign');
        $canManage = $user->hasRole('admin');
    @endphp

    <div class="w-full space-y-3 sm:space-y-4 pb-6 text-slate-900 dark:text-slate-100">

        {{-- ── 1. En-tête compact Mon Stage ────────────── --}}
        <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-[#1c3e96]/15 dark:bg-[#1c3e96]/30 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-sm sm:text-base font-semibold text-[#111827] dark:text-[#f8fafc] truncate">Suivi des tâches</h1>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd]">
                                Production
                            </span>
                        </div>
                        <p class="text-xs text-[#64748b] dark:text-[#94a3b8] truncate">
                            Avancement des livrables et des activités des stagiaires & employés
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                    @if($canCreate)
                    <a href="{{ route('tasks.create') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#1c3e96] hover:bg-[#162f73] text-white shadow-sm transition">
                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                        </svg>
                        <span>Nouvelle tâche</span>
                    </a>
                    @endif

                    @if($canAssign)
                    <a href="{{ route('tasks.assign.form') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#181c24] text-[#111827] dark:text-[#f8fafc] hover:bg-[#f1efe9]/50 dark:hover:bg-[#222630] transition">
                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Assigner</span>
                    </a>
                    @endif

                    <a href="{{ route('tasks.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#181c24] text-[#64748b] dark:text-[#94a3b8] hover:text-[#111827] dark:hover:text-[#f8fafc] hover:bg-[#f1efe9]/50 dark:hover:bg-[#222630] transition">
                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>Espace travail</span>
                    </a>

                    @if($canManage)
                    <a href="{{ route('tasks.trash') }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#181c24] text-[#64748b] dark:text-[#94a3b8] hover:text-red-600 dark:hover:text-red-400 hover:bg-[#f1efe9]/50 dark:hover:bg-[#222630] transition"
                       title="Corbeille des tâches">
                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── 2. 5 Cartes KPIs Mon Stage ────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 sm:gap-3">
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 transition-colors">
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Total Tâches</p>
                <p class="text-lg font-bold text-[#111827] dark:text-[#f8fafc] mt-0.5">{{ $stats['total'] }}</p>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 transition-colors">
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">En cours</p>
                <p class="text-lg font-bold text-[#1c3e96] dark:text-[#60a5fa] mt-0.5">{{ $stats['in_progress'] }}</p>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 transition-colors">
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Terminées</p>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $stats['completed'] }}</p>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 transition-colors">
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">En retard</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-0.5">{{ $stats['overdue'] }}</p>
            </div>

            <div class="col-span-2 sm:col-span-1 rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 transition-colors">
                <p class="text-[11px] font-medium text-[#64748b] dark:text-[#94a3b8]">Avancement moy.</p>
                <p class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $stats['avg'] }}%</p>
            </div>
        </div>

        {{-- ── 3. Barre de filtres Mon Stage ─────────────── --}}
        <form method="GET" action="{{ route('admin.tasks.tracking') }}"
              class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-2.5 sm:p-3 flex flex-col md:flex-row md:items-center justify-between gap-2.5 transition-colors shadow-sm">
            
            {{-- Onglets segmentés Période --}}
            <div class="flex items-center gap-1 p-0.5 rounded-lg bg-[#f8f7f4] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] w-fit shrink-0">
                @foreach(['daily' => 'Jour', 'weekly' => 'Semaine', 'monthly' => 'Mois'] as $key => $label)
                <a href="{{ route('admin.tasks.tracking', array_filter(['period' => $key, 'status' => $status, 'date' => $date->format('Y-m-d')])) }}"
                   class="px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ $period === $key ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#64748b] dark:text-[#94a3b8] hover:text-[#111827] dark:hover:text-[#f8fafc]' }}">
                   {{ $label }}
                </a>
                @endforeach
            </div>

            <input type="hidden" name="period" value="{{ $period }}">

            {{-- Contrôles de filtre --}}
            <div class="flex flex-1 flex-wrap items-center gap-2 md:justify-end">
                <select name="status"
                        class="px-2.5 py-1.5 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#181c24] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                    <option value="">Tous les statuts</option>
                    @foreach(['pending' => 'À faire', 'in_progress' => 'En cours', 'blocked' => 'Bloquée', 'changes_requested' => 'Corrections', 'completed' => 'Terminée'] as $k => $v)
                    <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>

                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                       class="px-2.5 py-1.5 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#181c24] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">

                <div class="relative min-w-[140px] flex-1 sm:flex-initial">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher…"
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#181c24] text-[#111827] dark:text-[#f8fafc] placeholder-[#94a3b8] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                </div>

                <button type="submit"
                        class="px-3 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold shadow-sm transition">
                    Filtrer
                </button>
            </div>
        </form>

        {{-- ── 4. Tableau & Cartes Suivi des Tâches ────── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden transition-colors shadow-sm">
            
            {{-- ── Version Desktop ──────── --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#f8f7f4] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] text-[#64748b] dark:text-[#94a3b8] font-semibold uppercase tracking-wider text-[11px]">
                            <th class="px-4 py-3">Producteur</th>
                            <th class="px-4 py-3">Tâche</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3 w-40">Progression</th>
                            <th class="px-4 py-3 text-center">Rapports</th>
                            <th class="px-4 py-3 text-center">Messages</th>
                            <th class="px-4 py-3">Échéance</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]/60 text-[#111827] dark:text-[#f8fafc]">
                        @forelse($tasks as $task)
                        <tr class="transition-colors hover:bg-[#f1efe9]/40 dark:hover:bg-[#181c24]/40 {{ $task->isOverdue() ? 'bg-red-500/[0.03] dark:bg-red-500/[0.05]' : '' }}">
                            <td class="px-4 py-3 font-medium whitespace-nowrap">
                                {{ $task->owner?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <x-priority-dot :priority="$task->priority" />
                                    <span class="font-medium text-[#111827] dark:text-[#f8fafc] truncate max-w-xs" title="{{ $task->title }}">
                                        {{ \Illuminate\Support\Str::limit($task->title, 45) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-task-status-badge :status="$task->status" />
                            </td>
                            <td class="px-4 py-3 w-40 whitespace-nowrap">
                                <x-progress-bar :percent="$task->last_progress_percent" />
                            </td>
                            <td class="px-4 py-3 text-center font-medium whitespace-nowrap text-[#64748b] dark:text-[#94a3b8]">
                                {{ $task->daily_reports_count }}
                            </td>
                            <td class="px-4 py-3 text-center font-medium whitespace-nowrap text-[#64748b] dark:text-[#94a3b8]">
                                {{ $task->messages_count }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap {{ $task->isOverdue() ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-[#64748b] dark:text-[#94a3b8]' }}">
                                {{ $task->due_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ encrypted_route('tasks.show', $task) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd] hover:bg-[#1c3e96]/20 transition-colors">
                                        Ouvrir
                                    </a>

                                    @if($canManage)
                                    <a href="{{ encrypted_route('tasks.edit', $task) }}"
                                       title="Modifier"
                                       class="p-1 rounded-md text-amber-600 dark:text-amber-400 bg-amber-500/10 dark:bg-amber-500/20 hover:bg-amber-500/20 transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 2.65 2.65M4 20l.72-3.95c.07-.39.26-.74.54-1.02L16.3 3.94a1.5 1.5 0 0 1 2.12 0l1.64 1.64a1.5 1.5 0 0 1 0 2.12L9.03 18.74c-.28.28-.63.47-1.02.54L4 20Z"/>
                                        </svg>
                                    </a>

                                    <div class="relative" x-data="{confirmDelete:false}">
                                        <button type="button" title="Supprimer" @click="confirmDelete = true"
                                                class="p-1 rounded-md text-red-600 dark:text-red-400 bg-red-500/10 dark:bg-red-500/20 hover:bg-red-500/20 transition-colors">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                            </svg>
                                        </button>

                                        {{-- Modal de confirmation suppression --}}
                                        <div x-show="confirmDelete" x-cloak x-transition.opacity
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="confirmDelete = false"></div>
                                            <div class="relative w-full max-w-sm rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-5 shadow-2xl transition-colors"
                                                 x-show="confirmDelete" x-transition>
                                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-500/10 dark:bg-red-500/20 text-red-600 dark:text-red-400">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.94-2.8L18 19a2 2 0 0 1-2 1.8H8A2 2 0 0 1 6 19L4.8 6.2M9 6.2V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2.2M3.5 6.2h17"/>
                                                    </svg>
                                                </div>
                                                <h3 class="mt-3 text-sm font-semibold text-[#111827] dark:text-[#f8fafc]">Supprimer cette tâche ?</h3>
                                                <p class="mt-1 text-xs text-[#64748b] dark:text-[#94a3b8] leading-relaxed">{{ \Illuminate\Support\Str::limit($task->title, 60) }}</p>
                                                <p class="mt-1.5 text-[11px] text-[#94a3b8]">La tâche sera déplacée dans la corbeille.</p>
                                                
                                                <div class="mt-4 flex items-center justify-end gap-2">
                                                    <button type="button" @click="confirmDelete = false"
                                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-[#dcdad2] dark:border-[#222630] text-[#64748b] dark:text-[#94a3b8] hover:bg-[#dcdad2]/30 dark:hover:bg-[#222630] transition">
                                                        Annuler
                                                    </button>
                                                    <form method="POST" action="{{ encrypted_route('tasks.destroy', $task) }}">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-xs font-semibold text-white transition">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-10 h-10 rounded-xl bg-[#f1efe9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] flex items-center justify-center text-[#64748b] dark:text-[#94a3b8] mb-2.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-[#111827] dark:text-[#f8fafc]">Aucune tâche sur cette période</p>
                                    <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] mt-0.5">Changez les dates ou ajoutez une nouvelle tâche.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Version Mobile : Cartes sans débordement ── --}}
            <div class="md:hidden divide-y divide-[#dcdad2]/60 dark:divide-[#222630]/60">
                @forelse($tasks as $task)
                <div class="p-3 space-y-2 transition-colors {{ $task->isOverdue() ? 'bg-red-500/[0.03] dark:bg-red-500/[0.05]' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-1">
                                <x-priority-dot :priority="$task->priority" />
                                <span class="text-xs font-semibold text-[#111827] dark:text-[#f8fafc] truncate">{{ $task->title }}</span>
                            </div>
                            <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8]">
                                Responsable : <span class="font-medium text-[#111827] dark:text-[#f8fafc]">{{ $task->owner?->name ?? '—' }}</span>
                            </p>
                        </div>
                        <div class="shrink-0">
                            <x-task-status-badge :status="$task->status" />
                        </div>
                    </div>

                    {{-- Progression --}}
                    <div class="pt-1">
                        <x-progress-bar :percent="$task->last_progress_percent" />
                    </div>

                    {{-- Meta & Actions --}}
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]/60 text-[11px]">
                        <div class="flex items-center gap-2 text-[#64748b] dark:text-[#94a3b8]">
                            <span>Rapports: {{ $task->daily_reports_count }}</span>
                            <span>·</span>
                            <span class="{{ $task->isOverdue() ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                {{ $task->due_date?->format('d/m/Y') ?? 'Pas d\'échéance' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ encrypted_route('tasks.show', $task) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd]">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-xs text-[#64748b] dark:text-[#94a3b8]">
                    Aucune tâche sur cette période.
                </div>
                @endforelse
            </div>

            {{-- ── Pagination ───────────────────────── --}}
            @if($tasks->hasPages())
            <div class="px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#f8f7f4] dark:bg-[#181c24]">
                {{ $tasks->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
