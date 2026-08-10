<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.all', request()->query()) }}" 
                   class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Détails du rapport</h1>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ $report->report_date->format('l j F Y') }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($report->status === 'submitted') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                            @elseif($report->status === 'reviewed') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                            @else bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 @endif">
                            @if($report->status === 'submitted')
                                📤 Soumis
                            @elseif($report->status === 'reviewed')
                                ✅ Révisé
                            @else
                                📝 Brouillon
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- Actions --}}
            <div class="flex items-center gap-3 w-full sm:w-auto">
                @can('admin.reports.send-bilan')
                <button onclick="sendBilan({{ $report->id }})"
                        class="flex-1 sm:flex-none px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer le bilan
                </button>
                @endcan
                <a href="{{ route('admin.reports.all', request()->query()) }}" 
                   class="flex-1 sm:flex-none px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        {{-- Grille principale --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Carte utilisateur --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0
                            @if($report->etudiant_id) bg-gradient-to-br from-blue-500 to-blue-600
                            @else bg-gradient-to-br from-emerald-500 to-emerald-600 @endif">
                            {{ strtoupper(substr($report->user?->personnel?->prenom ?? $report->user?->name ?? 'U', 0, 1)) }}
                            {{ strtoupper(substr($report->user?->personnel?->nom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $report->user?->personnel?->prenom ?? '' }} {{ $report->user?->personnel?->nom ?? $report->user?->name ?? 'Utilisateur' }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($report->etudiant_id) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 @endif">
                                    @if($report->etudiant_id)
                                        🎓 Étudiant
                                    @else
                                        💼 Employé
                                    @endif
                                </span>
                                <span>•</span>
                                <span class="truncate max-w-[200px]">{{ $report->user?->email ?? $report->etudiant?->user?->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contenu du rapport --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    
                    {{-- En-tête avec méta --}}
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium
                                @if($report->status === 'submitted') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($report->status === 'reviewed') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                @else bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 @endif">
                                @if($report->status === 'submitted')
                                    📤 Soumis
                                @elseif($report->status === 'reviewed')
                                    ✅ Révisé
                                @else
                                    📝 Brouillon
                                @endif
                            </span>
                            @if($report->hours_declared > 0)
                            <span class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M6 6h12"/>
                                </svg>
                                {{ $report->hours_declared }}h
                            </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 text-right">
                            <div>Créé {{ $report->created_at->diffForHumans() }}</div>
                            @if($report->updated_at != $report->created_at)
                            <div>Modifié {{ $report->updated_at->diffForHumans() }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Corps --}}
                    <div class="p-6 space-y-5">
                        @if($report->introduction)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Introduction</h4>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ nl2br(e($report->introduction)) }}</p>
                        </div>
                        @endif

                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Travail réalisé</h4>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ nl2br(e($report->summary)) }}</p>
                        </div>

                        @if($report->blockers)
                        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 rounded-r-xl p-4">
                            <h4 class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider mb-2">⚠️ Blocages rencontrés</h4>
                            <p class="text-red-700 dark:text-red-300 leading-relaxed">{{ nl2br(e($report->blockers)) }}</p>
                        </div>
                        @endif

                        @if($report->next_steps)
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-400 rounded-r-xl p-4">
                            <h4 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2">🎯 Prochaines étapes</h4>
                            <p class="text-emerald-700 dark:text-emerald-300 leading-relaxed">{{ nl2br(e($report->next_steps)) }}</p>
                        </div>
                        @endif

                        @if($report->task)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">📋 Tâche associée</h4>
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="text-gray-700 dark:text-gray-300">{{ $report->task->title }}</span>
                                @if($report->task_progress_percent !== null)
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ $report->task_progress_percent }}%
                                    </span>
                                    <div class="w-24 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" 
                                             style="width: {{ $report->task_progress_percent }}%"></div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($report->stage && $report->stage->theme)
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-4">
                            <h4 class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">🎓 Stage</h4>
                            <p class="text-indigo-700 dark:text-indigo-300">{{ $report->stage->theme }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Commentaires --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            💬 Commentaires ({{ $report->reviews->count() }})
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        @forelse($report->reviews as $review)
                        <div class="flex gap-3 {{ $review->reviewer_id === $report->user_id ? 'flex-row-reverse' : '' }}">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                                @if($review->reviewer_id === $report->user_id) 
                                    bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                @else 
                                    bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 
                                @endif">
                                {{ strtoupper(substr($review->reviewer?->personnel?->prenom ?? $review->reviewer?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 max-w-[85%]">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $review->reviewer?->personnel?->prenom ?? '' }} {{ $review->reviewer?->personnel?->nom ?? $review->reviewer?->name ?? 'Utilisateur' }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    @if($review->reviewer_id === $report->user_id)
                                    <span class="text-xs bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-0.5 rounded-full">Auteur</span>
                                    @endif
                                </div>
                                <div class="mt-1 inline-block rounded-2xl px-4 py-2.5
                                    @if($review->reviewer_id === $report->user_id)
                                        bg-blue-50 dark:bg-blue-900/20 text-right
                                    @else
                                        bg-gray-50 dark:bg-gray-900/50
                                    @endif">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ nl2br(e($review->comment)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                            Aucun commentaire pour ce rapport.
                        </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Colonne latérale --}}
            <div class="space-y-6">
                
                {{-- Métadonnées --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">📋 Informations</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Date</dt>
                            <dd class="text-gray-700 dark:text-gray-300 font-medium">{{ $report->report_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Statut</dt>
                            <dd>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($report->status === 'submitted') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($report->status === 'reviewed') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                    @else bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 @endif">
                                    @if($report->status === 'submitted') Soumis
                                    @elseif($report->status === 'reviewed') Révisé
                                    @else Brouillon @endif
                                </span>
                            </dd>
                        </div>
                        @if($report->hours_declared > 0)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Heures travaillées</dt>
                            <dd class="text-gray-700 dark:text-gray-300 font-medium">{{ $report->hours_declared }}h</dd>
                        </div>
                        @endif
                        <div class="flex justify-between border-t border-gray-100 dark:border-gray-700 pt-3 mt-3">
                            <dt class="text-gray-500 dark:text-gray-400">Créé</dt>
                            <dd class="text-gray-700 dark:text-gray-300 text-right">{{ $report->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        @if($report->updated_at != $report->created_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Modifié</dt>
                            <dd class="text-gray-700 dark:text-gray-300 text-right">{{ $report->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                {{-- Localisation GPS --}}
                @if($report->latitude && $report->longitude)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">📍 Localisation</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Latitude</dt>
                            <dd class="text-gray-700 dark:text-gray-300 font-mono">{{ number_format($report->latitude, 6) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Longitude</dt>
                            <dd class="text-gray-700 dark:text-gray-300 font-mono">{{ number_format($report->longitude, 6) }}</dd>
                        </div>
                        @if($report->accuracy_meters)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Précision</dt>
                            <dd class="text-gray-700 dark:text-gray-300">{{ $report->accuracy_meters }} m</dd>
                        </div>
                        @endif
                        @if($report->location_method)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Méthode</dt>
                            <dd class="text-gray-700 dark:text-gray-300">
                                @if($report->location_method === 'geolocation') GPS
                                @elseif($report->location_method === 'ip') IP
                                @else {{ $report->location_method }} @endif
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif

                {{-- Actions rapides --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">⚡ Actions</h3>
                    <div class="space-y-2.5">
                        @can('admin.reports.send-bilan')
                        <button onclick="sendBilan({{ $report->id }})"
                                class="w-full flex items-center gap-3 px-4 py-2.5 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-xl transition text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Envoyer le bilan
                        </button>
                        @endcan
                        <a href="{{ route('admin.reports.all', request()->query()) }}"
                           class="w-full flex items-center gap-3 px-4 py-2.5 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-xl transition text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        function sendBilan(reportId) {
            if (!confirm('Envoyer le bilan par email au responsable ?')) return;
            
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
    </script>
</x-app-layout>