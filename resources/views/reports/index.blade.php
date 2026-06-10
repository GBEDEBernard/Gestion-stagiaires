<x-app-layout title="Rapports de travail">

    <div class="max-w-4xl mx-auto px-6 py-12 space-y-12" x-data="{ open: false }" @click.window.escape="open = false">

        <!-- HEADER -->
        <div class="flex flex-col items-center gap-6 md:flex-row md:justify-between">
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold text-slate-900">Rapports de travail</h1>
                <p class="text-lg text-slate-500 max-w-xl">
                    Suivi quotidien de vos activités, progrès et réflexions professionnelles
                </p>
            </div>

            <!-- Quick Add Button -->
            @if(!isset($editReport))
            <button
                @click="open = true"
                class="relative inline-flex items-center gap-3 px-5 py-3 text-sm font-medium bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau rapport
            </button>
            @endif
        </div>

        <!-- FORM CREATE / EDIT -->
        <div x-show="open || @js(isset($editReport) || (old('summary') && !session('success')))" x-cloak>
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                @click.away="open = false">
                <div class="relative w-full max-w-md">
                    <button @click="open = false"
                        class="absolute right-4 top-4 text-slate-400 hover:text-slate-500 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-slate-100">
                        <h2 class="text-xl font-semibold text-slate-900 mb-6">
                            {{ isset($editReport) ? 'Modifier le rapport' : 'Nouveau rapport' }}
                        </h2>

                        <form method="POST"
                            action="{{ isset($editReport)
                                      ? route('reports.update', $editReport->id)
                                      : route('reports.store') }}"
                            class="space-y-6">

                            @csrf
                            @isset($editReport)
                            @method('PUT')
                            @endisset

                            <!-- Tâche concernée + progression -->
                            @if(($activeTasks ?? collect())->isNotEmpty())
                            <div x-data="{ prog: {{ (int) old('task_progress_percent', 0) }} }">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tâche concernée</label>
                                <select name="task_id"
                                    @change="prog = parseInt($event.target.selectedOptions[0].dataset.progress || prog)"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200">
                                    <option value="">— Aucune tâche —</option>
                                    @foreach($activeTasks as $t)
                                    <option value="{{ $t->id }}" data-progress="{{ (int) $t->last_progress_percent }}" {{ old('task_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                                    @endforeach
                                </select>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Progression de la tâche : <span class="font-semibold text-slate-900" x-text="prog + '%'"></span>
                                    </label>
                                    <input type="range" name="task_progress_percent" min="0" max="100" step="5" x-model="prog"
                                        class="w-full accent-slate-900">
                                </div>
                            </div>
                            @else
                            <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm text-slate-600">
                                Aucune tâche active.
                                <a href="{{ route('tasks.index') }}" class="font-medium text-slate-900 underline">Créer une tâche</a>
                                pour y rattacher ce rapport et suivre ta progression.
                            </div>
                            @endif

                            <!-- Summary -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Introduction <span class="text-slate-400 text-xs">(optionnel)</span></label>
                                <textarea name="introduction"
                                    rows="2"
                                    placeholder="Contexte ou objectif de la journée..."
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200">{{ old('introduction') }}</textarea>
                            </div>

                            <!-- Summary -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Résumé <span class="text-red-500">*</span></label>
                                <textarea name="summary"
                                    rows="4"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200">{{ old('summary', $editReport->summary ?? '') }}</textarea>
                            </div>

                            <!-- Blockers -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Blocages rencontrés</label>
                                <textarea name="blockers"
                                    rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200">{{ old('blockers', $editReport->blockers ?? '') }}</textarea>
                            </div>

                            <!-- Next Steps -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Prochaines étapes</label>
                                <textarea name="next_steps"
                                    rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200">{{ old('next_steps', $editReport->next_steps ?? '') }}</textarea>
                            </div>

                            <!-- Hours -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Heures déclarées</label>
                                    <input type="number"
                                        name="hours_declared"
                                        min="0"
                                        max="24"
                                        step="0.5"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200"
                                        value="{{ old('hours_declared', $editReport->hours_declared ?? 0) }}">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Date du rapport</label>
                                    <input type="date"
                                        name="report_date"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-base transition-all duration-200"
                                        value="{{ old('report_date', $editReport->report_date ?? today()->toDateString()) }}">
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-50">
                                <button type="button"
                                    @click="open = false"
                                    class="px-5 py-3 text-sm font-medium border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200">
                                    Annuler
                                </button>

                                <button type="submit"
                                    name="status_action"
                                    value="draft"
                                    class="px-5 py-3 text-sm font-medium border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition-all duration-200">
                                    Enregistrer brouillon
                                </button>

                                <button type="submit"
                                    name="status_action"
                                    value="submit"
                                    class="px-5 py-3 text-sm font-medium bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-all duration-200 shadow-md hover:shadow-lg">
                                    {{ isset($editReport) ? 'Mettre à jour' : 'Soumettre' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- REPORTS LIST -->
        <div class="space-y-6">
            <!-- CONTROLS -->
            <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-semibold text-slate-900">Historique des rapports</h2>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ $reports->count() }} rapport{{ $reports->count() > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Period Filter -->
                        <div class="flex items-center gap-1 bg-slate-50 rounded-lg p-1">
                            <a href="{{ route('reports.index', ['period' => 'daily']) }}"
                                class="px-3 py-1.5 text-sm rounded-md transition-all {{ ($period ?? 'daily') === 'daily' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Aujourd'hui
                            </a>
                            <a href="{{ route('reports.index', ['period' => 'weekly']) }}"
                                class="px-3 py-1.5 text-sm rounded-md transition-all {{ ($period ?? 'daily') === 'weekly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Cette semaine
                            </a>
                            <a href="{{ route('reports.index', ['period' => 'monthly']) }}"
                                class="px-3 py-1.5 text-sm rounded-md transition-all {{ ($period ?? 'daily') === 'monthly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Ce mois
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($reports as $report)
            <div class="bg-white rounded-xl border border-slate-100 hover:border-slate-200 transition-all duration-200 shadow-sm hover:shadow-md overflow-hidden">
                <div class="p-6">
                    <!-- HEADER -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-slate-900">
                                    {{ $report->report_date->format('l j F Y') }}
                                </h3>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($report->status === 'submitted') bg-blue-100 text-blue-700
                                    @elseif($report->status === 'reviewed') bg-emerald-100 text-emerald-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    @if($report->status === 'submitted')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Soumis
                                    @elseif($report->status === 'reviewed')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Révisé
                                    @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Brouillon
                                    @endif
                                </span>
                            </div>
                            <p class="text-base text-slate-600 leading-relaxed">
                            {{ nl2br(e($report->summary)) }}
                            </p>
                                @if($report->introduction)
                                <p class="mt-1 text-sm text-slate-400 italic leading-relaxed">
                                    {{ Str::limit($report->introduction, 120) }}
                                </p>
                                @endif
                        </div>

                        <!-- ACTIONS -->
                        <div class="flex flex-col items-end gap-3 ml-4">
                            <div class="flex items-center gap-2">
                                @if(auth()->user()->id === $report->user_id || optional(auth()->user()->etudiant)->id === $report->etudiant_id)
                                <button onclick="editReport({{ $report->id }})"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Modifier
                                </button>
                                @endif
                                <button onclick="viewReportDetails({{ $report->id }})"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-slate-700 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Détails
                                </button>
                            </div>

                            <!-- METADATA -->
                            <div class="text-right space-y-1">
                                @if($report->hours_declared > 0)
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M6 6h12"></path>
                                    </svg>
                                    <span class="font-medium">{{ $report->hours_declared }}h travaillées</span>
                                </div>
                                @endif

                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H3m2 14h10M5 17l5-5m0 0l5 5M7 7h10"></path>
                                    </svg>
                                    <span>{{ $report->created_at->diffForHumans() }}</span>
                                </div>

                                @if($report->updated_at != $report->created_at)
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Modifié {{ $report->updated_at->diffForHumans() }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($report->blockers || $report->next_steps)
                    <div class="mt-4 pt-4 border-t border-slate-50">
                        @if($report->blockers)
                        <div class="mb-3">
                            <p class="text-sm font-medium text-slate-700 mb-1">Blocages</p>
                            <p class="text-slate-600">{{ nl2br(e($report->blockers)) }}</p>
                        </div>
                        @endif

                        @if($report->next_steps)
                        <div>
                            <p class="text-sm font-medium text-slate-700 mb-1">Prochaines étapes</p>
                            <p class="text-slate-600">{{ nl2br(e($report->next_steps)) }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($report->reviews()->count() > 0)
                    <div class="mt-4 pt-4 border-t border-slate-50">
                        <p class="text-sm font-medium text-slate-700 mb-2">Revue et feedback</p>
                        <div class="space-y-2">
                            @foreach($report->reviews as $review)
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-600">{{ $review->created_at->diffForHumans() }}</p>
                                    <p class="text-base text-slate-900 leading-relaxed">{{ nl2br(e($review->comment)) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="flex flex-col items-center gap-4">
                    <svg class="w-12 h-12 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 11v2m2-2V9m2 2h2m-2 4v2m2-2h2V9m2 2h2M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7"></path>
                    </svg>
                    <p class="text-lg text-slate-500">Aucun rapport pour l'instant</p>
                    <p class="text-sm text-slate-400 max-w-xl">
                        Commencez par créer votre premier rapport pour suivre votre progression quotidienne
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- TODAY'S REPORT BANNER -->
        @if($todayReport && !$editReport)
        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 border-l-4 border-emerald-500 p-6 rounded-xl">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-emerald-800 mb-1">Rapport d'aujourd'hui</p>
                    <p class="text-emerald-600">
                        {{ nl2br(e($todayReport->summary)) }}
                    </p>
                    @if($todayReport->hours_declared > 0)
                    <span class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-emerald-700">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3M6 6h12"></path>
                        </svg>
                        {{ $todayReport->hours_declared }}h
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- ALERT -->
    @if(session('success'))
    <div class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-lg transform transition-all duration-300"
        x-data="{ show: true }"
        @click.away="show = false"
        @keydown.escape.window="show = false">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
        <button @click="show = false"
            class="ml-3 text-emerald-600 hover:text-emerald-800 transition-colors duration-200">
            ×
        </button>
    </div>
    @endif

    <!-- MODAL VIEW REPORT DETAILS -->
    <div id="reportDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Détails du rapport</h3>
                        <button onclick="closeReportDetailsModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="reportDetailsContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT REPORT -->
    <div id="editReportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Modifier le rapport</h3>
                        <button onclick="closeEditReportModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="editReportContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<!-- ALPINE JS -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reportForm', () => ({
            open: false,
            init() {
                // Close form when clicking outside
                window.addEventListener('click', (e) => {
                    if (this.open && !e.target.closest('.report-form-container')) {
                        this.open = false;
                    }
                });
            }
        }));
    });

    // Report Details Modal Functions
    function viewReportDetails(reportId) {
        fetch(`/reports/${reportId}`)
            .then(response => response.json())
            .then(data => {
                const report = data.report;
                const reviews = data.reviews || [];

                let statusBadge = '';
                if (report.status === 'submitted') {
                    statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Soumis</span>';
                } else if (report.status === 'reviewed') {
                    statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700">Révisé</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">Brouillon</span>';
                }

                // -- Sections du rapport --
                const introHtml = report.introduction
                    ? `<div class="bg-slate-50 rounded-xl p-4">
                        <h5 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Introduction</h5>
                        <p class="text-slate-800 leading-relaxed whitespace-pre-line">${escapeHtml(report.introduction)}</p>
                      </div>`
                    : '';

                const summaryHtml = report.summary
                    ? `<div class="bg-slate-50 rounded-xl p-4">
                        <h5 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Travail réalisé</h5>
                        <p class="text-slate-800 leading-relaxed whitespace-pre-line">${escapeHtml(report.summary)}</p>
                      </div>`
                    : '';

                const blockersHtml = report.blockers
                    ? `<div class="border-l-4 border-red-300 bg-red-50 rounded-r-xl p-4">
                        <h5 class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-2">Blocages rencontrés</h5>
                        <p class="text-red-900 whitespace-pre-line">${escapeHtml(report.blockers)}</p>
                      </div>`
                    : '';

                const nextStepsHtml = report.next_steps
                    ? `<div class="border-l-4 border-emerald-300 bg-emerald-50 rounded-r-xl p-4">
                        <h5 class="text-xs font-semibold text-emerald-600 uppercase tracking-wide mb-2">Prochaines étapes</h5>
                        <p class="text-emerald-900 whitespace-pre-line">${escapeHtml(report.next_steps)}</p>
                      </div>`
                    : '';

                const hoursHtml = report.hours_declared
                    ? `<div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M6 6h12"/></svg>
                        ${report.hours_declared}h déclarées
                       </div>`
                    : '';

                // -- Commentaires --
                let reviewsHtml = '';
                if (reviews.length > 0) {
                    reviewsHtml += `<div class="mt-6 pt-6 border-t border-slate-100">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">Commentaires (${reviews.length})</h4>
                        <div class="space-y-3">`;
                    reviews.forEach(review => {
                        const isAuthorReply = review.action === 'author_reply';
                        reviewsHtml += `
                            <div class="flex gap-3 ${isAuthorReply ? 'flex-row-reverse' : ''}">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold"
                                     style="background:${isAuthorReply ? '#dbeafe' : '#f1f5f9'}; color:${isAuthorReply ? '#1d4ed8' : '#475569'}">
                                    ${escapeHtml(review.reviewer_name.charAt(0).toUpperCase())}
                                </div>
                                <div class="flex-1 ${isAuthorReply ? 'text-right' : ''}">
                                    <div class="inline-block rounded-2xl px-4 py-2.5 max-w-prose"
                                         style="background:${isAuthorReply ? '#eff6ff' : '#f8fafc'}">
                                        <p class="text-sm font-medium text-slate-800 mb-0.5">${escapeHtml(review.reviewer_name)}</p>
                                        <p class="text-sm text-slate-700 leading-relaxed">${escapeHtml(review.comment)}</p>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 px-1">${review.created_at}</p>
                                </div>
                            </div>`;
                    });
                    reviewsHtml += '</div></div>';
                }

                // -- Formulaire d'ajout de commentaire --
                const commentFormHtml = `
                    <form class="mt-4 flex gap-2" onsubmit="submitComment(event, ${reportId})">
                        <input type="text" id="comment-input-${reportId}" placeholder="Ajouter un commentaire..."
                               class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-slate-400 focus:border-transparent" required />
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-xl hover:bg-slate-700 transition">
                            Envoyer
                        </button>
                    </form>`;

                const content = `
                    <div class="space-y-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-xl font-bold text-slate-900">${report.report_date_formatted}</h4>
                                <div class="mt-1.5 flex items-center gap-3">
                                    ${statusBadge}
                                    ${hoursHtml}
                                </div>
                            </div>
                            <div class="text-right text-xs text-slate-400">
                                <div>Créé ${report.created_at_formatted}</div>
                                ${report.updated_at_formatted !== report.created_at_formatted ? `<div>Modifié ${report.updated_at_formatted}</div>` : ''}
                            </div>
                        </div>

                        <div class="space-y-3">
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
                document.getElementById('reportDetailsModal').classList.remove('hidden');
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
            viewReportDetails(reportId); // recharge la modale
        })
        .catch(err => console.error(err));
    }

    function closeReportDetailsModal() {
        document.getElementById('reportDetailsModal').classList.add('hidden');
    }

    // Edit Report Modal Functions
    function editReport(reportId) {
        // Redirect to edit page with report ID
        window.location.href = `/reports/${reportId}/edit`;
    }

    function closeEditReportModal() {
        document.getElementById('editReportModal').classList.add('hidden');
    }
</script>
