<x-app-layout title="Créer une tâche">

    @php
        $currentPriority = old('priority', 'normal');
        $priorityOptions = [
            'low'    => ['label' => 'Basse',   'hint' => 'Suivi léger',       'dot' => 'bg-slate-400'],
            'normal' => ['label' => 'Normale',  'hint' => 'Priorité standard', 'dot' => 'bg-blue-500'],
            'high'   => ['label' => 'Haute',    'hint' => 'À traiter vite',    'dot' => 'bg-amber-500'],
            'urgent' => ['label' => 'Urgente',  'hint' => 'Immédiate',         'dot' => 'bg-rose-500'],
        ];
    @endphp

    <style>
        /* Fond warm beige façon Mon-Stage */
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 600;
            padding: .45rem .9rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 500;
            padding: .45rem .9rem;
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

        .ks-sec-head {
            padding: .65rem .85rem;
            border-bottom: 1px solid #dcdad2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .dark .ks-sec-head { border-bottom-color: #222630 !important; }

        .edit-choice input:checked + span {
            border-color: #1c3e96;
            background: #eef1fb;
            color: #1c3e96;
            font-weight: 600;
            box-shadow: 0 1px 4px rgba(28,62,150,.1);
        }
        .dark .edit-choice input:checked + span {
            border-color: #3b82f6;
            background: #17233d;
            color: #93c5fd;
        }

        /* Cartes de sélection de priorité avec couleur propre à chaque priorité */
        .prio-choice span.prio-card {
            border-radius: 8px;
            border-width: 1px;
            border-style: solid;
            transition: all .15s ease;
        }

        /* 1. Basse (gris ardoise) */
        .prio-choice--low span.prio-card {
            border-color: #cbd5e1;
            background-color: rgba(241, 245, 249, 0.75);
            color: #475569;
        }
        .dark .prio-choice--low span.prio-card {
            border-color: #334155;
            background-color: rgba(30, 41, 59, 0.35);
            color: #94a3b8;
        }
        .prio-choice--low:hover span.prio-card {
            border-color: #94a3b8;
        }
        .dark .prio-choice--low:hover span.prio-card {
            border-color: #64748b;
        }
        .prio-choice--low input:checked + span.prio-card {
            border-color: #64748b !important;
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.25) !important;
        }
        .dark .prio-choice--low input:checked + span.prio-card {
            border-color: #94a3b8 !important;
            background-color: rgba(51, 65, 85, 0.75) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.3) !important;
        }

        /* 2. Normale (bleu cobalt / Mon-Stage) */
        .prio-choice--normal span.prio-card {
            border-color: #bfdbfe;
            background-color: rgba(239, 246, 255, 0.75);
            color: #1e40af;
        }
        .dark .prio-choice--normal span.prio-card {
            border-color: #1e3a8a;
            background-color: rgba(30, 58, 138, 0.3);
            color: #93c5fd;
        }
        .prio-choice--normal:hover span.prio-card {
            border-color: #60a5fa;
        }
        .dark .prio-choice--normal:hover span.prio-card {
            border-color: #3b82f6;
        }
        .prio-choice--normal input:checked + span.prio-card {
            border-color: #1c3e96 !important;
            background-color: #dbeafe !important;
            color: #1c3e96 !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(28, 62, 150, 0.25) !important;
        }
        .dark .prio-choice--normal input:checked + span.prio-card {
            border-color: #3b82f6 !important;
            background-color: rgba(28, 62, 150, 0.55) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.35) !important;
        }

        /* 3. Haute (ambre chaud) */
        .prio-choice--high span.prio-card {
            border-color: #fde68a;
            background-color: rgba(254, 243, 199, 0.75);
            color: #92400e;
        }
        .dark .prio-choice--high span.prio-card {
            border-color: #78350f;
            background-color: rgba(120, 53, 15, 0.3);
            color: #fcd34d;
        }
        .prio-choice--high:hover span.prio-card {
            border-color: #f59e0b;
        }
        .dark .prio-choice--high:hover span.prio-card {
            border-color: #b45309;
        }
        .prio-choice--high input:checked + span.prio-card {
            border-color: #d97706 !important;
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.25) !important;
        }
        .dark .prio-choice--high input:checked + span.prio-card {
            border-color: #f59e0b !important;
            background-color: rgba(180, 83, 9, 0.55) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.35) !important;
        }

        /* 4. Urgente (rouge / rose vif) */
        .prio-choice--urgent span.prio-card {
            border-color: #fecdd3;
            background-color: rgba(255, 228, 230, 0.75);
            color: #9f1239;
        }
        .dark .prio-choice--urgent span.prio-card {
            border-color: #881337;
            background-color: rgba(136, 19, 55, 0.3);
            color: #fda4af;
        }
        .prio-choice--urgent:hover span.prio-card {
            border-color: #fb7185;
        }
        .dark .prio-choice--urgent:hover span.prio-card {
            border-color: #e11d48;
        }
        .prio-choice--urgent input:checked + span.prio-card {
            border-color: #e11d48 !important;
            background-color: #ffe4e6 !important;
            color: #be123c !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.25) !important;
        }
        .dark .prio-choice--urgent input:checked + span.prio-card {
            border-color: #f43f5e !important;
            background-color: rgba(225, 29, 72, 0.5) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.35) !important;
        }

        @keyframes editRise {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .edit-rise > * { animation: editRise .35s cubic-bezier(.2,.8,.2,1) both; }
        .edit-rise > *:nth-child(2) { animation-delay: .05s; }
    </style>

    <div class="w-full space-y-3 pb-6"
         x-data="subtaskForm()">

        <div class="edit-rise grid gap-4 lg:grid-cols-[280px_minmax(0,1fr)] items-start">

            {{-- ── COLONNE GAUCHE (SIDEBAR) ── --}}
            <aside class="space-y-3">
                <a href="{{ route('tasks.index') }}"
                   class="ks-btn-secondary ks-btn-xs !text-[12px] !px-3 !py-1.5 w-full justify-start">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19 8 12l7-7"/></svg>
                    Retour aux tâches
                </a>

                <section class="ks-sec overflow-hidden">
                    <div class="h-1 bg-[#1c3e96]"></div>
                    <div class="p-4 space-y-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#1c3e96] dark:text-blue-400">Nouvelle tâche</span>
                            <h1 class="text-lg font-bold text-[#241f1b] dark:text-white mt-0.5">Création</h1>
                            <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-1 leading-relaxed">
                                Définis les détails et au moins une sous-tâche pour suivre les étapes de travail.
                            </p>
                        </div>

                        {{-- Barre de progression --}}
                        <div class="rounded-lg border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-3">
                            <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">
                                <span>Progression</span>
                                <span class="font-mono font-bold text-[#241f1b] dark:text-white">0%</span>
                            </div>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#ecebe3] dark:bg-[#222630]">
                                <div class="h-full w-0 rounded-full bg-[#1c3e96] transition-all duration-500"></div>
                            </div>
                            <p class="mt-1.5 text-[10px] text-[#6e6862] dark:text-slate-400">Calculée automatiquement depuis les sous-tâches</p>
                        </div>

                        <dl class="space-y-2 text-xs pt-1 border-t border-[#ecebe3] dark:border-[#222630]">
                            <div class="flex items-center justify-between gap-2">
                                <dt class="text-[#6e6862] dark:text-slate-400">Créateur</dt>
                                <dd class="truncate font-semibold text-[#241f1b] dark:text-slate-200">{{ auth()->user()->name }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <dt class="text-[#6e6862] dark:text-slate-400">Sous-tâches</dt>
                                <dd class="font-mono font-bold text-[#241f1b] dark:text-slate-200" x-text="subtasks.length"></dd>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <dt class="text-[#6e6862] dark:text-slate-400">Date</dt>
                                <dd class="font-mono font-semibold text-[#241f1b] dark:text-slate-200">{{ now()->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>
            </aside>

            {{-- ── FORMULAIRE PRINCIPAL ── --}}
            <main class="min-w-0 space-y-3.5">

                {{-- Erreurs globales --}}
                @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 dark:border-red-900/40 dark:bg-red-950/20 px-3.5 py-2.5 text-xs text-red-700 dark:text-red-300">
                    <ul class="space-y-0.5">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data" class="space-y-3.5">
                    @csrf

                    {{-- §1 Informations principales --}}
                    <section class="ks-sec overflow-hidden">
                        <div class="ks-sec-head">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-white">Informations principales</span>
                            <span class="text-[11px] text-[#6e6862] dark:text-slate-400">* Champs obligatoires</span>
                        </div>

                        <div class="p-4 space-y-4">

                            {{-- Titre --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Titre <span class="text-red-500">*</span></label>
                                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex : Intégration du portail d'évaluation…"
                                    class="h-9 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-3 text-xs font-medium text-[#241f1b] dark:text-white placeholder-[#6e6862] dark:placeholder-slate-500 transition focus:border-[#1c3e96] focus:outline-none">
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Description <span class="text-red-500">*</span></label>
                                <textarea name="description" rows="4" required placeholder="Décrivez les objectifs, contraintes et périmètre de la tâche…"
                                    class="w-full resize-none rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-3 py-2 text-xs leading-5 text-[#241f1b] dark:text-white placeholder-[#6e6862] dark:placeholder-slate-500 transition focus:border-[#1c3e96] focus:outline-none">{{ old('description') }}</textarea>
                            </div>

                            {{-- Priorité --}}
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-4">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Priorité</label>
                                    <span class="text-[11px] text-[#6e6862] dark:text-slate-400">Affecte le tri et les alertes</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach($priorityOptions as $value => $option)
                                    <label class="prio-choice prio-choice--{{ $value }} cursor-pointer block">
                                        <input type="radio" name="priority" value="{{ $value }}" class="sr-only" {{ $currentPriority === $value ? 'checked' : '' }}>
                                        <span class="prio-card flex h-full min-h-[66px] flex-col justify-between p-2.5">
                                            <span class="flex items-center justify-between">
                                                <span class="text-xs font-bold">{{ $option['label'] }}</span>
                                                <span class="h-2 w-2 rounded-full {{ $option['dot'] }}"></span>
                                            </span>
                                            <span class="text-[10.5px] opacity-80">{{ $option['hint'] }}</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Période --}}
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-4">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Période d'exécution</label>
                                    <span class="text-[11px] text-[#6e6862] dark:text-slate-400">Cadre temporel de la tâche</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-[#6e6862] dark:text-slate-400">Date de début</label>
                                        <input type="date" name="start_date" id="task_start_date" value="{{ old('start_date') }}"
                                            x-model="taskStartDate"
                                            @change="validateSubtaskDates()"
                                            class="h-9 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-3 text-xs text-[#241f1b] dark:text-white transition focus:border-[#1c3e96] focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-[#6e6862] dark:text-slate-400">Date de fin <span class="opacity-70">(échéance)</span></label>
                                        <input type="date" name="due_date" id="task_due_date" value="{{ old('due_date') }}"
                                            x-model="taskEndDate"
                                            @change="validateSubtaskDates()"
                                            class="h-9 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-3 text-xs text-[#241f1b] dark:text-white transition focus:border-[#1c3e96] focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- Cahier des charges PDF --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Cahier des charges <span class="opacity-70">(PDF optionnel)</span></label>
                                <label class="flex cursor-pointer items-center gap-2.5 rounded-md border border-dashed border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] px-3 py-2 transition hover:border-[#1c3e96]">
                                    <svg class="h-4 w-4 shrink-0 text-[#6e6862] dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span class="text-xs text-[#6e6862] dark:text-slate-400 truncate" x-text="pdfName || 'Choisir un document PDF…'"></span>
                                    <input type="file" name="pdf" accept=".pdf" class="sr-only" @change="pdfName = $event.target.files[0]?.name || ''">
                                </label>
                            </div>

                        </div>
                    </section>

                    {{-- §2 Sous-tâches --}}
                    <section class="ks-sec overflow-hidden">
                        <div class="ks-sec-head">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-white">Sous-tâches</span>
                                <span class="text-[11px] text-[#6e6862] dark:text-slate-400 ml-2">Étapes de travail assignables</span>
                            </div>
                            <button type="button" @click="addSubtask()"
                                class="ks-btn-primary ks-btn-xs !text-[12px] !px-2.5 !py-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                                Ajouter
                            </button>
                        </div>

                        <div class="p-4 space-y-3">
                            {{-- Message si aucune sous-tâche --}}
                            <div x-show="subtasks.length === 0" x-cloak
                                class="rounded-lg border border-dashed border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-5 text-center">
                                <p class="text-xs font-semibold text-[#241f1b] dark:text-white">Aucune sous-tâche ajoutée</p>
                                <p class="mt-0.5 text-[11px] text-[#6e6862] dark:text-slate-400">Facultatif : vous pouvez créer la tâche puis découper les étapes et items plus tard.</p>
                            </div>

                            {{-- Liste des sous-tâches --}}
                            <template x-for="(st, index) in subtasks" :key="st.id">
                                <div class="rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-3 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#1c3e96] dark:text-blue-400" x-text="'Sous-tâche ' + (index + 1)"></span>
                                        <button type="button" @click="removeSubtask(index)"
                                            class="rounded p-1 text-[#6e6862] hover:text-red-600 transition" title="Supprimer cette sous-tâche">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-[#6e6862] dark:text-slate-400">Titre <span class="text-red-500">*</span></label>
                                        <input type="text"
                                            :name="'subtasks[' + index + '][title]'"
                                            x-model="st.title"
                                            required
                                            placeholder="Ex : Modélisation de la base de données…"
                                            class="h-8 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-2.5 text-xs text-[#241f1b] dark:text-white transition focus:border-[#1c3e96] focus:outline-none">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="mb-1 block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400">Date début</label>
                                            <input type="date"
                                                :name="'subtasks[' + index + '][start_date]'"
                                                x-model="st.start_date"
                                                :min="taskStartDate || ''"
                                                :max="taskEndDate || ''"
                                                @change="validateSubtaskDate(index)"
                                                class="h-8 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-2 text-xs text-[#241f1b] dark:text-white transition focus:border-[#1c3e96] focus:outline-none"
                                                :class="st.dateError ? '!border-red-400' : ''">
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-[10px] font-semibold text-[#6e6862] dark:text-slate-400">Date fin</label>
                                            <input type="date"
                                                :name="'subtasks[' + index + '][end_date]'"
                                                x-model="st.end_date"
                                                :min="st.start_date || taskStartDate || ''"
                                                :max="taskEndDate || ''"
                                                @change="validateSubtaskDate(index)"
                                                class="h-8 w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] px-2 text-xs text-[#241f1b] dark:text-white transition focus:border-[#1c3e96] focus:outline-none"
                                                :class="st.dateError ? '!border-red-400' : ''">
                                        </div>
                                    </div>

                                    <p x-show="st.dateError" x-text="st.dateError" class="text-[11px] text-red-500"></p>
                                </div>
                            </template>

                            {{-- Note sur le calcul automatique --}}
                            <div x-show="subtasks.length > 0" x-cloak
                                class="rounded-md bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-300">
                                <strong x-text="subtasks.length + ' sous-tâche(s)'"></strong> — Chaque sous-tâche représentera environ <span class="font-bold" x-text="subtasks.length > 0 ? Math.round(100/subtasks.length) + '%' : '0%'"></span> de la progression totale.
                            </div>
                        </div>
                    </section>

                    {{-- Actions en bas --}}
                    <div class="flex items-center justify-end gap-2 pt-1">
                        <a href="{{ route('tasks.index') }}" class="ks-btn-secondary ks-btn-xs !text-[12px] !px-3.5 !py-1.5">
                            Annuler
                        </a>
                        <button type="submit"
                            :disabled="hasDateErrors"
                            :class="hasDateErrors ? 'opacity-40 cursor-not-allowed' : ''"
                            class="ks-btn-primary ks-btn-xs !text-[13px] !px-4 !py-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                            Créer la tâche
                        </button>
                    </div>
                </form>
            </main>

        </div>
    </div>

    <script>
    function subtaskForm() {
        return {
            subtasks: @json(collect(old('subtasks', []))->values()),
            taskStartDate: '{{ old('start_date', '') }}',
            taskEndDate: '{{ old('due_date', '') }}',
            pdfName: '',
            counter: 0,

            get hasDateErrors() {
                return this.subtasks.some(st => st.dateError);
            },

            addSubtask() {
                this.subtasks.push({
                    id: ++this.counter,
                    title: '',
                    start_date: '',
                    end_date: '',
                    dateError: '',
                });
            },

            removeSubtask(index) {
                this.subtasks.splice(index, 1);
            },

            validateSubtaskDate(index) {
                const st = this.subtasks[index];
                st.dateError = '';

                if (this.taskStartDate && st.start_date && st.start_date < this.taskStartDate) {
                    st.dateError = 'Antérieure au début de la tâche principale.';
                    return;
                }
                if (this.taskEndDate && st.start_date && st.start_date > this.taskEndDate) {
                    st.dateError = 'Postérieure à la fin de la tâche principale.';
                    return;
                }
                if (st.start_date && st.end_date && st.end_date < st.start_date) {
                    st.dateError = 'La date de fin est antérieure à la date de début.';
                    return;
                }
                if (this.taskEndDate && st.end_date && st.end_date > this.taskEndDate) {
                    st.dateError = 'Dépasse la date de fin de la tâche principale.';
                    return;
                }
            },

            validateSubtaskDates() {
                this.subtasks.forEach((_, i) => this.validateSubtaskDate(i));
            },
        };
    }
    </script>

</x-app-layout>
