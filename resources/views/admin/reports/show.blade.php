<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        @php
            $authorPersonnel = $report->user?->personnel ?? $report->etudiant?->personnel;
            $authorName = trim(($authorPersonnel?->prenom ?? '') . ' ' . ($authorPersonnel?->nom ?? $report->user?->name ?? 'Utilisateur'));
            $isStudent = !is_null($report->etudiant_id);
            $totalTasks = $relatedTasks->count();
            $totalReportsCount = $relatedTasks->sum(fn($t) => $t->dailyReports->count());
            $totalHours = $relatedTasks->sum(fn($t) => $t->dailyReports->sum('hours_declared'));
            $avgProgress = $totalTasks > 0 ? round($relatedTasks->avg('last_progress_percent')) : 0;
        @endphp

        {{-- ── BARRE DE NAVIGATION ET ACTIONS DU HAUT ── --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.all', request()->query()) }}" 
                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition"
                   title="Retour à la liste">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                            Rapport d'activité • #{{ $report->id }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold
                            @if($report->status === 'submitted') bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800
                            @elseif($report->status === 'reviewed') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800
                            @else bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800 @endif">
                            <span class="w-1.5 h-1.5 rounded-full @if($report->status === 'submitted') bg-blue-600 @elseif($report->status === 'reviewed') bg-emerald-600 @else bg-amber-600 @endif"></span>
                            {{ $report->status === 'submitted' ? 'Soumis' : ($report->status === 'reviewed' ? 'Révisé / Validé' : 'Brouillon') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Séance du <span class="font-medium text-slate-700 dark:text-slate-300">{{ $report->report_date->isoFormat('dddd D MMMM YYYY') }}</span>
                        • Déposé {{ $report->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.reports.download-pdf', $report->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white dark:bg-indigo-600 dark:hover:bg-indigo-700 text-xs font-semibold rounded-xl transition shadow-sm"
                   title="Générer et télécharger le rapport complet multi-pages">
                    <svg class="w-4 h-4 text-slate-300 dark:text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger PDF Exécutif
                </a>

                @can('admin.reports.send-bilan')
                <button onclick="sendBilan({{ $report->id }})"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer le bilan
                </button>
                @endcan
            </div>
        </div>

        {{-- ── 1. BANDEAU DE SYNTHÈSE EXÉCUTIVE (AUTEUR, STAGE, MÉTRIQUES) ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Profil Auteur --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Collaborateur</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                            {{ $isStudent ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $isStudent ? 'Stagiaire' : 'Employé' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-800 dark:text-slate-200 font-bold text-base">
                            {{ strtoupper(substr($authorPersonnel?->prenom ?? $report->user?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($authorPersonnel?->nom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                                {{ $authorName }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate max-w-[220px]">
                                {{ $report->user?->email ?? $report->etudiant?->user?->email ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                    @if($isStudent && $report->stage)
                        <div class="flex justify-between">
                            <span class="text-slate-400">École :</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $report->stage->etudiant?->ecole ?? 'N/A' }}</span>
                        </div>
                        @if($report->stage->filiere)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Filière :</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $report->stage->filiere }}</span>
                        </div>
                        @endif
                    @else
                        <div class="flex justify-between">
                            <span class="text-slate-400">Domaine :</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $report->user?->domaine?->nom ?? 'Service général' }}</span>
                        </div>
                    @endif
                    @if($authorPersonnel?->telephone ?? $report->etudiant?->telephone)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Téléphone :</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $authorPersonnel?->telephone ?? $report->etudiant?->telephone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Contexte du Stage / Thème --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Cadre & Encadrement</span>
                        @if($report->stage)
                        <span class="text-xs text-slate-500 font-medium">
                            {{ $report->stage->site?->name ?? 'Site TFG' }}
                        </span>
                        @endif
                    </div>

                    @if($report->stage)
                        <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 p-3 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 tracking-wider block mb-0.5">Thème retenu</span>
                            <p class="text-xs font-semibold text-slate-900 dark:text-slate-100 leading-snug line-clamp-2">
                                {{ $report->stage->theme ?? 'Stage de perfectionnement' }}
                            </p>
                        </div>
                    @else
                        <p class="text-xs text-slate-500 italic py-2">Aucun stage spécifique rattaché.</p>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                    @if($report->stage)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Période :</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">
                            {{ $report->stage->date_debut?->format('d/m/Y') }} → {{ $report->stage->date_fin?->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Encadrant :</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">
                            {{ trim(($report->stage->supervisor?->personnel?->prenom ?? '') . ' ' . ($report->stage->supervisor?->personnel?->nom ?? $report->stage->supervisor?->name ?? 'Non défini')) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Indicateurs d'activité --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-4">Vue d'ensemble d'activité</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Tâches totales</span>
                            <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $totalTasks }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Rapports déposés</span>
                            <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $totalReportsCount }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Total heures</span>
                            <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $totalHours }} h</span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Progression moy.</span>
                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5 block">{{ $avgProgress }}%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                    <span>Heures sur ce rapport :</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $report->hours_declared ?? 0 }} h</span>
                </div>
            </div>
        </div>

        {{-- ── 2. TOUTES LES TÂCHES ET L'ENSEMBLE DES RAPPORTS DÉPOSÉS ── --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                        Missions, Tâches & Comptes-rendus Déposés
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Retrouvez ci-dessous le détail exhaustif de chaque tâche et l'historique complet de tous les rapports déposés.
                    </p>
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                    {{ $totalTasks }} tâche(s) au total
                </span>
            </div>

            @if($relatedTasks->isEmpty())
                <div class="p-10 text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-400 text-sm">
                    Aucune tâche n'est rattachée à cet enregistrement.
                </div>
            @else
                <div class="space-y-8">
                    @foreach($relatedTasks as $taskIndex => $taskItem)
                        @php
                            $isCurrentTask = ($report->task_id === $taskItem->id);
                            $taskReports = $taskItem->dailyReports;
                            $taskProgress = $taskItem->last_progress_percent ?? 0;
                        @endphp

                        <div class="bg-white dark:bg-slate-900 border {{ $isCurrentTask ? 'border-slate-400 dark:border-slate-600 ring-1 ring-slate-400/20' : 'border-slate-200 dark:border-slate-800' }} rounded-2xl shadow-sm overflow-hidden">
                            
                            {{-- En-tête de la tâche --}}
                            <div class="p-6 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <span class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                                Tâche {{ $taskIndex + 1 }}
                                            </span>
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                                {{ $taskItem->title }}
                                            </h3>
                                            @if($isCurrentTask)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-900 text-white dark:bg-white dark:text-slate-900">
                                                    ★ Tâche active du rapport actuel
                                                </span>
                                            @endif
                                        </div>
                                        @if($taskItem->description)
                                            <p class="text-xs text-slate-600 dark:text-slate-300 max-w-3xl leading-relaxed mt-1">
                                                {{ $taskItem->description }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap self-start md:self-auto">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border
                                            @if($taskItem->priority === 'high') bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800
                                            @elseif($taskItem->priority === 'low') bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700
                                            @else bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800 @endif">
                                            Priorité {{ ['high'=>'Haute','low'=>'Basse','medium'=>'Moyenne','urgent'=>'Urgente'][$taskItem->priority] ?? ucfirst($taskItem->priority ?? 'Normale') }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border
                                            @if($taskItem->status === 'completed') bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800
                                            @elseif($taskItem->status === 'in_progress') bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800
                                            @elseif($taskItem->status === 'blocked') bg-red-50 text-red-800 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800
                                            @else bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 @endif">
                                            {{ ['completed'=>'Terminée','in_progress'=>'En cours','blocked'=>'Bloquée','pending'=>'En attente','todo'=>'À faire','cancelled'=>'Annulée'][$taskItem->status] ?? 'En cours' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Jauge de progression & Échéance --}}
                                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60 items-center">
                                    <div class="sm:col-span-2">
                                        <div class="flex justify-between text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                            <span>Niveau d'accomplissement global</span>
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $taskProgress }}%</span>
                                        </div>
                                        <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-slate-900 dark:bg-indigo-500 rounded-full transition-all duration-300" style="width: {{ $taskProgress }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-right text-xs text-slate-500 dark:text-slate-400">
                                        @if($taskItem->due_date)
                                            <span>Échéance : <strong class="text-slate-800 dark:text-slate-200">{{ $taskItem->due_date->format('d/m/Y') }}</strong></span>
                                        @else
                                            <span>Aucune date d'échéance fixée</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Rapports soumis sur cette tâche --}}
                            <div class="p-6 space-y-6">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                        Comptes-rendus soumis sur cette tâche ({{ $taskReports->count() }})
                                    </h4>
                                </div>

                                @if($taskReports->isEmpty())
                                    <p class="text-xs text-slate-400 italic py-2">
                                        Aucun compte-rendu journalier n'a encore été déposé pour cette tâche.
                                    </p>
                                @else
                                    <div class="grid grid-cols-1 gap-5">
                                        @foreach($taskReports as $tReport)
                                            @php
                                                $isSelected = ($tReport->id === $report->id);
                                            @endphp
                                            <div class="rounded-xl border p-5 transition-all
                                                {{ $isSelected ? 'border-slate-900 bg-slate-50/50 dark:border-indigo-500 dark:bg-slate-800/70 shadow-sm' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700' }}">
                                                
                                                {{-- En-tête du rapport --}}
                                                <div class="flex items-start justify-between gap-3 flex-wrap border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-sm font-bold text-slate-900 dark:text-white">
                                                            📅 Session du {{ $tReport->report_date->format('d/m/Y') }}
                                                        </span>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border
                                                            @if($tReport->status === 'submitted') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300
                                                            @elseif($tReport->status === 'reviewed') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300
                                                            @else bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 @endif">
                                                            {{ ['submitted' => 'Soumis', 'reviewed' => 'Validé', 'draft' => 'Brouillon'][$tReport->status] ?? ucfirst($tReport->status) }}
                                                        </span>
                                                        @if($tReport->hours_declared > 0)
                                                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                                                ⏱ {{ $tReport->hours_declared }} h
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        @if($isSelected)
                                                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded border border-emerald-200 dark:border-emerald-800">
                                                                ✓ Rapport consulté
                                                            </span>
                                                        @else
                                                            <a href="{{ route('admin.reports.show', $tReport->id) }}" 
                                                               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-900 hover:text-indigo-600 dark:text-slate-200 dark:hover:text-indigo-400 transition">
                                                                Ouvrir ce rapport
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Corps du rapport --}}
                                                <div class="space-y-3.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                                    @if($tReport->introduction)
                                                    <div>
                                                        <span class="font-bold text-slate-900 dark:text-slate-200 block mb-1">Introduction :</span>
                                                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-lg border border-slate-100 dark:border-slate-800">
                                                            {{ nl2br(e($tReport->introduction)) }}
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <div>
                                                        <span class="font-bold text-slate-900 dark:text-slate-200 block mb-1">Travail accompli :</span>
                                                        <div class="p-3.5 bg-slate-50/60 dark:bg-slate-800/40 rounded-lg border border-slate-100 dark:border-slate-800 font-sans">
                                                            {{ nl2br(e($tReport->summary)) }}
                                                        </div>
                                                    </div>

                                                    @if($tReport->blockers)
                                                    <div class="p-3 bg-red-50/80 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-lg text-red-800 dark:text-red-300">
                                                        <span class="font-bold block mb-0.5">⚠️ Difficultés & Blocages :</span>
                                                        {{ nl2br(e($tReport->blockers)) }}
                                                    </div>
                                                    @endif

                                                    @if($tReport->next_steps)
                                                    <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 rounded-lg text-emerald-800 dark:text-emerald-300">
                                                        <span class="font-bold block mb-0.5">🚀 Prochaines étapes :</span>
                                                        {{ nl2br(e($tReport->next_steps)) }}
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Méta pointage GPS --}}
                                                @if($tReport->latitude && $tReport->longitude)
                                                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                                                    <span>Position GPS : {{ number_format($tReport->latitude, 4) }}, {{ number_format($tReport->longitude, 4) }} (±{{ $tReport->accuracy_meters ?? '?' }}m)</span>
                                                    @if($tReport->distance_to_site_meters !== null)
                                                        <span>Distance au site : <strong>{{ $tReport->distance_to_site_meters }} m</strong></span>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── 3. SECTION RETOURS ET COMMENTAIRES DU SUPERVISEUR ── --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 sm:p-7 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Avis & Commentaires de l'Encadrement ({{ $report->reviews->count() }})
                </h3>
            </div>

            <div class="space-y-4">
                @forelse($report->reviews as $review)
                <div class="flex gap-3 {{ $review->reviewer_id === $report->user_id ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300">
                        {{ strtoupper(substr($review->reviewer?->personnel?->prenom ?? $review->reviewer?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 max-w-[85%]">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $review->reviewer?->personnel?->prenom ?? '' }} {{ $review->reviewer?->personnel?->nom ?? $review->reviewer?->name ?? 'Utilisateur' }}
                            </span>
                            <span class="text-[11px] text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="p-3 rounded-xl text-xs leading-relaxed {{ $review->reviewer_id === $report->user_id ? 'bg-slate-100 dark:bg-slate-800 text-right' : 'bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800' }}">
                            {{ nl2br(e($review->comment)) }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 italic py-2 text-center">
                    Aucun commentaire ou avis n'a été émis pour le moment.
                </p>
                @endforelse
            </div>

            {{-- Formulaire de réponse directe --}}
            <form id="respond-form" class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">
                <label for="review-comment" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Ajouter une instruction ou validation
                </label>
                <textarea id="review-comment" name="comment" rows="3" required
                          placeholder="Écrivez vos retours, recommandations ou validations pour le collaborateur…"
                          class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-xs focus:border-slate-900 focus:ring-slate-900 transition"></textarea>
                <div class="flex justify-end">
                    <button type="submit" id="btn-submit-review"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white dark:bg-indigo-600 dark:hover:bg-indigo-700 text-xs font-semibold rounded-xl transition shadow-sm">
                        Envoyer le retour
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Script pour actions ── --}}
    <script>
        function sendBilan(reportId) {
            if (!confirm('Envoyer le bilan de présence par email à l\'utilisateur ?')) return;
            
            fetch(`/admin/reports/${reportId}/send-bilan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Bilan envoyé avec succès !');
                } else {
                    alert('❌ ' + (data.message || 'Impossible d\'envoyer le bilan'));
                }
            })
            .catch(error => {
                alert('❌ Erreur lors de l\'envoi du bilan');
                console.error(error);
            });
        }

        document.getElementById('respond-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-review');
            const commentField = document.getElementById('review-comment');
            const comment = commentField.value.trim();
            if (!comment) return;

            btn.disabled = true;
            btn.innerHTML = 'Envoi en cours...';

            fetch("{{ route('admin.reports.respond') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    report_id: {{ $report->id }},
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('❌ ' + (data.message || 'Erreur lors de l\'envoi de la réponse'));
                    btn.disabled = false;
                    btn.innerHTML = 'Envoyer le retour';
                }
            })
            .catch(err => {
                alert('❌ Erreur réseau');
                btn.disabled = false;
                btn.innerHTML = 'Envoyer le retour';
                console.error(err);
            });
        });
    </script>
</x-app-layout>