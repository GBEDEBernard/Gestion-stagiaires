<x-app-layout>
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">
        
        @php
            $authorPersonnel = $report->user?->personnel ?? $report->etudiant?->personnel;
            $authorName = trim(($authorPersonnel?->prenom ?? '') . ' ' . ($authorPersonnel?->nom ?? $report->user?->name ?? 'Utilisateur'));
            $isStudent = !is_null($report->etudiant_id);
            $totalTasks = $relatedTasks->count();
            $totalReportsCount = $relatedTasks->sum(fn($t) => $t->dailyReports->count());
            $totalHours = $relatedTasks->sum(fn($t) => $t->dailyReports->sum('hours_declared'));
            $avgProgress = $totalTasks > 0 ? round($relatedTasks->avg('last_progress_percent')) : 0;
            $initials = collect(explode(' ', $authorName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
        @endphp

        {{-- ── 1. BARRE DE NAVIGATION ET ACTIONS DU HAUT ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" 
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] text-slate-700 dark:text-slate-200 transition"
                   title="Retour à la liste">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-[#f8fafc] tracking-tight">
                            Rapport d'activité • #{{ $report->id }}
                        </h1>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                            @if($report->status === 'submitted') bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800
                            @elseif($report->status === 'reviewed') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800
                            @else bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 @endif">
                            <span class="w-1.5 h-1.5 rounded-full @if($report->status === 'submitted') bg-blue-500 @elseif($report->status === 'reviewed') bg-emerald-500 @else bg-amber-500 @endif"></span>
                            {{ $report->status === 'submitted' ? 'Soumis' : ($report->status === 'reviewed' ? 'Révisé / Validé' : 'Brouillon') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Séance du <span class="font-medium text-slate-700 dark:text-slate-300">{{ $report->report_date->isoFormat('dddd D MMMM YYYY') }}</span>
                        • Déposé {{ $report->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.reports.download-pdf', $report->id) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold rounded-lg transition shadow-xs"
                   title="Générer et télécharger le rapport complet">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger PDF
                </a>

                @can('admin.reports.send-bilan')
                <button onclick="sendBilan({{ $report->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer le bilan
                </button>
                @endcan
            </div>
        </div>

        {{-- ── 2. SYNTHÈSE EXÉCUTIVE (AUTEUR, STAGE, MÉTRIQUES) ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            {{-- Profil Auteur --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Collaborateur</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                            {{ $isStudent ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' }}">
                            {{ $isStudent ? 'Stagiaire' : 'Employé' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $isStudent ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center font-bold text-xs shrink-0">
                            {{ $initials ?: 'U' }}
                        </div>
                        <div class="truncate">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc] truncate leading-tight">
                                {{ $authorName }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[220px]">
                                {{ $report->user?->email ?? $report->etudiant?->user?->email ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-[#dcdad2] dark:border-[#222630] space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
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
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cadre & Encadrement</span>
                        @if($report->stage)
                        <span class="text-xs text-slate-500 font-medium">
                            {{ $report->stage->site?->name ?? 'Site TFG' }}
                        </span>
                        @endif
                    </div>

                    @if($report->stage)
                        <div class="bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 rounded-lg">
                            <span class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 tracking-wider block mb-0.5">Thème retenu</span>
                            <p class="text-xs font-semibold text-slate-900 dark:text-[#f8fafc] leading-snug line-clamp-2">
                                {{ $report->stage->theme ?? 'Stage de perfectionnement' }}
                            </p>
                        </div>
                    @else
                        <p class="text-xs text-slate-500 italic py-2">Aucun stage spécifique rattaché.</p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-[#dcdad2] dark:border-[#222630] space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
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
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-3">Synthèse globale</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="p-2.5 bg-[#f6f5f0] dark:bg-[#131519] rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Tâches</span>
                            <span class="text-lg font-bold text-slate-900 dark:text-[#f8fafc] mt-0.5 block">{{ $totalTasks }}</span>
                        </div>
                        <div class="p-2.5 bg-[#f6f5f0] dark:bg-[#131519] rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Rapports</span>
                            <span class="text-lg font-bold text-slate-900 dark:text-[#f8fafc] mt-0.5 block">{{ $totalReportsCount }}</span>
                        </div>
                        <div class="p-2.5 bg-[#f6f5f0] dark:bg-[#131519] rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Heures tot.</span>
                            <span class="text-lg font-bold text-slate-900 dark:text-[#f8fafc] mt-0.5 block">{{ $totalHours }} h</span>
                        </div>
                        <div class="p-2.5 bg-[#f6f5f0] dark:bg-[#131519] rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Progression</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-0.5 block">{{ $avgProgress }}%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-[#dcdad2] dark:border-[#222630] flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Heures sur ce rapport :</span>
                    <span class="font-bold text-slate-900 dark:text-[#f8fafc]">{{ $report->hours_declared ?? 0 }} h</span>
                </div>
            </div>
        </div>

        {{-- ── 3. TÂCHES ASSOCIÉES & COMPTES-RENDUS ── --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-[#dcdad2] dark:border-[#222630] pb-2.5">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">
                        Missions & Comptes-rendus rattachés
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Historique des tâches et comptes-rendus associés à ce profil.
                    </p>
                </div>
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#131519] px-2.5 py-0.5 rounded-full border border-[#dcdad2] dark:border-[#222630]">
                    {{ $totalTasks }} tâche(s)
                </span>
            </div>

            @if($relatedTasks->isEmpty())
                <div class="p-8 text-center rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] text-slate-400 text-xs">
                    Aucune tâche n'est rattachée à cet enregistrement.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($relatedTasks as $taskIndex => $taskItem)
                        @php
                            $isCurrentTask = ($report->task_id === $taskItem->id);
                            $taskReports = $taskItem->dailyReports;
                            $taskProgress = $taskItem->last_progress_percent ?? 0;
                        @endphp

                        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border {{ $isCurrentTask ? 'border-[#1c3e96] ring-1 ring-[#1c3e96]/30' : 'border-[#dcdad2] dark:border-[#222630]' }} shadow-xs overflow-hidden">
                            
                            {{-- En-tête de la tâche --}}
                            <div class="p-4 sm:p-5 bg-[#faf9f5] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630]">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                Tâche {{ $taskIndex + 1 }}
                                            </span>
                                            <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">
                                                {{ $taskItem->title }}
                                            </h3>
                                            @if($isCurrentTask)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#1c3e96] text-white">
                                                    ★ Tâche active
                                                </span>
                                            @endif
                                        </div>
                                        @if($taskItem->description)
                                            <p class="text-xs text-slate-600 dark:text-slate-300 max-w-3xl leading-relaxed">
                                                {{ $taskItem->description }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap self-start md:self-auto">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border
                                            @if($taskItem->priority === 'high') bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800
                                            @elseif($taskItem->priority === 'low') bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700
                                            @else bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800 @endif">
                                            {{ ['high'=>'Priorité Haute','low'=>'Priorité Basse','medium'=>'Priorité Moyenne','urgent'=>'Urgente'][$taskItem->priority] ?? ucfirst($taskItem->priority ?? 'Normale') }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border
                                            @if($taskItem->status === 'completed') bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800
                                            @elseif($taskItem->status === 'in_progress') bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800
                                            @elseif($taskItem->status === 'blocked') bg-red-50 text-red-800 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800
                                            @else bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 @endif">
                                            {{ ['completed'=>'Terminée','in_progress'=>'En cours','blocked'=>'Bloquée','pending'=>'En attente','todo'=>'À faire','cancelled'=>'Annulée'][$taskItem->status] ?? 'En cours' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Jauge de progression & Échéance --}}
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-[#dcdad2] dark:border-[#222630] items-center">
                                    <div class="sm:col-span-2">
                                        <div class="flex justify-between text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                            <span>Accomplissement</span>
                                            <span class="font-bold text-slate-900 dark:text-[#f8fafc]">{{ $taskProgress }}%</span>
                                        </div>
                                        <div class="w-full h-2 bg-[#f6f5f0] dark:bg-[#131519] rounded-full overflow-hidden border border-[#dcdad2] dark:border-[#222630]">
                                            <div class="h-full bg-[#1c3e96] rounded-full transition-all duration-300" style="width: {{ $taskProgress }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-right text-xs text-slate-500 dark:text-slate-400">
                                        @if($taskItem->due_date)
                                            <span>Échéance : <strong class="text-slate-800 dark:text-slate-200">{{ $taskItem->due_date->format('d/m/Y') }}</strong></span>
                                        @else
                                            <span>Aucune échéance fixée</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Rapports soumis sur cette tâche --}}
                            <div class="p-4 sm:p-5 space-y-4">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Comptes-rendus soumis sur cette tâche ({{ $taskReports->count() }})
                                </h4>

                                @if($taskReports->isEmpty())
                                    <p class="text-xs text-slate-400 italic py-2">
                                        Aucun compte-rendu journalier n'a encore été déposé pour cette tâche.
                                    </p>
                                @else
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach($taskReports as $tReport)
                                            @php
                                                $isSelected = ($tReport->id === $report->id);
                                            @endphp
                                            <div class="rounded-lg border p-3.5 transition-all
                                                {{ $isSelected ? 'border-[#1c3e96] bg-[#1c3e96]/5 dark:bg-[#1c3e96]/10 shadow-xs' : 'border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22]' }}">
                                                
                                                <div class="flex items-start justify-between gap-2 flex-wrap border-b border-[#dcdad2] dark:border-[#222630] pb-2.5 mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">
                                                            Session du {{ $tReport->report_date->format('d/m/Y') }}
                                                        </span>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border
                                                            @if($tReport->status === 'submitted') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800
                                                            @elseif($tReport->status === 'reviewed') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800
                                                            @else bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 @endif">
                                                            {{ ['submitted' => 'Soumis', 'reviewed' => 'Validé', 'draft' => 'Brouillon'][$tReport->status] ?? ucfirst($tReport->status) }}
                                                        </span>
                                                        @if($tReport->hours_declared > 0)
                                                            <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#131519] px-2 py-0.5 rounded border border-[#dcdad2] dark:border-[#222630]">
                                                                {{ $tReport->hours_declared }} h
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        @if($isSelected)
                                                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                                                ✓ En cours de consultation
                                                            </span>
                                                        @else
                                                            <a href="{{ route('admin.reports.show', $tReport->id) }}" 
                                                               class="inline-flex items-center gap-1 text-xs font-bold text-[#1c3e96] hover:underline">
                                                                Ouvrir
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="space-y-2.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                                    @if($tReport->introduction)
                                                    <div>
                                                        <span class="font-bold text-slate-900 dark:text-slate-200 block mb-1">Introduction :</span>
                                                        <div class="p-2.5 bg-[#f6f5f0] dark:bg-[#131519] rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                                                            {{ nl2br(e($tReport->introduction)) }}
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <div>
                                                        <span class="font-bold text-slate-900 dark:text-slate-200 block mb-1">Travail accompli :</span>
                                                        <div class="p-2.5 bg-[#f6f5f0] dark:bg-[#131519] rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                                                            {{ nl2br(e($tReport->summary)) }}
                                                        </div>
                                                    </div>

                                                    @if($tReport->blockers)
                                                    <div class="p-2.5 bg-amber-500/10 border border-amber-500/20 rounded-lg text-amber-800 dark:text-amber-300">
                                                        <span class="font-bold block mb-0.5">⚠️ Difficultés & Blocages :</span>
                                                        {{ nl2br(e($tReport->blockers)) }}
                                                    </div>
                                                    @endif

                                                    @if($tReport->next_steps)
                                                    <div class="p-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-800 dark:text-emerald-300">
                                                        <span class="font-bold block mb-0.5">🚀 Prochaines étapes :</span>
                                                        {{ nl2br(e($tReport->next_steps)) }}
                                                    </div>
                                                    @endif
                                                </div>
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

        {{-- ── 4. RETOURS ET COMMENTAIRES DE L'ENCADREMENT ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-[#dcdad2] dark:border-[#222630] pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc] flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Avis & Commentaires de l'Encadrement ({{ $report->reviews->count() }})
                </h3>
            </div>

            <div class="space-y-3">
                @forelse($report->reviews as $review)
                <div class="flex gap-2.5 {{ $review->reviewer_id === $report->user_id ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300">
                        {{ strtoupper(substr($review->reviewer?->personnel?->prenom ?? $review->reviewer?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 max-w-[85%]">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">
                                {{ $review->reviewer?->personnel?->prenom ?? '' }} {{ $review->reviewer?->personnel?->nom ?? $review->reviewer?->name ?? 'Utilisateur' }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="p-3 rounded-lg text-xs leading-relaxed {{ $review->reviewer_id === $report->user_id ? 'bg-[#f6f5f0] dark:bg-[#131519] text-right' : 'bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630]' }}">
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
            <form id="respond-form" class="mt-4 pt-3 border-t border-[#dcdad2] dark:border-[#222630] space-y-3">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">
                <label for="review-comment" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Ajouter une directive ou validation
                </label>
                <textarea id="review-comment" name="comment" rows="3" required
                          placeholder="Écrivez vos retours, recommandations ou directives pour le collaborateur…"
                          class="w-full rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] p-3 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96] resize-none transition"></textarea>
                <div class="flex justify-end">
                    <button type="submit" id="btn-submit-review"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold rounded-lg transition shadow-xs">
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
                    alert('Bilan envoyé avec succès !');
                } else {
                    alert(data.message || 'Impossible d\'envoyer le bilan');
                }
            })
            .catch(error => {
                alert('Erreur lors de l\'envoi du bilan');
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
                    alert(data.message || 'Erreur lors de l\'envoi de la réponse');
                    btn.disabled = false;
                    btn.innerHTML = 'Envoyer le retour';
                }
            })
            .catch(err => {
                alert('Erreur réseau');
                btn.disabled = false;
                btn.innerHTML = 'Envoyer le retour';
                console.error(err);
            });
        });
    </script>
</x-app-layout>