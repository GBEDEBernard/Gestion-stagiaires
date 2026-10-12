<x-app-layout title="Rapports de travail">

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

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

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            padding: .35rem .75rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 500;
            padding: .35rem .75rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary {
            background: #15181f;
            color: #fff;
            border-color: #222630;
        }
        .dark .ks-btn-secondary:hover {
            border-color: #3b82f6;
            color: #93c5fd;
        }
        .ks-btn-secondary:hover {
            border-color: #1c3e96;
            color: #1c3e96;
        }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }

        .ks-input {
            width: 100%;
            border-radius: 6px;
            border: 1px solid #dcdad2;
            background-color: #ffffff;
            color: #241f1b;
            font-size: .8rem;
            padding: .35rem .65rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .ks-input:focus {
            outline: none;
            border-color: #1c3e96;
            box-shadow: 0 0 0 1px #1c3e96;
        }
        .dark .ks-input {
            border-color: #222630;
            background-color: #15181f;
            color: #ffffff;
        }
        .dark .ks-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .ks-label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #6e6862;
            margin-bottom: .25rem;
        }
        .dark .ks-label { color: #a89f93; }
    </style>

    <div class="w-full space-y-3 pb-6"
         x-data="{
             open: @js(isset($editReport) || request()->boolean('nouveau') || (old('summary') && !session('success'))),
             isEdit: @js(isset($editReport)),
             editId: @js(isset($editReport) ? $editReport->id : null),
             formAction: '{{ isset($editReport) ? route('reports.update', $editReport->id) : route('reports.store') }}',
             introduction: '{{ addslashes(old('introduction', $editReport->introduction ?? '')) }}',
             summary: '{{ addslashes(old('summary', $editReport->summary ?? '')) }}',
             blockers: '{{ addslashes(old('blockers', $editReport->blockers ?? '')) }}',
             nextSteps: '{{ addslashes(old('next_steps', $editReport->next_steps ?? '')) }}',
             hoursDeclared: '{{ old('hours_declared', $editReport->hours_declared ?? '') }}',
             reportDate: '{{ old('report_date', isset($editReport) ? $editReport->report_date->toDateString() : today()->toDateString()) }}',
             taskId: '{{ old('task_id', $editReport->task_id ?? '') }}',
             openCreate() {
                 this.isEdit = false;
                 this.editId = null;
                 this.formAction = '{{ route('reports.store') }}';
                 this.introduction = '';
                 this.summary = '';
                 this.blockers = '';
                 this.nextSteps = '';
                 this.hoursDeclared = '';
                 this.reportDate = '{{ today()->toDateString() }}';
                 this.taskId = '';
                 this.open = true;
             },
             openEdit(reportId) {
                 fetch(`/reports/${reportId}`)
                     .then(r => r.json())
                     .then(data => {
                         const rep = data.report;
                         this.isEdit = true;
                         this.editId = rep.id;
                         this.formAction = `/reports/${rep.id}`;
                         this.introduction = rep.introduction || '';
                         this.summary = rep.summary || '';
                         this.blockers = rep.blockers || '';
                         this.nextSteps = rep.next_steps || '';
                         this.hoursDeclared = rep.hours_declared || '';
                         this.reportDate = rep.report_date ? rep.report_date.substring(0, 10) : '{{ today()->toDateString() }}';
                         this.taskId = rep.task_id || '';
                         this.open = true;
                     })
                     .catch(err => {
                         console.error('Erreur chargement rapport:', err);
                     });
             }
         }"
         @open-edit-report.window="openEdit($event.detail.id)"
         @click.window.escape="open = false">

        {{-- ── BARRE D'EN-TÊTE COMPACTE ── --}}
        <div class="ks-sec p-3 sm:p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#eef1fb] dark:bg-[#1c3e96]/20 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center flex-shrink-0 text-[#1c3e96] dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-sm sm:text-base font-bold text-[#241f1b] dark:text-white leading-tight">Rapports d'activité</h1>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#f1f0ed] dark:bg-[#1c212c] text-[#6e6862] dark:text-slate-300 border border-[#dcdad2] dark:border-[#222630] tabular-nums">
                                {{ $reports->count() }} rapport{{ $reports->count() > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                            Suivi quotidien de vos travaux, blocages et progression
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 w-full md:w-auto">
                    {{-- Filtres de période --}}
                    <div class="p-0.5 rounded-lg bg-[#f1f0ed] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] w-full sm:w-auto" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));">
                        <a href="{{ route('reports.index', ['period' => 'daily']) }}"
                            class="text-center px-1.5 py-1 text-[11px] sm:text-xs rounded-md font-medium transition truncate {{ ($period ?? 'daily') === 'daily' ? 'bg-[#fffefa] dark:bg-[#131519] text-[#1c3e96] dark:text-blue-400 font-semibold shadow-2xs' : 'text-[#6e6862] dark:text-slate-400 hover:text-[#241f1b] dark:hover:text-white' }}">
                            Aujourd'hui
                        </a>
                        <a href="{{ route('reports.index', ['period' => 'weekly']) }}"
                            class="text-center px-1.5 py-1 text-[11px] sm:text-xs rounded-md font-medium transition truncate {{ ($period ?? 'daily') === 'weekly' ? 'bg-[#fffefa] dark:bg-[#131519] text-[#1c3e96] dark:text-blue-400 font-semibold shadow-2xs' : 'text-[#6e6862] dark:text-slate-400 hover:text-[#241f1b] dark:hover:text-white' }}">
                            Semaine
                        </a>
                        <a href="{{ route('reports.index', ['period' => 'monthly']) }}"
                            class="text-center px-1.5 py-1 text-[11px] sm:text-xs rounded-md font-medium transition truncate {{ ($period ?? 'daily') === 'monthly' ? 'bg-[#fffefa] dark:bg-[#131519] text-[#1c3e96] dark:text-blue-400 font-semibold shadow-2xs' : 'text-[#6e6862] dark:text-slate-400 hover:text-[#241f1b] dark:hover:text-white' }}">
                            Mois
                        </a>
                    </div>

                    {{-- Bouton d'ajout --}}
                    <button
                        @click="openCreate()"
                        class="ks-btn-primary ks-btn-xs shadow-2xs w-full sm:w-auto shrink-0 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Nouveau rapport</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── BANNIÈRE RAPPORT DU JOUR (SI DÉJÀ REMPLI) ── --}}
        @if($todayReport && !$editReport)
        <div class="ks-sec p-3 border-l-4 border-l-[#15803d] bg-[#ecfdf5]/40 dark:bg-emerald-950/20">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-md bg-[#ecfdf5] dark:bg-emerald-950/60 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center flex-shrink-0 text-[#15803d] dark:text-emerald-400 mt-0.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-xs font-semibold text-[#15803d] dark:text-emerald-300">Rapport d'aujourd'hui enregistré</p>
                            @if($todayReport->hours_declared > 0)
                                <span class="text-[11px] font-semibold text-[#15803d] dark:text-emerald-400 tabular-nums">
                                    • {{ $todayReport->hours_declared }}h déclarées
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-[#241f1b] dark:text-slate-300 mt-0.5 line-clamp-1 leading-relaxed">
                            {{ $todayReport->summary }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <button onclick="viewReportDetails({{ $todayReport->id }})" class="ks-btn-secondary ks-btn-xs">
                        Consulter
                    </button>
                    <button @click="openEdit({{ $todayReport->id }})" class="ks-btn-secondary ks-btn-xs text-[#1c3e96] dark:text-blue-400">
                        Modifier
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ── MODALE DE CRÉATION / MODIFICATION RAPPORT ── --}}
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
             style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);">

            <div class="w-full max-w-xl max-h-[92vh] flex flex-col rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                {{-- En-tête de la modale --}}
                <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between bg-[#f1f0ed]/30 dark:bg-[#15181f]/40 flex-shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-[#eef1fb] dark:bg-[#1c3e96]/20 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center text-[#1c3e96] dark:text-blue-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-[#241f1b] dark:text-white" x-text="isEdit ? 'Modifier le rapport' : 'Nouveau rapport d\'activité'"></h2>
                    </div>

                    <button @click="open = false" class="w-7 h-7 rounded-md hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] flex items-center justify-center text-[#6e6862] dark:text-slate-400 hover:text-[#241f1b] dark:hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Corps scrollable du formulaire --}}
                <form method="POST"
                      :action="formAction"
                      class="flex-1 overflow-y-auto flex flex-col">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Position GPS (T-006) : capture pour vérification -->
                    <input type="hidden" name="latitude" id="report-index-latitude" value="">
                    <input type="hidden" name="longitude" id="report-index-longitude" value="">
                    <input type="hidden" name="accuracy_meters" id="report-index-accuracy" value="">
                    <input type="hidden" name="location_method" id="report-index-location-method" value="">

                    <div class="p-4 sm:p-5 space-y-3.5 flex-1 custom-scrollbar overflow-y-auto">

                        {{-- Tâche concernée + Sous-tâches --}}
                        @php
                            $tasksMap = ($activeTasks ?? collect())->mapWithKeys(function($t) {
                                $myItems = collect();
                                foreach ($t->subtasks as $st) {
                                    if ((int) $st->assigned_to_user_id === (int) auth()->id()) {
                                        foreach ($st->items as $item) {
                                            $myItems->push([
                                                'id' => $item->id,
                                                'title' => $item->title,
                                                'is_completed' => (bool) $item->is_completed,
                                                'subtask_title' => $st->title,
                                                'is_mine' => true,
                                            ]);
                                        }
                                    }
                                }

                                if ($myItems->isNotEmpty()) {
                                    $subtasksPayload = $myItems->values();
                                } else {
                                    $subtasksPayload = $t->subtasks->map(fn($st) => [
                                        'id' => $st->id,
                                        'title' => $st->title,
                                        'is_completed' => (bool) $st->is_completed,
                                        'assigned_to_user_id' => $st->assigned_to_user_id,
                                        'is_mine' => (int) $st->assigned_to_user_id === (int) auth()->id(),
                                        'assigned_name' => $st->assignedTo?->name,
                                    ])->values();
                                }

                                return [$t->id => [
                                    'id' => $t->id,
                                    'title' => $t->title,
                                    'subtasks' => $subtasksPayload,
                                ]];
                            });
                        @endphp

                        @if(($activeTasks ?? collect())->isNotEmpty())
                        <div x-data="{
                            tasksMap: @js($tasksMap),
                            checkedSubtasks: [],
                            currentTask() {
                                return this.tasksMap[taskId] || null;
                            },
                            subtasks() {
                                return this.currentTask() ? this.currentTask().subtasks : [];
                            },
                            mySubtasks() {
                                return this.subtasks().filter(s => s.is_mine);
                            },
                            otherSubtasks() {
                                return this.subtasks().filter(s => !s.is_mine);
                            },
                            computedProgress() {
                                const total = this.subtasks().length;
                                if (total === 0) return 0;
                                const alreadyDone = this.subtasks().filter(s => s.is_completed).length;
                                const newlyDone = this.checkedSubtasks.length;
                                return Math.min(100, Math.round(((alreadyDone + newlyDone) / total) * 100));
                            }
                        }">
                            <label class="ks-label">Tâche concernée</label>
                            <select name="task_id" x-model="taskId" class="ks-input !py-1.5 text-xs">
                                <option value="">— Aucune tâche associée —</option>
                                @foreach($activeTasks as $t)
                                <option value="{{ $t->id }}">{{ $t->title }}</option>
                                @endforeach
                            </select>

                            {{-- Liste des sous-tâches de la tâche sélectionnée --}}
                            <div x-show="taskId && subtasks().length > 0" x-cloak class="mt-2.5 p-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#f1f0ed]/40 dark:bg-[#181c24]/50 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">
                                        Sous-tâches terminées
                                    </span>
                                    <span class="text-[11px] font-bold text-[#15803d] dark:text-emerald-400" x-text="'Progression : ' + computedProgress() + '%'"></span>
                                </div>

                                <div class="space-y-1.5 max-h-36 overflow-y-auto custom-scrollbar pr-1">
                                    <template x-for="st in mySubtasks()" :key="st.id">
                                        <label class="flex items-center gap-2 p-1.5 rounded-md text-xs cursor-pointer transition border border-transparent"
                                               :class="st.is_completed ? 'bg-[#ecfdf5] dark:bg-emerald-950/30 text-[#15803d] dark:text-emerald-300' : 'bg-white dark:bg-[#15181f] border-[#dcdad2] dark:border-[#222630] hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] text-[#241f1b] dark:text-white'">
                                            <input type="checkbox"
                                                   name="completed_subtask_ids[]"
                                                   :value="st.id"
                                                   :disabled="st.is_completed"
                                                   :checked="st.is_completed"
                                                   x-model="checkedSubtasks"
                                                   class="h-3.5 w-3.5 rounded border-[#dcdad2] text-[#1c3e96] focus:ring-[#1c3e96]">
                                            <div class="min-w-0 flex-1 truncate">
                                                <span :class="st.is_completed ? 'line-through text-[#15803d] dark:text-emerald-400' : ''" x-text="st.title"></span>
                                                <span x-show="st.subtask_title" class="ml-1 text-[10px] text-[#6e6862] dark:text-slate-400" x-text="'↳ ' + st.subtask_title"></span>
                                                <span x-show="st.is_completed" class="ml-1 text-[10px] font-bold text-[#15803d] dark:text-emerald-400">(Validée)</span>
                                            </div>
                                        </label>
                                    </template>

                                    <template x-for="st in otherSubtasks()" :key="st.id">
                                        <div class="flex items-center justify-between p-1.5 rounded-md bg-[#f1f0ed]/60 dark:bg-[#15181f]/40 text-[#6e6862] dark:text-slate-400 text-xs opacity-75">
                                            <div class="flex items-center gap-2 truncate">
                                                <span x-text="st.is_completed ? '✓' : '•'"></span>
                                                <span :class="st.is_completed ? 'line-through' : ''" x-text="st.title"></span>
                                            </div>
                                            <span class="text-[10px] bg-white dark:bg-[#15181f] border border-[#dcdad2] dark:border-[#222630] px-1 rounded" x-text="'Attribuée à ' + st.assigned_name"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Introduction (optionnel) --}}
                        <div>
                            <label class="ks-label">Introduction <span class="text-[#6e6862]/70 dark:text-slate-500 font-normal normal-case">(optionnel)</span></label>
                            <textarea name="introduction" x-model="introduction" rows="2"
                                placeholder="Contexte ou intention générale de la journée..."
                                class="ks-input !py-1.5 text-xs leading-relaxed"></textarea>
                        </div>

                        {{-- Travail réalisé (obligatoire) --}}
                        <div>
                            <label class="ks-label">Travail réalisé <span class="text-red-500">*</span></label>
                            <textarea name="summary" x-model="summary" rows="3" required
                                placeholder="Décrivez vos réalisations, les tâches traitées, les résultats obtenus..."
                                class="ks-input !py-1.5 text-xs leading-relaxed"></textarea>
                        </div>

                        {{-- Blocages et prochaines étapes --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="ks-label">Difficultés ou blocages</label>
                                <textarea name="blockers" x-model="blockers" rows="2"
                                    placeholder="Obstacles, points bloquants..."
                                    class="ks-input !py-1.5 text-xs leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="ks-label">Prochaines étapes</label>
                                <textarea name="next_steps" x-model="nextSteps" rows="2"
                                    placeholder="Actions prévues pour la suite..."
                                    class="ks-input !py-1.5 text-xs leading-relaxed"></textarea>
                            </div>
                        </div>

                        {{-- Heures et Date --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="ks-label">Heures travaillées</label>
                                <input type="number" name="hours_declared" x-model="hoursDeclared" min="0" max="24" step="0.5"
                                    placeholder="ex: 7.5"
                                    class="ks-input !py-1.5 text-xs tabular-nums">
                            </div>

                            <div>
                                <label class="ks-label">Date du rapport</label>
                                <input type="date" name="report_date" x-model="reportDate"
                                    class="ks-input !py-1.5 text-xs">
                            </div>
                        </div>
                    </div>

                    {{-- Pied de modale avec boutons --}}
                    <div class="px-4 py-2.5 border-t border-[#dcdad2] dark:border-[#222630] bg-[#f1f0ed]/30 dark:bg-[#15181f]/40 flex items-center justify-end gap-2 flex-shrink-0">
                        <button type="button" @click="open = false" class="ks-btn-secondary ks-btn-xs">
                            Annuler
                        </button>

                        <button type="submit" name="status_action" value="draft" class="ks-btn-secondary ks-btn-xs">
                            Enregistrer brouillon
                        </button>

                        <button type="submit" name="status_action" value="submit" class="ks-btn-primary ks-btn-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="isEdit ? 'Mettre à jour' : 'Soumettre le rapport'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── LISTE DES RAPPORTS ── --}}
        <div class="space-y-2.5">
            @forelse($reports as $report)
            <div class="ks-sec p-3.5 sm:p-4 hover:border-[#bfbab2] dark:hover:border-[#3b4252] transition">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        {{-- En-tête : Date + Badge statut + Heures --}}
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <h3 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-white">
                                {{ $report->report_date->translatedFormat('l j F Y') }}
                            </h3>

                            {{-- Badge de statut --}}
                            @if($report->status === 'submitted')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#eef1fb] text-[#1c3e96] dark:bg-[#1c3e96]/20 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40">
                                    Soumis
                                </span>
                            @elseif($report->status === 'reviewed')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                                    Révisé
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40">
                                    Brouillon
                                </span>
                            @endif

                            @if($report->hours_declared > 0)
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[#6e6862] dark:text-slate-400 tabular-nums">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                                    </svg>
                                    {{ $report->hours_declared }}h déclarées
                                </span>
                            @endif
                        </div>

                        {{-- Résumé travail --}}
                        <p class="text-xs text-[#241f1b] dark:text-slate-200 leading-relaxed whitespace-pre-line">
                            {{ $report->summary }}
                        </p>

                        @if($report->introduction)
                            <p class="mt-1 text-xs text-[#6e6862] dark:text-slate-400 italic">
                                {{ Str::limit($report->introduction, 140) }}
                            </p>
                        @endif

                        {{-- Blocages et prochaines étapes --}}
                        @if($report->blockers || $report->next_steps)
                            <div class="mt-2.5 pt-2 border-t border-[#dcdad2] dark:border-[#222630] grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                @if($report->blockers)
                                    <div class="p-2 rounded-md bg-red-50/70 dark:bg-red-950/20 border border-red-200/60 dark:border-red-900/40">
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-red-700 dark:text-red-400">Difficultés / Blocages</span>
                                        <p class="text-xs text-red-900 dark:text-red-200 mt-0.5 leading-relaxed">{{ $report->blockers }}</p>
                                    </div>
                                @endif

                                @if($report->next_steps)
                                    <div class="p-2 rounded-md bg-emerald-50/70 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40">
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Prochaines étapes</span>
                                        <p class="text-xs text-emerald-900 dark:text-emerald-200 mt-0.5 leading-relaxed">{{ $report->next_steps }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Avis et retours --}}
                        @if($report->reviews()->count() > 0)
                            <div class="mt-2.5 pt-2 border-t border-[#dcdad2] dark:border-[#222630] space-y-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">
                                    Retours et commentaires ({{ $report->reviews()->count() }})
                                </span>
                                @foreach($report->reviews as $review)
                                    <div class="p-2 rounded-md bg-[#f1f0ed]/60 dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-slate-200 leading-relaxed">
                                        <div class="flex items-center justify-between text-[11px] text-[#6e6862] dark:text-slate-400 mb-0.5">
                                            <span class="font-semibold text-[#1c3e96] dark:text-blue-400">{{ $review->reviewer?->name ?? 'Tuteur' }}</span>
                                            <span>{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="whitespace-pre-line">{{ $review->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Actions et métadonnées --}}
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#dcdad2] dark:border-[#222630]">
                        <div class="flex items-center gap-1.5">
                            @if(auth()->user()->id === $report->user_id || optional(auth()->user()->etudiant)->id === $report->etudiant_id)
                                <button @click="openEdit({{ $report->id }})" class="ks-btn-secondary ks-btn-xs text-[#1c3e96] dark:text-blue-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Modifier</span>
                                </button>
                            @endif
                            <button onclick="viewReportDetails({{ $report->id }})" class="ks-btn-secondary ks-btn-xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Détails</span>
                            </button>
                        </div>

                        <span class="text-[10px] text-[#6e6862] dark:text-slate-400 tabular-nums">
                            {{ $report->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="ks-sec p-8 text-center">
                <div class="w-10 h-10 rounded-xl bg-[#f1f0ed] dark:bg-[#1c212c] border border-[#dcdad2] dark:border-[#222630] flex items-center justify-center mx-auto text-[#6e6862] dark:text-slate-400 mb-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Aucun rapport d'activité</h3>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Créez votre premier compte-rendu pour consigner le travail réalisé aujourd'hui.
                </p>
                <button @click="openCreate()" class="ks-btn-primary ks-btn-xs mt-3">
                    Créer mon rapport
                </button>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ── NOTIFICATION FLASH SESSION ── --}}
    @if(session('success'))
    <div class="fixed bottom-4 right-4 z-50 flex items-center gap-2.5 px-3.5 py-2.5 bg-[#ecfdf5] dark:bg-emerald-950/80 border border-[#a7f3d0] dark:border-emerald-800 text-[#15803d] dark:text-emerald-300 rounded-lg shadow-lg text-xs font-medium"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        @click.away="show = false">
        <svg class="w-4 h-4 text-[#15803d] dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="ml-2 text-[#15803d] dark:text-emerald-400 hover:opacity-80">×</button>
    </div>
    @endif

    {{-- ── MODALE DETAILS D'UN RAPPORT (JS DYNAMIQUE) ── --}}
    <div id="reportDetailsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-4" style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);">
        <div class="w-full max-w-2xl max-h-[90vh] flex flex-col rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden">
            <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between bg-[#f1f0ed]/30 dark:bg-[#15181f]/40 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-[#eef1fb] dark:bg-[#1c3e96]/20 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center text-[#1c3e96] dark:text-blue-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#241f1b] dark:text-white">Détails du rapport</h3>
                </div>
                <button onclick="closeReportDetailsModal()" class="w-7 h-7 rounded-md hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] flex items-center justify-center text-[#6e6862] dark:text-slate-400 hover:text-[#241f1b] dark:hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="reportDetailsContent" class="p-4 sm:p-5 overflow-y-auto flex-1 custom-scrollbar">
                <!-- Chargé dynamiquement -->
            </div>
        </div>
    </div>

</x-app-layout>

{{-- SCRIPTS JS --}}
<script>
    // Visualisation dynamique des détails du rapport
    function viewReportDetails(reportId) {
        fetch(`/reports/${reportId}`)
            .then(response => response.json())
            .then(data => {
                const report = data.report;
                const reviews = data.reviews || [];

                let statusBadge = '';
                if (report.status === 'submitted') {
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#eef1fb] text-[#1c3e96] dark:bg-[#1c3e96]/20 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40">Soumis</span>';
                } else if (report.status === 'reviewed') {
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">Révisé</span>';
                } else {
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40">Brouillon</span>';
                }

                const introHtml = report.introduction
                    ? `<div class="p-3 rounded-lg bg-[#f1f0ed]/50 dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400 mb-1">Introduction</span>
                        <p class="text-xs text-[#241f1b] dark:text-slate-200 leading-relaxed whitespace-pre-line">${escapeHtml(report.introduction)}</p>
                      </div>`
                    : '';

                const summaryHtml = report.summary
                    ? `<div class="p-3 rounded-lg bg-[#fffefa] dark:bg-[#15181f] border border-[#dcdad2] dark:border-[#222630]">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-[#1c3e96] dark:text-blue-400 mb-1">Travail réalisé</span>
                        <p class="text-xs text-[#241f1b] dark:text-slate-100 leading-relaxed whitespace-pre-line">${escapeHtml(report.summary)}</p>
                      </div>`
                    : '';

                const blockersHtml = report.blockers
                    ? `<div class="p-3 rounded-lg bg-red-50/70 dark:bg-red-950/20 border border-red-200/60 dark:border-red-900/40">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-red-700 dark:text-red-400 mb-1">Difficultés & Blocages</span>
                        <p class="text-xs text-red-900 dark:text-red-200 leading-relaxed whitespace-pre-line">${escapeHtml(report.blockers)}</p>
                      </div>`
                    : '';

                const nextStepsHtml = report.next_steps
                    ? `<div class="p-3 rounded-lg bg-emerald-50/70 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 mb-1">Prochaines étapes</span>
                        <p class="text-xs text-emerald-900 dark:text-emerald-200 leading-relaxed whitespace-pre-line">${escapeHtml(report.next_steps)}</p>
                      </div>`
                    : '';

                const hoursHtml = report.hours_declared
                    ? `<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#6e6862] dark:text-slate-400 tabular-nums">
                        • ${report.hours_declared}h travaillées
                       </span>`
                    : '';

                let reviewsHtml = '';
                if (reviews.length > 0) {
                    reviewsHtml += `<div class="mt-4 pt-3 border-t border-[#dcdad2] dark:border-[#222630]">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400 mb-2">Commentaires (${reviews.length})</span>
                        <div class="space-y-2">`;
                    reviews.forEach(review => {
                        const isAuthorReply = review.action === 'author_reply';
                        reviewsHtml += `
                            <div class="p-2.5 rounded-lg border text-xs leading-relaxed ${isAuthorReply ? 'bg-[#eef1fb]/50 border-blue-200/60 dark:bg-blue-950/20 dark:border-blue-900/40 ml-4' : 'bg-[#f1f0ed]/60 border-[#dcdad2] dark:bg-[#181c24] dark:border-[#222630]'}">
                                <div class="flex items-center justify-between text-[11px] text-[#6e6862] dark:text-slate-400 mb-1">
                                    <span class="font-semibold text-[#1c3e96] dark:text-blue-400">${escapeHtml(review.reviewer_name)}</span>
                                    <span>${review.created_at}</span>
                                </div>
                                <p class="text-[#241f1b] dark:text-slate-200 whitespace-pre-line">${escapeHtml(review.comment)}</p>
                            </div>`;
                    });
                    reviewsHtml += '</div></div>';
                }

                const commentFormHtml = `
                    <form class="mt-3.5 flex gap-2" onsubmit="submitComment(event, ${reportId})">
                        <input type="text" id="comment-input-${reportId}" placeholder="Ajouter une réponse ou un commentaire..."
                               class="ks-input !py-1.5 text-xs flex-1" required />
                        <button type="submit" class="ks-btn-primary ks-btn-xs">
                            Envoyer
                        </button>
                    </form>`;

                const content = `
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3 pb-2 border-b border-[#dcdad2] dark:border-[#222630]">
                            <div>
                                <h4 class="text-sm font-bold text-[#241f1b] dark:text-white">${report.report_date_formatted}</h4>
                                <div class="mt-1 flex items-center gap-2">
                                    ${statusBadge}
                                    ${hoursHtml}
                                </div>
                            </div>
                            <div class="text-right text-[11px] text-[#6e6862] dark:text-slate-400">
                                <div>Créé ${report.created_at_formatted}</div>
                                ${report.updated_at_formatted !== report.created_at_formatted ? `<div>Modifié ${report.updated_at_formatted}</div>` : ''}
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            ${introHtml}
                            ${summaryHtml}
                            ${blockersHtml}
                            ${nextStepsHtml}
                        </div>

                        ${reviewsHtml}
                        ${commentFormHtml}
                    </div>
                `;

                document.getElementById('reportDetailsContent').innerHTML = content;
                const modal = document.getElementById('reportDetailsModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            })
            .catch(error => {
                console.error('Erreur lors du chargement du rapport:', error);
            });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function submitComment(e, reportId) {
        e.preventDefault();
        const input = document.getElementById(`comment-input-${reportId}`);
        const comment = input.value.trim();
        if (!comment) return;

        fetch(`/reports/${reportId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ comment }),
        })
        .then(r => r.json())
        .then(() => {
            input.value = '';
            viewReportDetails(reportId);
        })
        .catch(err => console.error(err));
    }

    function closeReportDetailsModal() {
        const modal = document.getElementById('reportDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function editReport(reportId) {
        window.dispatchEvent(new CustomEvent('open-edit-report', { detail: { id: reportId } }));
    }

    // ── Capture GPS pour le formulaire de rapport (T-006) ──
    (function () {
        function fillReportLocation(position) {
            const latField = document.getElementById('report-index-latitude');
            const lngField = document.getElementById('report-index-longitude');
            const accField = document.getElementById('report-index-accuracy');
            const locField = document.getElementById('report-index-location-method');
            if (!latField || !lngField) return;
            latField.value = position.coords.latitude;
            lngField.value = position.coords.longitude;
            accField.value = Math.round(position.coords.accuracy || 0);
            locField.value = 'geolocation';
        }

        function setLocationError() {
            const locField = document.getElementById('report-index-location-method');
            if (locField) locField.value = 'gps-unavailable';
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(fillReportLocation, setLocationError, {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0,
            });
        } else {
            setLocationError();
        }
    })();
</script>
