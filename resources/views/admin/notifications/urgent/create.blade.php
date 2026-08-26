<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-8" x-data="urgentNotificationManager()">
        
        {{-- En-tête de la page --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-red-700 via-rose-700 to-red-800 p-6 sm:p-8 text-white shadow-2xl">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-40 h-40 bg-red-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase bg-white/20 border border-white/30 text-red-100 shadow-sm backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-amber-300 animate-ping"></span>
                            Zone d'Alerte Direction
                        </span>
                        <span class="text-xs text-white/80 font-medium">Priorité Maximale</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Notifications Urgentes & Ciblage Avancé
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-red-100/90 max-w-2xl">
                        Diffusez instantanément des messages prioritaires avec affichage persistant et suivi des accusés de réception en temps réel.
                    </p>
                </div>

                {{-- Stats rapides --}}
                <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md p-4 rounded-2xl border border-white/10 shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl">
                        👥
                    </div>
                    <div>
                        <p class="text-xs text-red-200 font-medium">Comptes Actifs</p>
                        <p class="text-2xl font-black text-white leading-tight">{{ $totalActiveUsers }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Messages Flash --}}
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
        @endif

        {{-- Formulaire de composition --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 flex items-center justify-center font-black">
                        ⚡
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Composer une alerte urgente</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Remplissez les détails et définissez avec précision le groupe cible.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.notifications.urgent.store') }}" method="POST" class="p-6 sm:p-8 space-y-6" @submit.prevent="confirmSubmit($event)">
                @csrf

                {{-- 1. Titre & Modèles rapides --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-900 dark:text-gray-200">
                        Titre de l'alerte <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           x-model="form.title"
                           required
                           placeholder="Ex: Arrêt d'urgence de la plateforme pour maintenance" 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-sm">
                    
                    {{-- Suggestions de titres rapides --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="text-xs text-gray-400">Modèles rapides :</span>
                        <button type="button" @click="form.title = 'Arrêt d\'urgence de la plateforme'" class="px-2.5 py-1 rounded-xl text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-red-50 hover:text-red-600 transition">
                            Arrêt plateforme
                        </button>
                        <button type="button" @click="form.title = 'Réunion générale obligatoire à la Direction'" class="px-2.5 py-1 rounded-xl text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-red-50 hover:text-red-600 transition">
                            Réunion obligatoire
                        </button>
                        <button type="button" @click="form.title = 'Consigne de sécurité importante'" class="px-2.5 py-1 rounded-xl text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-red-50 hover:text-red-600 transition">
                            Consigne sécurité
                        </button>
                        <button type="button" @click="form.title = 'Date limite impérative de soumission des rapports'" class="px-2.5 py-1 rounded-xl text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-red-50 hover:text-red-600 transition">
                            Rapports requis
                        </button>
                    </div>
                </div>

                {{-- 2. Message détaillé --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-bold text-gray-900 dark:text-gray-200">
                            Message détaillé <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-400" x-text="form.message.length + '/2000 caractères'"></span>
                    </div>
                    <textarea name="message" 
                              x-model="form.message"
                              rows="4" 
                              required
                              maxlength="2000"
                              placeholder="Ex: Merci de bien vouloir enregistrer tous vos travaux en cours. Une mise à jour critique sera déployée ce jour à 18h00. Veuillez accuser réception de ce message."
                              class="w-full px-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-sm"></textarea>
                </div>

                {{-- 3. Lien d'action optionnel --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-900 dark:text-gray-200">
                        Lien d'action optionnel (URL)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            🔗
                        </span>
                        <input type="url" 
                               name="url" 
                               x-model="form.url"
                               placeholder="https://... (ex: lien vers document ou formulaire)" 
                               class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-sm">
                    </div>
                </div>

                {{-- 4. Segmentation & Ciblage des destinataires --}}
                <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white">
                                Segmentation & Ciblage
                            </h3>
                            <p class="text-xs text-gray-500">Sélectionnez la catégorie exacte d'utilisateurs qui recevront cette alerte.</p>
                        </div>

                        {{-- Indicateur live des destinataires --}}
                        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs font-bold shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span x-text="loadingCount ? 'Calcul en cours…' : recipientCount + ' destinataire(s) concerné(s)'"></span>
                        </div>
                    </div>

                    {{-- Grille des 7 types de cibles --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        
                        {{-- Cible 1: Tous --}}
                        <label @click="selectTarget('all')" :class="form.target_type === 'all' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between">
                            <input type="radio" name="target_type" value="all" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🌐</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Tous les utilisateurs</p>
                                    <p class="text-[11px] text-gray-400">Diffusion générale</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cible 2: Tous les employés --}}
                        <label @click="selectTarget('employes')" :class="form.target_type === 'employes' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between">
                            <input type="radio" name="target_type" value="employes" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">👔</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Tous les employés</p>
                                    <p class="text-[11px] text-gray-400">Personnel & Cadres</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cible 3: Employés par poste --}}
                        <label @click="selectTarget('poste')" :class="form.target_type === 'poste' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between">
                            <input type="radio" name="target_type" value="poste" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🎯</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Par Poste spécifique</p>
                                    <p class="text-[11px] text-gray-400">DT, DTA, DG, etc.</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cible 4: Tous les stagiaires --}}
                        <label @click="selectTarget('stagiaires')" :class="form.target_type === 'stagiaires' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between">
                            <input type="radio" name="target_type" value="stagiaires" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🎓</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Tous les stagiaires</p>
                                    <p class="text-[11px] text-gray-400">Tous types confondus</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cible 5: Stagiaires par type de stage --}}
                        <label @click="selectTarget('typestage')" :class="form.target_type === 'typestage' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between">
                            <input type="radio" name="target_type" value="typestage" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📑</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Par Type de stage</p>
                                    <p class="text-[11px] text-gray-400">Académique / Pro</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cible 6: Par Domaine d'activité --}}
                        <label @click="selectTarget('domaine')" :class="form.target_type === 'domaine' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between">
                            <input type="radio" name="target_type" value="domaine" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🏢</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Par Domaine</p>
                                    <p class="text-[11px] text-gray-400">Direction / Service</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cible 7: Individuel --}}
                        <label @click="selectTarget('individual')" :class="form.target_type === 'individual' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/30 border-red-300' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700'" class="relative p-4 rounded-2xl border cursor-pointer hover:border-red-300 transition flex flex-col justify-between sm:col-span-2">
                            <input type="radio" name="target_type" value="individual" x-model="form.target_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">👤</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Sélection Individuelle</p>
                                    <p class="text-[11px] text-gray-400">Choisir un ou plusieurs comptes précis</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- Options secondaires conditionnelles --}}
                    
                    {{-- Si Poste sélectionné --}}
                    <div x-show="form.target_type === 'poste'" x-cloak class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                            Choisir le poste concerné
                        </label>
                        <select name="target_value" x-model="form.target_value" @change="fetchRecipientCount()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm">
                            <option value="">-- Sélectionnez un poste --</option>
                            @foreach($targetOptions['postes'] as $poste)
                            <option value="{{ $poste }}">{{ $poste }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Si Type de Stage sélectionné --}}
                    <div x-show="form.target_type === 'typestage'" x-cloak class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                            Choisir le type de stage
                        </label>
                        <select name="target_value" x-model="form.target_value" @change="fetchRecipientCount()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm">
                            <option value="">-- Sélectionnez un type de stage --</option>
                            @foreach($targetOptions['typeStages'] as $typeStage)
                            <option value="{{ $typeStage->id }}">{{ $typeStage->libelle }} ({{ $typeStage->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Si Domaine sélectionné --}}
                    <div x-show="form.target_type === 'domaine'" x-cloak class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                            Choisir la direction / domaine
                        </label>
                        <select name="target_value" x-model="form.target_value" @change="fetchRecipientCount()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm">
                            <option value="">-- Sélectionnez un domaine --</option>
                            @foreach($targetOptions['domaines'] as $domaine)
                            <option value="{{ $domaine->id }}">{{ $domaine->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Si Sélection Individuelle --}}
                    <div x-show="form.target_type === 'individual'" x-cloak class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 space-y-3">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                            Rechercher et sélectionner les utilisateurs (<span x-text="form.individual_ids.length"></span> sélectionnés)
                        </label>
                        
                        <input type="text" 
                               x-model="userSearchQuery" 
                               placeholder="Rechercher par nom ou email…"
                               class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm">

                        <div class="max-h-48 overflow-y-auto custom-scrollbar space-y-1 bg-white dark:bg-gray-900 p-2 rounded-xl border border-gray-200 dark:border-gray-700">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <label class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-xs">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" 
                                               name="individual_ids[]"
                                               :value="user.id" 
                                               x-model="form.individual_ids" 
                                               @change="fetchRecipientCount()"
                                               class="rounded text-red-600 focus:ring-red-500">
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white" x-text="user.name"></p>
                                            <p class="text-gray-400" x-text="user.email"></p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px]" x-text="user.role"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Bouton d'action --}}
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-black text-sm text-white bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-red-800 transition shadow-xl shadow-red-600/30 hover:shadow-red-600/50 hover:scale-[1.02] active:scale-95">
                        <svg class="w-5 h-5 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Diffuser l'Alerte Urgente</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Historique & Accusés de Réception --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        📋
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Historique & Accusés de réception</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Suivez en direct qui a pris connaissance de chaque alerte officielle.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                @if($recentBatches->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-2xl mb-3">
                        📭
                    </div>
                    <p class="font-bold text-gray-700 dark:text-gray-300 text-sm">Aucune alerte urgente diffusée pour le moment.</p>
                    <p class="text-xs text-gray-400 mt-1">Les alertes envoyées apparaîtront ici avec le taux d'acquittement.</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($recentBatches as $batch)
                    <div class="p-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/40 hover:border-red-200 transition">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            
                            {{-- Infos alerte --}}
                            <div class="space-y-1 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300">
                                        {{ $batch->target_label }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        Diffusée le {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y à H:i') }}
                                    </span>
                                    @if($batch->sender)
                                    <span class="text-xs text-gray-500">
                                        par <strong>{{ $batch->sender->name }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                    {{ $batch->title }}
                                </h3>

                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $batch->message }}
                                </p>
                            </div>

                            {{-- Taux de lecture & Barre de progression --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 shrink-0">
                                <div class="w-48 space-y-1.5">
                                    <div class="flex justify-between text-xs font-bold">
                                        <span class="text-gray-700 dark:text-gray-300">Accusés de réception</span>
                                        <span class="{{ $batch->read_percentage >= 80 ? 'text-emerald-600' : ($batch->read_percentage >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                            {{ $batch->read_count }} / {{ $batch->total_recipients }} ({{ $batch->read_percentage }}%)
                                        </span>
                                    </div>
                                    <div class="w-full h-2.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $batch->read_percentage >= 80 ? 'bg-emerald-500' : ($batch->read_percentage >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                             style="width: {{ $batch->read_percentage }}%"></div>
                                    </div>
                                </div>

                                <button type="button" 
                                        @click="openDetailsModal('{{ $batch->batch_id }}')"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition shadow-sm">
                                    <span>Détails & Destinataires</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Modale Détails du lot & Destinataires --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.away="modalOpen = false" class="w-full max-w-3xl bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
                
                {{-- Header modal --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900 dark:text-white" x-text="activeBatch?.title || 'Détails de l\'alerte'"></h3>
                        <p class="text-xs text-gray-500" x-text="'Cible : ' + (activeBatch?.target_label || '')"></p>
                    </div>
                    <button @click="modalOpen = false" class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 hover:bg-gray-200 transition">
                        ✕
                    </button>
                </div>

                {{-- Corps modal --}}
                <div class="p-6 overflow-y-auto custom-scrollbar space-y-4">
                    <template x-if="loadingDetails">
                        <div class="py-12 text-center text-gray-400">
                            <svg class="w-8 h-8 mx-auto animate-spin text-red-500 mb-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <p class="text-xs font-semibold">Chargement des destinataires…</p>
                        </div>
                    </template>

                    <template x-if="!loadingDetails && activeBatch">
                        <div class="space-y-4">
                            {{-- Synthèse --}}
                            <div class="grid grid-cols-3 gap-3 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-center">
                                <div>
                                    <p class="text-xs text-gray-400">Total envoyés</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white" x-text="activeBatch.total_recipients"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-emerald-500">Accusés reçus</p>
                                    <p class="text-lg font-black text-emerald-600" x-text="activeBatch.read_count"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-rose-500">En attente</p>
                                    <p class="text-lg font-black text-rose-600" x-text="activeBatch.unread_count"></p>
                                </div>
                            </div>

                            {{-- Liste des destinataires --}}
                            <div class="space-y-1.5">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">État individuel des destinataires</p>
                                
                                <div class="divide-y divide-gray-100 dark:divide-gray-700 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                    <template x-for="rec in activeBatch.recipients" :key="rec.id">
                                        <div class="p-3 flex items-center justify-between text-xs hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold"
                                                     :class="rec.is_read ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'">
                                                    <span x-text="rec.name.substring(0, 2).toUpperCase()"></span>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white" x-text="rec.name"></p>
                                                    <p class="text-gray-400 text-[11px]" x-text="rec.email + ' • ' + rec.role"></p>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <template x-if="rec.is_read">
                                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span x-text="'Lu le ' + rec.read_at"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!rec.is_read">
                                                    <span class="inline-flex items-center gap-1 text-rose-500 font-semibold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                        <span>Non acquitté</span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function urgentNotificationManager() {
            return {
                form: {
                    title: '',
                    message: '',
                    url: '',
                    target_type: 'all',
                    target_value: '',
                    individual_ids: []
                },
                recipientCount: {{ $totalActiveUsers }},
                loadingCount: false,
                userSearchQuery: '',
                allUsers: @json($targetOptions['users'] ?? []),
                modalOpen: false,
                loadingDetails: false,
                activeBatch: null,

                get filteredUsers() {
                    if (!this.userSearchQuery) return this.allUsers;
                    const q = this.userSearchQuery.toLowerCase();
                    return this.allUsers.filter(u => 
                        u.name.toLowerCase().includes(q) || 
                        u.email.toLowerCase().includes(q)
                    );
                },

                selectTarget(type) {
                    this.form.target_type = type;
                    this.form.target_value = '';
                    this.form.individual_ids = [];
                    this.fetchRecipientCount();
                },

                async fetchRecipientCount() {
                    this.loadingCount = true;
                    try {
                        const params = new URLSearchParams({
                            target_type: this.form.target_type,
                            target_value: this.form.target_value || '',
                            individual_ids: this.form.individual_ids.join(',')
                        });

                        const res = await fetch(`/admin/notifications/urgent/count-recipients?${params.toString()}`);
                        const data = await res.json();
                        this.recipientCount = data.count;
                    } catch (e) {
                        console.error('Erreur comptage destinataires:', e);
                    } finally {
                        this.loadingCount = false;
                    }
                },

                async openDetailsModal(batchId) {
                    this.modalOpen = true;
                    this.loadingDetails = true;
                    try {
                        const res = await fetch(`/admin/notifications/urgent/batch/${batchId}`);
                        this.activeBatch = await res.json();
                    } catch (e) {
                        console.error('Erreur chargement détails lot:', e);
                    } finally {
                        this.loadingDetails = false;
                    }
                },

                confirmSubmit(event) {
                    if (this.recipientCount === 0) {
                        Swal.fire({
                            title: 'Aucun destinataire',
                            text: 'Votre ciblage ne correspond à aucun compte utilisateur actif.',
                            icon: 'warning',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Diffuser cette alerte urgente ?',
                        html: `Cette alerte sera affichée avec la priorité maximale à <strong>${this.recipientCount} destinataire(s)</strong> et restera affichée jusqu'à validation de leur accusé de réception.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Oui, diffuser immédiatement !',
                        cancelButtonText: 'Annuler'
                    }).then(r => {
                        if (r.isConfirmed) {
                            event.target.submit();
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
